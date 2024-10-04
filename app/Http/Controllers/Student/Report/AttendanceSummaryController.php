<?php

namespace App\Http\Controllers\Student\Report;

use App\Http\Controllers\Controller;
use App\Services\Student\Report\AttendanceSummaryListService;
use App\Services\Student\Report\AttendanceSummaryService;
use Illuminate\Http\Request;

class AttendanceSummaryController extends Controller
{
    public function preRequisite(Request $request, AttendanceSummaryService $service)
    {
        return response()->ok($service->preRequisite($request));
    }

    public function fetch(Request $request, AttendanceSummaryListService $service)
    {
        return $service->filter($request);
    }
}
