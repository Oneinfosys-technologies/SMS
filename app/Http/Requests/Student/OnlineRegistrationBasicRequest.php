<?php

namespace App\Http\Requests\Student;

use App\Enums\BloodGroup;
use App\Enums\Gender;
use App\Enums\MaritalStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class OnlineRegistrationBasicRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'gender' => ['required', new Enum(Gender::class)],
            'birth_date' => 'required|date_format:Y-m-d',
            'anniversary_date' => 'nullable|date_format:Y-m-d',
            'birth_place' => 'nullable|string|max:255',
            'nationality' => 'required|string|max:255',
            'mother_tongue' => 'required|string|max:255',
            'blood_group' => ['required', new Enum(BloodGroup::class)],
            'marital_status' => ['required', new Enum(MaritalStatus::class)],
            'category' => 'nullable|uuid',
            'caste' => 'nullable|uuid',
            'religion' => 'nullable|uuid',
        ];

        return $rules;
    }

    public function withValidator($validator)
    {
        if (! $validator->passes()) {
            return;
        }

        $validator->after(function ($validator) {});
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'father_name' => __('contact.props.father_name'),
            'mother_name' => __('contact.props.mother_name'),
            'gender' => __('contact.props.gender'),
            'birth_date' => __('contact.props.birth_date'),
            'anniversary_date' => __('contact.props.anniversary_date'),
            'birth_place' => __('contact.props.birth_place'),
            'nationality' => __('contact.props.nationality'),
            'mother_tongue' => __('contact.props.mother_tongue'),
            'blood_group' => __('contact.props.blood_group'),
            'marital_status' => __('contact.props.marital_status'),
            'category' => __('contact.category.category'),
            'caste' => __('contact.caste.caste'),
            'religion' => __('contact.religion.religion'),
        ];
    }
}
