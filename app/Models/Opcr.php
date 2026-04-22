<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Opcr extends Model
{
    protected $table = 'opcrs';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_ENDORSED = 'endorsed';
    public const STATUS_RETURNED = 'returned';
    public const STATUS_APPROVED = 'approved';

    // Legacy constants kept for compatibility with existing callers.
    public const STATUS_FOR_REVIEW = 'for_dept_head_review';

    protected $fillable = [
        'unit_work_plan_id',
        'office_id',
        'performance_period_id',
        'generated_by',
        'status',
        'submitted_at',
        'approved_by',
        'approved_at',
        'returned_at',
        'remarks',
        'locked_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'returned_at' => 'datetime',
        'locked_at' => 'datetime',
    ];

    public function unitWorkPlan(): BelongsTo
    {
        return $this->belongsTo(UnitWorkPlan::class, 'unit_work_plan_id');
    }

    public function uwp(): BelongsTo
    {
        return $this->unitWorkPlan();
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function performancePeriod(): BelongsTo
    {
        return $this->belongsTo(PerformancePeriod::class, 'performance_period_id');
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function ipcrs(): HasMany
    {
        return $this->hasMany(Ipcr::class, 'opcr_id');
    }

    public function isLocked(): bool
    {
        return !is_null($this->locked_at);
    }

    public function isDraft(): bool
    {
        return strtolower((string) $this->status) === self::STATUS_DRAFT;
    }

    public function isSubmitted(): bool
    {
        return strtolower((string) $this->status) === self::STATUS_SUBMITTED;
    }

    public function isReturned(): bool
    {
        return strtolower((string) $this->status) === self::STATUS_RETURNED;
    }

    public function isApproved(): bool
    {
        return strtolower((string) $this->status) === self::STATUS_APPROVED;
    }

    public function isEditable(): bool
    {
        if ($this->isLocked()) {
            return false;
        }

        return $this->isDraft() || $this->isReturned();
    }
}
