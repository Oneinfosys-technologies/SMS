<?php

namespace App\Services\Reception;

use App\Enums\Gender;
use App\Enums\OptionType;
use App\Enums\Reception\EnquiryStatus;
use App\Http\Resources\Academic\PeriodResource;
use App\Http\Resources\OptionResource;
use App\Models\Academic\Period;
use App\Models\Option;
use App\Models\Reception\Enquiry;
use App\Models\Reception\EnquiryRecord;
use App\Support\FormatCodeNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class EnquiryService
{
    use FormatCodeNumber;

    private function codeNumber(): array
    {
        $numberPrefix = config('config.reception.enquiry_number_prefix');
        $numberSuffix = config('config.reception.enquiry_number_suffix');
        $digit = config('config.reception.enquiry_number_digit', 0);

        $numberFormat = $numberPrefix.'%NUMBER%'.$numberSuffix;

        $codeNumber = (int) Enquiry::query()
            ->byTeam()
            ->whereNumberFormat($numberFormat)
            ->max('number') + 1;

        return $this->getCodeNumber(number: $codeNumber, digit: $digit, format: $numberFormat);
    }

    public function preRequisite(Request $request): array
    {
        $types = OptionResource::collection(Option::query()
            ->byTeam()
            ->whereType(OptionType::ENQUIRY_TYPE->value)
            ->get());

        $sources = OptionResource::collection(Option::query()
            ->byTeam()
            ->whereType(OptionType::ENQUIRY_SOURCE->value)
            ->get());

        $genders = Gender::getOptions();

        $periods = PeriodResource::collection(Period::query()
            ->byTeam()
            ->get());

        $statuses = EnquiryStatus::getOptions();

        return compact('types', 'sources', 'genders', 'periods', 'statuses');
    }

    public function create(Request $request): Enquiry
    {
        \DB::beginTransaction();

        $enquiry = Enquiry::forceCreate($this->formatParams($request));

        $this->updateRecords($request, $enquiry);

        $enquiry->addMedia($request);

        \DB::commit();

        return $enquiry;
    }

    private function formatParams(Request $request, ?Enquiry $enquiry = null): array
    {
        $formatted = [
            'period_id' => $request->period_id,
            'date' => $request->date,
            'type_id' => $request->type_id,
            'source_id' => $request->source_id,
            'employee_id' => $request->employee_id,
            'name' => $request->name,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
            'remarks' => $request->remarks,
        ];

        if (! $enquiry) {
            $codeNumberDetail = $this->codeNumber();

            $formatted['number_format'] = Arr::get($codeNumberDetail, 'number_format');
            $formatted['number'] = Arr::get($codeNumberDetail, 'number');
            $formatted['code_number'] = Arr::get($codeNumberDetail, 'code_number');
            $formatted['status'] = EnquiryStatus::OPEN->value;
        }

        return $formatted;
    }

    private function updateRecords(Request $request, Enquiry $enquiry): void
    {
        $studentNames = [];
        foreach ($request->records as $record) {
            $enquiryRecord = EnquiryRecord::firstOrCreate([
                'enquiry_id' => $enquiry->id,
                'student_name' => Arr::get($record, 'student_name'),
            ]);

            $studentNames[] = Arr::get($record, 'student_name');

            $enquiryRecord->birth_date = Arr::get($record, 'birth_date');
            $enquiryRecord->gender = Arr::get($record, 'gender');
            $enquiryRecord->course_id = Arr::get($record, 'course_id');
            $enquiryRecord->status = EnquiryStatus::OPEN->value;
            $enquiryRecord->save();
        }

        EnquiryRecord::query()
            ->whereEnquiryId($enquiry->id)
            ->whereNotIn('student_name', $studentNames)
            ->delete();
    }

    public function update(Request $request, Enquiry $enquiry): void
    {
        \DB::beginTransaction();

        $enquiry->forceFill($this->formatParams($request, $enquiry))->save();

        $this->updateRecords($request, $enquiry);

        $enquiry->updateMedia($request);

        \DB::commit();
    }

    public function deletable(Enquiry $enquiry): void
    {
        if ($enquiry->status != EnquiryStatus::OPEN) {
            throw ValidationException::withMessages(['message' => trans('reception.enquiry.could_not_delete_if_closed')]);
        }
    }
}
