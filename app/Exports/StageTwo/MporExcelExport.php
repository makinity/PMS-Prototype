<?php

namespace App\Exports\StageTwo;

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

class MporExcelExport implements FromArray, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    use Exportable;

    private const WEEK_KEYS = [1, 2, 3, 4, 'total'];

    private const TABLE_HEADER_ROW = 10;
    private const TABLE_SUBHEADER_ROW = 11;
    private const TABLE_START_ROW = 12;

    private const BASE_ROW_HEIGHT = 22;
    private const LINE_HEIGHT = 14;
    private const CHARS_PER_LINE = 48;

    private const BAND_COLUMNS = [
        'qty' => ['B', 'C', 'D', 'E', 'F'],
        'quality' => ['G', 'H', 'I', 'J', 'K'],
        'timeliness' => ['L', 'M', 'N', 'O', 'P'],
    ];

    private array $payload;
    private array $coreRows = [];
    private array $supportRows = [];
    private array $totals = [];

    public function __construct(array $payload)
    {
        $this->payload = $payload;
        $this->coreRows = $this->normalizeRows($payload['core'] ?? []);
        $this->supportRows = $this->normalizeRows($payload['support'] ?? []);

        $coreTotals = $this->calculateSectionTotals($this->coreRows);
        $supportTotals = $this->calculateSectionTotals($this->supportRows);

        $this->totals = [
            'core' => $coreTotals,
            'support' => $supportTotals,
            'grand' => $this->sumTotals($coreTotals, $supportTotals),
        ];
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
            'A' => 52,
            'B' => 8,
            'C' => 8,
            'D' => 8,
            'E' => 8,
            'F' => 9,
            'G' => 8,
            'H' => 8,
            'I' => 8,
            'J' => 8,
            'K' => 9,
            'L' => 8,
            'M' => 8,
            'N' => 8,
            'O' => 8,
            'P' => 9,
        ];
    }

    public function title(): string
    {
        return 'MPOR';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $this->populateTemplate($event->sheet->getDelegate());
            },
        ];
    }

    private function populateTemplate(Worksheet $sheet): void
    {
        $this->setupPage($sheet);
        $sheet->setShowGridlines(true);

        $this->writeTopHeader($sheet);
        $this->writeIdentityBlock($sheet);
        $this->writeTableHeader($sheet);

        $row = self::TABLE_START_ROW;
        $row = $this->writeSectionHeader($sheet, $row, 'CORE FUNCTIONS (80%)');
        $row = $this->writeOutputRows($sheet, $row, $this->coreRows);

        $row = $this->writeSectionHeader($sheet, $row, 'SUPPORT FUNCTIONS (20%)');
        $row = $this->writeOutputRows($sheet, $row, $this->supportRows);
        $lastDataRow = max($row - 1, self::TABLE_START_ROW);
        $this->applyTableClosingBorder($sheet, $lastDataRow);

        $row = $this->writeAttendanceBlock($sheet, $row + 1);
        $this->writeSignatureBlock($sheet, $row + 1);
    }

    private function setupPage(Worksheet $sheet): void
    {
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_LEGAL);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
    }

    private function writeTopHeader(Worksheet $sheet): void
    {
        $sheet->mergeCells('A1:P1');
        $sheet->setCellValue('A1', 'REPUBLIC OF THE PHILIPPINES');

        $sheet->mergeCells('A2:P2');
        $sheet->setCellValue('A2', 'PROVINCE OF DAVAO DEL SUR');

        $sheet->mergeCells('A3:P3');
        $sheet->setCellValue('A3', 'PROVINCIAL HUMAN RESOURCE MANAGEMENT OFFICE');

        $sheet->mergeCells('A4:P4');
        $sheet->setCellValue('A4', 'MONTHLY PERFORMANCE OUTPUT REPORT (MPOR)');

        $sheet->mergeCells('A5:P5');
        $sheet->setCellValue('A5', '(Stage II - Monitoring Copy | Read-only)');

        $sheet->getStyle('A1:P5')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle('A1:P4')->getFont()->setBold(true);
        $sheet->getStyle('A5:P5')->getFont()->setSize(10);
    }

    private function writeIdentityBlock(Worksheet $sheet): void
    {
        $employee = (string) ($this->payload['employee'] ?? '');
        $office = (string) ($this->payload['office'] ?? '');
        $monthYear = (string) ($this->payload['month_year'] ?? '');

        $sheet->mergeCells('A7:C7');
        $sheet->setCellValue('A7', 'Employee Name:');
        $sheet->mergeCells('D7:I7');
        $sheet->setCellValue('D7', $employee);

        $sheet->mergeCells('J7:L7');
        $sheet->setCellValue('J7', 'Month & Year:');
        $sheet->mergeCells('M7:P7');
        $sheet->setCellValue('M7', $monthYear);

        $sheet->mergeCells('A8:C8');
        $sheet->setCellValue('A8', 'Office / Unit:');
        $sheet->mergeCells('D8:I8');
        $sheet->setCellValue('D8', $office);

        $sheet->getStyle('A7:C8')->getFont()->setBold(true);
        $sheet->getStyle('J7:L7')->getFont()->setBold(true);

        $sheet->getStyle('A7:P8')->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->getStyle('D7:I7')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('M7:P7')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('D8:I8')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
    }

    private function writeTableHeader(Worksheet $sheet): void
    {
        $sheet->mergeCells('A10:A11');
        $sheet->setCellValue('A10', 'EXPECTED OUTPUTS');

        $sheet->mergeCells('B10:F10');
        $sheet->setCellValue('B10', 'EFFICIENCY / QUANTITY');

        $sheet->mergeCells('G10:K10');
        $sheet->setCellValue('G10', 'QUALITY / EFFECTIVENESS');

        $sheet->mergeCells('L10:P10');
        $sheet->setCellValue('L10', 'TIMELINESS');

        $weekLabels = ['W1', 'W2', 'W3', 'W4', 'TOTAL'];

        foreach (self::BAND_COLUMNS as $columns) {
            foreach ($columns as $index => $column) {
                $sheet->setCellValue($column . self::TABLE_SUBHEADER_ROW, $weekLabels[$index]);
            }
        }

        $headerRange = 'A' . self::TABLE_HEADER_ROW . ':P' . self::TABLE_SUBHEADER_ROW;
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('D9D9D9');

        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }

    private function writeSectionHeader(Worksheet $sheet, int $row, string $label): int
    {
        $sheet->mergeCells("A{$row}:P{$row}");
        $sheet->setCellValue("A{$row}", $label);

        $sheet->getStyle("A{$row}:P{$row}")->getFont()->setBold(true);
        $sheet->getStyle("A{$row}:P{$row}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('E2E8F0');

        $sheet->getStyle("A{$row}:P{$row}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle("A{$row}:P{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        return $row + 1;
    }

    private function writeOutputRows(Worksheet $sheet, int $startRow, array $rows): int
    {
        $row = $startRow;

        foreach ($rows as $item) {
            $sheet->setCellValue("A{$row}", $item['label']);
            $this->writeBandValues($sheet, $row, 'qty', $item['qty']);
            $this->writeBandValues($sheet, $row, 'quality', $item['quality']);
            $this->writeBandValues($sheet, $row, 'timeliness', $item['timeliness']);

            $sheet->getStyle("A{$row}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                ->setVertical(Alignment::VERTICAL_TOP)
                ->setWrapText(true);

            $sheet->getStyle("B{$row}:P{$row}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $this->applyRowVerticalBorders($sheet, $row);
            $sheet->getRowDimension($row)->setRowHeight($this->estimateRowHeight((string) $item['label']));

            $row++;
        }

        return $row;
    }

    private function writeTotalRow(Worksheet $sheet, int $row, string $label, array $totals, bool $grand = false): int
    {
        $sheet->setCellValue("A{$row}", $label);
        $this->writeBandValues($sheet, $row, 'qty', $totals['qty']);
        $this->writeBandValues($sheet, $row, 'quality', $totals['quality']);
        $this->writeBandValues($sheet, $row, 'timeliness', $totals['timeliness']);

        $sheet->getStyle("A{$row}:P{$row}")->getFont()->setBold(true);
        $sheet->getStyle("A{$row}:P{$row}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $fillColor = $grand ? 'CBD5E1' : 'EEF2F7';
        $sheet->getStyle("A{$row}:P{$row}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB($fillColor);

        $sheet->getStyle("A{$row}:P{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        return $row + 1;
    }

    private function writeAttendanceBlock(Worksheet $sheet, int $startRow): int
    {
        $absence = $this->normalizeAttendanceBand($this->payload['attendance']['absence'] ?? []);
        $tardiness = $this->normalizeAttendanceBand($this->payload['attendance']['tardiness'] ?? []);

        $headerRow = $startRow;
        $sheet->mergeCells("A{$headerRow}:E{$headerRow}");
        $sheet->setCellValue("A{$headerRow}", '');
        $sheet->setCellValue("F{$headerRow}", 'WEEK 1');
        $sheet->setCellValue("G{$headerRow}", 'WEEK 2');
        $sheet->setCellValue("H{$headerRow}", 'WEEK 3');
        $sheet->setCellValue("I{$headerRow}", 'WEEK 4');
        $sheet->setCellValue("J{$headerRow}", 'TOTAL');

        $sheet->mergeCells("A" . ($headerRow + 1) . ":E" . ($headerRow + 1));
        $sheet->setCellValue("A" . ($headerRow + 1), 'MAN DAY(S) LOST THRU ABSENCE');
        $sheet->setCellValue("F" . ($headerRow + 1), $absence[1]);
        $sheet->setCellValue("G" . ($headerRow + 1), $absence[2]);
        $sheet->setCellValue("H" . ($headerRow + 1), $absence[3]);
        $sheet->setCellValue("I" . ($headerRow + 1), $absence[4]);
        $sheet->setCellValue("J" . ($headerRow + 1), $absence['total']);

        $sheet->mergeCells("A" . ($headerRow + 2) . ":E" . ($headerRow + 2));
        $sheet->setCellValue("A" . ($headerRow + 2), 'MAN HRS./MINUTES LOST THRU TARDINESS / UNDERTIME');
        $sheet->setCellValue("F" . ($headerRow + 2), $tardiness[1]);
        $sheet->setCellValue("G" . ($headerRow + 2), $tardiness[2]);
        $sheet->setCellValue("H" . ($headerRow + 2), $tardiness[3]);
        $sheet->setCellValue("I" . ($headerRow + 2), $tardiness[4]);
        $sheet->setCellValue("J" . ($headerRow + 2), $tardiness['total']);

        $sheet->getStyle("A{$headerRow}:J" . ($headerRow + 2))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("A{$headerRow}:J{$headerRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$headerRow}:J" . ($headerRow + 2))->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $sheet->getStyle("F{$headerRow}:J" . ($headerRow + 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A" . ($headerRow + 1) . ":A" . ($headerRow + 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        return $headerRow + 3;
    }

    private function writeSignatureBlock(Worksheet $sheet, int $startRow): void
    {
        $supervisor = (string) ($this->payload['supervisor'] ?? '');
        $employee = (string) ($this->payload['employee'] ?? '');

        $sheet->mergeCells("A{$startRow}:G{$startRow}");
        $sheet->mergeCells("J{$startRow}:P{$startRow}");
        $sheet->setCellValue("A{$startRow}", 'CONFIRMED:');
        $sheet->setCellValue("J{$startRow}", 'Above information are true and correct:');

        $lineRow = $startRow + 2;
        $sheet->mergeCells("A{$lineRow}:G{$lineRow}");
        $sheet->mergeCells("J{$lineRow}:P{$lineRow}");
        $sheet->getStyle("A{$lineRow}:G{$lineRow}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("J{$lineRow}:P{$lineRow}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);

        $sheet->mergeCells("A" . ($lineRow + 1) . ":G" . ($lineRow + 1));
        $sheet->mergeCells("J" . ($lineRow + 1) . ":P" . ($lineRow + 1));
        $sheet->setCellValue("A" . ($lineRow + 1), "Supervisor: {$supervisor}");
        $sheet->setCellValue("J" . ($lineRow + 1), "Employee: {$employee}");

        $sheet->getStyle("A{$startRow}:P" . ($lineRow + 1))->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$startRow}:P{$startRow}")->getFont()->setBold(true);
    }

    private function writeBandValues(Worksheet $sheet, int $row, string $band, array $values): void
    {
        $columns = self::BAND_COLUMNS[$band];

        foreach (self::WEEK_KEYS as $index => $key) {
            $column = $columns[$index];
            $sheet->setCellValue("{$column}{$row}", (int) ($values[$key] ?? 0));
        }
    }

    private function applyRowVerticalBorders(Worksheet $sheet, int $row): void
    {
        foreach (range('A', 'P') as $column) {
            $borders = $sheet->getStyle("{$column}{$row}")->getBorders();
            $borders->getLeft()->setBorderStyle(Border::BORDER_THIN);
            $borders->getRight()->setBorderStyle(Border::BORDER_THIN);
        }
    }

    private function applyTableClosingBorder(Worksheet $sheet, int $row): void
    {
        $sheet->getStyle("A{$row}:P{$row}")
            ->getBorders()
            ->getBottom()
            ->setBorderStyle(Border::BORDER_MEDIUM);
    }

    private function normalizeRows(array $rows): array
    {
        $normalized = [];

        foreach ($rows as $row) {
            $weeks = $this->normalizeWeeks($row['weeks'] ?? []);

            $normalized[] = [
                'label' => (string) ($row['label'] ?? ''),
                'qty' => $this->extractBand($weeks, 'qty'),
                'quality' => $this->extractBand($weeks, 'q_points'),
                'timeliness' => $this->extractBand($weeks, 't_points'),
            ];
        }

        return $normalized;
    }

    private function normalizeWeeks(array $weeks): array
    {
        $normalized = [];

        foreach ([1, 2, 3, 4] as $week) {
            $normalized[$week] = [
                'qty' => (int) ($weeks[$week]['qty'] ?? 0),
                'q_points' => (int) ($weeks[$week]['q_points'] ?? 0),
                't_points' => (int) ($weeks[$week]['t_points'] ?? 0),
            ];
        }

        return $normalized;
    }

    private function extractBand(array $weeks, string $field): array
    {
        $band = [];
        $total = 0;

        foreach ([1, 2, 3, 4] as $week) {
            $value = (int) ($weeks[$week][$field] ?? 0);
            $band[$week] = $value;
            $total += $value;
        }

        $band['total'] = $total;

        return $band;
    }

    private function calculateSectionTotals(array $rows): array
    {
        $totals = [
            'qty' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 'total' => 0],
            'quality' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 'total' => 0],
            'timeliness' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 'total' => 0],
        ];

        foreach ($rows as $row) {
            foreach (['qty', 'quality', 'timeliness'] as $band) {
                foreach (self::WEEK_KEYS as $key) {
                    $totals[$band][$key] += (int) ($row[$band][$key] ?? 0);
                }
            }
        }

        return $totals;
    }

    private function sumTotals(array $core, array $support): array
    {
        $result = [
            'qty' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 'total' => 0],
            'quality' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 'total' => 0],
            'timeliness' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 'total' => 0],
        ];

        foreach (['qty', 'quality', 'timeliness'] as $band) {
            foreach (self::WEEK_KEYS as $key) {
                $result[$band][$key] = (int) ($core[$band][$key] ?? 0) + (int) ($support[$band][$key] ?? 0);
            }
        }

        return $result;
    }

    private function normalizeAttendanceBand(array $values): array
    {
        $band = [
            1 => (int) ($values[1] ?? $values['week1'] ?? 0),
            2 => (int) ($values[2] ?? $values['week2'] ?? 0),
            3 => (int) ($values[3] ?? $values['week3'] ?? 0),
            4 => (int) ($values[4] ?? $values['week4'] ?? 0),
        ];

        $band['total'] = (int) ($values['total'] ?? array_sum($band));

        return $band;
    }

    private function estimateRowHeight(string $label): float
    {
        $label = trim($label);
        if ($label === '') {
            return self::BASE_ROW_HEIGHT;
        }

        $lines = (int) ceil(mb_strlen($label) / self::CHARS_PER_LINE);

        return self::BASE_ROW_HEIGHT + max(0, $lines - 1) * self::LINE_HEIGHT;
    }
}
