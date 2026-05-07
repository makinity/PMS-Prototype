<?php

namespace App\Exports\StageOne;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
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

class UwpExcelExport implements FromArray, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    use Exportable;

    private const RATINGS = [5, 4, 3, 2, 1];

    private const STANDARDS_COLUMNS = [
        5 => 'D',
        4 => 'E',
        3 => 'F',
        2 => 'G',
        1 => 'H',
    ];

    private const TABLE_HEADER_ROW = 17;
    private const TABLE_RATING_ROW = 18;
    private const TABLE_START_ROW = self::TABLE_RATING_ROW + 1;

    // Match the IPCR “approach” (auto height + vertical-only gridlines + group separators)
    private const BASE_ROW_HEIGHT = 22;
    private const LINE_HEIGHT = 14;
    private const CHARS_PER_LINE = 55; // tuned for UWP widths (A..H)

    private array $uwp;
    private array $standards;
    private array $sectionLabels = [];

    public function __construct(array $uwp, array $standards)
    {
        $this->uwp = $uwp;
        $this->standards = $standards;
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
            'A' => 32,
            'B' => 40,
            'C' => 18,
            'D' => 30,
            'E' => 30,
            'F' => 30,
            'G' => 30,
            'H' => 30,
        ];
    }

    public function title(): string
    {
        return 'Unit Work Plan';
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
            $currentRow = $this->writeSection($sheet, $section['type'], $section['label'], $currentRow);
        }

        $lastRow = max($currentRow - 1, self::TABLE_RATING_ROW);

        $tableRange = 'A' . self::TABLE_HEADER_ROW . ":H{$lastRow}";
        $sheet->getStyle($tableRange)->getAlignment()
            ->setWrapText(true)
            ->setVertical(Alignment::VERTICAL_TOP);

        $sheet->getStyle('A' . self::TABLE_HEADER_ROW . ':H' . self::TABLE_RATING_ROW)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // - Data rows: vertical borders only (no per-row horizontal lines)
        $this->applyVerticalBordersOnly($sheet, self::TABLE_START_ROW, $lastRow);

        // - Section label rows: keep bottom separator line
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
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'UNIT WORK PLAN (UWP)');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $period = $this->uwp['period'] ?? '';
        $period = preg_replace('/\s*[-–—]+\s*/u', ' ' . "\u{2013}" . ' ', $period);
        $sheet->setCellValue('D3', 'Period:');
        $sheet->getStyle('D3')->getFont()->setBold(true);
        $sheet->setCellValue('E3', trim($period));
        $sheet->getStyle('E3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A5', 'Office / Unit:');
        $sheet->getStyle('A5')->getFont()->setBold(true);
        $sheet->setCellValue('B5', $this->uwp['office'] ?? '');

        $sheet->setCellValue('F5', 'Supervisor:');
        $sheet->getStyle('F5')->getFont()->setBold(true);$sheet->getStyle('F5')->getFont()->setBold(true);
        $sheet->setCellValue('G5', $this->uwp['supervisor'] ?? '');

        $sheet->setCellValue('A6', 'Department Head:');
        $sheet->getStyle('A6')->getFont()->setBold(true);
        $sheet->setCellValue('B6', $this->uwp['dept_head'] ?? '');

        $sheet->getStyle('A5:H6')->getAlignment()
            ->setWrapText(true)
            ->setVertical(Alignment::VERTICAL_CENTER);
    }

    private function writeTableHeader(Worksheet $sheet): void
    {
        $headers = [
            'A' => 'PPA / MFO',
            'B' => 'Success Indicators',
            'C' => 'Allotted Budget',
        ];

        foreach ($headers as $column => $text) {
            $cell = "{$column}" . self::TABLE_HEADER_ROW;
            $sheet->setCellValue($cell, $text);
        }

        $sheet->setCellValue('D' . self::TABLE_HEADER_ROW, 'Standards per Success Indicator');
        $sheet->mergeCells('D' . self::TABLE_HEADER_ROW . ':H' . self::TABLE_HEADER_ROW);

        // Ratings row
        foreach (self::RATINGS as $rating) {
            $column = self::STANDARDS_COLUMNS[$rating];
            $sheet->setCellValue("{$column}" . self::TABLE_RATING_ROW, (string) $rating);
        }

        // ✅ Style header like IPCR (gray fill, centered, bold)
        $headerRange = 'A' . self::TABLE_HEADER_ROW . ':H' . self::TABLE_RATING_ROW;

        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('D9D9D9');

        $sheet->getRowDimension(self::TABLE_HEADER_ROW)->setRowHeight(28);
        $sheet->getRowDimension(self::TABLE_RATING_ROW)->setRowHeight(20);
    }

    private function writeSection(Worksheet $sheet, string $type, string $label, int $startRow): int
    {
        $sheet->setCellValue("A{$startRow}", $label);
        $sheet->mergeCells("A{$startRow}:H{$startRow}");

        $sheet->getStyle("A{$startRow}:H{$startRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$startRow}:H{$startRow}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('E2E8F0');

        $sheet->getStyle("A{$startRow}:H{$startRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // Section separator (keep)
        $sheet->getStyle("A{$startRow}:H{$startRow}")
            ->getBorders()
            ->getBottom()
            ->setBorderStyle(Border::BORDER_THIN);

        $row = $startRow + 1;

        $outputs = Arr::where($this->uwp['outputs'] ?? [], function ($row) use ($type) {
            $normalized = $this->normalizeFunctionType($row['function_type'] ?? null, $row['function'] ?? null);
            return $normalized === $type;
        });

        foreach ($outputs as $output) {
            $indicators = $output['success_indicators'] ?? [];
            if (empty($indicators)) {
                continue;
            }

            $mfoStart = $row;

            foreach ($indicators as $indicator) {
                $indicatorText = is_array($indicator)
                    ? trim((string) ($indicator['text'] ?? $indicator['indicator_text'] ?? ''))
                    : trim((string) $indicator);
                $indicatorTargetSummary = is_array($indicator)
                    ? trim((string) ($indicator['target_summary'] ?? ''))
                    : '';
                $indicatorCellText = $indicatorText;
                if ($indicatorCellText !== '' && $indicatorTargetSummary !== '') {
                    $indicatorCellText .= "\nTarget: {$indicatorTargetSummary}";
                }

                // PPA/MFO only on first row (merge later)
                $sheet->setCellValue("A{$row}", '');
                $sheet->setCellValue("B{$row}", $indicatorCellText);

                // Allotted Budget blank (locked demo)
                $sheet->setCellValue("C{$row}", '');

                // Standards (D..H)
                $stdTexts = [];
                foreach (self::RATINGS as $rating) {
                    $col = self::STANDARDS_COLUMNS[$rating];
                    $text = (string) ($this->formatStandards($indicatorText, $rating) ?? '');
                    $sheet->setCellValue("{$col}{$row}", $text);
                    $sheet->getStyle("{$col}{$row}")->getAlignment()->setWrapText(true);
                    $stdTexts[] = $text;
                }

                $sheet->getStyle("B{$row}")->getAlignment()->setWrapText(true);

                // Vertical borders only (IPCR approach)
                $this->applyRowVerticalBorders($sheet, $row);

                // Auto row height (IPCR approach)
                $sheet->getRowDimension($row)->setRowHeight(
                    $this->estimateRowHeight($indicatorCellText, ...$stdTexts)
                );

                $row++;
            }

            $mfoEnd = $row - 1;

            // ✅ Merge A for MFO group + set value once
            if ($mfoEnd >= $mfoStart) {
                if ($mfoEnd > $mfoStart) {
                    $sheet->mergeCells("A{$mfoStart}:A{$mfoEnd}");
                }
                $sheet->setCellValue("A{$mfoStart}", (string) ($output['mfo'] ?? ''));

                $sheet->getStyle("A{$mfoStart}:A{$mfoEnd}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                    ->setVertical(Alignment::VERTICAL_TOP)
                    ->setWrapText(true);

                // Ensure A column also has vertical borders for all rows in the group
                for ($r = $mfoStart; $r <= $mfoEnd; $r++) {
                    $this->applyRowVerticalBorders($sheet, $r);
                }

                // ✅ Group separator line at bottom only (keep “green line feel”)
                $sheet->getStyle("A{$mfoEnd}:H{$mfoEnd}")
                    ->getBorders()
                    ->getBottom()
                    ->setBorderStyle(Border::BORDER_THIN);
            }
        }

        return $row;
    }

    private function formatStandards(string $indicator, int $rating): ?string
    {
        $lines = [];
        foreach (['q' => 'Q', 'e' => 'E', 't' => 'T'] as $key => $label) {
            $values = Arr::wrap($this->standards[$indicator][$rating][$key] ?? []);
            $values = array_filter($values, fn ($value) => $value !== '' && $value !== null);
            if (empty($values)) {
                continue;
            }
            $lines[] = "{$label}: " . implode('; ', $values);
        }

        return empty($lines) ? null : implode("\n", $lines);
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
        foreach (range('A', 'H') as $col) {
            $style = $sheet->getStyle("{$col}{$row}")->getBorders();

            $style->getLeft()->setBorderStyle(Border::BORDER_THIN);
            $style->getRight()->setBorderStyle(Border::BORDER_THIN);

            // IMPORTANT: don't touch top/bottom here (so we don't create the “red X” lines)
        }
    }

    private function applySectionRowBorders(Worksheet $sheet, int $fromRow, int $toRow): void
    {
        for ($r = $fromRow; $r <= $toRow; $r++) {
            $value = trim((string) $sheet->getCell("A{$r}")->getValue());
            if (in_array($value, $this->sectionLabels, true)) {
                $sheet->getStyle("A{$r}:H{$r}")
                    ->getBorders()
                    ->getBottom()
                    ->setBorderStyle(Border::BORDER_THIN);
            }
        }
    }

    private function buildSectionDefinitions(): array
    {
        $types = [];
        foreach ($this->uwp['outputs'] ?? [] as $output) {
            $type = $this->normalizeFunctionType($output['function_type'] ?? null, $output['function'] ?? null);
            $types[$type] = true;
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
            $sections[] = ['type' => $type, 'label' => $label];
        }

        $this->sectionLabels = $labels;

        return $sections;
    }

    private function writeFooterBlock(Worksheet $sheet, int $startRow): void
    {
        $row = $startRow;

        foreach ($this->buildSectionDefinitions() as $section) {
            $label = trim((string) ($section['label'] ?? ''));
            $label = preg_replace('/^[A-Z]\.\s*/', '', $label ?? '') ?: 'FUNCTIONS';

            $sheet->mergeCells("A{$row}:F{$row}");
            $sheet->mergeCells("G{$row}:H{$row}");
            $sheet->setCellValue("A{$row}", "Weighted Average Rating for {$label}");
            $sheet->getStyle("A{$row}:H{$row}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A{$row}:H{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $row++;
        }

        foreach (['OVERALL RATING', 'ADJECTIVAL RATING'] as $label) {
            $sheet->mergeCells("A{$row}:F{$row}");
            $sheet->mergeCells("G{$row}:H{$row}");
            $sheet->setCellValue("A{$row}", $label);
            $sheet->getStyle("A{$row}:H{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:H{$row}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A{$row}:H{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $row++;
        }

        $labelRow = $row;
        $boxStartRow = $row + 1;
        $boxEndRow = $row + 3;
        $titleRow = $row + 4;

        foreach ($this->resolveSignatureBlocks() as $block) {
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

    private function normalizeFunctionType(?string $type, ?string $fallback): string
    {
        $normalized = strtolower(trim((string) $type));
        if (in_array($normalized, ['core', 'support', 'custom'], true)) {
            return $normalized;
        }

        $fallbackText = strtolower(trim((string) $fallback));
        if ($fallbackText !== '') {
            if (Str::contains($fallbackText, 'support')) {
                return 'support';
            }
            if (Str::contains($fallbackText, 'core')) {
                return 'core';
            }
        }

        return 'custom';
    }

    private function resolveSignatureBlocks(): array
    {
        return [
            [
                'label' => 'Prepared by:',
                'range' => ['A', 'B'],
                'name' => (string) ($this->uwp['supervisor'] ?? ''),
                'title' => 'Supervisor',
            ],
            [
                'label' => 'Discussed with and Agreed by:',
                'range' => ['C', 'D'],
                'name' => (string) ($this->uwp['dept_head'] ?? ''),
                'title' => 'PGDH',
            ],
            [
                'label' => 'Date',
                'range' => ['E', 'E'],
                'name' => '',
                'title' => '',
            ],
            [
                'label' => 'Assessed by:',
                'range' => ['F', 'G'],
                'name' => (string) ($this->uwp['pmt_chairperson'] ?? ''),
                'title' => 'PMT Chairperson',
            ],
            [
                'label' => 'Final Rating Approved by:',
                'range' => ['H', 'H'],
                'name' => (string) ($this->uwp['governor'] ?? ''),
                'title' => 'Governor',
            ],
        ];
    }
}
