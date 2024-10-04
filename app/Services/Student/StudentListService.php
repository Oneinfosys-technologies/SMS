<?php

namespace App\Services\Student;

use App\Contracts\ListGenerator;
use App\Http\Resources\Student\StudentListResource;
use App\Models\Student\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class StudentListService extends ListGenerator
{
    protected $allowedSorts = ['created_at', 'code_number', 'name', 'course', 'gender', 'birth_date', 'religion', 'caste', 'category'];

    protected $defaultSort = 'name';

    protected $defaultOrder = 'asc';

    public function getHeaders(): array
    {
        $headers = [
            [
                'key' => 'codeNumber',
                'label' => trans('student.admission.props.code_number'),
                'print_label' => 'code_number',
                'print_sub_label' => 'joining_date.formatted',
                'sortable' => true,
                'visibility' => true,
            ],
            [
                'key' => 'joiningDate',
                'label' => trans('student.admission.props.date'),
                'print_label' => 'joining_date.formatted',
                'sortable' => true,
                'visibility' => false,
                'printable' => true,
            ],
            [
                'key' => 'name',
                'label' => trans('contact.props.name'),
                'print_label' => 'name',
                'sortable' => true,
                'visibility' => true,
            ],
            [
                'key' => 'course',
                'label' => trans('academic.course.course'),
                'print_label' => 'course_name + batch_name',
                // 'print_sub_label' => 'batch_name',
                // 'print_sub_label' => 'enrollment_type.name',
                'sortable' => true,
                'visibility' => true,
            ],
            [
                'key' => 'gender',
                'label' => trans('contact.props.gender'),
                'print_label' => 'gender.label',
                'sortable' => true,
                'visibility' => true,
            ],
            [
                'key' => 'birthDate',
                'label' => trans('contact.props.birth_date'),
                'print_label' => 'birth_date.formatted',
                'sortable' => true,
                'visibility' => true,
            ],
            [
                'key' => 'contactNumber',
                'label' => trans('contact.props.contact_number'),
                'print_label' => 'contact_number',
                'print_sub_label' => 'email',
                'sortable' => false,
                'visibility' => true,
            ],
            [
                'key' => 'rollNumber',
                'label' => trans('student.roll_number.roll_number'),
                'print_label' => 'roll_number',
                'sortable' => false,
                'visibility' => false,
            ],
            [
                'key' => 'parent',
                'label' => trans('student.props.parent'),
                'print_label' => 'father_name',
                'print_sub_label' => 'mother_name',
                'sortable' => false,
                'visibility' => true,
            ],
            [
                'key' => 'motherName',
                'label' => trans('contact.props.mother_name'),
                'print_label' => 'mother_name',
                'sortable' => false,
                'visibility' => false,
                'printable' => true,
            ],
            [
                'key' => 'guardian',
                'label' => trans('guardian.guardian'),
                'print_label' => 'guardian.contact.name',
                'print_sub_label' => 'guardian.contact.contact_number',
                'sortable' => false,
                'visibility' => true,
            ],
            [
                'key' => 'bloodGroup',
                'label' => trans('contact.props.blood_group'),
                'print_label' => 'blood_group.label',
                'sortable' => false,
                'visibility' => false,
            ],
            [
                'key' => 'religion',
                'label' => trans('contact.religion.religion'),
                'print_label' => 'religion_name',
                'sortable' => true,
                'visibility' => false,
            ],
            [
                'key' => 'category',
                'label' => trans('contact.category.category'),
                'print_label' => 'category_name',
                'sortable' => true,
                'visibility' => false,
            ],
            [
                'key' => 'caste',
                'label' => trans('contact.caste.caste'),
                'print_label' => 'caste_name',
                'sortable' => true,
                'visibility' => false,
            ],
            [
                'key' => 'uniqueIdNumber1',
                'label' => config('config.contact.unique_id_number1_label'),
                'sortable' => true,
                'visibility' => false,
            ],
            [
                'key' => 'uniqueIdNumber2',
                'label' => config('config.contact.unique_id_number2_label'),
                'sortable' => true,
                'visibility' => false,
            ],
            [
                'key' => 'uniqueIdNumber3',
                'label' => config('config.contact.unique_id_number3_label'),
                'sortable' => true,
                'visibility' => false,
            ],
            [
                'key' => 'address',
                'label' => trans('contact.props.address.address'),
                'print_label' => 'address',
                'sortable' => false,
                'visibility' => false,
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
        $search = $request->query('search');
        $status = $request->query('status', 'studying');
        $withTransferred = $request->boolean('with_transferred');
        $withCancelled = $request->boolean('with_cancelled');
        $forSubject = $request->boolean('for_subject');

        $tagsIncluded = Str::toArray($request->query('tags_included'));
        $tagsExcluded = Str::toArray($request->query('tags_excluded'));

        return Student::query()
            ->detail()
            ->byPeriod()
            ->filterAccessible($forSubject)
            ->when($withTransferred == false && $withCancelled == false, function ($q) use ($status) {
                $q->filterByStatus($status);
            })
            ->when($withTransferred == true, function ($q) {
                $q->whereNull('students.cancelled_at');
            })
            ->when($withCancelled == true, function ($q) {
                $q->whereNull('students.end_date');
            })
            ->when($request->query('status') == 'alumni' && $request->query('alumni_period'), function ($q) use ($request) {
                $q->whereHas('period', function ($q) use ($request) {
                    $q->where('uuid', $request->query('alumni_period'));
                });
            })
            ->when($request->query('name'), function ($q, $name) {
                $q->where(\DB::raw('REGEXP_REPLACE(CONCAT_WS(" ", first_name, middle_name, third_name, last_name), "[[:space:]]+", " ")'), 'like', "%{$name}%");
            })
            ->when($search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where(\DB::raw('REGEXP_REPLACE(CONCAT_WS(" ", first_name, middle_name, third_name, last_name), "[[:space:]]+", " ")'), 'like', "%{$search}%")
                        ->orWhere('admissions.code_number', 'like', "%{$search}%");
                });
            })
            ->when($tagsIncluded, function ($q, $tagsIncluded) {
                $q->whereHas('tags', function ($q) use ($tagsIncluded) {
                    $q->whereIn('name', $tagsIncluded);
                });
            })
            ->when($tagsExcluded, function ($q, $tagsExcluded) {
                $q->whereDoesntHave('tags', function ($q) use ($tagsExcluded) {
                    $q->whereIn('name', $tagsExcluded);
                });
            })
            ->when($request->query('enrollment_type'), function ($q, $enrollmentType) {
                $q->where('enrollment_types.uuid', $enrollmentType);
            })
            ->filter([
                'App\QueryFilters\UuidMatch:students.uuid',
                'App\QueryFilters\LikeMatch:first_name',
                'App\QueryFilters\LikeMatch:last_name',
                'App\QueryFilters\LikeMatch:father_name',
                'App\QueryFilters\LikeMatch:mother_name',
                'App\QueryFilters\LikeMatch:code_number',
                'App\QueryFilters\LikeMatch:contact_number',
                'App\QueryFilters\ExactMatch:gender',
                'App\QueryFilters\WhereInMatch:students.uuid,uuid',
                'App\QueryFilters\WhereInMatch:blood_group,blood_groups',
                'App\QueryFilters\WhereInMatch:religions.uuid,religions',
                'App\QueryFilters\WhereInMatch:categories.uuid,categories',
                'App\QueryFilters\WhereInMatch:castes.uuid,castes',
                'App\QueryFilters\WhereInMatch:batches.uuid,batches',
                'App\QueryFilters\DateBetween:birth_start_date,birth_end_date,birth_date',
                'App\QueryFilters\DateBetween:admission_start_date,admission_end_date,joining_date',
                'App\QueryFilters\DateBetween:start_date,end_date,students.created_at,datetime',
            ]);
    }

    public function paginate(Request $request): AnonymousResourceCollection
    {
        $view = $request->query('view', 'card');
        $request->merge(['view' => $view]);

        $query = $this->filter($request);

        if ($this->getSort() == 'course') {
            $query->orderBy('course_name', $this->getOrder());
        } elseif ($this->getSort() == 'religion') {
            $query->orderBy('religion_name', $this->getOrder());
        } elseif ($this->getSort() == 'category') {
            $query->orderBy('category_name', $this->getOrder());
        } elseif ($this->getSort() == 'caste') {
            $query->orderBy('caste_name', $this->getOrder());
        } else {
            $query->orderBy($this->getSort(), $this->getOrder());
        }

        return StudentListResource::collection($query
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
