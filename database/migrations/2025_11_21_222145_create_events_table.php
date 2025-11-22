<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('location');
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

// Table events {
//   id             int [pk, increment]
//   name           varchar(255)         // nama kegiatan
//   description    text                 // detail kegiatan
//   location       varchar(255)         // lokasi kegiatan
//   start_time     datetime             // waktu mulai
//   end_time       datetime             // waktu selesai
//   created_at     timestamp
//   updated_at     timestamp
// }

// Table event_participant {
//   id            int [pk, increment]
//   event_id      int
//   devision_id   int
//   is_required   boolean
//   status        enum('pending','confirmed','absent','completed')
//   notes         text
//   created_at    timestamp
//   updated_at    timestamp
// }