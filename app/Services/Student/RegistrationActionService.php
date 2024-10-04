<?php

namespace App\Services\Student;

use App\Actions\SendMailTemplate;
use App\Actions\Student\AssignFee;
use App\Enums\OptionType;
use App\Enums\Student\RegistrationStatus;
use App\Enums\Transport\Direction;
use App\Enums\UserStatus;
use App\Http\Resources\Academic\BatchResource;
use App\Http\Resources\Academic\SubjectResource;
use App\Http\Resources\Finance\FeeConcessionResource;
use App\Http\Resources\Finance\FeeHeadResource;
use App\Http\Resources\OptionResource;
use App\Http\Resources\Transport\CircleResource;
use App\Models\Academic\Batch;
use App\Models\Academic\Subject;
use App\Models\Finance\FeeConcession;
use App\Models\Finance\FeeHead;
use App\Models\Option;
use App\Models\Student\Admission;
use App\Models\Student\Registration;
use App\Models\Student\Student;
use App\Models\Student\SubjectWiseStudent;
use App\Models\Transport\Circle;
use App\Models\User;
use App\Support\FormatCodeNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RegistrationActionService
{
    use FormatCodeNumber;

    public function preRequisite(Request $request, Registration $registration): array
    {
        $statuses = RegistrationStatus::getOptions();

        $codeNumber = Arr::get($this->codeNumber($registration), 'code_number');

        $batches = BatchResource::collection(Batch::query()
            ->withCount('students as max_strength')
            ->whereCourseId($registration->course_id)
            ->get());

        $feeHeads = FeeHeadResource::collection(FeeHead::query()
            ->wherePeriodId($registration->period_id)
            ->whereHas('group', function ($q) {
                $q->where(function ($q) {
                    $q->whereNull('meta->is_custom')->orWhere('meta->is_custom', false);
                });
            })
            ->get());

        $enrollmentTypes = OptionResource::collection(Option::query()
            ->byTeam()
            ->where('type', OptionType::STUDENT_ENROLLMENT_TYPE->value)
            ->get());

        $directions = Direction::getOptions();

        $transportCircles = CircleResource::collection(Circle::query()
            ->byPeriod($registration->period_id)
            ->get());

        $feeConcessions = FeeConcessionResource::collection(FeeConcession::query()
            ->byPeriod($registration->period_id)
            ->get());

        $electiveSubjects = SubjectResource::collection(Subject::query()
            // ->withSubjectRecordByCourse($registration->course_id)
            // ->where('subject_records.is_elective', true)
            ->get());

        return compact('statuses', 'codeNumber', 'batches', 'feeHeads', 'enrollmentTypes', 'directions', 'transportCircles', 'feeConcessions', 'electiveSubjects');
    }

    private function codeNumber(Registration $registration)
    {
        $numberPrefix = config('config.student.admission_number_prefix');
        $numberSuffix = config('config.student.admission_number_suffix');
        $digit = config('config.student.admission_number_digit', 0);

        $numberFormat = $numberPrefix.'%NUMBER%'.$numberSuffix;

        $string = $this->preFormatForAcademicCourse($registration->course_id, $numberFormat);

        if (Str::of($string)->contains('%GENDER%')) {
            $gender = $registration->contact->gender->value ?? '';
            $string = str_replace('%GENDER%', strtoupper(substr($gender, 0, 1)), $string);
        }

        $codeNumber = (int) Admission::query()
            ->byTeam()
            ->whereNumberFormat($string)
            ->max('number') + 1;

        return $this->getCodeNumber(number: $codeNumber, digit: $digit, format: $string);
    }

    private function validateCodeNumber(Request $request, Registration $registration, $uuid = null): array
    {
        $existingCodeNumber = Admission::query()
            ->byTeam()
            ->whereCodeNumber($request->code_number)
            ->when($uuid, function ($q, $uuid) {
                $q->where('uuid', '!=', $uuid);
            })->count();

        if ($existingCodeNumber) {
            throw ValidationException::withMessages(['code_number' => trans('global.duplicate', ['attribute' => trans('student.admission.props.code_number')])]);
        }

        $codeNumberDetail = $this->codeNumber($registration);

        return $request->code_number == Arr::get($codeNumberDetail, 'code_number') ? $codeNumberDetail : [
            'code_number' => $request->code_number,
        ];
    }

    public function action(Request $request, Registration $registration): void
    {
        if ($request->status == 'initiated') {
            $registration->status = RegistrationStatus::INITIATED;
            $registration->save();

            return;
        }

        if ($request->status == 'rejected') {
            $this->reject($request, $registration);
            $this->sendRejectionNotification($registration);

            return;
        }

        $this->approve($request, $registration);
    }

    private function sendRejectionNotification(Registration $registration)
    {
        if (! $registration->is_online) {
            return;
        }

        (new SendMailTemplate)->execute(
            email: $registration->contact->email,
            code: 'online-registration-rejected',
            variables: [
                'name' => $registration->contact->name,
                'reason' => $registration->rejection_remarks,
                'application_number' => $registration->getMeta('application_number'),
                'program' => $registration->course?->division?->program?->name,
                'period' => $registration->period->name,
                'course' => $registration->course->name,
            ]
        );
    }

    private function reject(Request $request, Registration $registration): void
    {
        $registration->status = RegistrationStatus::REJECTED;
        $registration->rejection_remarks = $request->rejection_remarks;
        $registration->rejected_at = now()->toDateTimeString();
        $registration->save();
    }

    private function sendApprovalNotification(Registration $registration, array $params = [])
    {
        if (empty($registration->contact->email)) {
            return;
        }

        if ($registration->is_online) {
            $template = 'online-registration-approved';
        } else {
            $template = 'registration-approved';
        }

        if (Arr::get($params, 'with_account')) {
            $template .= '-with-account';
        }

        (new SendMailTemplate)->execute(
            email: $registration->contact->email,
            code: $template,
            variables: [
                'name' => $registration->contact->name,
                'application_number' => $registration->getMeta('application_number'),
                'registration_number' => $registration->code_number,
                'program' => $registration->course?->division?->program?->name,
                'period' => $registration->period->name,
                'course' => $registration->course->name,
                'username' => Arr::get($params, 'username'),
                'password' => Arr::get($params, 'password'),
                'url' => url('/'),
            ]
        );
    }

    private function approve(Request $request, Registration $registration): void
    {
        if ($request->boolean('create_user_account')) {
            if (User::whereEmail($request->email)->exists()) {
                throw ValidationException::withMessages(['message' => trans('global.exists', ['attribute' => trans('user.user')])]);
            }
        }

        $subjects = collect([]);
        if ($request->elective_subjects) {
            $subjects = Subject::query()
                ->whereIn('subjects.uuid', $request->elective_subjects)
                ->get();

            $electiveSubjects = Subject::query()
                ->withSubjectRecord($request->batch_id, $registration->course_id)
                ->where('subject_records.is_elective', true)
                ->get();

            $missingSubjects = $subjects->pluck('name')->diff($electiveSubjects->pluck('name'))->all();

            if ($missingSubjects) {
                throw ValidationException::withMessages(['message' => trans('student.registration.could_not_find_elective_subjects', ['attribute' => implode(', ', $missingSubjects)])]);
            }
        }

        $codeNumberDetail = $this->validateCodeNumber($request, $registration);

        \DB::beginTransaction();

        $registration->status = RegistrationStatus::APPROVED;
        $registration->save();

        $admission = Admission::forceCreate([
            'number_format' => Arr::get($codeNumberDetail, 'number_format'),
            'number' => Arr::get($codeNumberDetail, 'number'),
            'code_number' => Arr::get($codeNumberDetail, 'code_number'),
            'registration_id' => $registration->id,
            'batch_id' => $request->batch_id,
            'joining_date' => $request->date,
            'remarks' => $request->remarks,
        ]);

        $student = Student::forceCreate([
            'admission_id' => $admission->id,
            'period_id' => $registration->period_id,
            'batch_id' => $request->batch_id,
            'contact_id' => $registration->contact_id,
            'start_date' => $request->date,
            'enrollment_type_id' => $request->enrollment_type_id,
        ]);

        $isNewStudent = false;
        if ($student->start_date->value == $admission->joining_date->value) {
            $isNewStudent = true;
        }

        if ($request->boolean('assign_fee')) {
            (new AssignFee)->execute(
                student: $student,
                feeConcession: $request->fee_concession,
                transportCircle: $request->transport_circle,
                params: [
                    'direction' => $request->direction,
                    'opted_fee_heads' => $request->opted_fee_heads,
                    'is_new_student' => $isNewStudent,
                ]
            );
        }

        if ($request->elective_subjects) {
            foreach ($request->elective_subjects as $subject) {
                $subject = $subjects->where('uuid', $subject)->first();

                SubjectWiseStudent::firstOrCreate([
                    'batch_id' => $request->batch_id,
                    'subject_id' => $subject->id,
                    'student_id' => $student->id,
                ]);
            }
        }

        if ($request->boolean('create_user_account')) {

            $contact = $registration->contact;

            if (! $contact->email) {
                $contact->email = $request->email;
                $contact->save();
            }

            $user = User::forceCreate([
                'name' => $student->contact->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => bcrypt($request->password),
                'status' => UserStatus::ACTIVATED,
            ]);

            $user->assignRole('student');

            $contact = $registration->contact;
            $contact->user_id = $user->id;
            $contact->save();
        }

        \DB::commit();

        $registration->refresh();

        $this->sendApprovalNotification($registration, [
            'with_account' => $request->boolean('create_user_account') ? true : false,
            'username' => $request->username,
            'password' => $request->password,
        ]);
    }
}
