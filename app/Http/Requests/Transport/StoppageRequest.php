<?php

namespace App\Http\Requests\Transport;

use App\Models\Transport\Stoppage;
use Illuminate\Foundation\Http\FormRequest;

class StoppageRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'description' => 'nullable|string|max:255',
        ];

        return $rules;
    }

    public function withValidator($validator)
    {
        if (! $validator->passes()) {
            return;
        }

        $validator->after(function ($validator) {
            $uuid = $this->route('stoppage');

            $existingRecords = Stoppage::query()
                ->byPeriod()
                ->when($uuid, function ($q, $uuid) {
                    $q->where('uuid', '!=', $uuid);
                })
                ->whereName($this->name)
                ->exists();

            if ($existingRecords) {
                $validator->errors()->add('name', trans('validation.unique', ['attribute' => trans('transport.stoppage.stoppage')]));
            }
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
            'name' => __('transport.stoppage.props.name'),
            'description' => __('transport.stoppage.props.description'),
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
