<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'roles' => optional($this->getRoleNames())->first(),
            'username' => $this->username,
            'institution' => $this->institution?->name,
            'division' => $this->division?->name,
            'url_image' => $this->photo_url,
        ];
    }
}