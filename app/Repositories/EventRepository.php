<?php

namespace App\Repositories;

use App\Interfaces\EventRepositoryInterface;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\EventUnitProof;
use App\Models\EventUnitProofFile;
use App\Models\User;
use Illuminate\Support\Collection;

class EventRepository implements EventRepositoryInterface
{
    public function createParticipant(array $data)
    {
        return EventParticipant::create($data);
    }

    public function getUserIdsByDivisionIds(array $divisionIds): Collection
    {
        return User::whereIn('division_id', $divisionIds)->pluck('id')->values();
    }

    public function createEvent(array $data)
    {
        return Event::create($data);
    }

    public function updateEventByUuid(string $uuid, array $data)
    {
        $event = Event::where('uuid', $uuid)->first();
        if (!$event) return null;
        $event->update($data);
        return $event;
    }

    public function deleteEventByUuid(string $uuid): bool
    {
        $event = Event::where('uuid', $uuid)->first();
        if (!$event) return false;
        return (bool) $event->delete();
    }

    public function findByUuid(string $uuid)
    {
        return Event::where('uuid', $uuid)
            ->with(['participants'])
            ->first();
    }

    public function listEvents(?string $search = null): Collection
    {
        $q = Event::query();
        if ($search) {
            $q->where(function ($qq) use ($search) {
                $qq->where('name', 'like', "%$search%")
                   ->orWhere('description', 'like', "%$search%")
                   ->orWhere('location', 'like', "%$search%");
            });
        }
        return $q->orderByDesc('id')->get();
    }

    public function clearParticipants(int $eventId): void
    {
        EventParticipant::where('event_id', $eventId)->delete();
    }

    public function getParticipants(int $eventId): Collection
    {
        return EventParticipant::where('event_id', $eventId)->get();
    }

    public function getUploadedDivisionIds(int $eventId): Collection
    {
        $headers = EventUnitProof::where('event_id', $eventId)->pluck('id', 'division_id');
        if ($headers->isEmpty()) {
            return collect();
        }
        $proofs = EventUnitProofFile::whereIn('event_unit_proof_id', $headers->values())
            ->pluck('event_unit_proof_id');
        $map = EventUnitProof::whereIn('id', $proofs)->pluck('division_id');
        return $map->unique()->values();
    }
}
