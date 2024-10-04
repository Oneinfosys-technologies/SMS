<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reception\EnquiryRequest;
use App\Http\Resources\Reception\EnquiryResource;
use App\Models\Reception\Enquiry;
use App\Services\Reception\EnquiryListService;
use App\Services\Reception\EnquiryService;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function __construct()
    {
        $this->middleware('test.mode.restriction')->only(['destroy']);
    }

    public function preRequisite(Request $request, EnquiryService $service)
    {
        return $service->preRequisite($request);
    }

    public function index(Request $request, EnquiryListService $service)
    {
        $this->authorize('viewAny', Enquiry::class);

        return $service->paginate($request);
    }

    public function store(EnquiryRequest $request, EnquiryService $service)
    {
        $this->authorize('create', Enquiry::class);

        $enquiry = $service->create($request);

        return response()->success([
            'message' => trans('global.created', ['attribute' => trans('reception.enquiry.enquiry')]),
            'enquiry' => EnquiryResource::make($enquiry),
        ]);
    }

    public function show(string $enquiry, EnquiryService $service)
    {
        $enquiry = Enquiry::findByUuidOrFail($enquiry);

        $this->authorize('view', $enquiry);

        $enquiry->load(['period', 'type', 'source', 'records.course', 'followUps', 'media', 'employee' => fn ($q) => $q->summary()]);

        return EnquiryResource::make($enquiry);
    }

    public function update(EnquiryRequest $request, string $enquiry, EnquiryService $service)
    {
        $enquiry = Enquiry::findByUuidOrFail($enquiry);

        $this->authorize('update', $enquiry);

        $service->update($request, $enquiry);

        return response()->success([
            'message' => trans('global.updated', ['attribute' => trans('reception.enquiry.enquiry')]),
        ]);
    }

    public function destroy(string $enquiry, EnquiryService $service)
    {
        $enquiry = Enquiry::findByUuidOrFail($enquiry);

        $this->authorize('delete', $enquiry);

        $service->deletable($enquiry);

        $enquiry->delete();

        return response()->success([
            'message' => trans('global.deleted', ['attribute' => trans('reception.enquiry.enquiry')]),
        ]);
    }

    public function downloadMedia(Enquiry $enquiry, string $uuid, EnquiryService $service)
    {
        $this->authorize('view', $enquiry);

        return $enquiry->downloadMedia($uuid);
    }
}
