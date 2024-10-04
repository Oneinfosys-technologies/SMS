<?php

namespace App\Http\Requests\Library;

use App\Enums\Library\IssueTo;
use App\Models\Employee\Employee;
use App\Models\Library\BookCopy;
use App\Models\Student\Student;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;

class TransactionRequest extends FormRequest
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
            'to' => ['required', new Enum(IssueTo::class)],
            'requester' => ['required', 'uuid'],
            'issue_date' => ['required', 'date_format:Y-m-d'],
            'due_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:issue_date'],
            'records' => ['required', 'array', 'min:1'],
            'records.*.copy.uuid' => ['required', 'uuid', 'distinct'],
            'remarks' => ['nullable', 'min:2', 'max:1000'],
        ];
    }

    public function withValidator($validator)
    {
        if (! $validator->passes()) {
            return;
        }

        $validator->after(function ($validator) {
            $transactionUuid = $this->route('transaction');

            $requester = null;
            if ($this->to == 'student') {
                $requester = Student::query()
                    ->byTeam()
                    ->whereUuid($this->requester)
                    ->getOrFail(__('student.student'), 'requester');
            } elseif ($this->to == 'employee') {
                $requester = Employee::query()
                    ->byTeam()
                    ->whereUuid($this->requester)
                    ->getOrFail(__('employee.employee'), 'requester');
            }

            $requesterType = Str::title($this->to);

            $bookCopies = BookCopy::query()
                ->select('uuid', 'id')
                ->get();

            foreach ($this->records as $index => $record) {
                $bookCopy = $bookCopies->firstWhere('uuid', Arr::get($record, 'copy.uuid'));

                if (! $bookCopy) {
                    throw ValidationException::withMessages(['records.'.$index.'.copy' => trans('global.could_not_find', ['attribute' => trans('library.book.book')])]);
                }

                $newRecords[] = [
                    'uuid' => Arr::get($record, 'uuid', (string) Str::uuid()),
                    'copy' => $bookCopy,
                ];
            }

            $this->merge([
                'records' => $newRecords,
                'requester_type' => $requesterType,
                'requester_id' => $requester?->id,
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
            'to' => __('library.transaction.props.to'),
            'requester' => __('library.transaction.props.requester'),
            'issue_date' => __('library.transaction.props.issue_date'),
            'due_date' => __('library.transaction.props.due_date'),
            'records' => __('library.transaction.props.details'),
            'records.*.copy.uuid' => __('library.transaction.props.number'),
            'records.*.condition' => __('library.transaction.props.condition'),
            'remarks' => __('library.transaction.props.remarks'),
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
