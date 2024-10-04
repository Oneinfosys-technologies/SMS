<?php

namespace App\Http\Resources\Student;

use App\Enums\Finance\PaymentStatus;
use App\Enums\Student\RegistrationStatus;
use App\Http\Resources\Academic\CourseResource;
use App\Http\Resources\Academic\PeriodResource;
use App\Http\Resources\ContactResource;
use App\Http\Resources\Finance\TransactionResource;
use App\Http\Resources\MediaResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RegistrationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'number_format' => $this->number_format,
            'number' => $this->number,
            'code_number' => $this->code_number,
            'admission_number' => $this->admission_number,
            'batch_name' => $this->batch_name,
            'admission_date' => \Cal::date($this->admission_date),
            'contact' => new ContactResource($this->whenLoaded('contact')),
            'period' => new PeriodResource($this->whenLoaded('period')),
            'course' => new CourseResource($this->whenLoaded('course')),
            'admission' => new AdmissionResource($this->whenLoaded('admission')),
            'date' => $this->date,
            'fee' => $this->fee,
            'payment_status' => PaymentStatus::getDetail($this->payment_status),
            'status' => RegistrationStatus::getDetail($this->status),
            'transactions' => TransactionResource::collection($this->whenLoaded('transactions')),
            'remarks' => $this->remarks,
            'is_online' => $this->is_online,
            $this->mergeWhen($this->is_online, [
                'application_number' => $this->getMeta('application_number'),
            ]),
            'media' => MediaResource::collection($this->whenLoaded('media')),
            $this->mergeWhen($this->status === RegistrationStatus::REJECTED, [
                'rejection_remarks' => $this->rejection_remarks,
                'rejected_at' => $this->rejected_at,
            ]
            ),
            'is_editable' => $this->isEditable(),
            'is_deletable' => $this->isEditable(),
            'created_at' => \Cal::dateTime($this->created_at),
            'updated_at' => \Cal::dateTime($this->updated_at),
        ];
    }
}
