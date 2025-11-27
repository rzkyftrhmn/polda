<?php

namespace App\Interfaces;

use Illuminate\Support\Collection;

interface EventRepositoryInterface
{
    public function createParticipant(array $data);
    public function getUserIdsByDivisionIds(array $divisionIds): Collection;
    public function createEvent(array $data);
    public function updateEventByUuid(string $uuid, array $data);
    public function deleteEventByUuid(string $uuid): bool;
    public function findByUuid(string $uuid);
    public function listEvents(?string $search = null): Collection;
    public function clearParticipants(int $eventId): void;
    public function getParticipants(int $eventId): Collection;
    public function getUploadedDivisionIds(int $eventId): Collection;
}
