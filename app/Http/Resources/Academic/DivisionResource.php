<?php

namespace App\Http\Resources\Academic;

use Illuminate\Http\Resources\Json\JsonResource;

class DivisionResource extends JsonResource
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
            'code' => $this->code,
            'shortcode' => $this->shortcode,
            'period' => new PeriodResource($this->whenLoaded('period')),
            'courses' => CourseResource::collection($this->whenLoaded('courses')),
            $this->mergeWhen($request->query('details'), [
                'incharge' => DivisionInchargeResource::make($this->whenLoaded('incharge')),
                'incharges' => DivisionInchargeResource::collection($this->whenLoaded('incharges')),
            ]),
            'program' => ProgramResource::make($this->whenLoaded('program')),
            'position' => $this->position,
            'pg_account' => $this->getMeta('pg_account'),
            'description' => $this->description,
            'created_at' => \Cal::dateTime($this->created_at),
            'updated_at' => \Cal::dateTime($this->updated_at),
        ];
    }
}
