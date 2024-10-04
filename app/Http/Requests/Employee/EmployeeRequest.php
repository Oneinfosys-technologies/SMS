<?php

namespace App\Http\Requests\Employee;

use App\Enums\Employee\Type;
use App\Enums\Gender;
use App\Models\Contact;
use App\Models\Employee\Department;
use App\Models\Employee\Designation;
use App\Models\Employee\Employee;
use App\Models\Option;
use App\Rules\AlphaSpace;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class EmployeeRequest extends FormRequest
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
            'type' => ['required', new Enum(Type::class)],
            'employee_type' => 'required|in:new,existing',
            'department' => 'required|uuid',
            'designation' => 'required|uuid',
            'employment_status' => 'required|uuid',
            'code_number' => 'required|max:50',
        ];

        if ($this->employee_type == 'new') {
            $rules['first_name'] = ['required', 'min:2', 'max:100', new AlphaSpace];
            $rules['last_name'] = ['nullable', 'max:100', new AlphaSpace];
            $rules['gender'] = ['required', new Enum(Gender::class)];
            $rules['birth_date'] = 'required|date_format:Y-m-d';
            $rules['contact_number'] = 'required|min:2|max:20';
            $rules['email'] = 'required|email|max:100';
            $rules['joining_date'] = 'required|date_format:Y-m-d|after:birth_date';
        } else {
            $rules['employee'] = 'required';
            $rules['joining_date'] = 'required|date_format:Y-m-d';
        }

        if (config('config.employee.enable_middle_name_field')) {
            $rules['middle_name'] = ['nullable', 'max:100', new AlphaSpace];
        }

        if (config('config.employee.enable_third_name_field')) {
            $rules['third_name'] = ['nullable', 'max:100', new AlphaSpace];
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        if (! $validator->passes()) {
            return;
        }

        $validator->after(function ($validator) {
            $department = Department::query()
                ->byTeam()
                ->whereUuid($this->department)
                ->getOrFail(__('employee.department.department'), 'department');

            $designation = Designation::query()
                ->byTeam()
                ->whereUuid($this->designation)
                ->getOrFail(__('employee.designation.designation'), 'designation');

            $employmentStatus = Option::query()
                ->byTeam()
                ->whereType('employment_status')
                ->whereUuid($this->employment_status)
                ->getOrFail(__('employee.employment_status.employment_status'), 'employment_status');

            if ($this->employee_type == 'existing') {
                $contact = Contact::query()
                    ->byTeam()
                    ->whereHas('employees', function ($q) {
                        $q->whereUuid($this->employee);
                    })
                    ->getOrFail(trans('global.could_not_find', ['attribute' => trans('employee.employee')]), 'employee');

                $this->merge([
                    'contact_id' => $contact->id,
                ]);

                $existingEmployee = Employee::query()
                    ->whereContactId($contact->id)
                    ->whereNull('leaving_date')
                    ->count();

                if ($existingEmployee) {
                    $validator->errors()->add('message', trans('employee.exists'));
                }

                $overlappingDate = Employee::query()
                    ->whereContactId($contact->id)
                    ->whereNotNull('leaving_date')
                    ->orderBy('leaving_date', 'desc')
                    ->first();

                if ($overlappingDate && $overlappingDate->leaving_date->value >= $this->joining_date) {
                    $validator->errors()->add('joining_date', trans('employee.joining_date_less_than_leaving_date', ['attribute' => $overlappingDate->leaving_date->formatted]));
                }
            } else {
                $existingContactNumber = Employee::query()
                    ->whereHas('contact', function ($q) {
                        $q->byTeam()
                            ->where('contact_number', $this->contact_number);
                    })
                    ->exists();

                if ($existingContactNumber) {
                    $validator->errors()->add('contact_number', trans('global.duplicate', ['attribute' => trans('contact.props.contact_number')]));
                }

                $existingEmail = Employee::query()
                    ->whereHas('contact', function ($q) {
                        $q->byTeam()
                            ->where('email', $this->email);
                    })
                    ->exists();

                if ($existingEmail) {
                    $validator->errors()->add('email', trans('global.duplicate', ['attribute' => trans('contact.props.email')]));
                }
            }

            $this->merge([
                'department_id' => $department->id,
                'designation_id' => $designation->id,
                'employment_status_id' => $employmentStatus->id,
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
            'type' => __('employee.type'),
            'department' => __('employee.department.department'),
            'designation' => __('employee.designation.designation'),
            'joining_date' => __('employee.props.joining_date'),
            'code_number' => __('employee.props.code_number'),
            'first_name' => __('contact.props.first_name'),
            'middle_name' => __('contact.props.middle_name'),
            'third_name' => __('contact.props.third_name'),
            'last_name' => __('contact.props.last_name'),
            'gender' => __('contact.props.gender'),
            'birth_date' => __('contact.props.birth_date'),
            'contact_number' => __('contact.props.contact_number'),
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
