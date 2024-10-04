<?php

namespace App\Http\Resources\Academic;

use Illuminate\Http\Resources\Json\JsonResource;

class PeriodResource extends JsonResource
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
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'session_name' => $this->session_name,
            'session' => SessionResource::make($this->whenLoaded('session')),
            'enable_registration' => (bool) $this->getConfig('enable_registration'),
            'code' => $this->code,
            'shortcode' => $this->shortcode,
            'alias' => $this->alias,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_default' => $this->is_default,
            'description' => $this->description,
            'created_at' => \Cal::dateTime($this->created_at),
            'updated_at' => \Cal::dateTime($this->updated_at),
        ];
    }
}
