<?php

namespace App\Http\Resources\Finance\Report;

use App\Http\Resources\Student\FeeRecordResource;
use Illuminate\Http\Resources\Json\JsonResource;

class FeeConcessionListResource extends JsonResource
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
            'name' => $this->name,
            'father_name' => $this->father_name,
            'code_number' => $this->code_number,
            'roll_number' => $this->roll_number,
            'concession_name' => $this->concession_name,
            'concession_type' => $this->concession_type,
            'joining_date' => \Cal::date($this->joining_date),
            'batch_name' => $this->batch_name,
            'course_name' => $this->course_name,
            'contact_number' => $this->contact_number,
            'installment_title' => $this->installment_title,
            'fee_group_name' => $this->fee_group_name,
            'records' => FeeRecordResource::collection($this->whenLoaded('records')),
        ];
    }
}
