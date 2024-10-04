<?php

namespace App\Http\Resources\Student;

use App\Http\Resources\MediaResource;
use App\Http\Resources\OptionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class QualificationResource extends JsonResource
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
            'course' => $this->course,
            'institute' => $this->institute,
            'affiliated_to' => $this->affiliated_to,
            'level' => OptionResource::make($this->whenLoaded('level')),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'result' => $this->result,
            'media_token' => $this->getMeta('media_token'),
            'media' => MediaResource::collection($this->whenLoaded('media')),
            'created_at' => \Cal::dateTime($this->created_at),
            'updated_at' => \Cal::dateTime($this->updated_at),
        ];
    }
}
