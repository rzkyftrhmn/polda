<?php

namespace App\Repositories;

use App\Models\Notification;

class NotificationRepository
{
    // Fungsi untuk menyimpan notifikasi ke database
    public function store(array $data)
    {
        return Notification::create($data);
    }
}
