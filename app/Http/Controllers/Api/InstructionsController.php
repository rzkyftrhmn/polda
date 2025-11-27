<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\InstructionStoreRequest;
use App\Http\Resources\InstructionResource;
use App\Models\Report;
use App\Services\PelaporanService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class InstructionsController extends Controller
{
    public function __construct(private PelaporanService $service, private NotificationService $notificationService)
    {
    }

    public function index(Request $request)
    {
        $request->validate([
            'report_uuid' => ['required', 'exists:reports,uuid'],
        ]);

        $report = $this->service->getReportByUuid((string) $request->query('report_uuid'));
        if (! $report) {
            return response()->json(format_error('Resource not found'), 404);
        }
        $items = $this->service->getInstructionsForReport($report->id)->load(['report', 'fromUser.division']);

        return response()->json(
            format_success(
                'Petunjuk dan arahan retrieved successfully',
                InstructionResource::collection($items)->toArray($request)
            )
        );
    }

    public function listByReport(Request $request, string $report_uuid)
    {
        $report = $this->service->getReportByUuid($report_uuid);
        if (! $report) {
            return response()->json(format_error('Resource not found'), 404);
        }
        $items = $this->service->getInstructionsForReport($report->id)->load(['report', 'fromUser.division']);

        return response()->json(
            format_success(
                'Petunjuk dan arahan retrieved successfully',
                InstructionResource::collection($items)->toArray($request)
            )
        );
    }

    public function users(Request $request, string $report_uuid)
    {
        $report = $this->service->getReportByUuid($report_uuid);
        if (! $report) {
            return response()->json(format_error('Resource not found'), 404);
        }

        $users = $this->service->getRelatedUsersForReport($report);
        $data = $users->map(function ($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'division' => [ 'name' => optional($u->division)->name ],
            ];
        })->values()->all();

        return response()->json(format_success('Users retrieved successfully', $data));
    }

    public function store(InstructionStoreRequest $request)
    {
        $data = $request->validated();
        $report = $this->service->getReportByUuid((string) $data['report_uuid']);
        if (! $report) {
            return response()->json(format_error('Resource not found'), 404);
        }
        $fromId = $request->user()->id;
        
        // validasi akses penerima berdasarkan akses terhadap laporan atau admin
        if (! $this->service->canSendInstructionToUser($report, (int) $fromId)) {
            return response()->json(format_error('Anda tidak berwenang mengirim instruksi ke pengguna ini'), 403);
        }

        $toId = (int) $data['user_id'];
        $message = (string) $data['message'];

        if (! $this->service->canSendInstructionToUser($report, $toId)) {
            return response()->json(format_error('Anda tidak berwenang mengirim instruksi ke pengguna ini'), 403);
        }

        $this->service->storeInstruction($report->id, $fromId, $toId, $message);
        $this->notificationService->send(
            $toId,
            'Petunjuk dan Arahan',
            $message,
            $report->id,
            'PETUNJUK_DAN_ARAHAN'
        );

        return response()->json(
            format_success(
                'Petunjuk dan arahan retrieved successfully'
            )
        );
    }
}
