<?php

namespace App\Http\Resources\Finance;

use App\Enums\Finance\TransactionType;
use App\Http\Resources\Academic\PeriodResource;
use App\Http\Resources\UserSummaryResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class TransactionResource extends JsonResource
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
            'date' => $this->date,
            'amount' => $this->amount,
            $this->mergeWhen($this->transactionable_type, [
                'transactionable' => [
                    'name' => $this->transactionable?->contact?->name,
                    'contact' => $this->transactionable?->contact?->contact_number,
                ],
            ]),
            'type' => TransactionType::getDetail($this->type),
            $this->mergeWhen($this->head, [
                'head' => trans('finance.transaction.heads.'.$this->head),
            ]),
            'period' => PeriodResource::make($this->whenLoaded('period')),
            'payment' => TransactionPaymentResource::make($this->whenLoaded('payment')),
            'payments' => TransactionPaymentResource::collection($this->whenLoaded('payments')),
            $this->mergeWhen($this->is_online, [
                'is_online' => (bool) $this->is_online,
                'is_completed' => $this->processed_at->value ? true : false,
                'gateway' => Arr::get($this->payment_gateway, 'name'),
                'reference_number' => Arr::get($this->payment_gateway, 'reference_number'),
            ]),
            'records' => TransactionRecordResource::collection($this->whenLoaded('records')),
            'record' => TransactionRecordResource::make($this->whenLoaded('record')),
            'is_cancelled' => $this->cancelled_at->value ? true : false,
            'is_rejected' => $this->rejected_at->value ? true : false,
            'cancelled_at' => $this->cancelled_at,
            'rejected_at' => $this->rejected_at,
            'remarks' => $this->remarks,
            'cancellation_remarks' => $this->cancellation_remarks,
            $this->mergeWhen($this->rejected_at->value, [
                'rejected_date' => \Cal::date(Arr::get($this->rejection_record, 'rejected_date', $this->rejected_at->value)),
                'rejection_charge' => \Price::from(Arr::get($this->rejection_record, 'rejection_charge', 0)),
                'rejection_remarks' => $this->rejection_remarks,
            ]),
            'user' => UserSummaryResource::make($this->whenLoaded('user')),
            'is_editable' => $this->isEditable(),
            'created_at' => \Cal::dateTime($this->created_at),
            'updated_at' => \Cal::dateTime($this->updated_at),
        ];
    }
}
