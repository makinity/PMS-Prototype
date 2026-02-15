<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Opcr extends Model
{
    protected $table = 'opcrs';

    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_ENDORSED = 'endorsed';
    public const STATUS_FOR_REVIEW = 'for_dept_head_review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_RETURNED = 'returned';

    protected $fillable = [
        'unit_work_plan_id',
        'generated_by',
        'status',
        'approved_by',
        'approved_at',
        'returned_at',
        'remarks',
        'locked_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'returned_at' => 'datetime',
        'locked_at' => 'datetime',
    ];

    public function unitWorkPlan(): BelongsTo
    {
        return $this->belongsTo(UnitWorkPlan::class, 'unit_work_plan_id');
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
}
