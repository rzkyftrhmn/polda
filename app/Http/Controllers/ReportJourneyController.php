<?php

namespace App\Http\Controllers;

use App\Enums\ReportJourneyType;
use App\Models\Division;
use App\Repositories\ReportJourneyRepository;
use App\Services\ReportJourneyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ReportJourneyController extends Controller
{
    protected $service;
    protected $repository;
    protected $feature_title;
    protected $feature_name;
    protected $feature_path;
    protected $user;

    public function __construct(ReportJourneyRepository $repository, ReportJourneyService $service)
    {
        $this->repository    = $repository;
        $this->service       = $service;
        $this->feature_title = 'Journey';
        $this->feature_name  = 'Journey';
        $this->feature_path  = 'journey';

        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function store(Request $request, int $reportId): RedirectResponse
    {
        $limpahValues = [
            ReportJourneyType::TRANSFER->value,
            ReportJourneyType::TRANSFER->name,
            ReportJourneyType::TRANSFER->label(),
            'TRANSFER',
        ];

        try {
            $validated = $request->validate([
                'type' => [
                    'required',
                    Rule::in(array_map(
                        static fn (ReportJourneyType $type) => $type->value,
                        ReportJourneyType::manualOptions()
                    )),
                ],
                'description' => ['required', 'string'],
                'files'       => ['nullable', 'array'],
                'files.*'     => ['nullable', 'file', 'max:4096', 'mimes:jpg,jpeg,png,pdf,doc,docx'],

                'institution_target_id' => [
                    'nullable',
                    'integer',
                    'exists:institutions,id',
                    Rule::requiredIf(fn () => in_array($request->input('type'), $limpahValues, true)),
                ],

                'subdivision_target_id' => [
                    'nullable',
                    'integer',
                    // hanya cek bahwa dia anak (punya parent_id), ga usah urusan sama institution
                    Rule::exists('divisions', 'id')->where(static function ($query) {
                        $query->whereNotNull('parent_id');
                    }),
                    Rule::requiredIf(fn () => in_array($request->input('type'), $limpahValues, true)),
                ],
            ]);

            // Ambil journey terakhir untuk report ini
            $lastJourney = $this->repository
                ->paginateByReport($reportId, 1, 'desc')
                ->first();

            $currentType = ReportJourneyType::from($validated['type']);
            $lastType    = $lastJourney ? ReportJourneyType::from($lastJourney->type) : null;

            // Tentukan alurnya
            $validFlow = match ($currentType) {

                // PENYELIDIKAN boleh selama terakhir belum SELESAI
                ReportJourneyType::INVESTIGATION =>
                    $lastType !== ReportJourneyType::COMPLETED,

                // LIMPAH cuma boleh kalau terakhir PENYELIDIKAN
                ReportJourneyType::TRANSFER =>
                    $lastType === ReportJourneyType::INVESTIGATION,

                // SIDANG cuma boleh kalau terakhir LIMPAH
                ReportJourneyType::TRIAL =>
                    $lastType === ReportJourneyType::TRANSFER,

                // SELESAI boleh dari mana saja asal belum SELESAI juga
                ReportJourneyType::COMPLETED =>
                    $lastType !== ReportJourneyType::COMPLETED,

                default => false,
            };

            // Kalau flow tidak valid
           if (! $validFlow) {

                $msg = match ($currentType) {

                    ReportJourneyType::TRANSFER =>
                        'Tidak bisa LIMPAH karena status terakhir belum PENYELIDIKAN.',

                    ReportJourneyType::TRIAL =>
                        'Tidak bisa SIDANG karena status terakhir belum LIMPAH.',

                    ReportJourneyType::INVESTIGATION =>
                        'Tidak bisa PENYELIDIKAN jika laporan sudah SELESAI.',

                    ReportJourneyType::COMPLETED =>
                        'Laporan sudah SELESAI sebelumnya.',

                    default =>
                        'Urutan tahapan tidak valid.',
                };

                throw ValidationException::withMessages(['type' => $msg]);
            }

        } catch (ValidationException $exception) {
            return back()
                ->withErrors($exception->errors())
                ->withInput()
                ->with('open_modal', 'journey');
        }

        // kalau bukan limpah, kosongkan target
        if (! in_array($validated['type'], $limpahValues, true)) {
            $validated['institution_target_id'] = null;
            $validated['subdivision_target_id'] = null;
        }

        $action = match ($currentType) {
            ReportJourneyType::COMPLETED => 'complete',
            ReportJourneyType::TRANSFER => 'transfer',
            default => 'save',
        };

        $payload = [
            'report_id'           => $reportId,
            'institution_id'      => optional(auth()->user())->institution_id,
            'division_id'         => optional(auth()->user())->division_id,
            'type'                => $validated['type'],
            'description'         => $validated['description'],
            'action'              => $action,
            'institution_target_id' => $validated['institution_target_id'] ?? null,
            'division_target_id'    => $validated['subdivision_target_id'] ?? null,
        ];

        $files = $request->file('files', []);
        $files = is_array($files) ? $files : [$files];
        $files = array_filter($files, static fn ($file) => $file instanceof UploadedFile);

        $result = $this->service->store($payload, $files);

        if ($result['status'] ?? false) {
            return redirect()
                // balik ke halaman pelaporan detail
                ->route('pelaporan.show', ['pelaporan' => $reportId])
                ->with('success', $result['message']);
        }

        return back()
            ->withInput()
            ->with('open_modal', 'journey')
            ->with('error', $result['message'] ?? 'Gagal menambahkan tahapan penanganan.....');
    }
}
