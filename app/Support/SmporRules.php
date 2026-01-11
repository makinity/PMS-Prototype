<?php

namespace App\Support;

final class SmporRules
{
    public const STATUS_LOCKED = 'LOCKED';

    /**
     * Lock an MPOR record (array payload) when all gating conditions are met.
     */
    public static function lock(array $mpor, int $supervisorId): array
    {
        if (
            ($mpor['pending_validation'] ?? 0) !== 0 ||
            ($mpor['missing_links'] ?? 0) !== 0
        ) {
            return $mpor;
        }

        $mpor['status'] = self::STATUS_LOCKED;
        $mpor['locked_at'] = $mpor['locked_at'] ?? now();
        $mpor['locked_by'] = $supervisorId;
        $mpor['eligible_for_smpor'] = true;
        $mpor['employee_editable'] = false;
        $mpor['supervisor_editable'] = false;

        return $mpor;
    }

    /**
     * Predicate to determine SMPOR eligibility.
     */
    public static function eligibleForSmpor(array $mpor): bool
    {
        return ($mpor['status'] ?? null) === self::STATUS_LOCKED
            && ($mpor['eligible_for_smpor'] ?? false) === true;
    }

    /**
     * Filter a list of MPOR payloads down to SMPOR-ready entries.
     *
     * @param  iterable<int,array>  $mpors
     * @return array<int,array>
     */
    public static function onlyLocked(iterable $mpors): array
    {
        $eligible = [];

        foreach ($mpors as $mpor) {
            if (self::eligibleForSmpor($mpor)) {
                $eligible[] = $mpor;
            }
        }

        return $eligible;
    }
}
