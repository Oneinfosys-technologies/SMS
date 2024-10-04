<?php

namespace App\Http\Requests\Student;

use App\Models\Academic\Batch;
use Illuminate\Foundation\Http\FormRequest;

class RecordRequest extends FormRequest
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
            'edit_code_number' => 'boolean',
            'joining_date' => 'required_if:edit_code_number,true|date_format:Y-m-d|before_or_equal:start_date',
            'start_date' => 'required_if:edit_code_number,true|date_format:Y-m-d',
            'code_number' => 'required_if:edit_code_number,true|min:1|max:100',
            'code_number_format' => 'nullable|min:1|max:50',
            'edit_batch' => 'boolean',
            'batch' => 'required_if:edit_batch,true|uuid',
            'remarks' => 'nullable|min:2|max:1000',
        ];

        return $rules;
    }

    public function withValidator($validator)
    {
        if (! $validator->passes()) {
            return;
        }

        $validator->after(function ($validator) {
            $employeeUuid = $this->route('employee');
            $recordUuid = $this->route('record');

            $batch = $this->batch ? Batch::query()
                ->byPeriod()
                ->filterAccessible()
                ->whereUuid($this->batch)
                ->getOrFail(trans('academic.batch.batch')) : null;

            $this->merge([
                'batch' => $batch,
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
            'edit_code_number' => __('global.edit', ['attribute' => __('student.admission.props.code_number')]),
            'joining_date' => __('student.admission.props.date'),
            'start_date' => __('student.record.props.promotion_date'),
            'code_number' => __('student.admission.props.code_number'),
            'code_number_format' => __('student.admission.props.code_number_format'),
            'edit_batch' => __('global.edit', ['attribute' => __('academic.batch.batch')]),
            'batch' => __('academic.batch.batch'),
            'remarks' => __('student.record.props.remarks'),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'code_number.required_if' => __('validation.required', ['attribute' => __('student.admission.props.code_number')]),
            'code_number_format.required_if' => __('validation.required', ['attribute' => __('student.admission.props.code_number_format')]),
            'code_number_sno.required_if' => __('validation.required', ['attribute' => __('student.admission.props.code_number_sno')]),
            'batch.required_if' => __('validation.required', ['attribute' => __('academic.batch.batch')]),
        ];
    }
}
