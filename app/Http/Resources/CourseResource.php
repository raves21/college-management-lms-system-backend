<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'name' => $this->name,
            'code' => $this->code,
            'departments' => $this->whenLoaded('departments', function () {
                return $this->departments->map(fn($department) => [
                    'id' => $department->id,
                    'name' => $department->name,
                    'code' => $department->code
                ]);
            })->toArray()
        ];
    }
}
