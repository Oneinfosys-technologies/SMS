<?php

namespace App\Http\Requests\Reception;

use App\Enums\Gender;
use App\Enums\OptionType;
use App\Models\Academic\Course;
use App\Models\Academic\Period;
use App\Models\Employee\Employee;
use App\Models\Option;
use App\Models\Reception\Enquiry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rules\Enum;

class EnquiryRequest extends FormRequest
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
        return [
            'period' => 'required|uuid',
            'type' => 'nullable|uuid',
            'source' => 'nullable|uuid',
            'employee' => 'nullable|uuid',
            'date' => 'required|date_format:Y-m-d',
            'name' => 'required|min:2|max:255',
            'email' => 'nullable|email|max:255',
            'contact_number' => 'nullable|min:2|max:20',
            'records' => 'required|array|min:1',
            'records.*.student_name' => 'required|min:2|max:255|distinct',
            'records.*.birth_date' => 'required|date_format:Y-m-d',
            'records.*.gender' => ['required', new Enum(Gender::class)],
            'records.*.course' => 'required|uuid',
            'remarks' => 'nullable|string|max:255',
        ];
    }

    public function withValidator($validator)
    {
        if (! $validator->passes()) {
            return;
        }

        $validator->after(function ($validator) {
            $mediaModel = (new Enquiry)->getModelName();

            $enquiryUuid = $this->route('enquiry.uuid');

            $period = Period::query()
                ->byTeam()
                ->whereUuid($this->period)
                ->getOrFail(__('academic.period.period'), 'period');

            $type = $this->type ? Option::query()
                ->byTeam()
                ->whereType(OptionType::ENQUIRY_TYPE->value)
                ->whereUuid($this->type)
                ->getOrFail(__('reception.enquiry.type.type'), 'type') : null;

            $source = $this->source ? Option::query()
                ->byTeam()
                ->whereType(OptionType::ENQUIRY_SOURCE->value)
                ->whereUuid($this->source)
                ->getOrFail(__('reception.enquiry.source.source'), 'source') : null;

            $employee = $this->employee ? Employee::query()
                ->byTeam()
                ->whereUuid($this->employee)
                ->getOrFail(__('employee.employee'), 'employee') : null;

            $courses = Course::query()
                ->byPeriod($period->id)
                ->filterAccessible()
                ->get();

            $newRecords = [];
            foreach ($this->records as $index => $record) {
                $course = $courses->firstWhere('uuid', Arr::get($record, 'course'));

                if (! $course) {
                    $validator->errors()->add('records.'.$index.'.course', __('global.could_not_find', ['attribute' => __('academic.course.course')]));
                }

                $newRecords[] = Arr::add($record, 'course_id', $course?->id);
            }

            $this->merge([
                'period_id' => $period->id,
                'type_id' => $type?->id,
                'source_id' => $source?->id,
                'employee_id' => $employee?->id,
                'records' => $newRecords,
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
            'type' => __('reception.enquiry.type.type'),
            'source' => __('reception.enquiry.source.source'),
            'date' => __('reception.enquiry.props.date'),
            'name' => __('reception.enquiry.props.name'),
            'email' => __('reception.enquiry.props.email'),
            'contact_number' => __('reception.enquiry.props.contact_number'),
            'records.*.student_name' => __('reception.enquiry.props.student_name'),
            'records.*.birth_date' => __('contact.props.birth_date'),
            'records.*.gender' => __('contact.props.gender'),
            'records.*.course' => __('academic.course.course'),
            'remarks' => __('reception.enquiry.props.remarks'),
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
