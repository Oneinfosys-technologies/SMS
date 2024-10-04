<?php

namespace App\Http\Requests\Employee;

use App\Concerns\SimpleValidation;
use App\Enums\Employee\Type;
use App\Enums\Gender;
use App\Models\Contact;
use App\Rules\AlphaSpace;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class EmployeeUpdateRequest extends FormRequest
{
    use SimpleValidation;

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
            'first_name' => ['sometimes', 'required', 'min:2', 'max:100', new AlphaSpace],
            'last_name' => ['sometimes', 'max:100', new AlphaSpace],
            'gender' => ['sometimes', 'required', new Enum(Gender::class)],
            'birth_date' => 'sometimes|required|date',
            'birth_place' => 'sometimes|max:100',
            'nationality' => 'sometimes|max:100',
            'mother_tongue' => 'sometimes|max:100',
            'contact_number' => 'sometimes|required|min:2|max:100',
            'email' => 'sometimes|nullable|email|min:2|max:100',
            'alternate_records.contact_number' => 'sometimes|min:2|max:100',
            'alternate_records.email' => 'sometimes|nullable|email|min:2|max:100',
            'present_address.address_line1' => 'sometimes|required|min:2|max:100',
            'present_address.address_line2' => 'sometimes|nullable|min:2|max:100',
            'present_address.city' => 'sometimes|nullable|min:2|max:100',
            'present_address.state' => 'sometimes|nullable|min:2|max:100',
            'present_address.zipcode' => 'sometimes|nullable|min:2|max:100',
            'present_address.country' => 'sometimes|required|min:2|max:100',
            'permanent_address.same_as_present_address' => 'sometimes|boolean',
            'permanent_address.address_line1' => 'sometimes|nullable|min:2|max:100',
            'permanent_address.address_line2' => 'sometimes|nullable|min:2|max:100',
            'permanent_address.city' => 'sometimes|nullable|min:2|max:100',
            'permanent_address.state' => 'sometimes|nullable|min:2|max:100',
            'permanent_address.zipcode' => 'sometimes|nullable|min:2|max:100',
            'permanent_address.country' => 'sometimes|nullable|min:2|max:100',
        ];

        if (config('config.contact.name_includes_middle_name')) {
            $rules['middle_name'] = ['sometimes', 'nullable', 'max:100', new AlphaSpace];
        }

        if (config('config.contact.name_includes_third_name')) {
            $rules['third_name'] = ['sometimes', 'nullable', 'max:100', new AlphaSpace];
        }

        if (config('config.employee.unique_id_number1_required')) {
            $rules['unique_id_number1'] = ['sometimes', 'required'];
        }

        if (config('config.employee.unique_id_number2_required')) {
            $rules['unique_id_number2'] = ['sometimes', 'required'];
        }

        if (config('config.employee.unique_id_number3_required')) {
            $rules['unique_id_number3'] = ['sometimes', 'required'];
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        if (! $validator->passes()) {
            $validator->after(function ($validator) {
                $this->change($validator, 'alternate_records.contact_number', 'alternate_contact_number');
                $this->change($validator, 'alternate_records.email', 'alternate_email');
                $this->change($validator, 'present_address.address_line1', 'present_address_address_line1');
                $this->change($validator, 'present_address.address_line2', 'present_address_address_line2');
                $this->change($validator, 'present_address.city', 'present_address_city');
                $this->change($validator, 'present_address.state', 'present_address_state');
                $this->change($validator, 'present_address.zipcode', 'present_address_zipcode');
                $this->change($validator, 'present_address.country', 'present_address_country');
                $this->change($validator, 'permanent_address.address_line1', 'permanent_address_address_line1');
                $this->change($validator, 'permanent_address.address_line2', 'permanent_address_address_line2');
                $this->change($validator, 'permanent_address.city', 'permanent_address_city');
                $this->change($validator, 'permanent_address.state', 'permanent_address_state');
                $this->change($validator, 'permanent_address.zipcode', 'permanent_address_zipcode');
                $this->change($validator, 'permanent_address.country', 'permanent_address_country');
            });

            return;
        }

        $validator->after(function ($validator) {
            // $employee = $this->route('employee');

            // $employee->load('contact');

            // $contact = $employee->contact;

            // $existingContact = Contact::byTeam()->where('uuid', '!=', $contact->uuid)
            //     ->whereFirstName($this->first_name)
            //     ->whereMiddleName($this->middle_name)
            //     ->whereThirdName($this->third_name)
            //     ->whereLastName($this->last_name)
            //     ->whereContactNumber($this->has('contact_number') ? $this->contact_number : $contact->contact_number)
            //     ->count();

            // if ($existingContact) {
            //     $validator->errors()->add('message', trans('employee.exists'));
            // }
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
            'first_name' => __('contact.props.first_name'),
            'middle_name' => __('contact.props.middle_name'),
            'third_name' => __('contact.props.third_name'),
            'last_name' => __('contact.props.last_name'),
            'gender' => __('contact.props.gender'),
            'birth_date' => __('contact.props.birth_date'),
            'birth_place' => __('contact.props.birth_place'),
            'nationality' => __('contact.props.nationality'),
            'mother_tongue' => __('contact.props.mother_tongue'),
            'unique_id_number1' => config('config.employee.unique_id_number1_label'),
            'unique_id_number2' => config('config.employee.unique_id_number2_label'),
            'unique_id_number3' => config('config.employee.unique_id_number3_label'),
            'contact_number' => __('contact.props.contact_number'),
            'email' => __('contact.props.email'),
            'alternate_records.contact_number' => __('global.alternate', ['attribute' => __('contact.props.contact_number')]),
            'alternate_records.email' => __('global.alternate', ['attribute' => __('contact.props.email')]),
            'present_address.address_line1' => __('contact.props.address.address_line1'),
            'present_address.address_line2' => __('contact.props.address.address_line2'),
            'present_address.city' => __('contact.props.address.city'),
            'present_address.state' => __('contact.props.address.state'),
            'present_address.zipcode' => __('contact.props.address.zipcode'),
            'present_address.country' => __('contact.props.address.country'),
            'permanent_address.same_as_present_address' => __('contact.props.same_as_present_address'),
            'permanent_address.address_line1' => __('contact.props.address.address_line1'),
            'permanent_address.address_line2' => __('contact.props.address.address_line2'),
            'permanent_address.city' => __('contact.props.address.city'),
            'permanent_address.state' => __('contact.props.address.state'),
            'permanent_address.zipcode' => __('contact.props.address.zipcode'),
            'permanent_address.country' => __('contact.props.address.country'),
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
