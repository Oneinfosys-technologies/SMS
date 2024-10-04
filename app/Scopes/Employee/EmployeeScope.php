<?php

namespace App\Scopes\Employee;

use App\Concerns\SubordinateAccess;
use App\Models\Employee\Record;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait EmployeeScope
{
    use SubordinateAccess;

    // For internal operation
    public function scopeBasic(Builder $query, ?string $date = null)
    {
        $date ??= today()->toDateString();

        $query
            ->select('employees.id', 'employees.uuid', 'employees.contact_id', 'contacts.team_id', 'contacts.user_id', 'employees.type')
            ->join('contacts', function ($join) {
                $join->on('employees.contact_id', '=', 'contacts.id')
                    ->where('contacts.team_id', '=', auth()->user()?->current_team_id);
            });
    }

    // To show summary of employee
    public function scopeSummary(Builder $query, ?string $date = null)
    {
        $date ??= today()->toDateString();

        $query->select(
            'employees.id', 'employees.uuid', 'employees.code_number', 'employees.joining_date', 'employees.leaving_date', 'employees.type', \DB::raw('REGEXP_REPLACE(CONCAT_WS(" ", first_name, middle_name, third_name, last_name), "[[:space:]]+", " ") as name'), 'employees.contact_id', 'contacts.team_id', 'contacts.user_id', 'contacts.gender', 'contacts.photo', 'contacts.birth_date', 'employee_records.designation_id', 'designations.name as designation_name'
        )
            ->join('contacts', function ($join) {
                $join->on('employees.contact_id', '=', 'contacts.id')
                    ->where('contacts.team_id', auth()->user()?->current_team_id);
            })
            ->leftJoin('employee_records', function ($join) use ($date) {
                $join->on('employees.id', '=', 'employee_records.employee_id')
                    ->on('start_date', '=', \DB::raw("(select start_date from employee_records where employees.id = employee_records.employee_id and start_date <= '".$date."' order by start_date desc limit 1)"))
                    ->join('designations', 'employee_records.designation_id', '=', 'designations.id');
            });
    }

    public function scopeDetail(Builder $query, ?string $date = null)
    {
        $date ??= today()->toDateString();

        $query->select(
            'employees.id', 'employees.uuid', 'employees.code_number', 'employees.joining_date', 'employees.leaving_date', 'employees.type', 'employees.created_at', 'employees.contact_id', 'employees.meta',
            'employee_records.start_date', 'employee_records.end_date', 'employee_records.id as last_record_id', \DB::raw('REGEXP_REPLACE(CONCAT_WS(" ", first_name, middle_name, third_name, last_name), "[[:space:]]+", " ") as name'),
            'contacts.birth_date', 'contacts.anniversary_date', 'contacts.gender', 'contacts.photo', 'contacts.user_id', 'contacts.team_id', 'contacts.contact_number',
            'departments.name as department_name', 'departments.uuid as department_uuid', 'departments.id as department_id',
            'designations.name as designation_name', 'designations.uuid as designation_uuid', 'designations.id as designation_id',
            'options.name as employment_status_name', 'options.uuid as employment_status_uuid', 'options.id as employment_status_id', 'religions.uuid as religion_uuid', 'religions.name as religion_name', 'castes.uuid as caste_uuid', 'castes.name as caste_name', 'categories.uuid as category_uuid', 'categories.name as category_name', 'users.uuid as user_uuid'
        )
            ->join('contacts', function ($join) {
                $join->on('employees.contact_id', '=', 'contacts.id')
                    ->where('contacts.team_id', auth()->user()?->current_team_id);
            })
            ->leftJoin('users', 'contacts.user_id', '=', 'users.id')
            ->leftJoin('employee_records', function ($join) use ($date) {
                $join->on('employees.id', '=', 'employee_records.employee_id')
                    ->on('start_date', '=', \DB::raw("(select start_date from employee_records where employees.id = employee_records.employee_id and start_date <= '".$date."' order by start_date desc limit 1)"))
                    ->join('departments', 'employee_records.department_id', '=', 'departments.id')
                    ->join('designations', 'employee_records.designation_id', '=', 'designations.id')
                    ->join('options', 'employee_records.employment_status_id', '=', 'options.id');
            })
            ->leftJoin('options as religions', 'contacts.religion_id', 'religions.id')
            ->leftJoin('options as castes', 'contacts.caste_id', 'castes.id')
            ->leftJoin('options as categories', 'contacts.caste_id', 'categories.id');
    }

    public function scopeRecord(Builder $query, bool $self = false, ?string $date = null)
    {
        $date ??= today()->toDateString();

        $query->select(
            'employees.id', 'employees.uuid', 'employees.contact_id', 'contacts.team_id', 'employees.type', 'contacts.user_id', 'employee_records.designation_id', 'employee_records.department_id'
        )
            ->join('contacts', function ($join) use ($self) {
                $join->on('employees.contact_id', '=', 'contacts.id')
                    ->where('contacts.team_id', auth()->user()?->current_team_id)
                    ->when($self, function ($q) {
                        $q->where('contacts.user_id', '=', auth()->id());
                    });
            })
            ->leftJoin('employee_records', function ($join) use ($date) {
                $join->on('employees.id', '=', 'employee_records.employee_id')
                    ->on('start_date', '=', \DB::raw("(select start_date from employee_records where employees.id = employee_records.employee_id and start_date <= '".$date."' order by start_date desc limit 1)"));
            });
    }

    public function scopeFilterAccessible(Builder $query, ?string $date = null)
    {
        $designationIds = $this->getAccessibleDesignationIds($date);

        if (is_array($designationIds)) {
            $query->where(function ($q) use ($designationIds) {
                $q->whereIn('designations.id', $designationIds)
                    ->orWhere('contacts.user_id', auth()->id());
            });
        }
    }

    public function scopeAuth(Builder $query, ?int $userId = null)
    {
        $userId = $userId ?? auth()->id();

        $query->select('employees.id', 'employees.uuid', 'contacts.user_id', 'employees.type')
            ->join('contacts', function ($join) use ($userId) {
                $join->on('employees.contact_id', '=', 'contacts.id')
                    ->where('contacts.team_id', auth()->user()?->current_team_id)
                    ->where('contacts.user_id', $userId);
            });
    }

    public function scopeWithCurrentDesignationId(Builder $query, ?string $date = null)
    {
        $date ??= today()->toDateString();

        $query->addSelect(['current_designation_id' => Record::select('designation_id')
            ->whereColumn('employee_id', 'employees.id')
            ->where('start_date', '<=', $date)
            ->orderBy('start_date', 'desc')
            ->limit(1),
        ]);
    }

    public function scopeWithLastRecordId(Builder $query, ?string $date = null)
    {
        $date ??= today()->toDateString();

        $query->addSelect(['last_record_id' => Record::select('id')
            ->whereColumn('employee_id', 'employees.id')
            ->where('start_date', '<=', $date)
            ->orderBy('start_date', 'desc')
            ->limit(1),
        ]);
    }

    public function scopeWithRecord(Builder $query, $type = 'designation', ?string $date = null)
    {
        $date ??= today()->toDateString();

        $field = $type.'_name';

        if ($type == 'employment_status') {
            $type = 'option';
        }

        $select = Str::plural($type).'.name';

        $query->addSelect([
            $field => Record::select($select)
                ->when($type == 'designation', function ($q) {
                    $q->join('designations', 'employee_records.designation_id', '=', 'designations.id');
                })
                ->when($type == 'department', function ($q) {
                    $q->join('departments', 'employee_records.department_id', '=', 'departments.id');
                })
                ->when($type == 'option', function ($q) {
                    $q->join('options', 'employee_records.employment_status_id', '=', 'options.id');
                })
                ->whereColumn('employee_id', 'employees.id')
                ->where('start_date', '<=', $date)
                // This will not get last record if employee is not active
                // ->where(function ($q) use ($date) {
                //     $q->whereNull('end_date')->orWhere(function ($q) use ($date) {
                //         $q->whereNotNull('end_date')->where('end_date', '>=', $date);
                //     });
                // })
                ->orderBy('start_date', 'desc')
                ->limit(1),
        ]);
    }

    public function scopeFilterByStatus(Builder $query, ?string $status = null)
    {
        if ($status == 'all') {
            return;
        }

        $query->when($status == 'active', function ($q) {
            $q->where(function ($q) {
                $q->whereNull('leaving_date')
                    ->orWhere(function ($q) {
                        $q->whereNotNull('leaving_date')
                            ->where('leaving_date', '>=', today()->toDateString());
                    });
            });
        })
            ->when($status == 'inactive', function ($q) {
                $q->where(function ($q) {
                    $q->whereNotNull('leaving_date')
                        ->where('leaving_date', '<', today()->toDateString());
                });
            });
    }
}
