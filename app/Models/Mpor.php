<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mpor extends Model
{
    protected $fillable = [
        'employee_id',
        'office_id',
        'month',
        'status',
        'generated_at',
        'created_by',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function orsEntries(): HasMany
    {
        return $this->hasMany(OrsEntry::class, 'mpor_id');
    }

    public function ratedOrsEntries(): HasMany
    {
        return $this->hasMany(OrsEntry::class, 'mpor_id')->where('status', 'rated');
    }
}
