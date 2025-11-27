<?php

namespace App\Services;

use App\Interfaces\EventRepositoryInterface;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class EventService
{
    public function __construct(private EventRepositoryInterface $repo)
    {
    }

    public function attachParticipantsAndResolveUsers(Event $event, array $participants): array
    {
        $divisions = [];
        foreach ($participants as $p) {
            $divisions[] = $p['division_id'];
            $this->repo->createParticipant([
                'event_id' => $event->id,
                'division_id' => $p['division_id'],
                'is_required' => (bool) ($p['is_required'] ?? true),
                'note' => $p['note'] ?? null,
            ]);
        }

        $userIds = $this->repo->getUserIdsByDivisionIds($divisions);

        return [
            'user_ids' => $userIds,
        ];
    }

    public function create(array $data): array
    {
        DB::beginTransaction();
        try {
            $userId = auth()->id();
            $event = $this->repo->createEvent([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'location' => $data['location'] ?? null,
                'start_at' => $data['start_at'] ?? null,
                'end_at' => $data['end_at'] ?? null,
                'created_by' => $userId,
            ]);

            $participants = $data['participants'] ?? [];
            $this->repo->clearParticipants($event->id);
            $this->attachParticipantsAndResolveUsers($event, $participants);

            DB::commit();
            return ['status' => true, 'event' => $event];
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function update(string $uuid, array $data): array
    {
        DB::beginTransaction();
        try {
            $event = $this->repo->updateEventByUuid($uuid, [
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'location' => $data['location'] ?? null,
                'start_at' => $data['start_at'] ?? null,
                'end_at' => $data['end_at'] ?? null,
            ]);
            if (!$event) {
                return ['status' => false, 'message' => 'Event not found'];
            }

            $participants = $data['participants'] ?? [];
            $this->repo->clearParticipants($event->id);
            $this->attachParticipantsAndResolveUsers($event, $participants);

            DB::commit();
            return ['status' => true, 'event' => $event];
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function delete(string $uuid): bool
    {
        return $this->repo->deleteEventByUuid($uuid);
    }

    public function list(?string $search = null): Collection
    {
        return $this->repo->listEvents($search)->map(function (Event $event) {
            return [
                'id' => $event->id,
                'uuid' => $event->uuid,
                'name' => $event->name,
                'location' => $event->location,
                'description' => $event->description,
                'start_at' => optional($event->start_at)->toISOString(),
                'end_at' => optional($event->end_at)->toISOString(),
                'total_participants' => $event->participants()->count(),
                'created_at' => optional($event->created_at)->toISOString(),
                'updated_at' => optional($event->updated_at)->toISOString(),
            ];
        });
    }

    public function detail(string $uuid): ?array
    {
        $event = $this->repo->findByUuid($uuid);
        if (!$event) return null;

        $participants = $this->repo->getParticipants($event->id);
        $uploadedDivisions = $this->repo->getUploadedDivisionIds($event->id);

        return [
            'id' => $event->id,
            'uuid' => $event->uuid,
            'name' => $event->name,
            'location' => $event->location,
            'description' => $event->description,
            'start_at' => optional($event->start_at)->toISOString(),
            'end_at' => optional($event->end_at)->toISOString(),
            'total_participants' => $participants->count(),
            'participants' => $participants->map(function ($p) use ($uploadedDivisions) {
                $status = $uploadedDivisions->contains($p->division_id) ? 'Sudah upload' : 'Belum upload';
                return [
                    'id' => $p->id,
                    'division_id' => $p->division_id,
                    'is_required' => (bool) $p->is_required,
                    'note' => $p->note,
                    'status' => $status,
                    'created_at' => optional($p->created_at)->toISOString(),
                    'updated_at' => optional($p->updated_at)->toISOString(),
                ];
            })->values()->all(),
            'created_at' => optional($event->created_at)->toISOString(),
            'updated_at' => optional($event->updated_at)->toISOString(),
        ];
    }

    public function usersByEvent(string $uuid): Collection
    {
        $event = $this->repo->findByUuid($uuid);
        if (!$event) return collect();
        $divisionIds = $event->participants->pluck('division_id')->filter()->unique()->values()->all();
        return $this->repo->getUserIdsByDivisionIds($divisionIds);
    }
}
