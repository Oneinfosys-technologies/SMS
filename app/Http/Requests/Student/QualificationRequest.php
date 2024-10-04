<?php

namespace App\Http\Requests\Student;

use App\Enums\OptionType;
use App\Models\Contact;
use App\Models\Option;
use App\Models\Qualification;
use App\Models\Student\Student;
use Illuminate\Foundation\Http\FormRequest;

class QualificationRequest extends FormRequest
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
            'level' => 'required',
            'course' => 'required|min:2|max:100',
            'institute' => 'nullable|min:2|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'affiliated_to' => 'nullable|min:2|max:100',
            'result' => 'nullable|min:2|max:200',
        ];
    }

    public function withValidator($validator)
    {
        if (! $validator->passes()) {
            return;
        }

        $validator->after(function ($validator) {
            $studentUuid = $this->route('student');
            $qualificationUuid = $this->route('qualification');

            $student = Student::query()
                ->whereUuid($studentUuid)
                ->firstOrFail();

            $qualificationLevel = Option::query()
                ->byTeam()
                ->whereType(OptionType::QUALIFICATION_LEVEL->value)
                ->whereUuid($this->level)
                ->getOrFail(__('student.qualification_level.qualification_level'), 'level');

            $existingQualification = Qualification::whereHasMorph(
                'model', [Contact::class],
                function ($q) use ($student) {
                    $q->whereId($student->contact_id);
                }
            )
                ->when($qualificationUuid, function ($q, $qualificationUuid) {
                    $q->where('uuid', '!=', $qualificationUuid);
                })
                ->whereCourse($this->course)
                ->exists();

            if ($existingQualification) {
                $validator->errors()->add('course', trans('validation.unique', ['attribute' => __('student.qualification.props.course')]));
            }

            $this->merge([
                'level_id' => $qualificationLevel->id,
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
            'course' => __('student.qualification.props.course'),
            'institute' => __('student.qualification.props.institute'),
            'start_date' => __('student.qualification.props.start_date'),
            'end_date' => __('student.qualification.props.end_date'),
            'affiliated_to' => __('student.qualification.props.affiliated_to'),
            'result' => __('student.qualification.props.result'),
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
