<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray($request): array
    {
        $withParticipants = (bool) ($this->resource['participants'] ?? false);

        $base = [
            'uuid' => $this->resource['uuid'] ?? null,
            'name' => $this->resource['name'] ?? null,
            'location' => $this->resource['location'] ?? null,
            'description' => $this->resource['description'] ?? null,
            'start_at' => $this->resource['start_at'] ?? null,
            'end_at' => $this->resource['end_at'] ?? null,
            'total_participants' => $this->resource['total_participants'] ?? null,
            'created_at' => $this->resource['created_at'] ?? null,
            'updated_at' => $this->resource['updated_at'] ?? null,
        ];

        if ($withParticipants) {
            $base['participants'] = $this->resource['participants'];
        }

        return $base;
    }
}

