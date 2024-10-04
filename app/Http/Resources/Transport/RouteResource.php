<?php

namespace App\Http\Resources\Transport;

use App\Enums\Transport\Direction;
use App\Http\Resources\Academic\PeriodResource;
use App\Http\Resources\Transport\Vehicle\VehicleResource;
use App\Models\Academic\Batch;
use Illuminate\Http\Resources\Json\JsonResource;

class RouteResource extends JsonResource
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
            'max_capacity' => $this->max_capacity,
            'vehicle' => VehicleResource::make($this->whenLoaded('vehicle')),
            'period' => PeriodResource::make($this->whenLoaded('period')),
            'route_stoppages_count' => $this->route_stoppages_count,
            'route_passengers_count' => $this->route_passengers_count,
            'stoppages' => RouteStoppageResource::collection($this->whenLoaded('routeStoppages')),
            $this->mergeWhen($this->whenLoaded('routeStoppages'), [
                'arrival_stoppages' => $this->getArrivalStoppageTimings(),
                'departure_stoppages' => $this->getDepartureStoppageTimings(),
            ]),
            'direction' => Direction::getDetail($this->direction),
            'arrival_starts_at' => $this->arrival_starts_at,
            'departure_starts_at' => $this->departure_starts_at,
            $this->mergeWhen($request->show_passengers === true, [
                'passengers' => $this->getPassengers(),
            ]),
            'duration_to_destination' => $this->duration_to_destination,
            'description' => $this->description,
            'created_at' => \Cal::dateTime($this->created_at),
            'updated_at' => \Cal::dateTime($this->updated_at),
        ];
    }

    private function getPassengers()
    {
        $batches = Batch::query()
            ->byPeriod()
            ->with('course')
            ->get();

        return $this->routePassengers->map(function ($routePassenger) use ($batches) {
            $detail = '';
            $contactNumber = '';

            if ($routePassenger->model_type == 'Student') {
                $type = ['label' => trans('student.student'), 'value' => 'student'];
                $batch = $batches->firstWhere('id', $routePassenger->model->batch_id);
                $detail = $batch->course->name.' '.$batch->name;
            } elseif ($routePassenger->model_type == 'Employee') {
                $type = ['label' => trans('employee.employee'), 'value' => 'employee'];
                $detail = $routePassenger->getMeta('title');

                if ($routePassenger->getMeta('publish_contact_number')) {
                    $contactNumber = $routePassenger->model->contact->contact_number;
                }
            }

            return [
                'uuid' => $routePassenger->uuid,
                'type' => $type,
                'stoppage' => $routePassenger->stoppage?->name,
                'name' => $routePassenger->model?->contact->name,
                'detail' => $detail,
                'contact_number' => $contactNumber,
            ];
        });
    }
}
