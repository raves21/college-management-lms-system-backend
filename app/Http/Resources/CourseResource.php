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
        $departments = DepartmentResource::collection($this->whenLoaded('departments'));
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'departments' => $departments->collection?->map(function ($department) {
                return [
                    'id' => $department->id,
                    'name' => $department->name,
                    'code' => $department->code
                ];
            })
        ];
    }
}
