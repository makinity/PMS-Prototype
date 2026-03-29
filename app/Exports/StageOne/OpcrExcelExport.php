<?php

namespace App\Exports\StageOne;

use App\Models\Opcr;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OpcrExcelExport implements FromArray, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    use Exportable;

    private const RATINGS = [5, 4, 3, 2, 1];

    private const STANDARDS_COLUMNS = [
        5 => 'K',
        4 => 'L',
        3 => 'M',
        2 => 'N',
        1 => 'O',
    ];

    private const TABLE_HEADER_ROW = 9;
    private const TABLE_SUBHEADER_ROW = 10;
    private const TABLE_START_ROW = 11;

    private const BASE_ROW_HEIGHT = 22;
    private const LINE_HEIGHT = 14;
    private const CHARS_PER_LINE = 42;

    private Opcr $opcrModel;

    private array $opcrData = [];

    private array $standards = [];
    private array $sectionLabels = [];
    private array $sectionMeta = [];

    public function __construct(Opcr $opcr)
    {
        $this->opcrModel = $opcr;
        $this->hydrateFromModel();
    }

    public function array(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 35,
            'B' => 40,
            'C' => 12,
            'D' => 18,
            'E' => 22,
            'F' => 6,
            'G' => 6,
            'H' => 6,
            'I' => 6,
            'J' => 14,
            'K' => 30,
            'L' => 30,
            'M' => 30,
            'N' => 30,
            'O' => 30,
        ];
    }

    public function title(): string
    {
        return 'OPCR';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $this->populateTemplate($event->sheet->getDelegate());
            },
        ];
    }

    private function hydrateFromModel(): void
    {
        $uwp = $this->opcrModel->unitWorkPlan;
        if (!$uwp) {
            return;
        }

        foreach ($uwp->uwpFunctions as $function) {
            $bucket = $this->normalizeFunctionType((string) ($function->function_type ?? ''));

            if (!isset($this->sectionMeta[$bucket])) {
                $this->sectionMeta[$bucket] = [
                    'weight_percent' => 0.0,
                    'sort_order' => is_null($function->sort_order) ? (1000 + count($this->sectionMeta)) : (int) $function->sort_order,
                ];
            }

            $this->sectionMeta[$bucket]['weight_percent'] += (float) ($function->weight_percent ?? 0);

            if (!isset($this->opcrData[$bucket])) {
                $this->opcrData[$bucket] = [];
            }

            foreach ($function->mfos as $mfo) {
                $indicatorRows = [];

                foreach ($mfo->successIndicators as $indicator) {
                    $indicatorText = (string) ($indicator->indicator_text ?? '');
                    $assigneeNames = $indicator->assignments
                        ->map(fn ($assignment) => $assignment->employee?->name)
                        ->filter()
                        ->values()
                        ->all();

                    $indicatorRows[] = [
                        'text' => $indicatorText,
                        'employee' => implode(', ', $assigneeNames),
                    ];

                    foreach (self::RATINGS as $rating) {
                        $this->standards[$indicatorText][$rating] = ['q' => [], 'e' => [], 't' => []];
                    }

                    foreach ($indicator->qetStandards as $standard) {
                        $rating = (int) $standard->rating;
                        if (!in_array($rating, self::RATINGS, true)) {
                            continue;
                        }

                        $dimension = strtolower((string) $standard->dimension);
                        $dimension = match ($dimension) {
                            'q', 'quality' => 'q',
                            'e', 'efficiency' => 'e',
                            't', 'timeliness' => 't',
                            default => null,
                        };

                        if ($dimension === null) {
                            continue;
                        }

                        $text = trim((string) ($standard->standard_text ?? ''));
                        if ($text !== '') {
                            $this->standards[$indicatorText][$rating][$dimension][] = $text;
                        }
                    }
                }

                $this->opcrData[$bucket][] = [
                    'mfo' => (string) ($mfo->title ?? ''),
                    'indicators' => $indicatorRows,
                ];
            }
        }
    }

    private function populateTemplate(Worksheet $sheet): void
    {
        $this->setupPage($sheet);
        $sheet->setShowGridlines(true);

        $this->writeManualHeader($sheet);
        $this->writeTableHeader($sheet);

        $currentRow = self::TABLE_START_ROW;
        foreach ($this->buildSectionDefinitions() as $section) {
            $includeRevenueRow = $section['type'] === 'core';
            $currentRow = $this->writeSection($sheet, $section['type'], $section['label'], $currentRow, $includeRevenueRow);
        }

        $lastRow = max($currentRow - 1, self::TABLE_SUBHEADER_ROW);

        $range = 'A' . self::TABLE_HEADER_ROW . ":O{$lastRow}";
        $sheet->getStyle($range)->getAlignment()
            ->setWrapText(true)
            ->setVertical(Alignment::VERTICAL_TOP);

        $sheet->getStyle('A' . self::TABLE_HEADER_ROW . ':O' . self::TABLE_SUBHEADER_ROW)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        $this->applyVerticalBordersOnly($sheet, self::TABLE_START_ROW, $lastRow);
        $this->applySectionRowBorders($sheet, self::TABLE_START_ROW, $lastRow);

        $this->writeFooterBlock($sheet, $lastRow + 1);
    }

    private function setupPage(Worksheet $sheet): void
    {
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_LEGAL);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
    }

    private function writeManualHeader(Worksheet $sheet): void
    {
        $uwp = $this->opcrModel->unitWorkPlan;
        $periodName = $uwp?->performancePeriod?->name ?? '';
        $officeName = $uwp?->office?->name ?? '';
        $officeHead = $uwp?->creator?->name ?? '';
        $departmentHead = $uwp?->office?->head?->name ?? '';

        $sheet->mergeCells('A1:O1');
        $sheet->setCellValue('A1', 'OFFICE PERFORMANCE COMMITMENT AND REVIEW (OPCR)');

        $sheet->mergeCells('A2:O2');
        $sheet->setCellValue('A2', $periodName);

        $sheet->setCellValue('A4', 'Office / Unit:');
        $sheet->setCellValue('B4', $officeName);

        $sheet->setCellValue('A5', 'Office Head:');
        $sheet->setCellValue('B5', $officeHead);

        $sheet->setCellValue('A6', 'Department Head:');
        $sheet->setCellValue('B6', $departmentHead);

        $sheet->getStyle('A1:O2')->getFont()->setBold(true);
        $sheet->getStyle('A1:O2')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
    }

    private function writeTableHeader(Worksheet $sheet): void
    {
        $headers = [
            'A' => 'MFOs / PPAs',
            'B' => 'Success Indicators',
            'C' => 'Allotted Budget',
            'D' => 'Division / Individual Accountable',
            'E' => '6 Months Summary of Accomplishment',
            'J' => 'Remarks',
        ];

        foreach ($headers as $col => $text) {
            $sheet->setCellValue($col . self::TABLE_HEADER_ROW, $text);
        }

        $sheet->mergeCells('F' . self::TABLE_HEADER_ROW . ':I' . self::TABLE_HEADER_ROW);
        $sheet->setCellValue('F' . self::TABLE_HEADER_ROW, 'Rating');

        $sheet->mergeCells('K' . self::TABLE_HEADER_ROW . ':O' . self::TABLE_HEADER_ROW);
        $sheet->setCellValue('K' . self::TABLE_HEADER_ROW, 'Standards per Success Indicator');

        $headerRange = 'A' . self::TABLE_HEADER_ROW . ':O' . self::TABLE_SUBHEADER_ROW;

        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('D9D9D9');

        foreach (['A', 'B', 'C', 'D', 'E', 'J'] as $col) {
            $sheet->mergeCells($col . self::TABLE_HEADER_ROW . ':' . $col . self::TABLE_SUBHEADER_ROW);
        }

        $subHeaders = [
            'F' => 'Q',
            'G' => 'E',
            'H' => 'T',
            'I' => 'A',
            'K' => '5',
            'L' => '4',
            'M' => '3',
            'N' => '2',
            'O' => '1',
        ];

        foreach ($subHeaders as $col => $text) {
            $sheet->setCellValue($col . self::TABLE_SUBHEADER_ROW, $text);
        }
    }

    private function writeSection(
        Worksheet $sheet,
        string $type,
        string $label,
        int $startRow,
        bool $includeRevenueRow
    ): int {
        $sheet->setCellValue("A{$startRow}", $label);
        $sheet->mergeCells("A{$startRow}:O{$startRow}");

        $sheet->getStyle("A{$startRow}:O{$startRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$startRow}:O{$startRow}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('E2E8F0');

        $sheet->getStyle("A{$startRow}:O{$startRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle("A{$startRow}:O{$startRow}")
            ->getBorders()
            ->getBottom()
            ->setBorderStyle(Border::BORDER_THIN);

        $row = $startRow + 1;

        if ($includeRevenueRow) {
            $sheet->setCellValue("A{$row}", 'REVENUE');
            $sheet->mergeCells("A{$row}:O{$row}");
            $sheet->getStyle("A{$row}:O{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:O{$row}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A{$row}:O{$row}")
                ->getBorders()
                ->getBottom()
                ->setBorderStyle(Border::BORDER_THIN);
            $row++;
        }

        return $this->writeIndicatorsLikeUwp($sheet, $row, $this->opcrData[$type] ?? []);
    }

    private function writeIndicatorsLikeUwp(Worksheet $sheet, int $row, array $items): int
    {
        foreach ($items as $item) {
            $indicators = $item['indicators'] ?? [];
            if (empty($indicators)) {
                continue;
            }

            $mfoStart = $row;

            foreach ($indicators as $indicator) {
                $indicatorText = (string) ($indicator['text'] ?? '');
                $employee = (string) ($indicator['employee'] ?? '');

                $sheet->setCellValue("A{$row}", '');
                $sheet->setCellValue("B{$row}", $indicatorText);
                $sheet->setCellValue("C{$row}", '');
                $sheet->setCellValue("D{$row}", $employee);
                $sheet->setCellValue("E{$row}", '');

                foreach (range('F', 'I') as $col) {
                    $sheet->setCellValue("{$col}{$row}", '');
                }

                $sheet->setCellValue("J{$row}", '');

                $stdTexts = [];
                foreach (self::RATINGS as $rating) {
                    $col = self::STANDARDS_COLUMNS[$rating];
                    $text = $this->formatStdBlock($indicatorText, $rating);
                    $sheet->setCellValue("{$col}{$row}", $text);
                    $sheet->getStyle("{$col}{$row}")->getAlignment()->setWrapText(true);
                    $stdTexts[] = $text;
                }

                $sheet->getStyle("B{$row}")->getAlignment()->setWrapText(true);
                $this->applyRowVerticalBorders($sheet, $row);

                $sheet->getRowDimension($row)->setRowHeight(
                    $this->estimateRowHeight($indicatorText, ...$stdTexts)
                );

                $row++;
            }

            $mfoEnd = $row - 1;
            if ($mfoEnd >= $mfoStart) {
                if ($mfoEnd > $mfoStart) {
                    $sheet->mergeCells("A{$mfoStart}:A{$mfoEnd}");
                }

                $sheet->setCellValue("A{$mfoStart}", (string) ($item['mfo'] ?? ''));
                $sheet->getStyle("A{$mfoStart}:A{$mfoEnd}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                    ->setVertical(Alignment::VERTICAL_TOP)
                    ->setWrapText(true);
            }
        }

        return $row;
    }

    private function formatStdBlock(string $indicator, int $rating): string
    {
        $entry = $this->standards[$indicator][$rating] ?? ['q' => [], 'e' => [], 't' => []];

        $q = $this->formatDimension(Arr::wrap($entry['q'] ?? []));
        $e = $this->formatDimension(Arr::wrap($entry['e'] ?? []));
        $t = $this->formatDimension(Arr::wrap($entry['t'] ?? []));

        return implode("\n", [
            "Q = {$q}",
            "E = {$e}",
            "T = {$t}",
        ]);
    }

    private function formatDimension(array $values): string
    {
        $values = array_filter($values, fn ($v) => $v !== null && trim((string) $v) !== '');
        return empty($values) ? '-' : implode('; ', array_map(fn ($v) => trim((string) $v), $values));
    }

    private function estimateRowHeight(string ...$texts): float
    {
        $maxLines = 1;

        foreach ($texts as $text) {
            $text = trim((string) $text);
            if ($text === '') {
                continue;
            }

            $explicit = substr_count($text, "\n") + 1;
            $wrapped = (int) ceil(mb_strlen($text) / self::CHARS_PER_LINE);
            $maxLines = max($maxLines, $explicit, $wrapped);
        }

        return self::BASE_ROW_HEIGHT + ($maxLines - 1) * self::LINE_HEIGHT;
    }

    private function applyVerticalBordersOnly(Worksheet $sheet, int $fromRow, int $toRow): void
    {
        for ($r = $fromRow; $r <= $toRow; $r++) {
            $this->applyRowVerticalBorders($sheet, $r);
        }
    }

    private function applyRowVerticalBorders(Worksheet $sheet, int $row): void
    {
        foreach (range('A', 'O') as $col) {
            $borders = $sheet->getStyle("{$col}{$row}")->getBorders();
            $borders->getLeft()->setBorderStyle(Border::BORDER_THIN);
            $borders->getRight()->setBorderStyle(Border::BORDER_THIN);
        }
    }

    private function applySectionRowBorders(Worksheet $sheet, int $fromRow, int $toRow): void
    {
        for ($r = $fromRow; $r <= $toRow; $r++) {
            $value = trim((string) $sheet->getCell("A{$r}")->getValue());
            if ($value === 'REVENUE' || in_array($value, $this->sectionLabels, true)) {
                $sheet->getStyle("A{$r}:O{$r}")
                    ->getBorders()
                    ->getBottom()
                    ->setBorderStyle(Border::BORDER_THIN);
            }
        }
    }

    private function buildSectionDefinitions(): array
    {
        $ordered = collect($this->opcrData)
            ->filter(fn (array $rows): bool => !empty($rows))
            ->keys()
            ->sort(function (string $left, string $right): int {
                $priority = static fn (string $type): int => match ($type) {
                    'core' => 10,
                    'support' => 20,
                    default => 30,
                };

                $leftPriority = $priority($left);
                $rightPriority = $priority($right);

                if ($leftPriority !== $rightPriority) {
                    return $leftPriority <=> $rightPriority;
                }

                $leftOrder = (int) ($this->sectionMeta[$left]['sort_order'] ?? 1000);
                $rightOrder = (int) ($this->sectionMeta[$right]['sort_order'] ?? 1000);

                if ($leftOrder !== $rightOrder) {
                    return $leftOrder <=> $rightOrder;
                }

                return strnatcasecmp($left, $right);
            })
            ->values()
            ->all();

        $labels = [];
        $sections = [];
        foreach ($ordered as $index => $type) {
            $label = $this->buildSectionLabel($type, $index);
            $labels[] = $label;
            $sections[] = [
                'type' => $type,
                'label' => $label,
                'weight_percent' => (float) ($this->sectionMeta[$type]['weight_percent'] ?? 0),
            ];
        }

        $this->sectionLabels = $labels;

        return $sections;
    }

    private function buildSectionLabel(string $type, int $index): string
    {
        $prefix = chr(65 + $index);
        $label = match ($type) {
            'core' => 'CORE FUNCTIONS',
            'support' => 'SUPPORT FUNCTIONS',
            'custom' => 'CUSTOM FUNCTIONS',
            default => strtoupper(str_replace('_', ' ', $type)) . ' FUNCTIONS',
        };

        $weightPercent = (float) ($this->sectionMeta[$type]['weight_percent'] ?? 0);
        if ($weightPercent > 0) {
            $weightLabel = rtrim(rtrim(number_format($weightPercent, 2, '.', ''), '0'), '.');
            $label .= " ({$weightLabel}%)";
        }

        return "{$prefix}. {$label}";
    }

    private function normalizeFunctionType(string $type): string
    {
        $normalized = strtolower(trim($type));
        return $normalized !== '' ? $normalized : 'custom';
    }

    private function writeFooterBlock(Worksheet $sheet, int $startRow): void
    {
        $row = $startRow;

        foreach ($this->buildSectionDefinitions() as $section) {
            $weightPercent = (float) ($section['weight_percent'] ?? 0);
            $label = trim((string) ($section['label'] ?? ''));
            $label = preg_replace('/^[A-Z]\.\s*/', '', $label ?? '') ?: 'FUNCTIONS';
            $weightLabel = $weightPercent > 0
                ? rtrim(rtrim(number_format($weightPercent, 2, '.', ''), '0'), '.')
                : null;

            $text = $weightLabel
                ? "Weighted Average Rating for {$label}"
                : "Weighted Average Rating for {$label}";

            $sheet->mergeCells("A{$row}:J{$row}");
            $sheet->mergeCells("K{$row}:O{$row}");
            $sheet->setCellValue("A{$row}", $text);
            $sheet->getStyle("A{$row}:O{$row}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A{$row}:O{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $row++;
        }

        foreach (['OVERALL RATING', 'ADJECTIVAL RATING'] as $label) {
            $sheet->mergeCells("A{$row}:J{$row}");
            $sheet->mergeCells("K{$row}:O{$row}");
            $sheet->setCellValue("A{$row}", $label);
            $sheet->getStyle("A{$row}:O{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:O{$row}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A{$row}:O{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $row++;
        }

        $labelRow = $row;
        $boxStartRow = $row + 1;
        $boxEndRow = $row + 3;
        $titleRow = $row + 4;

        $discussedName = (string) ($this->opcrModel->unitWorkPlan?->office?->head?->name ?? '');
        $assessedName = (string) ($this->opcrModel->approver?->name ?? '');
        $approvedName = '';

        $blocks = [
            [
                'label' => 'Discussed with and Agreed by:',
                'range' => ['A', 'D'],
                'name' => $discussedName,
                'title' => 'PGDH',
            ],
            [
                'label' => 'Date',
                'range' => ['E', 'F'],
                'name' => '',
                'title' => '',
            ],
            [
                'label' => 'Assessed by:',
                'range' => ['G', 'I'],
                'name' => $assessedName,
                'title' => 'PMT Chairperson',
            ],
            [
                'label' => 'Date',
                'range' => ['J', 'J'],
                'name' => '',
                'title' => '',
            ],
            [
                'label' => 'Final Rating Approved by:',
                'range' => ['K', 'O'],
                'name' => $approvedName,
                'title' => 'Governor',
            ],
        ];

        foreach ($blocks as $block) {
            [$from, $to] = $block['range'];

            $sheet->mergeCells("{$from}{$labelRow}:{$to}{$labelRow}");
            $sheet->setCellValue("{$from}{$labelRow}", $block['label']);
            $sheet->getStyle("{$from}{$labelRow}:{$to}{$labelRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("{$from}{$labelRow}:{$to}{$labelRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            $sheet->mergeCells("{$from}{$boxStartRow}:{$to}{$boxEndRow}");
            $sheet->setCellValue("{$from}{$boxStartRow}", $block['name']);
            $sheet->getStyle("{$from}{$boxStartRow}:{$to}{$boxEndRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setWrapText(true);
            $sheet->getStyle("{$from}{$boxStartRow}:{$to}{$boxEndRow}")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('D9D9D9');
            $sheet->getStyle("{$from}{$boxStartRow}:{$to}{$boxEndRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            $sheet->mergeCells("{$from}{$titleRow}:{$to}{$titleRow}");
            $sheet->setCellValue("{$from}{$titleRow}", $block['title']);
            $sheet->getStyle("{$from}{$titleRow}:{$to}{$titleRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("{$from}{$titleRow}:{$to}{$titleRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }
    }
}
