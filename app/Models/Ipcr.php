<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ipcr extends Model
{
    protected $table = 'ipcrs';

    public const STATUS_GENERATED = 'generated';
    public const STATUS_COMMITTED = 'committed';

    protected $fillable = [
        'opcr_id',
        'unit_work_plan_id',
        'employee_id',
        'status',
        'committed_at',
        'locked_at',
    ];

    protected $casts = [
        'committed_at' => 'datetime',
        'locked_at' => 'datetime',
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

    public function isLocked(): bool
    {
        return !is_null($this->locked_at);
    }
}
