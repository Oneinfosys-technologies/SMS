<?php

namespace App\Services\Employee;

use App\Models\Employee\Department;
use App\Models\Employee\Record as EmployeeRecord;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DepartmentService
{
    public function preRequisite(Request $request): array
    {
        return [];
    }

    public function create(Request $request): Department
    {
        \DB::beginTransaction();

        $department = Department::forceCreate($this->formatParams($request));

        \DB::commit();

        return $department;
    }

    private function formatParams(Request $request, ?Department $department = null): array
    {
        $formatted = [
            'name' => $request->name,
            'alias' => $request->alias,
            'description' => $request->description,
        ];

        if (! $department) {
            $formatted['team_id'] = auth()->user()?->current_team_id;
        }

        return $formatted;
    }

    public function update(Request $request, Department $department): void
    {
        \DB::beginTransaction();

        $department->forceFill($this->formatParams($request, $department))->save();

        \DB::commit();
    }

    public function deletable(Department $department): void
    {
        $employeeRecordExists = EmployeeRecord::whereDepartmentId($department->id)->exists();

        if ($employeeRecordExists) {
            throw ValidationException::withMessages(['message' => trans('global.associated_with_dependency', ['attribute' => trans('employee.department.department'), 'dependency' => trans('employee.employee')])]);
        }
    }
}
