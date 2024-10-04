<?php

namespace App\Services\Reception;

use App\Actions\CreateContact;
use App\Enums\Finance\PaymentStatus;
use App\Enums\Student\RegistrationStatus;
use App\Models\Academic\Course;
use App\Models\Contact;
use App\Models\Reception\Enquiry;
use App\Models\Reception\EnquiryRecord;
use App\Models\Student\Registration;
use App\Models\Student\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class EnquiryActionService
{
    public function convertToRegistration(Request $request, Enquiry $enquiry, EnquiryRecord $enquiryRecord, array $params = [])
    {
        if ($enquiryRecord->getMeta('is_converted')) {
            throw ValidationException::withMessages(['message' => trans('reception.enquiry.already_converted')]);
        }

        $params = [
            'name' => $enquiryRecord->student_name,
            'contact_number' => $enquiry->contact_number,
            'email' => $enquiry->email,
            'gender' => $enquiryRecord->gender,
            'birth_date' => $enquiryRecord->birth_date->value,
        ];

        \DB::beginTransaction();

        $params['source'] = 'enquiry';

        $contact = (new CreateContact)->execute($params);

        $this->validateData($enquiry, $enquiryRecord, $contact);

        $course = Course::query()
            ->where('id', $enquiryRecord->course_id)
            ->firstOrFail();

        $registrationFee = $course->registration_fee->value;

        $codeNumberDetail = Arr::get($params, 'code_number_detail');

        $registration = Registration::forceCreate([
            'period_id' => $enquiry->period_id,
            'course_id' => $enquiryRecord->course_id,
            'date' => today()->toDateString(),
            'remarks' => trans('reception.enquiry.converted_to_registration'),
            'fee' => $registrationFee,
            'payment_status' => $registrationFee ? PaymentStatus::UNPAID : PaymentStatus::NA,
            'contact_id' => $contact->id,
            'code_number' => Arr::get($codeNumberDetail, 'code_number'),
            'number_format' => Arr::get($codeNumberDetail, 'number_format'),
            'number' => Arr::get($codeNumberDetail, 'number'),
            'status' => RegistrationStatus::PENDING,
            'is_online' => false,
        ]);

        $enquiryRecord->setMeta([
            'registration_uuid' => $registration->uuid,
            'is_converted' => true,
        ]);
        $enquiryRecord->save();

        \DB::commit();

        // throw ValidationException::withMessages(['message' => trans('general.errors.feature_under_development')]);
    }

    private function validateData(Enquiry $enquiry, EnquiryRecord $enquiryRecord, Contact $contact)
    {
        $existingRegistration = Registration::query()
            ->whereContactId($contact->id)
            ->wherePeriodId($enquiry->period_id)
            ->whereCourseId($enquiryRecord->course_id)
            ->where('date', '=', today()->toDateString())
            ->exists();

        if ($existingRegistration) {
            throw ValidationException::withMessages(['date' => trans('global.duplicate', ['attribute' => trans('student.registration.registration')])]);
        }

        $existingStudent = Student::query()
            ->whereContactId($contact->id)
            ->where('period_id', '=', $enquiry->period_id)
            ->whereHas('batch', function ($q) use ($enquiryRecord) {
                $q->where('course_id', $enquiryRecord->course_id);
            })
            ->whereHas('admission', function ($q) use ($enquiryRecord) {
                $q->whereNull('leaving_date')->orWhere(function ($q1){
                    $q1->whereNotNull('leaving_date')->where('leaving_date', '>', today()->toDateString());
                });
            })->exists();

        if ($existingStudent) {
            throw ValidationException::withMessages(['date' => trans('global.duplicate', ['attribute' => trans('student.student')])]);
        }
    }
}
