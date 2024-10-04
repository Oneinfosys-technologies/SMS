<?php

namespace App\Http\Resources\Site;

use App\Enums\Site\MenuPlacement;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
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
            'placement' => MenuPlacement::getDetail($this->placement),
            'page' => PageResource::make($this->whenLoaded('page')),
            'parent' => self::make($this->whenLoaded('parent')),
            'created_at' => \Cal::dateTime($this->created_at),
            'updated_at' => \Cal::dateTime($this->updated_at),
        ];
    }
}
