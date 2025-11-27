<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructionsAndDirection extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'user_id_from',
        'user_id_to',
        'message',
    ];
}
