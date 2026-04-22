<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            !Schema::hasTable('uwp_success_indicators')
            || !Schema::hasTable('uwp_mfos')
            || !Schema::hasTable('ipcr_items')
        ) {
            return;
        }

        if (
            !Schema::hasColumn('uwp_success_indicators', 'target_quantity')
            || !Schema::hasColumn('uwp_success_indicators', 'target_timeline')
            || !Schema::hasColumn('ipcr_items', 'uwp_success_indicator_id')
            || !Schema::hasColumn('ipcr_items', 'target_quantity')
            || !Schema::hasColumn('ipcr_items', 'target_timeline')
        ) {
            return;
        }

        DB::table('uwp_success_indicators as si')
            ->join('uwp_mfos as mfo', 'mfo.id', '=', 'si.uwp_mfo_id')
            ->select([
                'si.id',
                'si.target_quantity',
                'si.target_timeline',
                'mfo.target_quantity as mfo_target_quantity',
                'mfo.target_timeline as mfo_target_timeline',
            ])
            ->orderBy('si.id')
            ->chunk(200, function ($rows): void {
                foreach ($rows as $row) {
                    DB::table('uwp_success_indicators')
                        ->where('id', $row->id)
                        ->update([
                            'target_quantity' => $row->target_quantity ?? $row->mfo_target_quantity,
                            'target_timeline' => $this->coalesceText($row->target_timeline, $row->mfo_target_timeline),
                        ]);
                }
            });

        $indicatorMap = DB::table('uwp_success_indicators as si')
            ->join('uwp_mfos as mfo', 'mfo.id', '=', 'si.uwp_mfo_id')
            ->select([
                'si.id as success_indicator_id',
                'mfo.uwp_function_id',
                'mfo.title as output_title',
                'si.indicator_text',
                'si.target_quantity',
                'si.target_timeline',
            ])
            ->get()
            ->reduce(function (array $carry, $row): array {
                $key = $this->buildLookupKey(
                    (int) ($row->uwp_function_id ?? 0),
                    (string) ($row->output_title ?? ''),
                    (string) ($row->indicator_text ?? '')
                );

                if ($key !== '') {
                    $carry[$key] = [
                        'uwp_success_indicator_id' => (int) $row->success_indicator_id,
                        'target_quantity' => is_numeric($row->target_quantity) ? (int) $row->target_quantity : null,
                        'target_timeline' => $this->coalesceText($row->target_timeline),
                    ];
                }

                return $carry;
            }, []);

        DB::table('ipcr_items')
            ->select([
                'id',
                'uwp_function_id',
                'output_title',
                'indicator_text',
                'target_summary',
            ])
            ->orderBy('id')
            ->chunk(200, function ($rows) use ($indicatorMap): void {
                foreach ($rows as $row) {
                    $key = $this->buildLookupKey(
                        (int) ($row->uwp_function_id ?? 0),
                        (string) ($row->output_title ?? ''),
                        (string) ($row->indicator_text ?? '')
                    );

                    $indicator = $indicatorMap[$key] ?? null;
                    if (!$indicator) {
                        continue;
                    }

                    $summary = $this->formatTargetSummary(
                        $indicator['target_quantity'] ?? null,
                        $indicator['target_timeline'] ?? null
                    );

                    DB::table('ipcr_items')
                        ->where('id', $row->id)
                        ->update([
                            'uwp_success_indicator_id' => $indicator['uwp_success_indicator_id'] ?? null,
                            'target_quantity' => $indicator['target_quantity'] ?? null,
                            'target_timeline' => $indicator['target_timeline'] ?? null,
                            'target_summary' => $summary !== '' ? $summary : $row->target_summary,
                        ]);
                }
            });
    }

    public function down(): void
    {
        if (!Schema::hasTable('ipcr_items') || !Schema::hasTable('uwp_success_indicators')) {
            return;
        }

        if (Schema::hasColumn('ipcr_items', 'uwp_success_indicator_id')) {
            DB::table('ipcr_items')->update([
                'uwp_success_indicator_id' => null,
            ]);
        }

        if (Schema::hasColumn('ipcr_items', 'target_quantity')) {
            DB::table('ipcr_items')->update([
                'target_quantity' => null,
            ]);
        }

        if (Schema::hasColumn('ipcr_items', 'target_timeline')) {
            DB::table('ipcr_items')->update([
                'target_timeline' => null,
            ]);
        }

        if (Schema::hasColumn('uwp_success_indicators', 'target_quantity')) {
            DB::table('uwp_success_indicators')->update([
                'target_quantity' => null,
            ]);
        }

        if (Schema::hasColumn('uwp_success_indicators', 'target_timeline')) {
            DB::table('uwp_success_indicators')->update([
                'target_timeline' => null,
            ]);
        }
    }

    private function buildLookupKey(int $functionId, string $outputTitle, string $indicatorText): string
    {
        $output = trim($outputTitle);
        $indicator = trim($indicatorText);

        if ($functionId <= 0 || $output === '' || $indicator === '') {
            return '';
        }

        return $functionId . '||' . $output . '||' . $indicator;
    }

    private function coalesceText(?string ...$values): ?string
    {
        foreach ($values as $value) {
            $text = trim((string) $value);
            if ($text !== '') {
                return $text;
            }
        }

        return null;
    }

    private function formatTargetSummary(mixed $targetQuantity, ?string $targetTimeline): string
    {
        $quantityText = $targetQuantity === null ? '' : trim((string) $targetQuantity);
        $timelineText = trim((string) ($targetTimeline ?? ''));

        return trim($quantityText . ' ' . $timelineText);
    }
};
