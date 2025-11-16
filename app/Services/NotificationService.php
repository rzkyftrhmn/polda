<?php
namespace App\Services;

use App\Models\Report;
use App\Models\User;
use App\Repositories\NotificationRepository;

class NotificationService
{
    protected $repo;

    public function __construct(NotificationRepository $repo)
    {
        $this->repo = $repo;
    }

    // ============================
    // SEND NOTIFICATION
    // ============================
    public function send($userId, $title, $message, $reportId = null, $type = 'report_status')
    {
        return $this->repo->store([
            'user_id'   => $userId,
            'report_id' => $reportId,
            'type'      => $type,
            'title'     => $title,
            'message'   => $message,
            'read_at'   => null,
        ]);
    }

    // ============================
    // TRIGGER NOTIF
    // ============================
    public function notifyReportStatus(Report $report, $status)
    {
        $template = $this->buildMessageForStatus($report, $status);

        // ----- Pelapor -----
        $creator = $report->creator;
        // Kirim notifikasi ke pelapor
        if ($report->creator) {
            $this->send(
                $report->creator->id,
                $template['title'],
                $template['pelapor'],
                $report->id
            );
            \Log::info("Notifikasi Laporan Diperbarui dikirim ke pelapor: {$report->creator->id}, untuk laporan {$report->code}.");
        } else {
            \Log::warning("Creator (pelapor) tidak ditemukan untuk laporan {$report->code}.");
        }

        // Kirim notifikasi ke kasubbid
        $kasubbid = $this->getKasubbid($report);
        if ($kasubbid && isset($template['kasubbid'])) {
            $this->send(
                $kasubbid->id,
                $template['title'],
                $template['kasubbid'],
                $report->id
            );
            \Log::info("Notifikasi Laporan Diperbarui dikirim ke kasubbid: {$kasubbid->id}, untuk laporan {$report->code}.");
        } else {
            \Log::warning("Kasubbid tidak ditemukan untuk laporan {$report->code}.");
        }

    }

    public function getKasubbid()
    {
        return User::role('kasubbid')->first();
    }

    // ============================
    // TEMPLATE NOTIF
    // ============================
    public function buildMessageForStatus(Report $report, $status)
    {
        $code   = $report->code;
        $title  = $report->title;

        $divisionName       = $report->division->name ?? 'Bidang terkait';
        $targetDivisionName = $report->targetDivision->name ?? 'Bidang tujuan';
        $finishTime         = $report->finish_time ?? '-';
        $followUp           = $report->follow_up_note ?? '-';

        \Log::info("Status yang diterima: {$status}");

        return match ($status) {
            'SUBMITTED' => [
                'title'   => 'Laporan Diterima',
                'pelapor' => "Laporan [$code] $title telah diterima oleh sistem.",
            ],
            'PEMERIKSAAN' => [
                'title'   => 'Laporan Sedang Diperiksa',
                'pelapor' => "Laporan [$code] $title sedang diperiksa oleh $divisionName.",
                'kasubbid'=> "Laporan [$code] $title masuk tahap pemeriksaan. PIC: $divisionName",
            ],
            'LIMPAH' => [
                'title'   => 'Laporan Dilimpahkan',
                'pelapor' => "Laporan [$code] $title dilimpahkan ke $targetDivisionName.",
                'kasubbid'=> "Laporan [$code] $title telah dilimpahkan.",
            ],
            'SIDANG' => [
                'title'   => 'Laporan Masuk Sidang',
                'pelapor' => "Laporan [$code] $title memasuki proses sidang.",
                'kasubbid'=> "Laporan [$code] $title dijadwalkan sidang.",
            ],
            'SELESAI' => [
                'title'   => 'Laporan Telah Selesai',
                'pelapor' => "Laporan [$code] $title selesai pada $finishTime.",
                'kasubbid'=> "Laporan [$code] $title selesai $finishTime.",
            ]
        };
    }

    public function notifyBuktiDitambahkan(Report $report, $fileNames)
    {
        $code = $report->code;
        $title = $report->title;

        $titleNotif = "Bukti Ditambahkan";
        $messagePelapor = "Bukti baru ditambahkan pada laporan [$code] $title: $fileNames.";

        if ($report->creator) {
            $this->send(
                $report->creator->id,
                $titleNotif,
                $messagePelapor,
                $report->id
            );

            \Log::info("Notifikasi bukti ditambahkan -> Pelapor ({$report->creator->id}) | $code");
        }
    }


    public function notifyLaporanDiupdate(Report $report, $changedFields)
    {
        $template = [
            'title' => 'Laporan Diperbarui',
            'pelapor' => "Laporan {$report->code} ({$report->title}) diperbarui: {$changedFields}",
            'kasubbid' => "Laporan {$report->code} ({$report->title}) diperbarui: {$changedFields}",
        ];

        // Kirim notifikasi ke pelapor
        if ($report->creator) {
            $this->send(
                $report->creator->id,
                $template['title'],
                $template['pelapor'],
                $report->id
            );
            \Log::info("Notifikasi Laporan Diperbarui dikirim ke pelapor: {$report->creator->id}, untuk laporan {$report->code}.");
        } else {
            \Log::warning("Creator (pelapor) tidak ditemukan untuk laporan {$report->code}.");
        }

        // Kirim notifikasi ke kasubbid (jika ada)
        $kasubbid = $this->getKasubbid($report);
        if ($kasubbid && isset($template['kasubbid'])) {
            $this->send(
                $kasubbid->id,
                $template['title'],
                $template['kasubbid'],
                $report->id
            );
            \Log::info("Notifikasi Laporan Diperbarui dikirim ke kasubbid: {$kasubbid->id}, untuk laporan {$report->code}.");
        } else {
            \Log::warning("Kasubbid tidak ditemukan untuk laporan {$report->code}.");
        }
    }
}
