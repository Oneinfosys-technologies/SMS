<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\Config\PushNotificationTemplateRequest;
use App\Http\Resources\Config\PushNotificationTemplateResource;
use App\Models\Config\Template;
use App\Services\Config\PushNotificationTemplateListService;
use App\Services\Config\PushNotificationTemplateService;
use Illuminate\Http\Request;

class PushNotificationTemplateController extends Controller
{
    public function index(Request $request, PushNotificationTemplateListService $service)
    {
        return $service->paginate($request);
    }

    public function show(Template $pushNotificationTemplate)
    {
        request()->merge(['detail' => true]);

        return PushNotificationTemplateResource::make($pushNotificationTemplate);
    }

    public function update(PushNotificationTemplateRequest $request, Template $pushNotificationTemplate, PushNotificationTemplateService $service)
    {
        $service->update($request, $pushNotificationTemplate);

        return response()->success(['message' => trans('global.updated', ['attribute' => trans('config.push_notification.template.template')])]);
    }
}
