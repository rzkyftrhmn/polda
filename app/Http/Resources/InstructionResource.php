<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InstructionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'report_uuid' => $this->report?->uuid,
            'user' => [
                'name' => $this->fromUser?->name,
                'email' => $this->fromUser?->email,
                'division' => [
                    'name' => $this->fromUser?->division?->name,
                ],
            ],
            'message' => $this->message,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}

