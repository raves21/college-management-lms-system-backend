<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'department' => $this->whenLoaded('department', function () {
                return [
                    'name' => $this->department->name,
                    'code' => $this->department->code
                ];
            }),
            'userProfile' => new UserResource($this->whenLoaded('user'))
        ];
    }
}
