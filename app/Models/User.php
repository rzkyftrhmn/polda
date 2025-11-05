<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'institution_id',
        'division_id',
        'username',
        'name',
        'email',
        'password',
        'photo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the institution that the user belongs to.
     */
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Get the division that the user belongs to.
     */
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * Resolve the public URL for the stored profile photo.
     */
    protected function photoUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (! $this->photo) {
                return null;
            }

            return Storage::disk('public')->url($this->photo);
        });
    }
}
