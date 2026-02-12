<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UwpSuccessIndicator extends Model
{
    protected $table = 'uwp_success_indicators';

    protected $fillable = [
        'uwp_mfo_id',
        'description',
        'target_timeline',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function uwpMfo(): BelongsTo
    {
        return $this->belongsTo(UwpMfo::class, 'uwp_mfo_id');
    }

    public function qetStandards(): HasMany
    {
        return $this->hasMany(UwpQetStandard::class, 'uwp_success_indicator_id');
    }
}
