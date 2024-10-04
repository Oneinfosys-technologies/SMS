<?php

namespace App\Imports\Reception;

use App\Concerns\ItemImport;
use App\Enums\Gender;
use App\Enums\OptionType;
use App\Enums\Reception\EnquiryStatus;
use App\Helpers\CalHelper;
use App\Models\Academic\Course;
use App\Models\Employee\Employee;
use App\Models\Option;
use App\Models\Reception\Enquiry;
use App\Models\Reception\EnquiryRecord;
use App\Support\FormatCodeNumber;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class EnquiryImport implements ToCollection, WithHeadingRow
{
    use ItemImport, FormatCodeNumber;

    protected $limit = 100;

    public function collection(Collection $rows)
    {
        if (count($rows) > $this->limit) {
            throw ValidationException::withMessages(['message' => trans('general.errors.max_import_limit_crossed', ['attribute' => $this->limit])]);
        }

        $logFile = $this->getLogFile('enquiry');

        [$errors, $rows] = $this->validate($rows);

        $this->checkForErrors('enquiry', $errors);

        if (! request()->boolean('validate') && ! \Storage::disk('local')->exists($logFile)) {
            $this->import($rows);
        }
    }

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

    private function import(Collection $rows)
    {
        activity()->disableLogging();

        foreach ($rows as $row) {
            $codeNumberDetail = $this->codeNumber();

            $enquiryDate = Arr::get($row, 'date_of_enquiry');
            $birthDate = Arr::get($row, 'birth_date');

            if (is_int($enquiryDate)) {
                $enquiryDate = Date::excelToDateTimeObject($enquiryDate)->format('Y-m-d');
            } else {
                $enquiryDate = Carbon::parse($enquiryDate)->toDateString();
            }

            if (is_int($birthDate)) {
                $birthDate = Date::excelToDateTimeObject($birthDate)->format('Y-m-d');
            } else {
                $birthDate = Carbon::parse($birthDate)->toDateString();
            }

            $enquiry = Enquiry::forceCreate([
                'number_format' => Arr::get($codeNumberDetail, 'number_format'),
                'number' => Arr::get($codeNumberDetail, 'number'),
                'code_number' => Arr::get($codeNumberDetail, 'code_number'),
                'period_id' => auth()->user()?->current_period_id,
                'name' => Arr::get($row, 'name'),
                'email' => Arr::get($row, 'email'),
                'contact_number' => Arr::get($row, 'contact_number'),
                'type_id' => Arr::get($row, 'type_id'),
                'source_id' => Arr::get($row, 'source_id'),
                'employee_id' => Arr::get($row, 'employee_id'),
                'date' => $enquiryDate,
                'status' => EnquiryStatus::OPEN,
                'remarks' => Arr::get($row, 'remarks'),
            ]);

            $enquiryRecord = EnquiryRecord::forceCreate([
                'enquiry_id' => $enquiry->id,
                'student_name' => Arr::get($row, 'student_name'),
                'birth_date' => $birthDate,
                'gender' => strtolower(Arr::get($row, 'gender')),
                'course_id' => Arr::get($row, 'course_id'),
            ]);
        }

        activity()->enableLogging();
    }

    private function validate(Collection $rows)
    {
        $types = Option::query()
            ->byTeam()
            ->whereType(OptionType::ENQUIRY_TYPE)
            ->get();

        $sources = Option::query()
            ->byTeam()
            ->whereType(OptionType::ENQUIRY_SOURCE)
            ->get();

        $courses = Course::query()
            ->byPeriod()
            ->get();

        $employees = Employee::query()
            ->byTeam()
            ->select('code_number', 'id')
            ->get();

        $errors = [];

        $newRows = [];
        foreach ($rows as $index => $row) {
            $rowNo = $index + 2;

            $name = Arr::get($row, 'name');
            $email = Arr::get($row, 'email');
            $contactNumber = Arr::get($row, 'contact_number');
            $type = Arr::get($row, 'type');
            $source = Arr::get($row, 'source');
            $employee = Arr::get($row, 'assigned_to');
            $enquiryDate = Arr::get($row, 'date_of_enquiry');
            $studentName = Arr::get($row, 'student_name');
            $birthDate = Arr::get($row, 'birth_date');
            $gender = Arr::get($row, 'gender');
            $course = Arr::get($row, 'course');
            $remarks = Arr::get($row, 'remarks');

            if (! $name) {
                $errors[] = $this->setError($rowNo, trans('reception.enquiry.props.name'), 'required');
            } elseif (strlen($name) < 2 || strlen($name) > 100) {
                $errors[] = $this->setError($rowNo, trans('reception.enquiry.props.name'), 'min_max', ['min' => 2, 'max' => 100]);
            }

            if ($email && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = $this->setError($rowNo, trans('reception.enquiry.props.email'), 'invalid');
            }

            if (! $contactNumber) {
                $errors[] = $this->setError($rowNo, trans('reception.enquiry.props.contact_number'), 'required');
            }

            if (is_int($enquiryDate)) {
                $enquiryDate = Date::excelToDateTimeObject($enquiryDate)->format('Y-m-d');
            }

            if ($enquiryDate && ! CalHelper::validateDate($enquiryDate)) {
                $errors[] = $this->setError($rowNo, trans('reception.enquiry.props.date'), 'invalid');
            }

            if ($type && ! in_array($type, $types->pluck('name')->all())) {
                $errors[] = $this->setError($rowNo, trans('reception.enquiry.props.type'), 'invalid');
            }

            if ($source && ! in_array($source, $sources->pluck('name')->all())) {
                $errors[] = $this->setError($rowNo, trans('reception.enquiry.props.source'), 'invalid');
            }

            if (! $studentName) {
                $errors[] = $this->setError($rowNo, trans('reception.enquiry.props.student_name'), 'required');
            } elseif (strlen($studentName) < 2 || strlen($studentName) > 100) {
                $errors[] = $this->setError($rowNo, trans('reception.enquiry.props.student_name'), 'min_max', ['min' => 2, 'max' => 100]);
            }

            if (is_int($birthDate)) {
                $birthDate = Date::excelToDateTimeObject($birthDate)->format('Y-m-d');
            }

            if ($birthDate && ! CalHelper::validateDate($birthDate)) {
                $errors[] = $this->setError($rowNo, trans('contact.props.birth_date'), 'invalid');
            }

            if (! $gender) {
                $errors[] = $this->setError($rowNo, trans('contact.props.gender'), 'required');
            } elseif ($gender && ! in_array(strtolower($gender), Gender::getKeys())) {
                $errors[] = $this->setError($rowNo, trans('contact.props.gender'), 'invalid');
            }

            if ($course && ! in_array($course, $courses->pluck('name')->all())) {
                $errors[] = $this->setError($rowNo, trans('academic.course.course'), 'invalid');
            }

            if ($employee && ! in_array($employee, $employees->pluck('code_number')->all())) {
                $errors[] = $this->setError($rowNo, trans('employee.employee'), 'invalid');
            }

            if ($remarks && (strlen($remarks) < 2 || strlen($remarks) > 1000)) {
                $errors[] = $this->setError($rowNo, trans('reception.enquiry.props.remarks'), 'min_max', ['min' => 2, 'max' => 1000]);
            }

            $row['employee_id'] = $employees->firstWhere('code_number', $employee)?->id;
            $row['type_id'] = $types->firstWhere('name', $type)?->id;
            $row['source_id'] = $sources->firstWhere('name', $source)?->id;
            $row['course_id'] = $courses->firstWhere('name', $course)?->id;

            $newRows[] = $row;
        }

        $rows = collect($newRows);

        return [$errors, $rows];
    }
}
