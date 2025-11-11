<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JourneyService;

class JourneyController extends Controller
{
    protected $service;

    public function __construct(JourneyService $service)
    {
        $this->service = $service;
    }

    public function store(Request $request, $reportId)
    {
        $request->validate([
            'type' => 'required|in:PEMERIKSAAN,LIMPAH,SIDANG,SELESAI',
            'description' => 'required|string',
            'files.*' => 'nullable|file|max:4096',
        ]);

        $payload = [
            'report_id' => $reportId,
            'institution_id' => auth()->user()->institution_id,
            'division_id' => auth()->user()->division_id,
            'type' => $request->type,
            'description' => $request->description,
        ];

        $files = $request->file('files') ?? [];

        $result = $this->service->store($payload, $files);

        if ($result['status']) {
            return back()->with('success', $result['message']);
        }
        return back()->with('error', $result['message'])->withInput();
    }

}
