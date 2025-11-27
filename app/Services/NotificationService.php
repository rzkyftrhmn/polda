<?php
namespace App\Services;

use App\Models\Report;
use App\Models\User;
use App\Models\AccessData;
use App\Repositories\NotificationRepository;

class NotificationService
{
    protected $repo;

    public function __construct(NotificationRepository $repo)
    {
        $this->repo = $repo;
    }

    // ============================
    // SIMPAN NOTIF
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
    // NOTIF STATUS — UNTUK SEMUA USER
    // ============================
    public function notifyReportStatus(Report $report, $status, $context = null)
    {
        [$title, $message] = $this->buildMessageForStatus(
            $report,
            $context ?? $status
        );

        $reportId = $report->id;

        $divisionIds = AccessData::where('report_id', $reportId)
            ->pluck('division_id')
            ->toArray();
        $usersFromDivision = User::whereIn('division_id', $divisionIds)
            ->pluck('id')
            ->toArray();
        $adminUsers = User::role('admin')->pluck('id')->toArray();
        $recipients = array_unique(array_merge($usersFromDivision, $adminUsers));

        foreach ($recipients as $user) {
            $this->send($user, $title, $message, $report->id);
        }
    }

    // ============================
    // TEMPLATE STATUS
    // ============================
    public function buildMessageForStatus(Report $report, $status)
    {
        $code  = $report->code;
        $title = $report->title;

        return match ($status) {

            'SUBMITTED' => [
                'Laporan Diterima',
                "Laporan [$code, $title] telah diterima oleh sistem."
            ],

            //SIMPAN & LIMPAH
            'INSPECTION_TRANSFER' => [
                'Laporan Dilimpahkan',
                "Pemeriksaan pada laporan [$code, $title] telah selesai dan dilimpahkan"
            ],

            //SIMPAN & SELESAI
            'INSPECTION_COMPLETE' => [
                'Perkara Selesai',
                "Pemeriksaan pada laporan [$code, $title] selesai. Perkara dinyatakan selesai."
            ],

            //SIMPAN & SELESAI (SETELAH SIDANG)
            'SIDANG_COMPLETE' => [
                'Sidang Selesai',
                "Penyelidikan dan sidang untuk laporan [$code, $title] telah selesai."
            ],

            default => [
                'Laporan Diperbarui',
                "Laporan [$code, $title] telah diperbarui."
            ],
        };
    }

}
