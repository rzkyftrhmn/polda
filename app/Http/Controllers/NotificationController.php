<?php
namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function toArray($notifiable)
    {
        return [
            'message' => 'Ada laporan baru',
            'report_id' => $this->report->id,
        ];
    }

    public function allNotifications()
    {
        $notifications = Notification::where('user_id', auth()->id())
                                    ->orderBy('created_at', 'desc')
                                    ->get();

        return view('pages.notification.index', compact('notifications'));
    }

    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->get();

        return response()->json([
            'data' => $notifications
        ]);
    }

    public function unreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }

    public function markAsRead($id)
    {
        $notif = Notification::where('user_id', auth()->id())->findOrFail($id);

        $notif->update([
            'read_at' => now()
        ]);

        $reportId = $notif->report_id; 

        if (!$reportId) {
            return response()->json([
                'error' => 'report_id kosong',
            ], 500);
        }

        return response()->json([
            'redirect' => route('pelaporan.show', $reportId)
        ]);
    }
    
    public function markAllAsRead()
    {
        $updated = Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]); 

        if ($updated) {
            return response()->json(['success' => true, 'message' => 'Semua notifikasi berhasil ditandai sebagai dibaca']);
        }

        return response()->json(['success' => false, 'message' => 'Tidak ada notifikasi yang perlu diperbarui'], 404);
    }
}
