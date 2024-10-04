<?php

namespace App\Http\Requests\Academic;

use App\Models\Academic\Period;
use App\Models\Academic\Session;
use Illuminate\Foundation\Http\FormRequest;

class PeriodRequest extends FormRequest
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
            'session' => ['nullable', 'uuid'],
            'name' => ['required', 'string', 'min:3', 'max:50'],
            'code' => ['nullable', 'string', 'max:50'],
            'shortcode' => ['nullable', 'string', 'max:50'],
            'alias' => ['nullable', 'string', 'max:50'],
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
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
            $uuid = $this->route('period');

            $session = $this->input('session') ? Session::query()
                ->byTeam()
                ->where('uuid', $this->input('session'))
                ->getOrFail(trans('academic.session.session')) : null;

            $existingRecords = Period::query()
                ->byTeam()
                ->when($uuid, function ($q, $uuid) {
                    $q->where('uuid', '!=', $uuid);
                })
                ->whereSessionId($session?->id)
                ->whereName($this->name)
                ->exists();

            if ($existingRecords) {
                $validator->errors()->add('name', trans('validation.unique', ['attribute' => trans('academic.period.period')]));
            }

            $this->whenFilled('code', function (string $input) use ($validator, $session, $uuid) {
                $existingCodes = Period::query()
                    ->byTeam()
                    ->when($uuid, function ($q, $uuid) {
                        $q->where('uuid', '!=', $uuid);
                    })
                    ->whereSessionId($session?->id)
                    ->whereCode($input)
                    ->exists();

                if ($existingCodes) {
                    $validator->errors()->add('code', trans('validation.unique', ['attribute' => trans('academic.period.period')]));
                }
            });

            $this->whenFilled('shortcode', function (string $input) {
                // Can have duplicate shortcodes
                // $existingShortcodes = Period::query()
                //     ->byTeam()
                //     ->when($uuid, function ($q, $uuid) {
                //         $q->where('uuid', '!=', $uuid);
                //     })
                //     ->whereSessionId($session?->id)
                //     ->whereShortcode($input)
                //     ->exists();

                // if ($existingShortcodes) {
                //     $validator->errors()->add('shortcode', trans('validation.unique', ['attribute' => trans('academic.period.period')]));
                // }
            });

            $this->whenFilled('alias', function (string $input) use ($validator, $session, $uuid) {
                $existingAliases = Period::query()
                    ->byTeam()
                    ->when($uuid, function ($q, $uuid) {
                        $q->where('uuid', '!=', $uuid);
                    })
                    ->whereSessionId($session?->id)
                    ->whereAlias($input)
                    ->exists();

                if ($existingAliases) {
                    $validator->errors()->add('alias', trans('validation.unique', ['attribute' => trans('academic.period.period')]));
                }
            });

            $periodCount = Period::query()
                ->count();

            $this->merge([
                'session_id' => $session?->id,
                'is_default' => $periodCount === 0 ? true : false,
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
            'session' => __('academic.session.session'),
            'name' => __('academic.period.props.name'),
            'code' => __('academic.period.props.code'),
            'shortcode' => __('academic.period.props.shortcode'),
            'alias' => __('academic.period.props.alias'),
            'start_date' => __('academic.period.props.start_date'),
            'end_date' => __('academic.period.props.end_date'),
            'description' => __('academic.period.props.description'),
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
