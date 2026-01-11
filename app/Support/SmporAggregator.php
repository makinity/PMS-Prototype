<?php

namespace App\Support;

final class SmporAggregator
{
    /**
     * Build SMPOR data strictly from locked MPOR payloads.
     *
     * @param  iterable<int,array>  $lockedMpor
     * @return array<string,mixed>
     */
    public static function aggregate(iterable $lockedMpor): array
    {
        $source = SmporRules::onlyLocked($lockedMpor);

        return [
            'generated_at' => now(),
            'sources' => array_map(
                fn (array $mpor) => [
                    'mpor_id' => $mpor['id'] ?? null,
                    'period' => $mpor['period'] ?? null,
                    'employee_id' => $mpor['employee_id'] ?? null,
                    'locked_at' => $mpor['locked_at'] ?? null,
                    'locked_by' => $mpor['locked_by'] ?? null,
                ],
                $source
            ),
            'entries' => $source,
        ];
    }
}
