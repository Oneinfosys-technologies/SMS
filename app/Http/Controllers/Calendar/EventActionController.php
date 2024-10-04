<?php

namespace App\Http\Controllers\Calendar;

use App\Http\Controllers\Controller;
use App\Models\Calendar\Event;
use App\Services\Calendar\EventActionService;
use Illuminate\Http\Request;

class EventActionController extends Controller
{
    public function uploadAsset(Request $request, EventActionService $service, string $event, string $type)
    {
        $event = Event::findByUuidOrFail($event);

        $this->authorize('update', $event);

        $service->uploadAsset($request, $event, $type);

        return response()->ok();
    }
}
