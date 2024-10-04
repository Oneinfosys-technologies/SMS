<?php

namespace App\Http\Requests\Academic;

use App\Enums\Day;
use App\Models\Academic\Subject;
use App\Models\Asset\Building\Room;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class TimetableAllocationRequest extends FormRequest
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
            'days' => 'array',
            'days.*.value' => 'required|distinct',
            'days.*.sessions' => 'array',
        ];

        return $rules;
    }

    public function withValidator($validator)
    {
        if (! $validator->passes()) {
            return;
        }

        $validator->after(function ($validator) {
            $uuid = $this->route('timetable');

            $days = Day::getKeys();

            $subjects = Subject::query()
                ->byPeriod()
                ->get();

            if (count($this->days) != 7) {
                throw ValidationException::withMessages(['message' => trans('academic.timetable.invalid_days')]);
            }

            $rooms = Room::query()
                ->withFloorAndBlock()
                ->notAHostel()
                ->get();

            $newDays = [];
            foreach ($this->days as $index => $day) {
                $dayName = Arr::get($day, 'value');

                if (! in_array($dayName, $days)) {
                    throw ValidationException::withMessages(['message' => trans('academic.timetable.invalid_day')]);
                }

                $sessions = Arr::get($day, 'sessions', []);

                $newSessions = [];
                foreach ($sessions as $sessionIndex => $session) {
                    $subject = $subjects->where('uuid', Arr::get($session, 'subject.uuid'))->first();

                    $room = null;
                    if (Arr::get($session, 'room')) {
                        $room = $rooms->where('uuid', Arr::get($session, 'room'))->first();

                        if (! $room) {
                            $validator->errors()->add('days.'.$index.'.sessions.'.$sessionIndex.'.room', trans('global.could_not_find', ['attribute' => trans('asset.building.room.room')]));
                        }
                    }

                    if (Arr::get($session, 'subject.uuid') && ! $subject) {
                        $validator->errors()->add('days.'.$index.'.sessions.'.$sessionIndex.'.subject', trans('global.could_not_find', ['attribute' => trans('academic.subject.subject')]));
                    }

                    $newSessions[] = [
                        ...$session,
                        'room_id' => $room?->id,
                    ];
                }

                $newDays[] = [
                    ...$day,
                    'sessions' => $newSessions,
                ];
            }

            $this->merge(['days' => $newDays]);
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
            'days' => __('list.durations.day'),
            'days.*.value' => __('list.durations.day'),
            'days.*.sessions' => __('academic.class_timing.session'),
            'days.*.sessions.*.subject' => __('academic.subject.subject'),
            'days.*.sessions.*.room' => __('asset.building.room.room'),
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
        ];
    }
}
