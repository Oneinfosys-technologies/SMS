<?php

namespace App\Http\Controllers\Student\Report;

use App\Http\Controllers\Controller;
use App\Services\Student\Report\AttendanceSummaryListService;
use Illuminate\Http\Request;

class AttendanceSummaryExportController extends Controller
{
    public function __invoke(Request $request, AttendanceSummaryListService $service)
    {
        $list = $service->list($request);

        return $service->export($list);
    }
}
