<?php

namespace App\Http\Requests\Student;

use App\Models\Academic\Course;
use App\Models\Academic\Period;
use Illuminate\Foundation\Http\FormRequest;

class RegistrationUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'date' => 'required|date_format:Y-m-d',
            'period' => 'required|uuid',
            'course' => 'required|uuid',
        ];

        return $rules;
    }

    public function withValidator($validator)
    {
        if (! $validator->passes()) {
            return;
        }

        $validator->after(function ($validator) {
            $uuid = $this->route('registration');

            $period = Period::query()
                ->byTeam()
                ->whereUuid($this->period)
                ->getOrFail(trans('validation.exists', ['attribute' => trans('academic.period.period')]), 'period');

            $course = Course::query()
                ->byPeriod($period->id)
                ->filterAccessible()
                ->whereUuid($this->course)
                ->getOrFail(trans('validation.exists', ['attribute' => trans('academic.course.course')]), 'course');

            if (! $course->enable_registration) {
                $validator->errors()->add('course', trans('academic.course.registration_disabled_info'));
            }

            $this->merge([
                'period_id' => $period?->id,
                'course_id' => $course?->id,
                'registration_fee' => $course->enable_registration ? $course->registration_fee->value : 0,
            ]);
        });
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'period' => __('academic.period.period'),
            'course' => __('academic.course.course'),
            'date' => __('student.registration.props.date'),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [];
    }
}
