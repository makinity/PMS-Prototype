<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'employee_id',
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'activated_at',
        'profile_photo_path',
        'office_id', // Make sure this is added
        'position',  // Make sure this is added
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'bool',
            'activated_at' => 'datetime',
        ];
    }

    // Add these relationships
    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function supervisedOffice()
    {
        return $this->hasOne(Office::class, 'head_id');
    }

    public function isSupervisor()
    {
        return $this->role === 'supervisor';
    }

    public function isDepartmentHead()
    {
        return $this->role === 'dept-head';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
