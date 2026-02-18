<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IpcrItem extends Model
{
    protected $table = 'ipcr_items';

    protected $fillable = [
        'ipcr_id',
        'output_title',
        'function_type',
        'indicator_text',
        'target_summary',
        'standards_payload',
    ];

    protected $casts = [
        'standards_payload' => 'array',
    ];

    public function ipcr(): BelongsTo
    {
        return $this->belongsTo(Ipcr::class, 'ipcr_id');
    }
}
