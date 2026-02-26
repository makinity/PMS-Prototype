<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ipcr extends Model
{
    protected $table = 'ipcrs';

    public const STATUS_FOR_COMMITMENT = 'for_commitment';
    public const STATUS_COMMITTED = 'committed';
    // Legacy alias for compatibility.
    public const STATUS_GENERATED = self::STATUS_FOR_COMMITMENT;

    protected $fillable = [
        'opcr_id',
        'unit_work_plan_id',
        'employee_id',
        'performance_period_id',
        'office_id',
        'status',
        'generated_at',
        'committed_at',
        'locked_at',
        'finalized_from_qar_header_id',
        'finalized_at',
        'final_score',
        'adjectival_rating',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'committed_at' => 'datetime',
        'locked_at' => 'datetime',
        'finalized_at' => 'datetime',
        'final_score' => 'decimal:2',
    ];

    public function opcr(): BelongsTo
    {
        return $this->belongsTo(Opcr::class, 'opcr_id');
    }

    public function unitWorkPlan(): BelongsTo
    {
        return $this->belongsTo(UnitWorkPlan::class, 'unit_work_plan_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function performancePeriod(): BelongsTo
    {
        return $this->belongsTo(PerformancePeriod::class, 'performance_period_id');
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function finalizedFromQarHeader(): BelongsTo
    {
        return $this->belongsTo(QarHeader::class, 'finalized_from_qar_header_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(IpcrItem::class, 'ipcr_id');
    }

    public function ipcrItems(): HasMany
    {
        return $this->hasMany(IpcrItem::class, 'ipcr_id');
    }

    public function isLocked(): bool
    {
        return !is_null($this->locked_at);
    }

    public function isFinalized(): bool
    {
        return !is_null($this->finalized_at);
    }

    public function employeeMpors(): HasMany
    {
        return $this->hasMany(Mpor::class, 'employee_id', 'employee_id')
            ->where('office_id', $this->office_id)
            ->whereBetween('month', [$this->performancePeriod->start_month, $this->performancePeriod->end_month]);
    }
}
