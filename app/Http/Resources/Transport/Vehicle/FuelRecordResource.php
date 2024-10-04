<?php

namespace App\Http\Resources\Transport\Vehicle;

use App\Http\Resources\MediaResource;
use Illuminate\Http\Resources\Json\JsonResource;

class FuelRecordResource extends JsonResource
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
            'vehicle' => VehicleResource::make($this->whenLoaded('vehicle')),
            'quantity' => round($this->quantity, 2),
            'price_per_unit' => $this->price_per_unit,
            'cost' => \Price::from($this->quantity * $this->price_per_unit->value),
            'log' => $this->log,
            'date' => $this->date,
            'remarks' => $this->remarks,
            'media_token' => $this->getMeta('media_token'),
            'media' => MediaResource::collection($this->whenLoaded('media')),
            'created_at' => \Cal::dateTime($this->created_at),
            'updated_at' => \Cal::dateTime($this->updated_at),
        ];
    }
}
