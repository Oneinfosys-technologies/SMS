<?php

namespace App\Http\Resources\Academic;

use App\Enums\Academic\ProgramType;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramResource extends JsonResource
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
            'name' => $this->name,
            'type' => ProgramType::getDetail($this->type),
            'code' => $this->code,
            'shortcode' => $this->shortcode,
            'alias' => $this->alias,
            'enable_registration' => (bool) $this->getConfig('enable_registration'),
            'periods' => PeriodResource::collection($this->whenLoaded('periods')),
            'description' => $this->description,
            'created_at' => \Cal::dateTime($this->created_at),
            'updated_at' => \Cal::dateTime($this->updated_at),
        ];
    }
}
