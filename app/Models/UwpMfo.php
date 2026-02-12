<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UwpMfo extends Model
{
    protected $table = 'uwp_mfos';

    protected $fillable = [
        'unit_work_plan_id',
        'function_id',
        'title',
        'target_summary',
        'weight',
        'sort_order',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function unitWorkPlan(): BelongsTo
    {
        return $this->belongsTo(UnitWorkPlan::class, 'unit_work_plan_id');
    }

    public function function(): BelongsTo
    {
        return $this->belongsTo(FunctionModel::class, 'function_id');
    }

    public function successIndicators(): HasMany
    {
        return $this->hasMany(UwpSuccessIndicator::class, 'uwp_mfo_id');
    }
}
