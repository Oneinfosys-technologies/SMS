<?php

namespace App\Services\Employee;

use App\Actions\CreateContact;
use App\Actions\UpdateContact;
use App\Enums\BloodGroup;
use App\Enums\Employee\Status;
use App\Enums\Employee\Type;
use App\Enums\Gender;
use App\Enums\MaritalStatus;
use App\Enums\OptionType;
use App\Http\Resources\OptionResource;
use App\Models\Contact;
use App\Models\Employee\Employee;
use App\Models\Employee\Record;
use App\Models\Option;
use App\Support\FormatCodeNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class EmployeeService
{
    use FormatCodeNumber;

    private function codeNumber()
    {
        $numberPrefix = config('config.employee.code_number_prefix');
        $numberSuffix = config('config.employee.code_number_suffix');
        $digit = config('config.employee.code_number_digit', 0);

        $numberFormat = $numberPrefix.'%NUMBER%'.$numberSuffix;
        $codeNumber = (int) Employee::byTeam()->whereNumberFormat($numberFormat)->max('number') + 1;

        return $this->getCodeNumber(number: $codeNumber, digit: $digit, format: $numberFormat);
    }

    private function validateCodeNumber(Request $request, ?string $uuid = null): array
    {
        $existingCodeNumber = Employee::byTeam()->whereCodeNumber($request->code_number)->when($uuid, function ($q, $uuid) {
            $q->where('uuid', '!=', $uuid);
        })->exists();

        if ($existingCodeNumber) {
            throw ValidationException::withMessages(['code_number' => trans('global.duplicate', ['attribute' => trans('employee.props.code_number')])]);
        }

        $codeNumberDetail = $this->codeNumber();

        return $request->code_number == Arr::get($codeNumberDetail, 'code_number') ? $codeNumberDetail : [
            'code_number' => $request->code_number,
        ];
    }

    public function preRequisite(Request $request): array
    {
        $codeNumber = Arr::get($this->codeNumber(), 'code_number');

        $genders = Gender::getOptions();

        $statuses = Status::getOptions();

        $maritalStatuses = MaritalStatus::getOptions();

        $bloodGroups = BloodGroup::getOptions();

        $categories = OptionResource::collection(Option::query()
            ->byTeam()
            ->where('type', OptionType::MEMBER_CATEGORY->value)
            ->get());

        $castes = OptionResource::collection(Option::query()
            ->byTeam()
            ->where('type', OptionType::MEMBER_CASTE->value)
            ->get());

        $religions = OptionResource::collection(Option::query()
            ->byTeam()
            ->where('type', OptionType::RELIGION->value)
            ->get());

        $types = Type::getOptions();

        $employeeTypes = [
            ['label' => trans('global.new', ['attribute' => trans('employee.employee')]), 'value' => 'new'],
            ['label' => trans('global.existing', ['attribute' => trans('employee.employee')]), 'value' => 'existing'],
        ];

        return compact('codeNumber', 'genders', 'statuses', 'maritalStatuses', 'types', 'employeeTypes', 'bloodGroups', 'categories', 'castes', 'religions');
    }

    public function create(Request $request): Employee
    {
        \DB::beginTransaction();

        if ($request->employee_type == 'new') {
            $params = $request->all();
            $params['source'] = 'employee';

            $contact = (new CreateContact)->execute($params);

            $request->merge([
                'contact_id' => $contact->id,
            ]);
        }

        $employee = Employee::forceCreate($this->formatParams($request));

        $employeeRecord = Record::forceCreate([
            'employee_id' => $employee->id,
            'department_id' => $request->department_id,
            'designation_id' => $request->designation_id,
            'employment_status_id' => $request->employment_status_id,
            'start_date' => $request->joining_date,
        ]);

        \DB::commit();

        return $employee;
    }

    private function formatParams(Request $request, ?Employee $employee = null): array
    {
        $codeNumberDetail = $this->validateCodeNumber($request);

        $formatted = [
            'type' => $request->type,
            'contact_id' => $request->contact_id,
            'joining_date' => $request->joining_date,
            'number_format' => Arr::get($codeNumberDetail, 'number_format'),
            'number' => Arr::get($codeNumberDetail, 'number'),
            'code_number' => $request->code_number,
        ];

        return $formatted;
    }

    public function update(Request $request, Employee $employee): void
    {
        $employee->type = $request->type;
        $employee->save();

        $contact = $employee->contact;

        $existingContact = Contact::byTeam()->where('uuid', '!=', $contact->uuid)
            ->whereFirstName($request->input('first_name', $contact->first_name))
            ->whereMiddleName($request->input('middle_name', $contact->middle_name))
            ->whereThirdName($request->input('third_name', $contact->third_name))
            ->whereLastName($request->input('last_name', $contact->last_name))
            ->whereContactNumber($request->input('contact_number', $contact->contact_number))
            ->count();

        if ($existingContact) {
            throw ValidationException::withMessages(['message' => trans('employee.exists')]);
        }

        \DB::beginTransaction();

        (new UpdateContact)->execute($employee->contact, $request->all());

        \DB::commit();
    }

    public function deletable(Employee $employee): void {}
}
