<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'parent_id',
        'level',
        'permissions',
        'name',
        'type',
        'level',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function parent()
    {
        return $this->belongsTo(Division::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Division::class, 'parent_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function journeys()
    {
        return $this->hasMany(ReportJourney::class, 'institution_id');
    }

    public function report()
    {
        return $this->hasMany(Report::class, 'division_id');
    }

    public function access_datas()
    {
        return $this->hasMany(AccessData::class, 'division_id');
    }

    public function division()
    {
        return $this->hasMany(Suspect::class, 'division_id');
    }

    public function hasPermission(string $key): bool
    {
        $permissions = $this->permissions ?? [];

        if (is_string($permissions)) {
            $decoded = json_decode($permissions, true);
            $permissions = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($permissions)) {
            return false;
        }

        return !empty($permissions[$key]);
    }

    /**
     * Divisions that can perform "pemeriksaan" (inspection) – TOP section.
     */
    public function canInspection(): bool
    {
        return $this->hasPermission('inspection');
    }

    /**
     * Divisions that can perform "penyidikan/penyelidikan" (investigation) – BOTTOM section.
     */
    public function canInvestigation(): bool
    {
        return $this->hasPermission('investigation');
    }
}

