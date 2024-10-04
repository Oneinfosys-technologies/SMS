<?php

namespace App\Http\Resources\Academic;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'term' => $this->term,
            'name_with_term' => $this->name_with_term,
            'code' => $this->code,
            'shortcode' => $this->shortcode,
            'division' => DivisionResource::make($this->whenLoaded('division')),
            'batches' => BatchResource::collection($this->whenLoaded('batches')),
            $this->mergeWhen($request->query('details'), [
                'incharge' => CourseInchargeResource::make($this->whenLoaded('incharge')),
                'incharges' => DivisionInchargeResource::collection($this->whenLoaded('incharges')),
            ]),
            'enable_registration' => $this->enable_registration,
            'registration_fee' => $this->registration_fee,
            'position' => $this->position,
            'pg_account' => $this->getMeta('pg_account'),
            'description' => $this->description,
            'batch_with_same_subject' => (bool) $this->getConfig('batch_with_same_subject'),
            'created_at' => \Cal::dateTime($this->created_at),
            'updated_at' => \Cal::dateTime($this->updated_at),
        ];
    }
}
