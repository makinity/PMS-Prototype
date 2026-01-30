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
        $this->writeManualHeader($sheet);
        $this->writeTableHeader($sheet);

        $currentRow = self::TABLE_START_ROW;
        $currentRow = $this->writeSection($sheet, 'core', 'A. CORE FUNCTIONS (80%)', $currentRow);
        $currentRow = $this->writeSection($sheet, 'support', 'B. SUPPORT FUNCTIONS (20%)', $currentRow);

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

        $outputs = Arr::where(
            $this->uwp['outputs'] ?? [],
            fn ($r) => Str::contains(Str::lower($r['function'] ?? ''), $type)
        );

        foreach ($outputs as $output) {
            $indicators = $output['success_indicators'] ?? [];
            if (empty($indicators)) {
                continue;
            }

            $mfoStart = $row;

            foreach ($indicators as $indicator) {
                // PPA/MFO only on first row (merge later)
                $sheet->setCellValue("A{$row}", '');
                $sheet->setCellValue("B{$row}", $indicator);

                // Allotted Budget blank (locked demo)
                $sheet->setCellValue("C{$row}", '');

                // Standards (D..H)
                $stdTexts = [];
                foreach (self::RATINGS as $rating) {
                    $col = self::STANDARDS_COLUMNS[$rating];
                    $text = (string) ($this->formatStandards($indicator, $rating) ?? '');
                    $sheet->setCellValue("{$col}{$row}", $text);
                    $sheet->getStyle("{$col}{$row}")->getAlignment()->setWrapText(true);
                    $stdTexts[] = $text;
                }

                $sheet->getStyle("B{$row}")->getAlignment()->setWrapText(true);

                // Vertical borders only (IPCR approach)
                $this->applyRowVerticalBorders($sheet, $row);

                // Auto row height (IPCR approach)
                $sheet->getRowDimension($row)->setRowHeight(
                    $this->estimateRowHeight($indicator, ...$stdTexts)
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
            if (in_array($value, ['A. CORE FUNCTIONS (80%)', 'B. SUPPORT FUNCTIONS (20%)'], true)) {
                $sheet->getStyle("A{$r}:H{$r}")
                    ->getBorders()
                    ->getBottom()
                    ->setBorderStyle(Border::BORDER_THIN);
            }
        }
    }
}
