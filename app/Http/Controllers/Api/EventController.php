<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\EventStoreRequest;
use App\Http\Requests\Api\EventUpdateRequest;
use App\Http\Resources\EventResource;
use App\Services\EventService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct(private EventService $service)
    {
    }

    private function ensureAdmin(Request $request)
    {
        $adminRoleNames = ['Admin', 'admin', 'super admin', 'super-admin'];
        $user = $request->user();
        $isAdmin = $user && $user->roles()->whereIn('name', $adminRoleNames)->where('guard_name', 'web')->exists();
        if (! $isAdmin) {
            return response()->json(format_error('Forbidden'), 403);
        }
        return null;
    }

    public function store(EventStoreRequest $request)
    {
        if ($resp = $this->ensureAdmin($request)) {
            return $resp;
        }
        $result = $this->service->create($request->validated());
        if (!$result['status']) {
            return response()->json(format_error('Failed to create event', ['message' => $result['message'] ?? ''] ), 400);
        }
        return response()->json(format_success('Event created successfully'));
    }

    public function index(Request $request)
    {
        $items = $this->service->list((string) $request->query('search'))
            ->map(function ($arr) {
                return (new EventResource($arr))->toArray(request());
            })->values()->all();
        return response()->json(format_success('Event retrieved successfully', $items));
    }

    public function show(string $event_uuid)
    {
        $detail = $this->service->detail($event_uuid);
        if (!$detail) {
            return response()->json(format_error('Resource not found'), 404);
        }
        // set flag participants agar EventResource mengikutkan participants
        $detail['participants'] = $detail['participants'] ?? [];
        return response()->json(format_success('Event retrieved successfully', (new EventResource($detail))->toArray(request())));
    }

    public function update(EventUpdateRequest $request, string $event_uuid)
    {
        if ($resp = $this->ensureAdmin($request)) {
            return $resp;
        }
        $result = $this->service->update($event_uuid, $request->validated());
        if (!$result['status']) {
            return response()->json(format_error($result['message'] ?? 'Failed to update event'), 404);
        }
        return response()->json(format_success('Event updated successfully'));
    }

    public function destroy(string $event_uuid)
    {
        if ($resp = $this->ensureAdmin(request())) {
            return $resp;
        }
        $ok = $this->service->delete($event_uuid);
        if (!$ok) {
            return response()->json(format_error('Resource not found'), 404);
        }
        return response()->json(format_success('Event deleted successfully'));
    }

    public function users(string $event_uuid)
    {
        $userIds = $this->service->usersByEvent($event_uuid);
        if ($userIds->isEmpty()) {
            return response()->json(format_error('Resource not found'), 404);
        }
        return response()->json(format_success('Users retrieved successfully', $userIds->toArray()));
    }
}
