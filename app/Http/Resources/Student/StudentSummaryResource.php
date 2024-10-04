<?php

namespace App\Http\Resources\Student;

use App\Enums\Gender;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentSummaryResource extends JsonResource
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
            'roll_number' => $this->roll_number,
            'contact_number' => $this->contact_number,
            'photo' => $this->photo_url,
            'photo_url' => url($this->photo_url),
            'father_name' => $this->father_name,
            'mother_name' => $this->mother_name,
            'email' => $this->email,
            'birth_date' => \Cal::date($this->birth_date),
            'gender' => Gender::getDetail($this->gender),
            'code_number' => $this->code_number,
            'batch_uuid' => $this->batch_uuid,
            'batch_name' => $this->batch_name,
            'course_uuid' => $this->course_uuid,
            'course_name' => $this->course_name,
            'course_term' => $this->course_term,
            'joining_date' => \Cal::date($this->joining_date),
            'leaving_date' => \Cal::date($this->leaving_date),
            'is_transferred' => $this->leaving_date ? true : false,
            $this->mergeWhen($this->has_incharge, [
                'incharges' => $this->incharges,
            ]),
            'start_date' => \Cal::date($this->start_date),
            'end_date' => \Cal::date($this->end_date),
            'is_alumni' => (bool) $this->getMeta('is_alumni'),
            $this->mergeWhen($this->getMeta('is_alumni'), [
                'alumni_date' => \Cal::date($this->getMeta('alumni_date')),
            ]),
            'created_at' => \Cal::dateTime($this->created_at),
            'updated_at' => \Cal::dateTime($this->updated_at),
        ];
    }
}
