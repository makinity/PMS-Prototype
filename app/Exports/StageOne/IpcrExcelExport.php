<?php

namespace App\Exports\StageOne;

use App\Models\Ipcr;
use App\Models\IpcrItem;
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

class IpcrExcelExport implements FromArray, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    use Exportable;

    private const RATINGS = [5, 4, 3, 2, 1];
    private const BASE_ROW_HEIGHT = 22;
    private const LINE_HEIGHT = 14;
    private const CHARS_PER_LINE = 45;

    private const STANDARDS_COLUMNS = [
        5 => 'J',
        4 => 'K',
        3 => 'L',
        2 => 'M',
        1 => 'N',
    ];

    private const TABLE_HEADER_ROW = 15;
    private const TABLE_SUBHEADER_ROW = 16;
    private const TABLE_START_ROW = 18;

    private Ipcr $ipcr;
    private array $groupedItems;
    private array $targetQuantityByOutput;
    private array $sectionLabels = [];
    private array $sectionRanges = [];

    public function __construct(Ipcr $ipcr)
    {
        $this->ipcr = $ipcr;
        $this->groupedItems = $this->groupItemsBySection();
        $this->targetQuantityByOutput = $this->buildTargetQuantityByOutput();
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
            'A' => 18.33,
            'B' => 37.89,
            'C' => 8.33,
            'D' => 9.11,
            'E' => 9.0,
            'F' => 9.0,
            'G' => 9.0,
            'H' => 9.0,
            'I' => 9.0,
            'J' => 13.44,
            'K' => 13.0,
            'L' => 13.0,
            'M' => 13.0,
            'N' => 13.0,
        ];
    }

    public function title(): string
    {
        return 'IPCR';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $this->populateTemplate($sheet);
            },
        ];
    }

    private function populateTemplate(Worksheet $sheet): void
    {
        $this->setupPage($sheet);
        $this->writeManualHeader($sheet);
        $this->writeTableHeader($sheet);

        $currentRow = self::TABLE_START_ROW;
        foreach ($this->buildSectionDefinitions() as $section) {
            $currentRow = $this->writeSection(
                $sheet,
                $section['type'],
                $section['label'],
                $currentRow,
                (float) ($section['weight_percent'] ?? 0)
            );
        }

        $lastRow = max($currentRow - 1, self::TABLE_SUBHEADER_ROW);

        $sheet->getStyle('A' . self::TABLE_HEADER_ROW . ":N{$lastRow}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_TOP)
            ->setWrapText(true);

        $this->applyVerticalBordersOnly($sheet, self::TABLE_HEADER_ROW, $lastRow);

        $sheet->getStyle('A' . self::TABLE_HEADER_ROW . ':N' . self::TABLE_SUBHEADER_ROW)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        $this->applySectionRowBorders($sheet, self::TABLE_START_ROW, $lastRow);
        $this->writeFooterBlock($sheet, $lastRow + 2);
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
        $employeeName = strtoupper((string) ($this->ipcr->employee?->name ?? 'EMPLOYEE'));
        $officeName = strtoupper((string) ($this->ipcr->office?->name ?? 'OFFICE'));
        $periodName = strtoupper((string) ($this->ipcr->performancePeriod?->name ?? 'PERIOD'));
        $reviewedBy = strtoupper((string) ($this->ipcr->opcr?->unitWorkPlan?->creator?->name ?? 'SUPERVISOR'));
        $approvedBy = strtoupper((string) ($this->ipcr->office?->head?->name ?? 'DEPARTMENT HEAD'));

        $sheet->mergeCells('A1:N1');
        $sheet->setCellValue('A1', 'INDIVIDUAL PERFORMANCE COMMITMENT AND REVIEW (IPCR)');

        $sheet->mergeCells('A3:N3');
        $sheet->setCellValue(
            'A3',
            "I {$employeeName}, of {$officeName} section of {$officeName}, commit to deliver and agree to be rated on the attainment of the following targets in accordance with the indicated measures for the period {$periodName}."
        );

        $sheet->mergeCells('A10:C10');
        $sheet->setCellValue('A10', 'Reviewed by:');

        $sheet->mergeCells('D10:F10');
        $sheet->setCellValue('D10', 'Date');

        $sheet->mergeCells('G10:L10');
        $sheet->setCellValue('G10', 'Approved by:');

        $sheet->mergeCells('M10:N10');
        $sheet->setCellValue('M10', 'Date');

        $sheet->mergeCells('A11:C11');
        $sheet->setCellValue('A11', $reviewedBy);

        $sheet->mergeCells('G11:L11');
        $sheet->setCellValue('G11', $approvedBy);

        $sheet->mergeCells('A13:C13');
        $sheet->setCellValue('A13', 'Supervisor');

        $sheet->mergeCells('G13:L13');
        $sheet->setCellValue('G13', 'Department Head');

        $sheet->getStyle('A1:N1')->getFont()->setBold(true);
        $sheet->getStyle('A1:N1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle('A3:N3')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_TOP)
            ->setWrapText(true);
    }

    private function writeTableHeader(Worksheet $sheet): void
    {
        $sheet->setCellValue('A' . self::TABLE_HEADER_ROW, 'OUTPUT');
        $sheet->setCellValue('B' . self::TABLE_HEADER_ROW, "Success Indicators\n(Measure + Target)");
        $sheet->setCellValue('C' . self::TABLE_HEADER_ROW, "6 Months Summary\nof Accomplishment");
        $sheet->setCellValue('E' . self::TABLE_HEADER_ROW, 'Rating');
        $sheet->setCellValue('I' . self::TABLE_HEADER_ROW, 'Remarks');
        $sheet->setCellValue('J' . self::TABLE_HEADER_ROW, 'Standards per Success Indicator');

        $sheet->mergeCells('A' . self::TABLE_HEADER_ROW . ':A' . self::TABLE_SUBHEADER_ROW);
        $sheet->mergeCells('B' . self::TABLE_HEADER_ROW . ':B' . self::TABLE_SUBHEADER_ROW);
        $sheet->mergeCells('C' . self::TABLE_HEADER_ROW . ':D' . self::TABLE_SUBHEADER_ROW);
        $sheet->mergeCells('E' . self::TABLE_HEADER_ROW . ':H' . self::TABLE_HEADER_ROW);
        $sheet->mergeCells('I' . self::TABLE_HEADER_ROW . ':I' . self::TABLE_SUBHEADER_ROW);
        $sheet->mergeCells('J' . self::TABLE_HEADER_ROW . ':N' . self::TABLE_HEADER_ROW);

        $subHeaders = [
            'E' => 'Q',
            'F' => 'E',
            'G' => 'T',
            'H' => 'A',
            'J' => '5',
            'K' => '4',
            'L' => '3',
            'M' => '2',
            'N' => '1',
        ];

        foreach ($subHeaders as $col => $text) {
            $sheet->setCellValue($col . self::TABLE_SUBHEADER_ROW, $text);
        }

        $headerRange = 'A' . self::TABLE_HEADER_ROW . ':N' . self::TABLE_SUBHEADER_ROW;

        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('D9D9D9');

        $sheet->getRowDimension(self::TABLE_HEADER_ROW)->setRowHeight(30);
        $sheet->getRowDimension(self::TABLE_SUBHEADER_ROW)->setRowHeight(20);
    }

    private function writeSection(Worksheet $sheet, string $type, string $label, int $startRow, float $weightPercent = 0): int
    {
        $sheet->setCellValue("A{$startRow}", $label);
        $sheet->mergeCells("A{$startRow}:N{$startRow}");

        $sheet->getStyle("A{$startRow}:N{$startRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$startRow}:N{$startRow}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('E2E8F0');

        $sheet->getStyle("A{$startRow}:N{$startRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle("A{$startRow}:N{$startRow}")
            ->getBorders()
            ->getBottom()
            ->setBorderStyle(Border::BORDER_THIN);

        $row = $startRow + 1;

        $nextRow = $this->writeIndicatorsFlat($sheet, $row, $this->groupedItems[$type] ?? []);

        $this->sectionRanges[] = [
            'label' => $label,
            'start_row' => $row,
            'end_row' => max($row, $nextRow - 1),
            'weight_percent' => $weightPercent,
        ];

        return $nextRow;
    }

    private function writeIndicatorsFlat(Worksheet $sheet, int $row, array $groupedOutputs): int
    {
        foreach ($groupedOutputs as $outputGroup) {
            $items = $outputGroup['items'] ?? [];
            if (empty($items)) {
                continue;
            }

            $startRow = $row;

            foreach ($items as $index => $item) {
                $sheet->setCellValue("A{$row}", $index === 0 ? ($outputGroup['output'] ?? '') : '');
                $sheet->setCellValue("B{$row}", $this->buildIndicatorCellText($item));

                $sheet->setCellValue("C{$row}", '');
                $sheet->setCellValue("D{$row}", '');
                $sheet->mergeCells("C{$row}:D{$row}");

                $sheet->setCellValue("E{$row}", '');
                $sheet->setCellValue("F{$row}", '');
                $sheet->setCellValue("G{$row}", '');
                $sheet->setCellValue(
                    "H{$row}",
                    '=IF(COUNTA(E' . $row . ':G' . $row . ')=0,"",AVERAGE(E' . $row . ':G' . $row . '))'
                );

                $sheet->setCellValue("I{$row}", '');

                $stdTexts = [];
                foreach (self::RATINGS as $rating) {
                    $col = self::STANDARDS_COLUMNS[$rating];
                    $text = $this->formatStdBlock($item, $rating);
                    $sheet->setCellValue("{$col}{$row}", $text);
                    $sheet->getStyle("{$col}{$row}")->getAlignment()->setWrapText(true);
                    $stdTexts[] = $text;
                }

                $sheet->getStyle("B{$row}")->getAlignment()->setWrapText(true);
                $sheet->getStyle("E{$row}:H{$row}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $this->applyRowVerticalBorders($sheet, $row);

                $sheet->getRowDimension($row)->setRowHeight(
                    $this->estimateRowHeight($this->buildIndicatorCellText($item), ...$stdTexts)
                );

                $row++;
            }

            $endRow = $row - 1;

            if ($endRow > $startRow) {
                $sheet->mergeCells("A{$startRow}:A{$endRow}");
                $sheet->getStyle("A{$startRow}:A{$endRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                    ->setVertical(Alignment::VERTICAL_TOP)
                    ->setWrapText(true);
            }

            $sheet->getStyle("A{$endRow}:N{$endRow}")
                ->getBorders()
                ->getBottom()
                ->setBorderStyle(Border::BORDER_THIN);
        }

        return $row;
    }

    private function groupItemsBySection(): array
    {
        $grouped = [
            'core' => [],
            'support' => [],
            'custom' => [],
        ];

        foreach ($this->ipcr->items as $item) {
            $type = $this->normalizeFunctionType((string) ($item->function_type ?? ''));
            $outputTitle = trim((string) ($item->output_title ?? ''));
            if ($outputTitle === '') {
                $outputTitle = 'Untitled Output';
            }

            if (!isset($grouped[$type][$outputTitle])) {
                $grouped[$type][$outputTitle] = [
                    'output' => $outputTitle,
                    'items' => [],
                ];
            }

            $grouped[$type][$outputTitle]['items'][] = $item;
        }

        return [
            'core' => array_values($grouped['core']),
            'support' => array_values($grouped['support']),
            'custom' => array_values($grouped['custom']),
        ];
    }

    private function buildIndicatorCellText(IpcrItem $item): string
    {
        $indicatorText = trim((string) ($item->indicator_text ?? ''));
        $outputTitle = trim((string) ($item->output_title ?? ''));
        $targetQuantity = is_numeric($item->target_quantity ?? null)
            ? (float) $item->target_quantity
            : ($outputTitle !== '' ? ($this->targetQuantityByOutput[$outputTitle] ?? null) : null);

        if (!is_numeric($targetQuantity)) {
            return $indicatorText;
        }

        return $indicatorText . "\nTarget: " . $this->formatTargetQuantity((float) $targetQuantity);
    }

    private function formatStdBlock(IpcrItem $item, int $rating): string
    {
        $entry = $this->extractStandardsPerRating($item->standards_payload, $rating);

        $q = $this->formatDimension($entry['q']);
        $e = $this->formatDimension($entry['e']);
        $t = $this->formatDimension($entry['t']);

        return implode("\n", [
            "Q = {$q}",
            "E = {$e}",
            "T = {$t}",
        ]);
    }

    private function extractStandardsPerRating(mixed $payload, int $rating): array
    {
        if (is_string($payload)) {
            $decoded = json_decode($payload, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $payload = $decoded;
            }
        }

        if (!is_array($payload)) {
            return ['q' => [], 'e' => [], 't' => []];
        }

        $bucket = $payload[(string) $rating] ?? $payload[$rating] ?? [];
        if (!is_array($bucket)) {
            $bucket = [];
        }

        return [
            'q' => $this->normalizeStandardsValue($bucket['Q'] ?? $bucket['q'] ?? []),
            'e' => $this->normalizeStandardsValue($bucket['E'] ?? $bucket['e'] ?? []),
            't' => $this->normalizeStandardsValue($bucket['T'] ?? $bucket['t'] ?? []),
        ];
    }

    private function normalizeStandardsValue(mixed $value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        if (is_array($value)) {
            return array_values(array_filter(
                array_map(static fn ($entry) => trim((string) $entry), $value),
                static fn ($entry) => $entry !== ''
            ));
        }

        $stringValue = trim((string) $value);

        return $stringValue === '' ? [] : [$stringValue];
    }

    private function formatDimension(array $values): string
    {
        if (empty($values)) {
            return '--';
        }

        return implode('; ', $values);
    }

    private function normalizeFunctionType(string $type): string
    {
        $normalized = strtolower(trim($type));

        if ($normalized === 'support') {
            return 'support';
        }

        if ($normalized === 'core') {
            return 'core';
        }

        return 'custom';
    }

    private function estimateRowHeight(string ...$cells): float
    {
        $maxLines = 1;

        foreach ($cells as $text) {
            $text = trim((string) $text);
            if ($text === '') {
                continue;
            }

            $lines = substr_count($text, "\n") + 1;
            $wrapped = (int) ceil(mb_strlen($text) / self::CHARS_PER_LINE);

            $maxLines = max($maxLines, $lines, $wrapped);
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
        foreach (range('A', 'N') as $col) {
            $sheet->getStyle("{$col}{$row}")
                ->getBorders()
                ->getLeft()
                ->setBorderStyle(Border::BORDER_THIN);

            $sheet->getStyle("{$col}{$row}")
                ->getBorders()
                ->getRight()
                ->setBorderStyle(Border::BORDER_THIN);
        }
    }

    private function applySectionRowBorders(Worksheet $sheet, int $fromRow, int $toRow): void
    {
        for ($r = $fromRow; $r <= $toRow; $r++) {
            $value = trim((string) $sheet->getCell("A{$r}")->getValue());
            if (in_array($value, $this->sectionLabels, true)) {
                $sheet->getStyle("A{$r}:N{$r}")
                    ->getBorders()
                    ->getBottom()
                    ->setBorderStyle(Border::BORDER_THIN);
            }
        }
    }

    private function buildSectionDefinitions(): array
    {
        $types = [];
        foreach ($this->groupedItems as $type => $rows) {
            if (!empty($rows)) {
                $types[$type] = true;
            }
        }

        $ordered = [];
        foreach (['core', 'support'] as $preferred) {
            if (isset($types[$preferred])) {
                $ordered[] = $preferred;
                unset($types[$preferred]);
            }
        }

        foreach (array_keys($types) as $remaining) {
            $ordered[] = $remaining;
        }

        $labels = [];
        $sections = [];
        foreach ($ordered as $index => $type) {
            $label = $this->buildSectionLabel($type, $index);
            $labels[] = $label;
            $sections[] = [
                'type' => $type,
                'label' => $label,
                'weight_percent' => $this->resolveSectionWeightPercent($type),
            ];
        }

        $this->sectionLabels = $labels;

        return $sections;
    }

    private function buildSectionLabel(string $type, int $index): string
    {
        $prefix = chr(65 + $index);
        $label = match ($type) {
            'core' => 'CORE FUNCTIONS (80%)',
            'support' => 'SUPPORT FUNCTIONS (20%)',
            'custom' => 'CUSTOM FUNCTIONS',
            default => strtoupper(str_replace('_', ' ', $type)) . ' FUNCTIONS',
        };

        return "{$prefix}. {$label}";
    }

    private function writeFooterBlock(Worksheet $sheet, int $startRow): void
    {
        $row = $startRow;
        $weightedValueCells = [];

        foreach ($this->sectionRanges as $section) {
            $sheet->mergeCells("A{$row}:L{$row}");
            $sheet->mergeCells("M{$row}:N{$row}");
            $sheet->setCellValue("A{$row}", $this->buildWeightedAverageLabel((string) ($section['label'] ?? '')));

            $weightPercent = (float) ($section['weight_percent'] ?? 0);
            $start = (int) ($section['start_row'] ?? 0);
            $end = (int) ($section['end_row'] ?? 0);

            if ($start > 0 && $end >= $start && $weightPercent > 0) {
                $sheet->setCellValue(
                    "M{$row}",
                    sprintf('=IFERROR(AVERAGE(H%d:H%d)*%.4F,"")', $start, $end, $weightPercent / 100)
                );
            } else {
                $sheet->setCellValue("M{$row}", '');
            }

            $weightedValueCells[] = "M{$row}";
            $row++;
        }

        $overallRow = $row;
        $sheet->mergeCells("A{$overallRow}:L{$overallRow}");
        $sheet->mergeCells("M{$overallRow}:N{$overallRow}");
        $sheet->setCellValue("A{$overallRow}", 'OVERALL RATING');
        $sheet->setCellValue(
            "M{$overallRow}",
            empty($weightedValueCells) ? '' : '=' . implode('+', $weightedValueCells)
        );

        $row++;
        $sheet->mergeCells("A{$row}:L{$row}");
        $sheet->mergeCells("M{$row}:N{$row}");
        $sheet->setCellValue("A{$row}", 'ADJECTIVAL RATING');
        $sheet->setCellValue(
            "M{$row}",
            '=IF(M' . $overallRow . '="","",IF(M' . $overallRow . '>=4.5,"Outstanding",IF(M' . $overallRow . '>=3.5,"Very Satisfactory",IF(M' . $overallRow . '>=2.5,"Satisfactory",IF(M' . $overallRow . '>=1.5,"Unsatisfactory","Poor")))))'
        );

        $sheet->getStyle("A{$startRow}:N{$row}")->getFont()->setBold(true);
        $sheet->getStyle("A{$startRow}:N{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("A{$startRow}:L{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("M{$startRow}:N{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row += 2;
        $sheet->mergeCells("A{$row}:N{$row}");
        $sheet->setCellValue("A{$row}", 'Comments and Recommendations for Development Purposes');
        $sheet->getStyle("A{$row}:N{$row}")->getFont()->setBold(true);
        $sheet->getStyle("A{$row}:N{$row}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('F4B6FF');
        $sheet->getStyle("A{$row}:N{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("A{$row}:N{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $row++;
        $sheet->mergeCells("A{$row}:N" . ($row + 1));
        $sheet->setCellValue("A{$row}", '');
        $sheet->getStyle("A{$row}:N" . ($row + 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getRowDimension($row)->setRowHeight(22);
        $sheet->getRowDimension($row + 1)->setRowHeight(22);

        $row += 3;
        $signatureBlocks = $this->resolveSignatureBlocks();

        foreach ($signatureBlocks as $block) {
            [$fromCol, $toCol] = explode(':', $block['range']);

            $sheet->mergeCells("{$fromCol}{$row}:{$toCol}{$row}");
            $sheet->setCellValue("{$fromCol}{$row}", $block['label']);
            $sheet->mergeCells("{$fromCol}" . ($row + 1) . ":{$toCol}" . ($row + 1));
            $sheet->setCellValue("{$fromCol}" . ($row + 1), $block['name']);
            $sheet->mergeCells("{$fromCol}" . ($row + 2) . ":{$toCol}" . ($row + 2));
            $sheet->setCellValue("{$fromCol}" . ($row + 2), $block['title']);
            $sheet->mergeCells("{$fromCol}" . ($row + 3) . ":{$toCol}" . ($row + 3));
            $sheet->setCellValue("{$fromCol}" . ($row + 3), 'Date');

            $sheet->getStyle("{$fromCol}{$row}:{$toCol}" . ($row + 3))
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("{$fromCol}{$row}:{$toCol}" . ($row + 3))
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
        }
    }

    private function buildWeightedAverageLabel(string $sectionLabel): string
    {
        $cleaned = preg_replace('/^[A-Z]\.\s*/', '', trim($sectionLabel)) ?? trim($sectionLabel);
        $normalized = ucwords(strtolower($cleaned));

        return "Weighted Average Rating for {$normalized}";
    }

    private function resolveSectionWeightPercent(string $type): float
    {
        foreach ($this->groupedItems[$type] ?? [] as $group) {
            $firstItem = $group['items'][0] ?? null;
            $weight = $firstItem?->uwpFunction?->weight_percent ?? null;
            if (is_numeric($weight)) {
                return (float) $weight;
            }
        }

        return match ($type) {
            'core' => 80.0,
            'support' => 20.0,
            default => 0.0,
        };
    }

    private function resolveSignatureBlocks(): array
    {
        return [
            [
                'range' => 'A:D',
                'label' => 'Discussed with and Agreed by:',
                'name' => strtoupper((string) ($this->ipcr->employee?->name ?? '')),
                'title' => strtoupper((string) ($this->ipcr->employee?->position ?? 'Employee')),
            ],
            [
                'range' => 'E:I',
                'label' => 'Assessed by:',
                'name' => strtoupper((string) ($this->ipcr->opcr?->unitWorkPlan?->creator?->name ?? '')),
                'title' => 'SUPERVISOR',
            ],
            [
                'range' => 'J:N',
                'label' => 'Approved by:',
                'name' => strtoupper((string) ($this->ipcr->office?->head?->name ?? '')),
                'title' => 'DEPARTMENT HEAD',
            ],
        ];
    }

    private function buildTargetQuantityByOutput(): array
    {
        $this->ipcr->loadMissing([
            'unitWorkPlan.uwpFunctions.mfos',
            'opcr.unitWorkPlan.uwpFunctions.mfos',
        ]);

        $targetQuantities = [];
        $unitWorkPlan = $this->ipcr->unitWorkPlan ?? $this->ipcr->opcr?->unitWorkPlan;

        foreach ($unitWorkPlan?->uwpFunctions ?? [] as $function) {
            foreach ($function->mfos ?? [] as $mfo) {
                $outputTitle = trim((string) ($mfo->title ?? ''));
                if ($outputTitle === '' || !is_numeric($mfo->target_quantity)) {
                    continue;
                }

                $targetQuantities[$outputTitle] = (float) $mfo->target_quantity;
            }
        }

        return $targetQuantities;
    }

    private function formatTargetQuantity(float $quantity): string
    {
        if (fmod($quantity, 1.0) === 0.0) {
            return (string) (int) $quantity;
        }

        return rtrim(rtrim(number_format($quantity, 2, '.', ''), '0'), '.');
    }
}
