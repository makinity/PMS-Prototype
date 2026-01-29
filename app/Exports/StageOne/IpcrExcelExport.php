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

class IpcrExcelExport implements FromArray, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    use Exportable;

    private const RATINGS = [5, 4, 3, 2, 1];

    // IPCR Standards columns
    private const STANDARDS_COLUMNS = [
        5 => 'J',
        4 => 'K',
        3 => 'L',
        2 => 'M',
        1 => 'N',
    ];

    // Based on Annex D / sample
    private const TABLE_HEADER_ROW = 15;
    private const TABLE_SUBHEADER_ROW = 16;
    private const TABLE_START_ROW = 18;

    private array $ipcr;
    private array $standards;

    public function __construct(array $ipcr, array $standards)
    {
        $this->ipcr = $ipcr;
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
        // Matches Annex D_ IPCR Form.xlsx closely (A–N)
        return [
            'A' => 18.33,
            'B' => 37.89,
            'C' => 8.33,
            'D' => 9.11,
            'E' => 5.33,
            'F' => 13.0,
            'G' => 13.0,
            'H' => 13.0,
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

        // A. CORE (80%)
        $currentRow = $this->writeSection($sheet, 'core', 'A. CORE FUNCTIONS (80%)', $currentRow);

        // B. SUPPORT (20%)
        $currentRow = $this->writeSection($sheet, 'support', 'B. SUPPORT FUNCTIONS (20%)', $currentRow);

        $lastRow = max($currentRow - 1, self::TABLE_SUBHEADER_ROW);

        // Alignment/wrap
        $sheet->getStyle("A" . self::TABLE_HEADER_ROW . ":N{$lastRow}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_TOP)
            ->setWrapText(true);

        // Borders for table area
        $sheet->getStyle("A" . self::TABLE_HEADER_ROW . ":N{$lastRow}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
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
        // Title
        $sheet->mergeCells('A1:N1');
        $sheet->setCellValue('A1', 'INDIVIDUAL PERFORMANCE COMMITMENT AND REVIEW (IPCR)');

        // Commitment statement (locked demo)
        $sheet->mergeCells('A3:N3');
        $sheet->setCellValue(
            'A3',
            'I RAMON REYES, of REVENUE COLLECTION UNIT section of REVENUE COLLECTION UNIT, commit to deliver and agree to be rated on the attainment of the following targets in accordance with the indicated measures for the period JANUARY – JUNE 2026.'
        );

        // Reviewer / approver block (header area)
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

        // Style header area
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
        // Main headers (row 15)
        $sheet->setCellValue('A' . self::TABLE_HEADER_ROW, 'OUTPUT');
        $sheet->setCellValue('B' . self::TABLE_HEADER_ROW, "Success Indicators\n(Measure + Target)");
        $sheet->setCellValue('C' . self::TABLE_HEADER_ROW, "6 Months Summary of Accomplishment");
        $sheet->setCellValue('E' . self::TABLE_HEADER_ROW, 'Rating');
        $sheet->setCellValue('I' . self::TABLE_HEADER_ROW, 'Remarks');
        $sheet->setCellValue('J' . self::TABLE_HEADER_ROW, 'Standards per Success Indicator');

        // Merges like Annex D
        $sheet->mergeCells('A' . self::TABLE_HEADER_ROW . ':A' . self::TABLE_SUBHEADER_ROW);
        $sheet->mergeCells('B' . self::TABLE_HEADER_ROW . ':B' . self::TABLE_SUBHEADER_ROW);
        $sheet->mergeCells('C' . self::TABLE_HEADER_ROW . ':D' . self::TABLE_SUBHEADER_ROW);
        $sheet->mergeCells('E' . self::TABLE_HEADER_ROW . ':H' . self::TABLE_HEADER_ROW);
        $sheet->mergeCells('I' . self::TABLE_HEADER_ROW . ':I' . self::TABLE_SUBHEADER_ROW);
        $sheet->mergeCells('J' . self::TABLE_HEADER_ROW . ':N' . self::TABLE_HEADER_ROW);

        // Subheaders (row 16)
        $subHeaders = [
            'E' => 'Q',
            'F' => 'E',
            'G' => 'T',
            'H' => 'A',
            'J' => '5.0',
            'K' => '4.0',
            'L' => '3.0',
            'M' => '2.0',
            'N' => '1.0',
        ];

        foreach ($subHeaders as $col => $text) {
            $sheet->setCellValue($col . self::TABLE_SUBHEADER_ROW, $text);
        }

        // Style header rows
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

    private function writeSection(Worksheet $sheet, string $type, string $label, int $startRow): int
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

        $row = $startRow + 1;

        return $this->writeIndicatorsFlat($sheet, $row, $this->ipcr[$type] ?? [], $this->standards);
    }

    private function writeIndicatorsFlat(Worksheet $sheet, int $row, array $items, array $standards): int
    {
        foreach ($items as $item) {
            $indicators = $item['indicators'] ?? [];
            if (empty($indicators)) {
                continue;
            }

            foreach ($indicators as $index => $indicator) {
                // Output only on first indicator row
                $sheet->setCellValue("A{$row}", $index === 0 ? ($item['output'] ?? '') : '');
                $sheet->setCellValue("B{$row}", $indicator);

                // 6 Months Summary of Accomplishment (blank in Stage I export)
                $sheet->setCellValue("C{$row}", '');
                $sheet->setCellValue("D{$row}", '');
                $sheet->mergeCells("C{$row}:D{$row}");

                // Rating cells (blank; A is computed)
                $sheet->setCellValue("E{$row}", '');
                $sheet->setCellValue("F{$row}", '');
                $sheet->setCellValue("G{$row}", '');
                $sheet->setCellValue("H{$row}", "=IF(COUNT(E{$row}:G{$row})=0, \"\", AVERAGE(E{$row}:G{$row}))");

                // Remarks
                $sheet->setCellValue("I{$row}", '');

                // Standards per indicator (J–N)
                foreach (self::RATINGS as $rating) {
                    $col = self::STANDARDS_COLUMNS[$rating];
                    $sheet->setCellValue("{$col}{$row}", $this->formatStdBlock($standards, $indicator, $rating));
                }

                // Wrap heavy text columns
                $sheet->getStyle("B{$row}")->getAlignment()->setWrapText(true);
                foreach (self::STANDARDS_COLUMNS as $col) {
                    $sheet->getStyle("{$col}{$row}")->getAlignment()->setWrapText(true);
                }

                // Center rating columns
                $sheet->getStyle("E{$row}:H{$row}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
}
