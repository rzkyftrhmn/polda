<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\EventUnitProof;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('pages.events.index');
    }

    public function datatables(Request $request)
    {
        $query = Event::withCount('participants');

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
            $data[] = [
                'DT_RowIndex' => $idx + 1 + $start,
                'name' => $event->name,
                'location' => $event->location,
                'start_at' => optional($event->start_at)->format('d-m-Y H:i') ?? '-',
                'end_at' => optional($event->end_at)->format('d-m-Y H:i') ?? '-',
                'participants' => $event->participants_count,
                'action' => '<a href="' . route('events.show', $event) . '" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></a>',
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

        foreach ($validated['participants'] ?? [] as $p) {
            EventParticipant::create([
                'event_id' => $event->id,
                'division_id' => $p['division_id'],
                'is_required' => (bool) ($p['is_required'] ?? true),
                'note' => $p['note'] ?? null,
            ]);
        }

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
        $combinedProofs = collect($event->uniProofs)->concat($unitProofs);
        $proofGroups = $combinedProofs
            ->groupBy('user_id')
            ->map(function ($items) {
                $first = $items->first();
                return [
                    'user_name' => optional($first->uploader)->name,
                    'division_name' => optional($first->division)->name,
                    'files' => $items->sortByDesc('created_at')->values()->map(function ($pf) {
                        return [
                            'path' => $pf->file_path,
                            'type' => $pf->file_type,
                            'created_at' => optional($pf->created_at)->format('d M Y H:i'),
                            'description' => $pf->description,
                        ];
                    })->all(),
                ];
            })->values();

        return view('pages.events.show', [
            'event' => $event,
            'percentageUploaded' => $percentageUploaded,
            'uploadedCount' => $uploadedCount,
            'totalParticipants' => $totalParticipants,
            'uploadedDivisionIds' => $uploadedDivisionIds->toArray(),
            'proofGroups' => $proofGroups,
        ]);
    }

    public function edit(Event $event)
    {
        $event->load('participants');
        return view('pages.events.create', [
            'divisions' => Division::orderBy('name')->get(['id','name']),
            'event' => $event,
        ]);
    }

    public function update(Request $request, Event $event)
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
            'proof_files.*' => ['required','file','max:4096','mimes:jpg,jpeg,png,pdf,doc,docx'],
            'report_file' => ['nullable','file','max:4096','mimes:jpg,jpeg,png,pdf,doc,docx'],
            'description' => ['nullable','string'],
        ], [
            'proof_files.*.max' => 'Maksimal ukuran file 4 MB.',
        ]);

        $files = $request->file('proof_files', []);
        foreach ($files as $file) {
            if (!$file) continue;
            $path = $file->store('events/proofs', 'public');
            $mime = $file->getClientMimeType();
            $type = str_contains($mime, 'image') ? 'image' : (str_contains($mime, 'pdf') ? 'pdf' : 'doc');

            \App\Models\EventUnitProof::create([
                'event_id' => $event->id,
                'user_id' => $user?->id,
                'division_id' => $divisionId,
                'file_path' => '/storage/' . $path,
                'file_type' => $type,
                'description' => $validated['description'] ?? null,
            ]);
        }

        $reportFile = $request->file('report_file');
        if ($reportFile) {
            $path = $reportFile->store('events/proofs', 'public');
            $mime = $reportFile->getClientMimeType();
            $type = str_contains($mime, 'image') ? 'image' : (str_contains($mime, 'pdf') ? 'pdf' : 'doc');

            \App\Models\EventUnitProof::create([
                'event_id' => $event->id,
                'user_id' => $user?->id,
                'division_id' => $divisionId,
                'file_path' => '/storage/' . $path,
                'file_type' => $type,
                'description' => $validated['description'] ?? null,
            ]);
        }

        return redirect()->route('events.show', $event)->with('success', 'Bukti kegiatan berhasil diunggah');
    }
}
