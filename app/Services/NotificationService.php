<?php
namespace App\Services;

use App\Models\Event;
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

    // send event
    public function sendEvent($userId, $title, $message, $eventId = null, $type = 'event_participant')
    {
        return $this->repo->store([
            'user_id'   => $userId,
            'event_id'  => $eventId,
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

            'PETUNJUK_DAN_ARAHAN_SEND' => [
                'Petunjuk dan Arahan',
                "Petunjuk dan arahan untuk laporan [$code, $title] telah dikirim."
            ],

            'PETUNJUK_DAN_ARAHAN' => [
                'Petunjuk dan Arahan',
                "Petunjuk dan arahan untuk laporan [$code, $title] diterima."
            ],

            'EVENT_PARTICIPANT' => [
                'Kegiatan',
                "Anda telah ditambahkan pada kegiatan $title, harap mengikuti event tersebut dan upload bukti partisipasinya."
            ],

            default => [
                'Laporan Diperbarui',
                "Laporan [$code, $title] telah diperbarui."
            ],
        };
    }

    public function buildMessageForStatusEvent(Event $event, $status)
    {
        $title = $event->name;

        return match ($status) {
            'EVENT_PARTICIPANT' => [
                'Kegiatan',
                "Anda telah ditambahkan pada kegiatan $title, harap mengikuti event tersebut dan upload bukti partisipasinya."
            ],

            default => [
                'Laporan Diperbarui',
                "Laporan $title telah diperbarui."
            ],
        };
    }

    public function notifyPetunjukDanArahan(Report $report, $recipientIds, $context = null)
    {
        [$title, $message] = $this->buildMessageForStatus(
            $report,
            $context ?? NOTIF_PETUNJUK_DAN_ARAHAN
        );

        foreach ($recipientIds as $user) {
            $this->send(
                $user->id,
                $title,
                $message,
                $report->id
            );
        }
    }

    public function notifyEvent(Event $event, $recipientIds, $context = null)
    {
        [$title, $message] = $this->buildMessageForStatusEvent(
            $event,
            $context ?? NOTIF_EVENT_PARTICIPANT
        );

        foreach ($recipientIds as $userId) {
            $this->sendEvent(
                $userId,
                $title,
                $message,
                $event->id
            );
        }
    }
}
