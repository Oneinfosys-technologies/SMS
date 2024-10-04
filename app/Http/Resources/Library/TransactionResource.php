<?php

namespace App\Http\Resources\Library;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

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
            'records_count' => $this->records_count,
            'non_returned_books_count' => $this->non_returned_books_count,
            'to' => [
                'label' => $this->transactionable_type,
                'value' => Str::lower($this->transactionable_type),
            ],
            'requester' => [
                'uuid' => $this->transactionable?->uuid,
                'name' => $this->transactionable?->contact->name,
                'contact_number' => $this->transactionable?->contact->contact_number,
            ],
            'issue_date' => $this->issue_date,
            'due_date' => $this->due_date,
            'records' => TransactionRecordResource::collection($this->whenLoaded('records')),
            $this->mergeWhen($this->whenLoaded('records'), [
                'is_returned' => $this->records->filter(function ($record) {
                    return empty($record->return_date->value);
                })
                    ->count() ? false : true,
            ]),
            'remarks' => $this->remarks,
            'created_at' => \Cal::dateTime($this->created_at),
            'updated_at' => \Cal::dateTime($this->updated_at),
        ];
    }
}
