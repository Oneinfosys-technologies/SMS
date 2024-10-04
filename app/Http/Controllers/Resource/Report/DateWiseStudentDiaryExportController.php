<?php

namespace App\Http\Controllers\Resource\Report;

use App\Http\Controllers\Controller;
use App\Services\Resource\Report\DateWiseStudentDiaryListService;
use Illuminate\Http\Request;

class DateWiseStudentDiaryExportController extends Controller
{
    public function __invoke(Request $request, DateWiseStudentDiaryListService $service)
    {
        $list = $service->list($request);

        return $service->export($list);
    }
}
