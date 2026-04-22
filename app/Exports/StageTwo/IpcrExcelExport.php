<?php

namespace App\Exports\StageTwo;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
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

    private array $ipcr;
    private array $standards;
    private array $valuesByIndicator;
    private array $meta;
    private array $sectionLabels = [];

    public function __construct(array $ipcr, array $standards, array $valuesByIndicator = [], array $meta = [])
    {
        $this->ipcr = $ipcr;
        $this->standards = $standards;
        $this->valuesByIndicator = $valuesByIndicator;
        $this->meta = $meta;
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

        // Important: Stage 1 look uses borders more than Excel gridlines.
        $sheet->setShowGridlines(true);

        $this->writeManualHeader($sheet);
        $this->writeTableHeader($sheet);

        $currentRow = self::TABLE_START_ROW;
        foreach ($this->resolveSections() as $index => $section) {
            $sectionLabel = $this->prefixSectionLabel($index, (string) ($section['label'] ?? 'FUNCTIONS'));
            $currentRow = $this->writeSection(
                $sheet,
                $sectionLabel,
                $currentRow,
                is_array($section['items'] ?? null) ? $section['items'] : []
            );
        }

        $lastRow = max($currentRow - 1, self::TABLE_SUBHEADER_ROW);

        // Wrap + top-align the whole table area like Stage 1
        $sheet->getStyle("A" . self::TABLE_HEADER_ROW . ":N{$lastRow}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_TOP)
            ->setWrapText(true);

        // Vertical borders only across the whole table block (Stage 1 style)
        $this->applyVerticalBordersOnly($sheet, self::TABLE_HEADER_ROW, $lastRow);

        // Header block boxed
        $sheet->getStyle("A" . self::TABLE_HEADER_ROW . ":N" . self::TABLE_SUBHEADER_ROW)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Ensure section rows have bottom border “green line feel”
        $this->applySectionRowBorders($sheet, self::TABLE_START_ROW, $lastRow);
    }

    private function setupPage(Worksheet $sheet): void
    {
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_LEGAL);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
    }

    /**
     * Keep SAME header layout as Stage 1 target.
     * If you want this dynamic later, swap hardcoded strings with payload values.
     */
    private function writeManualHeader(Worksheet $sheet): void
    {
        $employeeName = trim((string) ($this->meta['employee'] ?? '—'));
        $officeName = trim((string) ($this->meta['office'] ?? '—'));
        $periodLabel = trim((string) ($this->meta['period'] ?? 'the period'));

        $sheet->mergeCells('A1:N1');
        $sheet->setCellValue('A1', 'INDIVIDUAL PERFORMANCE COMMITMENT AND REVIEW (IPCR)');

        $sheet->mergeCells('A3:N3');
        $sheet->setCellValue(
            'A3',
            "I {$employeeName}, of {$officeName}, commit to deliver and agree to be rated on the attainment of the following targets in accordance with the indicated measures for the period {$periodLabel}."
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
        $sheet->setCellValue('A11', 'CARLO D. BERAY');

        $sheet->mergeCells('G11:L11');
        $sheet->setCellValue('G11', 'DEPT-HEAD');

        $sheet->mergeCells('A13:C13');
        $sheet->setCellValue('A13', 'Division Head');

        $sheet->mergeCells('G13:L13');
        $sheet->setCellValue('G13', 'PGDH');

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

        $headerRange = "A" . self::TABLE_HEADER_ROW . ":N" . self::TABLE_SUBHEADER_ROW;

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

    private function writeSection(Worksheet $sheet, string $label, int $startRow, array $items): int
    {
        $sheet->setCellValue("A{$startRow}", $label);
        $sheet->mergeCells("A{$startRow}:N{$startRow}");
        $this->sectionLabels[] = $label;

        $sheet->getStyle("A{$startRow}:N{$startRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$startRow}:N{$startRow}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('E2E8F0');

        $sheet->getStyle("A{$startRow}:N{$startRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // Green-line feel (bottom border only)
        $sheet->getStyle("A{$startRow}:N{$startRow}")
            ->getBorders()
            ->getBottom()
            ->setBorderStyle(Border::BORDER_THIN);

        $row = $startRow + 1;

        return $this->writeIndicatorsFlat(
            $sheet,
            $row,
            $items,
            $this->standards,
            $this->valuesByIndicator
        );
    }

    /**
     * Stage II difference vs Stage I:
     * - we still list ALL indicators
     * - but we fill accomplishment + ratings + remarks when values exist
     * - otherwise mark UNFINISHED and keep ratings blank
     */
    private function writeIndicatorsFlat(
        Worksheet $sheet,
        int $row,
        array $items,
        array $standards,
        array $valuesByIndicator
    ): int {
        foreach ($items as $item) {
            $indicators = $item['indicators'] ?? [];
            if (empty($indicators)) {
                continue;
            }

            $startRow = $row;

            foreach ($indicators as $index => $indicator) {
                $indicatorText = is_array($indicator)
                    ? trim((string) ($indicator['text'] ?? ''))
                    : trim((string) $indicator);
                $targetQuantity = is_array($indicator) && is_numeric($indicator['target_quantity'] ?? null)
                    ? trim((string) $indicator['target_quantity'])
                    : '';

                $lookupKey = trim((string) ($item['output'] ?? '')) . '||' . $indicatorText;
                $vals = $valuesByIndicator[$lookupKey] ?? $valuesByIndicator[$indicatorText] ?? null;
                $hasVals = is_array($vals);

                // OUTPUT (first row only; merged later)
                $sheet->setCellValue("A{$row}", $index === 0 ? (string) ($item['output'] ?? '') : '');

                // Indicator text
                $indicatorCellText = $this->buildIndicatorCellText($indicatorText, $targetQuantity);
                $sheet->setCellValue("B{$row}", $indicatorCellText);

                // 6 Months Summary of Accomplishment
                $accomplishment = $hasVals
                    ? (string) ($vals['accomplishment'] ?? '')
                    : 'UNFINISHED (no ORS entries)';
                $sheet->setCellValue("C{$row}", $accomplishment);
                $sheet->setCellValue("D{$row}", '');
                $sheet->mergeCells("C{$row}:D{$row}");

                // Ratings (Q/E/T). If unfinished -> blanks.
                $qNum = $hasVals ? $this->toNumericOrNull($vals['q'] ?? null) : null;
                $eNum = $hasVals ? $this->toNumericOrNull($vals['e'] ?? null) : null;
                $tNum = $hasVals ? $this->toNumericOrNull($vals['t'] ?? null) : null;

                if ($qNum !== null) {
                    $sheet->setCellValueExplicit("E{$row}", $qNum, DataType::TYPE_NUMERIC);
                } else {
                    $sheet->setCellValue("E{$row}", '');
                }

                if ($eNum !== null) {
                    $sheet->setCellValueExplicit("F{$row}", $eNum, DataType::TYPE_NUMERIC);
                } else {
                    $sheet->setCellValue("F{$row}", '');
                }

                if ($tNum !== null) {
                    $sheet->setCellValueExplicit("G{$row}", $tNum, DataType::TYPE_NUMERIC);
                } else {
                    $sheet->setCellValue("G{$row}", '');
                }

                $aNum = $hasVals
                    ? $this->toNumericOrNull($vals['a'] ?? null)
                    : null;

                if ($aNum !== null) {
                    $sheet->setCellValueExplicit("H{$row}", $aNum, DataType::TYPE_NUMERIC);
                } else {
                    $sheet->setCellValue("H{$row}", '');
                }

                // Remarks
                $remarks = $hasVals
                    ? (string) ($vals['remarks'] ?? '')
                    : 'Assigned indicator but not logged in ORS for Jan–Jun 2026 (demo).';
                $sheet->setCellValue("I{$row}", $remarks);

                // Standards (locked from standardsSeedMap)
                $stdTexts = [];
                foreach (self::RATINGS as $rating) {
                    $col = self::STANDARDS_COLUMNS[$rating];
                    $text = $this->formatStdBlock($standards, $indicatorText, (int) $rating);
                    $sheet->setCellValue("{$col}{$row}", $text);
                    $sheet->getStyle("{$col}{$row}")->getAlignment()->setWrapText(true);
                    $stdTexts[] = $text;
                }

                // Wrapping/alignment like Stage 1
                $sheet->getStyle("B{$row}")->getAlignment()->setWrapText(true);
                $sheet->getStyle("C{$row}:D{$row}")->getAlignment()->setWrapText(true);
                $sheet->getStyle("E{$row}:H{$row}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("F{$row}:H{$row}")
                    ->getNumberFormat()
                    ->setFormatCode('0.00');

                // Vertical borders only (Stage 1)
                $this->applyRowVerticalBorders($sheet, $row);

                // Auto row height (Stage 1)
                $sheet->getRowDimension($row)->setRowHeight(
                    $this->estimateRowHeight($indicatorCellText, $accomplishment, $remarks, ...$stdTexts)
                );

                $row++;
            }

            $endRow = $row - 1;

            // Merge OUTPUT column across indicator rows (Stage 1)
            if ($endRow > $startRow) {
                $sheet->mergeCells("A{$startRow}:A{$endRow}");
                $sheet->getStyle("A{$startRow}:A{$endRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                    ->setVertical(Alignment::VERTICAL_TOP)
                    ->setWrapText(true);
            }

            // Green boundary line at the end of each output block (Stage 1)
            $sheet->getStyle("A{$endRow}:N{$endRow}")
                ->getBorders()
                ->getBottom()
                ->setBorderStyle(Border::BORDER_THIN);
        }

        return $row;
    }

    private function toNumericOrNull(mixed $v): ?float
    {
        if ($v === null) {
            return null;
        }

        if (is_string($v)) {
            $v = trim($v);
        }

        return is_numeric($v) ? (float) $v : null;
    }

    private function buildIndicatorCellText(string $indicatorText, string $targetQuantity): string
    {
        if ($targetQuantity === '') {
            return $indicatorText;
        }

        return $indicatorText . "\nTarget: " . $targetQuantity;
    }

    private function formatStdBlock(array $standards, string $indicator, int $rating): string
    {
        $entry = $standards[$indicator][$rating] ?? ['q' => [], 'e' => [], 't' => []];

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
        if (empty($values)) {
            return '—';
        }

        return implode('; ', array_map(fn ($v) => trim((string) $v), $values));
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

    private function resolveSections(): array
    {
        $sections = is_array($this->ipcr['sections'] ?? null) ? $this->ipcr['sections'] : [];
        if (!empty($sections)) {
            return $sections;
        }

        return [
            [
                'label' => 'CORE FUNCTIONS (80%)',
                'items' => is_array($this->ipcr['core'] ?? null) ? $this->ipcr['core'] : [],
            ],
            [
                'label' => 'SUPPORT FUNCTIONS (20%)',
                'items' => is_array($this->ipcr['support'] ?? null) ? $this->ipcr['support'] : [],
            ],
        ];
    }

    private function prefixSectionLabel(int $index, string $label): string
    {
        $letters = range('A', 'Z');
        $prefix = $letters[$index] ?? ('S' . ($index + 1));

        return $prefix . '. ' . $label;
    }
}
