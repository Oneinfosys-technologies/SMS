<?php

namespace App\Http\Resources\Reception;

use App\Enums\Reception\EnquiryStatus;
use App\Http\Resources\Academic\PeriodResource;
use App\Http\Resources\Employee\EmployeeSummaryResource;
use App\Http\Resources\MediaResource;
use App\Http\Resources\OptionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class EnquiryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'code_number' => $this->code_number,
            'name' => $this->name,
            'email' => $this->email,
            'contact_number' => $this->contact_number,
            'period' => PeriodResource::make($this->whenLoaded('period')),
            'type' => OptionResource::make($this->whenLoaded('type')),
            'source' => OptionResource::make($this->whenLoaded('source')),
            'employee' => EmployeeSummaryResource::make($this->whenLoaded('employee')),
            'date' => $this->date,
            'status' => EnquiryStatus::getDetail($this->status),
            'records_count' => $this->records_count,
            'records' => EnquiryRecordResource::collection($this->whenLoaded('records')),
            'follow_ups' => EnquiryFollowUpResource::collection($this->whenLoaded('followUps')),
            'remarks' => $this->remarks,
            'media_token' => $this->getMeta('media_token'),
            'media' => MediaResource::collection($this->whenLoaded('media')),
            'created_at' => \Cal::dateTime($this->created_at),
            'updated_at' => \Cal::dateTime($this->updated_at),
        ];
    }
}
