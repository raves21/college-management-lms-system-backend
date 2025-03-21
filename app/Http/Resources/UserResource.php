<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'userType' => new UserTypeResource($this->whenLoaded('user_type')),
            'email' => $this->email,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name
        ];
    }
}
