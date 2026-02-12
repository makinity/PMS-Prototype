<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UwpQetStandard extends Model
{
    protected $table = 'uwp_qet_standards';

    public const DIM_QUALITY = 'quality';
    public const DIM_EFFICIENCY = 'efficiency';
    public const DIM_TIMELINESS = 'timeliness';

    protected $fillable = [
        'uwp_success_indicator_id',
        'dimension',
        'rating_level',
        'standard',
    ];

    protected $casts = [
        'rating_level' => 'integer',
    ];

    public function successIndicator(): BelongsTo
    {
        return $this->belongsTo(UwpSuccessIndicator::class, 'uwp_success_indicator_id');
    }
}
