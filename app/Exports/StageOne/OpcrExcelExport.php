<?php

namespace App\Exports\StageOne;

use Illuminate\Support\Arr;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
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
            'K' => 14,
            'L' => 14,
            'M' => 14,
            'N' => 14,
            'O' => 14,
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
        $this->writeManualHeader($sheet);
        $this->writeTableHeader($sheet);

        $currentRow = self::TABLE_START_ROW;
        $currentRow = $this->writeSection($sheet, 'core', 'A. CORE FUNCTIONS (80%)', $currentRow, true);
        $currentRow = $this->writeSection($sheet, 'support', 'C. SUPPORT FUNCTIONS (20%)', $currentRow, false);

        $lastRow = max($currentRow - 1, self::TABLE_SUBHEADER_ROW);
        $this->unmergeDataArea($sheet, self::TABLE_START_ROW, $lastRow);
        $sheet->getStyle("A" . self::TABLE_HEADER_ROW . ":O{$lastRow}")->getAlignment()
            ->setVertical(Alignment::VERTICAL_TOP)
            ->setWrapText(true);
        $range = "B" . self::TABLE_HEADER_ROW . ":O{$lastRow}";
        $sheet->getStyle($range)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
        $this->applyMfoColumnBorders($sheet, self::TABLE_START_ROW, $lastRow);
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


        foreach ($headers as $column => $text) {
            $sheet->setCellValue("{$column}" . self::TABLE_HEADER_ROW, $text);
            $sheet->getStyle("{$column}" . self::TABLE_HEADER_ROW)->getFont()->setBold(true);
            $sheet->getStyle("{$column}" . self::TABLE_HEADER_ROW)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("{$column}" . self::TABLE_HEADER_ROW)
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('D9D9D9');
        }

        $sheet->mergeCells('F' . self::TABLE_HEADER_ROW . ':I' . self::TABLE_HEADER_ROW);
        $sheet->setCellValue('F' . self::TABLE_HEADER_ROW, 'Rating');
        $sheet->mergeCells('K' . self::TABLE_HEADER_ROW . ':O' . self::TABLE_HEADER_ROW);
        $sheet->setCellValue('K' . self::TABLE_HEADER_ROW, 'Standards per Success Indicator');

        $this->fillHeaderRow(self::TABLE_HEADER_ROW, $sheet);

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

        // ✅ Vertically merge these headers across row 9–10
        foreach (['A','B','C','D','E','J'] as $col) {
            $sheet->mergeCells($col . self::TABLE_HEADER_ROW . ':' . $col . self::TABLE_SUBHEADER_ROW);
        }


        foreach ($subHeaders as $column => $text) {
            $cell = "{$column}" . self::TABLE_SUBHEADER_ROW;
            $sheet->setCellValue($cell, $text);
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($cell)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('D9D9D9');
        }
    }

    private function fillHeaderRow(int $row, Worksheet $sheet): void
    {
        $sheet->getStyle("A{$row}:O{$row}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('D9D9D9');
        $sheet->getStyle("A{$row}:O{$row}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
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
        $row = $startRow + 1;

        if ($includeRevenueRow) {
            $sheet->setCellValue("A{$row}", 'REVENUE');
            $sheet->mergeCells("A{$row}:O{$row}");
            $sheet->getStyle("A{$row}:O{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:O{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $row++;
        }

        return $this->writeIndicatorsFlat($sheet, $row, $this->opcr[$type] ?? [], $this->standards, $type);
    }

    private function writeIndicatorsFlat(
        Worksheet $sheet,
        int $row,
        array $items,
        array $standards,
        string $type
    ): int {
        foreach ($items as $item) {
            $indicators = $item['indicators'] ?? [];
            if (empty($indicators)) {
                continue;
            }
            $indicatorCount = count($indicators);
            foreach ($indicators as $index => $indicator) {
                $sheet->setCellValue("A{$row}", $index === 0 ? $item['mfo'] : '');
                $sheet->setCellValue("B{$row}", $indicator);
                $sheet->setCellValue("C{$row}", '');
                $sheet->setCellValue("D{$row}", 'Revenue');
                $sheet->setCellValue("E{$row}", '');
                foreach (range('F', 'I') as $column) {
                    $sheet->setCellValue("{$column}{$row}", '');
                }
                $sheet->setCellValue("J{$row}", '');

                foreach (self::RATINGS as $rating) {
                    $column = self::STANDARDS_COLUMNS[$rating];
                    $sheet->setCellValue("{$column}{$row}", $this->formatStdBlock($standards, $indicator, $rating));
                }
                $sheet->getStyle("B{$row}")->getAlignment()->setWrapText(true);
                foreach (self::STANDARDS_COLUMNS as $column) {
                    $sheet->getStyle("{$column}{$row}")->getAlignment()->setWrapText(true);
                }
                $sheet->getStyle("B{$row}:O{$row}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
                $isBoundary = $index === 0 || $index === $indicatorCount - 1;
                if ($isBoundary) {
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000'],
                            ],
                        ],
                    ]);
                } else {
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'borders' => [
                            'left' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000'],
                            ],
                            'right' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000'],
                            ],
                        ],
                    ]);
                }
                $row++;
            }
        }

        return $row;
    }

    private function formatStdBlock(array $standards, string $indicator, int $rating): string
    {
        $entry = $standards[$indicator][$rating] ?? ['q' => [], 'e' => [], 't' => []];
        $q = $this->formatDimension(Arr::wrap($entry['q'] ?? []));
        $e = $this->formatDimension(Arr::wrap($entry['e'] ?? []));
        $t = $this->formatDimension(Arr::wrap($entry['t'] ?? []));

        $lines = [
            "Q = {$q}",
            "E = {$e}",
            "T = {$t}",
        ];

        return implode("\n", $lines);
    }

    private function formatDimension(array $values): string
    {
        $values = array_filter($values, fn ($value) => $value !== null && $value !== '');
        if (empty($values)) {
            return '—';
        }
        return implode('; ', array_map(fn ($value) => trim((string) $value), $values));
    }

    private function unmergeDataArea(Worksheet $sheet, int $fromRow, int $toRow): void
    {
        foreach ($sheet->getMergeCells() as $range) {
            if (!str_contains($range, ':')) {
                continue;
            }
            [$start, $end] = explode(':', $range);
            $startCol = preg_replace('/[0-9]/', '', $start);
            $endCol = preg_replace('/[0-9]/', '', $end);
            $startRow = (int) preg_replace('/[^0-9]/', '', $start);
            $endRow = (int) preg_replace('/[^0-9]/', '', $end);

            if ($endRow < $fromRow || $startRow > $toRow) {
                continue;
            }

            if ($startCol === 'A' && $endCol === 'O' && $startRow === $endRow) {
                continue;
            }

            $sheet->unmergeCells($range);
        }
    }

    private function applyMfoColumnBorders(Worksheet $sheet, int $fromRow, int $toRow): void
    {
        $row = $fromRow;
        while ($row <= $toRow) {
            if ($this->isSectionRow($sheet, $row)) {
                $row++;
                continue;
            }

            $valueA = trim((string) $sheet->getCell("A{$row}")->getValue());
            $valueB = trim((string) $sheet->getCell("B{$row}")->getValue());
            if ($valueA === '' || $valueB === '') {
                $row++;
                continue;
            }

            $start = $row;
            $end = $row;
            $next = $row + 1;
            while ($next <= $toRow) {
                if ($this->isSectionRow($sheet, $next)) {
                    break;
                }
                $nextA = trim((string) $sheet->getCell("A{$next}")->getValue());
                if ($nextA !== '') {
                    break;
                }
                $nextB = trim((string) $sheet->getCell("B{$next}")->getValue());
                if ($nextB === '') {
                    break;
                }
                $end = $next;
                $next++;
            }

            for ($r = $start; $r <= $end; $r++) {
                $sheet->getStyle("A{$r}")->applyFromArray([
                    'borders' => [
                        'left' => $this->thinBorderSpec(),
                        'right' => $this->thinBorderSpec(),
                        'top' => $this->thinBorderSpec($r === $start),
                        'bottom' => $this->thinBorderSpec($r === $end),
                    ],
                ]);
            }

            $row = $end + 1;
        }
    }

    private function isSectionRow(Worksheet $sheet, int $row): bool
    {
        $value = trim((string) $sheet->getCell("A{$row}")->getValue());
        $protected = [
            'A. CORE FUNCTIONS (80%)',
            'REVENUE',
            'C. SUPPORT FUNCTIONS (20%)',
        ];
        return in_array($value, $protected, true);
    }

    private function thinBorderSpec(bool $active = true): array
    {
        if ($active) {
            return [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ];
        }

        return ['borderStyle' => Border::BORDER_NONE];
    }
}
