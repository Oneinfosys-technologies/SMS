<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\PaymentRequest;
use App\Models\Student\Student;
use App\Services\Student\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function preRequisite(Request $request, string $student, PaymentService $service)
    {
        $student = Student::findByUuidOrFail($student);

        $this->authorize('makePayment', $student);

        return response()->ok($service->preRequisite($request, $student));
    }

    public function makePayment(PaymentRequest $request, string $student, PaymentService $service)
    {
        $student = Student::findByUuidOrFail($student);

        $this->authorize('makePayment', $student);

        $service->makePayment($request, $student);

        return response()->success([
            'message' => trans('global.paid', ['attribute' => trans('student.fee.fee')]),
        ]);
    }

    public function getPayment(Request $request, string $student, string $uuid, PaymentService $service)
    {
        $student = Student::findSummaryByUuidOrFail($student);

        $this->authorize('view', $student);

        $transaction = $service->getPayment($request, $student, $uuid);

        return [];
    }

    public function export(Request $request, string $student, string $uuid, PaymentService $service)
    {
        $student = Student::findSummaryByUuidOrFail($student);

        $this->authorize('exportPayment', $student);

        $transaction = $service->getPayment($request, $student, $uuid);

        $transaction->load('records.model.payments.head', 'payments.method');

        $student->load('batch.course.division.program');

        return view()->first([
            config('config.print.custom_path').'student.fee-receipt',
            'print.student.fee-receipt',
        ], compact('student', 'transaction'));

        // return view('print.student.fee-receipt', compact('student', 'transaction'));
    }

    public function updatePayment(Request $request, string $student, string $uuid, PaymentService $service)
    {
        $student = Student::findByUuidOrFail($student);

        $this->authorize('updatePayment', $student);

        $service->updatePayment($request, $student, $uuid);

        return response()->success([
            'message' => trans('global.updated', ['attribute' => trans('student.fee.fee')]),
        ]);
    }

    public function cancelPayment(Request $request, string $student, string $uuid, PaymentService $service)
    {
        $student = Student::findByUuidOrFail($student);

        $this->authorize('cancelPayment', $student);

        $service->cancelPayment($request, $student, $uuid);

        return response()->success([
            'message' => trans('global.cancelled', ['attribute' => trans('student.fee.fee')]),
        ]);
    }
}
