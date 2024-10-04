<?php

namespace App\Services\Student;

use App\Actions\CreateContact;
use App\Enums\FamilyRelation;
use App\Enums\Finance\PaymentStatus;
use App\Enums\Gender;
use App\Enums\Student\RegistrationStatus;
use App\Http\Resources\Academic\PeriodResource;
use App\Models\Academic\Period;
use App\Models\Contact;
use App\Models\Guardian;
use App\Models\Student\Registration;
use App\Models\Student\Student;
use App\Support\FormatCodeNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RegistrationService
{
    use FormatCodeNumber;

    public function codeNumber(int $courseId): array
    {
        $numberPrefix = config('config.student.registration_number_prefix');
        $numberSuffix = config('config.student.registration_number_suffix');
        $digit = config('config.student.registration_number_digit', 0);

        $numberFormat = $numberPrefix.'%NUMBER%'.$numberSuffix;

        $string = $this->preFormatForAcademicCourse($courseId, $numberFormat);

        $codeNumber = (int) Registration::query()
            ->join('periods', 'periods.id', '=', 'registrations.period_id')
            ->when(auth()->check(), function ($q) {
                $q->where('periods.team_id', auth()->user()?->current_team_id);
            })
            ->whereNumberFormat($string)
            ->max('number') + 1;

        return $this->getCodeNumber(number: $codeNumber, digit: $digit, format: $string);
    }

    public function preRequisite(): array
    {
        $genders = Gender::getOptions();

        $relations = FamilyRelation::getOptions();

        $studentTypes = [
            ['label' => trans('global.new', ['attribute' => trans('student.student')]), 'value' => 'new'],
            ['label' => trans('global.existing', ['attribute' => trans('student.student')]), 'value' => 'existing'],
        ];

        $guardianTypes = [
            ['label' => trans('global.new', ['attribute' => trans('guardian.guardian')]), 'value' => 'new'],
            ['label' => trans('global.existing', ['attribute' => trans('guardian.guardian')]), 'value' => 'existing'],
        ];

        $periods = PeriodResource::collection(Period::query()
            ->byTeam()
            ->get());

        $statuses = RegistrationStatus::getOptions();

        $types = [
            ['label' => trans('student.registration.online'), 'value' => 'online'],
            ['label' => trans('student.registration.offline'), 'value' => 'offline'],
        ];

        return compact('genders', 'relations', 'studentTypes', 'guardianTypes', 'periods', 'statuses', 'types');
    }

    public function create(Request $request): Registration
    {
        \DB::beginTransaction();

        if ($request->student_type == 'new') {
            $params = $request->all();
            $params['source'] = 'student';

            $contact = (new CreateContact)->execute($params);

            $request->merge([
                'contact_id' => $contact->id,
            ]);
        }

        $this->validateInput($request);

        $registration = Registration::forceCreate($this->formatParams($request));

        foreach ($request->guardians as $index => $guardian) {
            $guardianType = Arr::get($guardian, 'guardian_type');

            if ($guardianType == 'new') {
                $newGuardian = (new CreateContact)->execute($guardian);

                if (Arr::get($guardian, 'relation') == 'father') {
                    $contact->father_name = $newGuardian->name;
                } elseif (Arr::get($guardian, 'relation') == 'mother') {
                    $contact->mother_name = $newGuardian->name;
                }
            }

            Guardian::forceCreate([
                'position' => $index + 1,
                'primary_contact_id' => $request->contact_id,
                'contact_id' => $guardianType == 'new' ? $newGuardian->id : Arr::get($guardian, 'guardian_id'),
                'relation' => Arr::get($guardian, 'relation'),
            ]);
        }

        if (isset($contact)) {
            $contact->save();
        }

        \DB::commit();

        return $registration;
    }

    public function createOnline(Request $request): Registration
    {
        \DB::beginTransaction();

        $contact = (new CreateContact)->execute($request->all());

        $existingPendingRegistration = Registration::query()
            ->whereContactId($contact->id)
            ->whereCourseId($request->course_id)
            ->whereStatus(RegistrationStatus::PENDING)
            ->exists();

        if ($existingPendingRegistration) {
            throw ValidationException::withMessages(['message' => trans('global.duplicate', ['attribute' => trans('student.registration.registration')])]);
        }

        $this->updateContactAddress($request, $contact);

        $request->merge([
            'date' => today()->format('Y-m-d'),
            'is_online' => true,
            'contact_id' => $contact->id,
        ]);

        $this->validateInput($request);

        $registration = Registration::forceCreate($this->formatParams($request));

        $registration->addMedia($request);

        \DB::commit();

        return $registration;
    }

    private function updateContactAddress(Request $request, Contact $contact)
    {
        $address = $request->present_address;

        $contact->address = [
            'present' => [
                'address_line1' => Arr::get($address, 'address_line1'),
                'address_line2' => Arr::get($address, 'address_line2'),
                'city' => Arr::get($address, 'city'),
                'state' => Arr::get($address, 'state'),
                'zipcode' => Arr::get($address, 'zipcode'),
                'country' => Arr::get($address, 'country'),
            ],
        ];
        $contact->save();
    }

    private function validateInput(Request $request)
    {
        $existingRegistration = Registration::query()
            ->whereContactId($request->contact_id)
            ->wherePeriodId($request->period_id)
            ->whereCourseId($request->course_id)
            ->where('date', '=', $request->date)
            ->exists();

        if ($existingRegistration) {
            throw ValidationException::withMessages(['date' => trans('global.duplicate', ['attribute' => trans('student.registration.registration')])]);
        }

        $existingStudent = Student::query()
            ->whereContactId($request->contact_id)
            ->where('period_id', '=', $request->period_id)
            ->whereHas('batch', function ($q) use ($request) {
                $q->where('course_id', $request->course_id);
            })
            ->whereHas('admission', function ($q) use ($request) {
                $q->whereNull('leaving_date')->orWhere(function ($q1) use ($request) {
                    $q1->whereNotNull('leaving_date')->where('leaving_date', '>', $request->date);
                });
            })->exists();

        if ($existingStudent) {
            throw ValidationException::withMessages(['date' => trans('global.duplicate', ['attribute' => trans('student.student')])]);
        }
    }

    private function formatParams(Request $request, ?Registration $registration = null): array
    {
        $formatted = [
            'period_id' => $request->period_id,
            'course_id' => $request->course_id,
            'date' => $request->date,
            'remarks' => $request->remarks,
            'fee' => $request->registration_fee,
            'payment_status' => $request->registration_fee ? PaymentStatus::UNPAID : PaymentStatus::NA,
        ];

        if (! $registration) {
            $codeNumberDetail = $this->codeNumber($request->course_id);

            $formatted['contact_id'] = $request->contact_id;
            $formatted['number_format'] = Arr::get($codeNumberDetail, 'number_format');
            $formatted['number'] = Arr::get($codeNumberDetail, 'number');
            $formatted['code_number'] = Arr::get($codeNumberDetail, 'code_number');
            $formatted['is_online'] = $request->boolean('is_online');
            $formatted['status'] = RegistrationStatus::PENDING;

            if ($request->boolean('is_online')) {
                $formatted['meta']['application_number'] = strtoupper(date('Ymd').Str::random(8));
            }
        }

        return $formatted;
    }

    public function update(Request $request, Registration $registration): void
    {
        if (! $registration->isEditable()) {
            throw ValidationException::withMessages(['message' => trans('user.errors.permission_denied')]);
        }

        \DB::beginTransaction();

        $registration->forceFill($this->formatParams($request, $registration))->save();

        \DB::commit();
    }

    public function deletable(Registration $registration, $validate = false): ?bool
    {
        if (! $registration->isEditable()) {
            throw ValidationException::withMessages(['message' => trans('user.errors.permission_denied')]);
        }

        return true;
    }
}
