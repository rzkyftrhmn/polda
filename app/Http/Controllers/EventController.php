<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\EventUnitProof;
use App\Models\EventUnitProofFile;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Storage;
use App\Services\EventService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    protected $notifService;
    protected $eventService;
    public function __construct(NotificationService $notifService, EventService $eventService)
    {
        $this->middleware('auth');
        $this->notifService = $notifService;
        $this->eventService = $eventService;
    }

    public function index()
    {
        return view('pages.events.index');
    }

    public function datatables(Request $request)
    {
        $query = Event::withCount('participants');

        $user = Auth::user();
        $divisionId = $user?->division_id;
        $isAdmin = $user && method_exists($user, 'hasAnyRole')
            ? $user->hasAnyRole([ROLE_ADMIN])
            : false;

        if (!$isAdmin && $divisionId) {
            $query = $query->whereHas('participants', function ($q) use ($divisionId) {
                $q->where('division_id', $divisionId);
            });
        }

        $search = $request->input('search.value', '');
        if (!empty($search)) {
            $query = $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('location', 'like', "%$search%");
            });
        }

        $total = $query->count();
        $limit = (int) $request->input('length', 10);
        $start = (int) $request->input('start', 0);
        $orderColIndex = (int) $request->input('order.0.column', 1);
        $dir = $request->input('order.0.dir', 'asc');
        $orderColumns = ['id', 'name', 'location', 'start_at', 'end_at', 'participants_count'];
        $order = $orderColumns[$orderColIndex] ?? 'created_at';

        $filter = $request->input('filter_q', '');
        if (!empty($filter)) {
            $query = $query->where('name', 'like', "%$filter%");
        }

        $events = $query->orderBy($order, $dir)
            ->skip($start)
            ->take($limit)
            ->get();

        $data = [];
        foreach ($events as $idx => $event) {
            $action = '<a href="' . route('events.show', $event) . '" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></a>';
            if ($isAdmin) {
                $action .= ' <a href="' . route('events.edit', $event) . '" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></a>';
                $action .= ' <form method="POST" action="' . route('events.destroy', $event) . '" class="delete-event-form" style="display:inline-block;margin-left:4px">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button></form>';
            }
            $data[] = [
                'DT_RowIndex' => $idx + 1 + $start,
                'name' => $event->name,
                'location' => $event->location,
                'start_at' => optional($event->start_at)->format('d-m-Y H:i') ?? '-',
                'end_at' => optional($event->end_at)->format('d-m-Y H:i') ?? '-',
                'participants' => $event->participants_count,
                'action' => $action,
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data,
        ]);
    }

    public function create()
    {
        return view('pages.events.create', [
            'divisions' => Division::orderBy('name')->get(['id','name']),
            'event' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date',
            'participants' => 'nullable|array',
            'participants.*.division_id' => 'required_with:participants|integer|exists:divisions,id',
            'participants.*.is_required' => 'nullable|boolean',
            'participants.*.note' => 'nullable|string',
        ]);

        $event = Event::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'location' => $validated['location'] ?? null,
            'start_at' => $validated['start_at'] ?? null,
            'end_at' => $validated['end_at'] ?? null,
            'created_by' => Auth::id(),
        ]);

        $result = $this->eventService->attachParticipantsAndResolveUsers($event, $validated['participants'] ?? []);
        $userParticipants = $result['user_ids'];
        $this->notifService->notifyEvent($event, $userParticipants, NOTIF_EVENT_PARTICIPANT);

        return redirect()->route('events.show', $event)->with('success', 'Event berhasil dibuat');
    }

    public function show(Event $event)
    {
        $event->load(['participants.division', 'uniProofs.uploader', 'uniProofs.division']);

        $participantDivisionIds = $event->participants->pluck('division_id')->filter()->unique()->values();
        $unitProofDivisions = EventUnitProof::where('event_id', $event->id)->pluck('division_id')->filter();
        $uploadedDivisionIds = $event->uniProofs->pluck('division_id')->filter()->merge($unitProofDivisions)->unique()->values();
        $totalParticipants = $participantDivisionIds->count();
        $uploadedCount = $uploadedDivisionIds->intersect($participantDivisionIds)->count();
        $percentageUploaded = $totalParticipants > 0 ? round(($uploadedCount / $totalParticipants) * 100) : 0;

        $unitProofs = EventUnitProof::where('event_id', $event->id)
            ->with(['uploader','division'])
            ->get();
        $fileMap = EventUnitProofFile::whereIn('event_unit_proof_id', $unitProofs->pluck('id'))
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy('event_unit_proof_id');
        $proofGroups = $unitProofs
            ->groupBy('user_id')
            ->map(function ($items) use ($fileMap) {
                $first = $items->first();
                $files = [];
                foreach ($items as $pf) {
                    foreach (($fileMap[$pf->id] ?? collect()) as $f) {
                        $files[] = [
                            'path' => $f->file_path,
                            'type' => $f->file_type,
                            'created_at' => optional($f->created_at)->format('d M Y H:i'),
                        ];
                    }
                }
                return [
                    'user_name' => optional($first->uploader)->name,
                    'division_name' => optional($first->division)->name,
                    'description' => $first->description,
                    'files' => collect($files)->sortByDesc('created_at')->values()->all(),
                ];
            })->values();

        $user = Auth::user();
        $divisionId = $user?->division_id;
        $isAdmin = $user && method_exists($user, 'hasAnyRole')
            ? $user->hasAnyRole([ROLE_ADMIN])
            : false;
        $isParticipant = $divisionId && $participantDivisionIds->contains($divisionId);

        return view('pages.events.show', [
            'event' => $event,
            'percentageUploaded' => $percentageUploaded,
            'uploadedCount' => $uploadedCount,
            'totalParticipants' => $totalParticipants,
            'uploadedDivisionIds' => $uploadedDivisionIds->toArray(),
            'proofGroups' => $proofGroups,
            'isAdmin' => $isAdmin,
            'isParticipant' => $isParticipant,
        ]);
    }

    public function edit(Event $event)
    {
        $user = Auth::user();
        $isAdmin = $user && method_exists($user, 'hasAnyRole')
            ? $user->hasAnyRole([ROLE_ADMIN])
            : false;
        if (!$isAdmin) {
            return redirect()->route('events.show', $event)->with('error', 'Hanya admin yang dapat mengedit event.');
        }
        $event->load('participants');
        return view('pages.events.create', [
            'divisions' => Division::orderBy('name')->get(['id','name']),
            'event' => $event,
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $user = Auth::user();
        $isAdmin = $user && method_exists($user, 'hasAnyRole')
            ? $user->hasAnyRole([ROLE_ADMIN])
            : false;
        if (!$isAdmin) {
            return redirect()->route('events.show', $event)->with('error', 'Hanya admin yang dapat mengedit event.');
        }
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date',
            'participants' => 'nullable|array',
            'participants.*.division_id' => 'required_with:participants|integer|exists:divisions,id',
            'participants.*.is_required' => 'nullable|boolean',
            'participants.*.note' => 'nullable|string',
        ]);

        $event->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'location' => $validated['location'] ?? null,
            'start_at' => $validated['start_at'] ?? null,
            'end_at' => $validated['end_at'] ?? null,
        ]);

        $event->participants()->delete();
        foreach ($validated['participants'] ?? [] as $p) {
            EventParticipant::create([
                'event_id' => $event->id,
                'division_id' => $p['division_id'],
                'is_required' => (bool) ($p['is_required'] ?? true),
                'note' => $p['note'] ?? null,
            ]);
        }

        return redirect()->route('events.show', $event)->with('success', 'Event berhasil diperbarui');
    }

    public function destroy(Event $event)
    {
        $user = Auth::user();
        $isAdmin = $user && method_exists($user, 'hasAnyRole')
            ? $user->hasAnyRole([ROLE_ADMIN])
            : false;
        if (!$isAdmin) {
            return redirect()->route('events.show', $event)->with('error', 'Hanya admin yang dapat menghapus event.');
        }
        $event->delete();
        return redirect()->route('events.index')->with('success', 'Event dihapus');
    }

    public function storeProof(Request $request, Event $event)
    {
        $user = Auth::user();
        $divisionId = $user?->division_id;

        $isAdmin = $user && method_exists($user, 'hasAnyRole')
            ? $user->hasAnyRole(['super admin', 'super-admin', 'admin', ROLE_ADMIN])
            : false;

        $participantDivisionIds = $event->participants()->pluck('division_id')->filter()->unique();
        $isParticipant = $divisionId && $participantDivisionIds->contains($divisionId);

        if (!$isAdmin && !$isParticipant) {
            return back()->with('error', 'Anda bukan peserta event ini.');
        }

        $validated = $request->validate([
            'proof_files' => ['required','array'],
            'proof_files.*' => ['required','file','max:2096','mimes:jpg,jpeg,png,pdf,doc,docx'],
            'report_file' => ['nullable','file','max:2096','mimes:jpg,jpeg,png,pdf,doc,docx'],
            'description' => ['nullable','string'],
        ], [
            'proof_files.*.max' => 'Maksimal ukuran file 2 MB.',
            'report_file.max' => 'Maksimal ukuran file 2 MB.',
        ]);


        $existingProofs = EventUnitProof::where('event_id', $event->id)
            ->where('user_id', $user?->id)
            ->get();
        foreach ($existingProofs as $pf) {
            $files = EventUnitProofFile::where('event_unit_proof_id', $pf->id)->get();
            foreach ($files as $f) {
                $rel = ltrim(str_replace('/storage/', '', (string) $f->file_path), '/');
                if ($rel) {
                    try { Storage::disk('public')->delete($rel); } catch (\Throwable $e) {}
                }
                $f->delete();
            }
            $pf->delete();
        }

        $header = EventUnitProof::create([
            'event_id' => $event->id,
            'user_id' => $user?->id,
            'division_id' => $divisionId,
            'description' => $validated['description'] ?? null,
        ]);

        $files = $request->file('proof_files', []);
        foreach ($files as $file) {
            if (!$file) continue;
            $path = $file->store('events/proofs', 'public');
            EventUnitProofFile::create([
                'event_unit_proof_id' => $header->id,
                'file_path' => '/storage/' . $path,
                'file_type' => 'file-kegiatan',
            ]);
        }

        $reportFile = $request->file('report_file');
        if ($reportFile) {
            $path = $reportFile->store('events/proofs', 'public');
            EventUnitProofFile::create([
                'event_unit_proof_id' => $header->id,
                'file_path' => '/storage/' . $path,
                'file_type' => 'file-laporan',
            ]);
        }

        return redirect()->route('events.show', $event)->with('success', 'Bukti kegiatan berhasil diunggah');
    }
}
