<?php

namespace App\Exports\StageOne;

use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
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

    // ✅ Same “approach” as UWP (auto height + vertical-only borders + group separators)
    private const BASE_ROW_HEIGHT = 22;
    private const LINE_HEIGHT = 14;
    private const CHARS_PER_LINE = 42;

    private array $opcr;
    private array $standards;

    public function __construct(array $opcr, array $standards)
    {
        $this->opcr = $opcr;
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
        // ✅ Make standards columns wider (match UWP “feel”)
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
                $sheet = $event->sheet->getDelegate();
                $this->populateTemplate($sheet);
            },
        ];
    }

    private function populateTemplate(Worksheet $sheet): void
    {
        $this->setupPage($sheet);

        // ✅ UWP look: these “horizontal lines” come from Excel gridlines
        // (and merged MFO cells hide gridlines inside)
        $sheet->setShowGridlines(true);

        $this->writeManualHeader($sheet);
        $this->writeTableHeader($sheet);

        $currentRow = self::TABLE_START_ROW;
        $currentRow = $this->writeSection($sheet, 'core', 'A. CORE FUNCTIONS (80%)', $currentRow, true);
        $currentRow = $this->writeSection($sheet, 'support', 'C. SUPPORT FUNCTIONS (20%)', $currentRow, false);

        $lastRow = max($currentRow - 1, self::TABLE_SUBHEADER_ROW);

        // Global wrap + top vertical align (same as UWP)
        $range = "A" . self::TABLE_HEADER_ROW . ":O{$lastRow}";
        $sheet->getStyle($range)->getAlignment()
            ->setWrapText(true)
            ->setVertical(Alignment::VERTICAL_TOP);

        // ✅ Header block boxed (ONLY header rows)
        $sheet->getStyle("A" . self::TABLE_HEADER_ROW . ":O" . self::TABLE_SUBHEADER_ROW)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // ✅ Data rows: vertical borders only (NO per-row horizontal lines)
        $this->applyVerticalBordersOnly($sheet, self::TABLE_START_ROW, $lastRow);

        // ✅ Section label rows: keep bottom separator line
        $this->applySectionRowBorders($sheet, self::TABLE_START_ROW, $lastRow);
    }

    private function setupPage(Worksheet $sheet): void
    {
        // ❌ This was breaking the UWP look (you want gridlines)
        // $sheet->setShowGridlines(false);

        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_LEGAL);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
    }

    private function writeManualHeader(Worksheet $sheet): void
    {
        $sheet->mergeCells('A1:O1');
        $sheet->setCellValue('A1', 'OFFICE PERFORMANCE COMMITMENT AND REVIEW (OPCR)');

        $sheet->mergeCells('A2:O2');
        $sheet->setCellValue('A2', 'January – June 2026');

        $sheet->setCellValue('A4', 'Office / Unit:');
        $sheet->setCellValue('B4', 'Revenue Collection Unit');

        $sheet->setCellValue('A5', 'Office Head:');
        $sheet->setCellValue('B5', 'Carlo D. Beray');

        $sheet->setCellValue('A6', 'Department Head:');
        $sheet->setCellValue('B6', 'Dept-head');

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

        return $this->writeIndicatorsLikeUwp($sheet, $row, $this->opcr[$type] ?? []);
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

                // ✅ Support both formats:
                // - old: 'indicator string'
                // - new: ['text' => '...', 'employee' => '...']
                $indicatorText = is_array($indicator)
                    ? (string) ($indicator['text'] ?? '')
                    : (string) $indicator;

                $employee = is_array($indicator)
                    ? (string) ($indicator['employee'] ?? '')
                    : (string) ($item['employee'] ?? ''); // fallback if you ever keep MFO-level employee

                $sheet->setCellValue("A{$row}", '');
                $sheet->setCellValue("B{$row}", $indicatorText);
                $sheet->setCellValue("C{$row}", '');
                $sheet->setCellValue("D{$row}", $employee); // ✅ NOW PER INDICATOR
                $sheet->setCellValue("E{$row}", '');

                foreach (range('F', 'I') as $col) {
                    $sheet->setCellValue("{$col}{$row}", '');
                }

                $sheet->setCellValue("J{$row}", '');

                $stdTexts = [];
                foreach (self::RATINGS as $rating) {
                    $col = self::STANDARDS_COLUMNS[$rating];
                    $text = $this->formatStdBlock($indicatorText, $rating); // ✅ use text for standards lookup
                    $sheet->setCellValue("{$col}{$row}", $text);
                    $sheet->getStyle("{$col}{$row}")->getAlignment()->setWrapText(true);
                    $stdTexts[] = $text;
                }

                $sheet->getStyle("B{$row}")->getAlignment()->setWrapText(true);

                // ✅ vertical borders only (A..O), NO top/bottom
                $this->applyRowVerticalBorders($sheet, $row);

                // Auto row height
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

                // ✅ still no bottom border in A (keeps MFO clean)
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
        $values = array_filter($values, fn ($v) => $v !== null && $v !== '');
        return empty($values) ? '—' : implode('; ', array_map(fn ($v) => trim((string) $v), $values));
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
            $b = $sheet->getStyle("{$col}{$row}")->getBorders();
            $b->getLeft()->setBorderStyle(Border::BORDER_THIN);
            $b->getRight()->setBorderStyle(Border::BORDER_THIN);
            // IMPORTANT: don't touch top/bottom (same as UWP)
        }
    }

    private function applySectionRowBorders(Worksheet $sheet, int $fromRow, int $toRow): void
    {
        for ($r = $fromRow; $r <= $toRow; $r++) {
            $value = trim((string) $sheet->getCell("A{$r}")->getValue());
            if (in_array($value, ['A. CORE FUNCTIONS (80%)', 'REVENUE', 'C. SUPPORT FUNCTIONS (20%)'], true)) {
                $sheet->getStyle("A{$r}:O{$r}")
                    ->getBorders()
                    ->getBottom()
                    ->setBorderStyle(Border::BORDER_THIN);
            }
        }
    }
}
