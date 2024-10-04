<?php

namespace App\Services\Student;

use App\Contracts\ListGenerator;
use App\Enums\Student\RegistrationStatus;
use App\Http\Resources\Student\RegistrationResource;
use App\Models\Student\Registration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class RegistrationListService extends ListGenerator
{
    protected $allowedSorts = ['created_at', 'date'];

    protected $defaultSort = 'date';

    protected $defaultOrder = 'desc';

    public function getHeaders(): array
    {
        $headers = [
            [
                'key' => 'codeNumber',
                'label' => trans('student.registration.props.code_number'),
                'print_label' => 'code_number',
                'print_sub_label' => 'period.name',
                'print_additional_label' => 'admission_date.formatted',
                'sortable' => false,
                'visibility' => true,
            ],
            [
                'key' => 'name',
                'label' => trans('student.props.name'),
                'print_label' => 'contact.name',
                'print_sub_label' => 'contact.contact_number',
                'sortable' => false,
                'visibility' => true,
            ],
            [
                'key' => 'guardianName',
                'label' => trans('guardian.props.name'),
                'print_label' => 'contact.guardian.contact.name',
                'print_sub_label' => 'contact.guardian.contact.contact_number',
                'sortable' => false,
                'visibility' => true,
            ],
            [
                'key' => 'birthDate',
                'label' => trans('contact.props.birth_date'),
                'print_label' => 'contact.birth_date.formatted',
                'sortable' => false,
                'visibility' => true,
            ],
            [
                'key' => 'course',
                'label' => trans('academic.course.course'),
                'print_label' => 'course.name_with_term',
                'print_sub_label' => 'batch_name',
                'sortable' => false,
                'visibility' => true,
            ],
            [
                'key' => 'status',
                'label' => trans('student.registration.props.status'),
                'print_label' => 'status.label',
                'sortable' => false,
                'visibility' => true,
            ],
            [
                'key' => 'date',
                'label' => trans('student.registration.props.date'),
                'print_label' => 'date.formatted',
                'print_sub_label' => 'application_number',
                'sortable' => true,
                'visibility' => true,
            ],
            [
                'key' => 'createdAt',
                'label' => trans('general.created_at'),
                'print_label' => 'created_at.formatted',
                'sortable' => true,
                'visibility' => true,
            ],
        ];

        if (request()->ajax()) {
            $headers[] = $this->actionHeader;
        }

        return $headers;
    }

    public function filter(Request $request): Builder
    {
        $courses = $request->query('courses');
        $name = $request->query('name');
        $guardianName = $request->query('guardian_name');
        $status = $request->query('status');
        $applicationNumber = $request->query('application_number');
        $type = $request->query('type');

        return Registration::query()
            ->select('registrations.*', 'admissions.code_number as admission_number', 'admissions.joining_date as admission_date', 'batches.name as batch_name')
            ->with(['period', 'course', 'contact' => function ($q) {
                $q->withGuardian();
            }, 'contact.guardian'])
            ->byPeriod()
            ->leftJoin('admissions', 'admissions.registration_id', '=', 'registrations.id')
            ->leftJoin('batches', 'batches.id', '=', 'admissions.batch_id')
            ->when($status, function ($q, $status) {
                $q->where('status', '=', $status);
            }, function ($q) {
                $q->where('status', '!=', RegistrationStatus::INITIATED);
            })
            ->when($applicationNumber, function ($q, $applicationNumber) {
                $q->where('meta->application_number', 'like', "%{$applicationNumber}%");
            })
            ->when($type, function ($q, $type) {
                if ($type == 'online') {
                    $q->where('is_online', true);
                } else {
                    $q->where('is_online', false);
                }
            })
            ->when($courses, function ($q, $courses) {
                $q->whereHas('course', function ($q1) use ($courses) {
                    $q1->whereIn('uuid', Str::toArray($courses));
                });
            })->when($name, function ($q, $name) {
                $q->whereHas('contact', function ($q) use ($name) {
                    $q->searchByName($name);
                });
            })->when($guardianName, function ($q, $guardianName) {
                $q->whereHas('contact', function ($q1) use ($guardianName) {
                    $q1->whereHas('guardians', function ($q2) use ($guardianName) {
                        $q2->whereHas('contact', function ($q3) use ($guardianName) {
                            $q3->searchByName($guardianName);
                        });
                    });
                });
            })
            ->filter([
                'App\QueryFilters\ExactMatch:code_number',
                'App\QueryFilters\ExactMatch:status',
                'App\QueryFilters\DateBetween:start_date,end_date,date',
            ]);
    }

    public function paginate(Request $request): AnonymousResourceCollection
    {
        return RegistrationResource::collection($this->filter($request)
            ->orderBy($this->getSort(), $this->getOrder())
            ->paginate((int) $this->getPageLength(), ['*'], 'current_page'))
            ->additional([
                'headers' => $this->getHeaders(),
                'meta' => [
                    'allowed_sorts' => $this->allowedSorts,
                    'default_sort' => $this->defaultSort,
                    'default_order' => $this->defaultOrder,
                ],
            ]);
    }

    public function list(Request $request): AnonymousResourceCollection
    {
        return $this->paginate($request);
    }
}
