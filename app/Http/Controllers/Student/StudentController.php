<?php

namespace App\Http\Controllers\Student;

use App\Actions\Student\FetchBatchWiseStudent;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\Student\StudentResource;
use App\Models\Student\Student;
use App\Services\ContactService;
use App\Services\Student\StudentListService;
use App\Services\Student\StudentService;
use App\Services\Student\StudentSummaryListService;
use App\Services\Student\StudentSummaryService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('test.mode.restriction')->only(['destroy']);
    }

    public function preRequisite(Request $request, StudentService $service)
    {
        $this->authorize('preRequisite', Student::class);

        return response()->ok($service->preRequisite($request));
    }

    public function index(Request $request, StudentListService $service)
    {
        $this->authorize('viewAny', Student::class);

        return $service->paginate($request);
    }

    public function list(Request $request, StudentSummaryListService $service)
    {
        $this->authorize('viewSummary', Student::class);

        return $service->paginate($request);
    }

    public function summary(Request $request, StudentSummaryService $service)
    {
        $this->authorize('selfSummary', Student::class);

        return $service->summary($request);
    }

    public function listAll(Request $request, FetchBatchWiseStudent $action)
    {
        $this->authorize('viewAny', Student::class);

        return $action->execute([
            'batch' => $request->query('batch'),
            'status' => $request->query('status', 'studying'),
        ]);
    }

    public function show(Request $request, string $student)
    {
        $student = Student::findSummaryByUuidOrFail($student);

        $this->authorize('view', $student);

        if (! $request->query('summary')) {
            $student->load(['contact.user.roles', 'contact.guardians', 'contact.guardians.contact', 'contact.religion', 'contact.category', 'contact.caste', 'tags', 'enrollmentType']);

            $request->merge(['has_records' => true]);

            $student->records = Student::query()
                ->where('students.id', '!=', $student->id)
                ->where('students.contact_id', $student->contact_id)
                ->where('students.admission_id', $student->admission_id)
                ->join('batches', 'batches.id', '=', 'students.batch_id')
                ->join('courses', 'courses.id', '=', 'batches.course_id')
                ->join('periods', 'periods.id', '=', 'students.period_id')
                ->select('students.uuid', 'courses.name as course_name', 'periods.name as period_name', 'batches.name as batch_name')
                ->get();
        }

        return StudentResource::make($student);
    }

    public function update(ContactUpdateRequest $request, string $student, ContactService $service)
    {
        $student = Student::findByUuidOrFail($student);

        $this->authorize('update', $student);

        $service->update($request, $student->contact);

        return response()->success([
            'message' => trans('global.updated', ['attribute' => trans('student.student')]),
        ]);
    }

    public function destroy(string $student, StudentService $service)
    {
        $student = Student::findByUuidOrFail($student);

        $this->authorize('delete', $student);

        $service->deletable($student);

        $service->delete($student);

        return response()->success([
            'message' => trans('global.deleted', ['attribute' => trans('student.student')]),
        ]);
    }
}
