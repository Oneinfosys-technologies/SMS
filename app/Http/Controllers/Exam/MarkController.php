<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\Controller;
use App\Http\Requests\Exam\MarkRequest;
use App\Services\Exam\MarkService;
use Illuminate\Http\Request;

class MarkController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:exam-schedule:read')->only(['preRequisite', 'fetch']);
        $this->middleware('permission:exam:marks-record|exam:subject-incharge-wise-marks-record')->only(['store', 'remove']);
    }

    public function preRequisite(Request $request, MarkService $service)
    {
        return response()->ok($service->preRequisite($request));
    }

    public function fetch(Request $request, MarkService $service)
    {
        return $service->fetch($request);
    }

    public function store(MarkRequest $request, MarkService $service)
    {
        $service->store($request);

        return response()->success([
            'message' => trans('global.stored', ['attribute' => trans('exam.assessment.props.mark')]),
        ]);
    }

    public function remove(Request $request, MarkService $service)
    {
        $service->remove($request);

        return response()->success([
            'message' => trans('global.removed', ['attribute' => trans('exam.assessment.props.mark')]),
        ]);
    }
}
