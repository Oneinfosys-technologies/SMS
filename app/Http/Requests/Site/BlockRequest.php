<?php

namespace App\Http\Requests\Site;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlockRequest extends FormRequest
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
        $uuid = $this->route('block.uuid');

        return [
            'name' => ['required', 'max:50', Rule::unique('site_blocks')->ignore($uuid, 'uuid')],
            'title' => 'required|max:255',
            'sub_title' => 'nullable|max:255',
            'content' => 'nullable|max:1000',
            'url' => 'nullable|max:255',
        ];
    }

    public function withValidator($validator)
    {
        if (! $validator->passes()) {
            return;
        }

        $validator->after(function ($validator) {
            $blockUuid = $this->route('block.uuid');
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
            'name' => __('site.block.props.name'),
            'title' => __('site.block.props.title'),
            'sub_title' => __('site.block.props.sub_title'),
            'content' => __('site.block.props.content'),
            'url' => __('site.block.props.url'),
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
