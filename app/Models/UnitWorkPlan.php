<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UnitWorkPlan extends Model
{
    // Status constants
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_ENDORSED = 'endorsed';
    public const STATUS_PMT_APPROVED = 'pmt_approved';

    protected $fillable = [
        'office_id',
        'performance_period_id',
        'created_by',
        'status',
        'submitted_at',
        'endorsed_at',
        'approved_at',
        'locked_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'endorsed_at'  => 'datetime',
        'approved_at'  => 'datetime',
        'locked_at'    => 'datetime',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function performancePeriod(): BelongsTo
    {
        return $this->belongsTo(PerformancePeriod::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function mfos(): HasMany
    {
        return $this->hasMany(UwpMfo::class, 'unit_work_plan_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(UwpEmployeeAssignment::class, 'unit_work_plan_id');
    }

    public function opcr(): HasOne
    {
        return $this->hasOne(Opcr::class, 'unit_work_plan_id');
    }

    public function isLocked(): bool
    {
        return !is_null($this->locked_at);
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }
}
