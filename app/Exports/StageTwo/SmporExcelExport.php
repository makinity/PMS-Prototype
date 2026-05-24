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

class SmporExcelExport implements FromArray, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    use Exportable;

    private const MONTH_KEYS = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'total', 'avg'];
    private const RAW_MONTH_KEYS = ['jan', 'feb', 'mar', 'apr', 'may', 'jun'];

    private const TABLE_HEADER_ROW = 8;
    private const TABLE_SUBHEADER_ROW = 9;
    private const TABLE_START_ROW = 10;

    private const BASE_ROW_HEIGHT = 22;
    private const LINE_HEIGHT = 14;
    private const CHARS_PER_LINE = 56;

    private const BAND_COLUMNS = [
        'qty' => ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'],
        'quality' => ['J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q'],
        'timeliness' => ['R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y'],
    ];

    private const MONTH_LABELS = ['Jan', 'Feb', 'March', 'April', 'May', 'June', 'Total', 'Average'];

    private array $payload;
    private array $sections = [];

    public function __construct(array $payload)
    {
        $this->payload = $payload;
        $this->sections = $this->normalizeSections($payload);
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
            'A' => 56,
            'B' => 8,
            'C' => 8,
            'D' => 8,
            'E' => 8,
            'F' => 8,
            'G' => 8,
            'H' => 9,
            'I' => 10,
            'J' => 8,
            'K' => 8,
            'L' => 8,
            'M' => 8,
            'N' => 8,
            'O' => 8,
            'P' => 9,
            'Q' => 10,
            'R' => 8,
            'S' => 8,
            'T' => 8,
            'U' => 8,
            'V' => 8,
            'W' => 8,
            'X' => 9,
            'Y' => 10,
        ];
    }

    public function title(): string
    {
        return 'SMPOR';
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
        $lastDataRow = self::TABLE_START_ROW;

        foreach ($this->sections as $section) {
            $row = $this->writeSectionHeader($sheet, $row, (string) ($section['label'] ?? 'FUNCTIONS'));
            [$row, $sectionLastRow] = $this->writeOutputRows($sheet, $row, $section['rows'] ?? []);
            $lastDataRow = max($lastDataRow, $sectionLastRow);
        }

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
        $sheet->mergeCells('A1:Y1');
        $sheet->setCellValue('A1', 'Republic of the Philippines');

        $sheet->mergeCells('A2:Y2');
        $sheet->setCellValue('A2', 'PROVINCE OF DAVAO DEL SUR');

        $sheet->mergeCells('A3:Y3');
        $sheet->setCellValue('A3', 'Province of Davao del Sur - Matti, Digos City');

        $sheet->mergeCells('A4:Y4');
        $sheet->setCellValue('A4', 'SUMMARY MONTHLY PERFORMANCE OUTPUT REPORT');

        $sheet->getStyle('A1:Y4')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle('A1:Y4')->getFont()->setBold(true);
        $sheet->getStyle('A1:Y2')->getFont()->setSize(11);
        $sheet->getStyle('A3:Y4')->getFont()->setSize(12);
    }

    private function writeIdentityBlock(Worksheet $sheet): void
    {
        $name = (string) ($this->payload['name'] ?? '');
        $office = (string) ($this->payload['office'] ?? '');
        $semestralPeriod = (string) ($this->payload['semestral_period'] ?? '');

        $sheet->mergeCells('A6:B6');
        $sheet->setCellValue('A6', 'Name:');
        $sheet->mergeCells('C6:H6');
        $sheet->setCellValue('C6', $name);

        $sheet->mergeCells('I6:L6');
        $sheet->setCellValue('I6', 'Office/Division:');
        $sheet->mergeCells('M6:R6');
        $sheet->setCellValue('M6', $office);

        $sheet->mergeCells('S6:V6');
        $sheet->setCellValue('S6', 'Semestral Period:');
        $sheet->mergeCells('W6:Y6');
        $sheet->setCellValue('W6', $semestralPeriod);

        $sheet->getStyle('A6:B6')->getFont()->setBold(true);
        $sheet->getStyle('I6:L6')->getFont()->setBold(true);
        $sheet->getStyle('S6:V6')->getFont()->setBold(true);

        $sheet->getStyle('A6:Y6')->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->getStyle('C6:H6')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('M6:R6')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('W6:Y6')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
    }

    private function writeTableHeader(Worksheet $sheet): void
    {
        $sheet->mergeCells('A8:A9');
        $sheet->setCellValue('A8', 'EXPECTED OUTPUTS');

        $sheet->mergeCells('B8:I8');
        $sheet->setCellValue('B8', 'EFFICIENCY/QUANTITY');

        $sheet->mergeCells('J8:Q8');
        $sheet->setCellValue('J8', 'QUALITY/EFFECTIVENESS');

        $sheet->mergeCells('R8:Y8');
        $sheet->setCellValue('R8', 'TIMELINESS');

        foreach (self::BAND_COLUMNS as $columns) {
            foreach ($columns as $index => $column) {
                $sheet->setCellValue($column . self::TABLE_SUBHEADER_ROW, self::MONTH_LABELS[$index]);
            }
        }

        $headerRange = 'A' . self::TABLE_HEADER_ROW . ':Y' . self::TABLE_SUBHEADER_ROW;
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
        $sheet->mergeCells("A{$row}:Y{$row}");
        $sheet->setCellValue("A{$row}", $label);

        $sheet->getStyle("A{$row}:Y{$row}")->getFont()->setBold(true);
        $sheet->getStyle("A{$row}:Y{$row}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('E2E8F0');

        $sheet->getStyle("A{$row}:Y{$row}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle("A{$row}:Y{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        return $row + 1;
    }

    /**
     * @return array{int,int}
     */
    private function writeOutputRows(Worksheet $sheet, int $startRow, array $rows): array
    {
        $row = $startRow;
        $lastWrittenRow = $startRow - 1;

        foreach ($rows as $item) {
            $sheet->setCellValue("A{$row}", $item['label']);
            $this->writeBandValues($sheet, $row, 'qty', $item['qty']);
            $this->writeBandValues($sheet, $row, 'quality', $item['quality']);
            $this->writeBandValues($sheet, $row, 'timeliness', $item['timeliness']);

            $sheet->getStyle("A{$row}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                ->setVertical(Alignment::VERTICAL_TOP)
                ->setWrapText(true);

            $sheet->getStyle("B{$row}:Y{$row}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->getStyle("A{$row}:Y{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getRowDimension($row)->setRowHeight($this->estimateRowHeight((string) $item['label']));

            $lastWrittenRow = $row;
            $row++;
        }

        return [$row, $lastWrittenRow];
    }

    private function writeBandValues(Worksheet $sheet, int $row, string $band, array $values): void
    {
        $columns = self::BAND_COLUMNS[$band];

        foreach (self::MONTH_KEYS as $index => $key) {
            $column = $columns[$index];
            $value = $values[$key] ?? 0;

            $sheet->setCellValue("{$column}{$row}", $value);

            if ($key === 'avg') {
                $sheet->getStyle("{$column}{$row}")
                    ->getNumberFormat()
                    ->setFormatCode('0.00');
            } else {
                $sheet->getStyle("{$column}{$row}")
                    ->getNumberFormat()
                    ->setFormatCode('0');
            }
        }
    }

    private function applyTableClosingBorder(Worksheet $sheet, int $row): void
    {
        $sheet->getStyle("A{$row}:Y{$row}")
            ->getBorders()
            ->getBottom()
            ->setBorderStyle(Border::BORDER_MEDIUM);
    }

    private function writeAttendanceBlock(Worksheet $sheet, int $startRow): int
    {
        $absence = $this->normalizeAttendanceBand($this->payload['attendance']['absence'] ?? []);
        $tardiness = $this->normalizeAttendanceBand($this->payload['attendance']['tardiness'] ?? []);

        $headerRow = $startRow;
        $sheet->setCellValue("A{$headerRow}", '');
        $sheet->setCellValue("B{$headerRow}", 'Jan');
        $sheet->setCellValue("C{$headerRow}", 'Feb');
        $sheet->setCellValue("D{$headerRow}", 'Mar');
        $sheet->setCellValue("E{$headerRow}", 'Apr');
        $sheet->setCellValue("F{$headerRow}", 'May');
        $sheet->setCellValue("G{$headerRow}", 'Jun');
        $sheet->setCellValue("H{$headerRow}", 'Total');

        $sheet->setCellValue("A" . ($headerRow + 1), 'MAN DAY(S) LOST THRU ABSENCE');
        $sheet->setCellValue("B" . ($headerRow + 1), $absence['jan']);
        $sheet->setCellValue("C" . ($headerRow + 1), $absence['feb']);
        $sheet->setCellValue("D" . ($headerRow + 1), $absence['mar']);
        $sheet->setCellValue("E" . ($headerRow + 1), $absence['apr']);
        $sheet->setCellValue("F" . ($headerRow + 1), $absence['may']);
        $sheet->setCellValue("G" . ($headerRow + 1), $absence['jun']);
        $sheet->setCellValue("H" . ($headerRow + 1), $absence['total'] . 'days');

        $sheet->setCellValue("A" . ($headerRow + 2), 'MAN HRS./MINUTES LOST THRU TARDINESS/UNDERTIME');
        $sheet->setCellValue("B" . ($headerRow + 2), $tardiness['jan']);
        $sheet->setCellValue("C" . ($headerRow + 2), $tardiness['feb']);
        $sheet->setCellValue("D" . ($headerRow + 2), $tardiness['mar']);
        $sheet->setCellValue("E" . ($headerRow + 2), $tardiness['apr']);
        $sheet->setCellValue("F" . ($headerRow + 2), $tardiness['may']);
        $sheet->setCellValue("G" . ($headerRow + 2), $tardiness['jun']);
        $sheet->setCellValue("H" . ($headerRow + 2), $tardiness['total'] . 'mins');

        $sheet->getStyle("A{$headerRow}:H" . ($headerRow + 2))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("A{$headerRow}:H{$headerRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$headerRow}:H" . ($headerRow + 2))->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $sheet->getStyle("B{$headerRow}:H" . ($headerRow + 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A" . ($headerRow + 1) . ":A" . ($headerRow + 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        return $headerRow + 3;
    }

    private function writeSignatureBlock(Worksheet $sheet, int $startRow): void
    {
        $supervisor = (string) ($this->payload['supervisor'] ?? '');
        $departmentHead = (string) ($this->payload['department_head'] ?? '');
        $employee = (string) ($this->payload['employee'] ?? '');

        $blocks = [
            ['range' => 'A:I', 'label' => 'Direct Supervisor', 'name' => $supervisor],
            ['range' => 'J:Q', 'label' => 'Department Head', 'name' => $departmentHead],
            ['range' => 'R:Y', 'label' => "Employees' Name", 'name' => $employee],
        ];

        foreach ($blocks as $block) {
            [$from, $to] = explode(':', $block['range']);
            $sheet->mergeCells("{$from}{$startRow}:{$to}{$startRow}");
            $sheet->setCellValue("{$from}{$startRow}", $block['label']);
            $sheet->getStyle("{$from}{$startRow}:{$to}{$startRow}")->getFont()->setBold(true);
            $sheet->getStyle("{$from}{$startRow}:{$to}{$startRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $lineRow = $startRow + 2;
            $sheet->mergeCells("{$from}{$lineRow}:{$to}{$lineRow}");
            $sheet->getStyle("{$from}{$lineRow}:{$to}{$lineRow}")
                ->getBorders()
                ->getTop()
                ->setBorderStyle(Border::BORDER_THIN);

            $sheet->mergeCells("{$from}" . ($lineRow + 1) . ":{$to}" . ($lineRow + 1));
            $sheet->setCellValue("{$from}" . ($lineRow + 1), $block['name']);
            $sheet->mergeCells("{$from}" . ($lineRow + 2) . ":{$to}" . ($lineRow + 2));
            $sheet->setCellValue("{$from}" . ($lineRow + 2), 'Position');
            $sheet->mergeCells("{$from}" . ($lineRow + 3) . ":{$to}" . ($lineRow + 3));
            $sheet->setCellValue("{$from}" . ($lineRow + 3), 'Date: _____________');

            $sheet->getStyle("{$from}" . ($lineRow + 1) . ":{$to}" . ($lineRow + 3))->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
        }
    }

    private function normalizeRows(array $rows): array
    {
        $normalized = [];

        foreach ($rows as $row) {
            $months = $this->normalizeMonths($row['months'] ?? []);
            $qtyBand = $this->buildBandFromMonths($months, 'qty');
            $qualityBand = $this->buildBandFromMonths($months, 'q_points');
            $timelinessBand = $this->buildBandFromMonths($months, 't_points');

            $qtyTotal = (int) ($qtyBand['total'] ?? 0);
            $targetQuantity = isset($row['target_quantity']) && is_numeric($row['target_quantity'])
                ? (float) $row['target_quantity']
                : 0.0;

            $qtyBand['avg'] = ($qtyTotal > 0 && $targetQuantity > 0)
                ? round(min(5.0, 5.0 * ($qtyTotal / $targetQuantity)), 2)
                : 0;

            $qualityBand['avg'] = $qtyTotal > 0
                ? round(((int) ($qualityBand['total'] ?? 0)) / $qtyTotal, 2)
                : 0;
            $timelinessBand['avg'] = $qtyTotal > 0
                ? round(((int) ($timelinessBand['total'] ?? 0)) / $qtyTotal, 2)
                : 0;

            $normalized[] = [
                'label' => (string) ($row['label'] ?? ''),
                'qty' => $qtyBand,
                'quality' => $qualityBand,
                'timeliness' => $timelinessBand,
            ];
        }

        return $normalized;
    }

    private function normalizeSections(array $payload): array
    {
        $sections = [];
        $payloadSections = is_array($payload['sections'] ?? null) ? $payload['sections'] : [];

        foreach ($payloadSections as $section) {
            $rows = $this->normalizeRows(is_array($section['rows'] ?? null) ? $section['rows'] : []);
            if (empty($rows)) {
                continue;
            }

            $sections[] = [
                'label' => (string) ($section['label'] ?? 'FUNCTIONS'),
                'rows' => $rows,
            ];
        }

        if (!empty($sections)) {
            return $sections;
        }

        $legacySections = [
            [
                'label' => 'CORE FUNCTION (80%)',
                'rows' => $this->normalizeRows($payload['core'] ?? []),
            ],
            [
                'label' => 'SUPPORT FUNCTIONS (20%)',
                'rows' => $this->normalizeRows($payload['support'] ?? []),
            ],
        ];

        return array_values(array_filter($legacySections, static fn (array $section): bool => !empty($section['rows'])));
    }

    private function normalizeMonths(array $months): array
    {
        $normalized = [];

        foreach (self::RAW_MONTH_KEYS as $month) {
            $normalized[$month] = [
                'qty' => (int) ($months[$month]['qty'] ?? 0),
                'q_points' => (int) ($months[$month]['q_points'] ?? 0),
                't_points' => (int) ($months[$month]['t_points'] ?? 0),
            ];
        }

        return $normalized;
    }

    private function buildBandFromMonths(array $months, string $field): array
    {
        $band = [];
        $total = 0;
        $activeMonths = 0;

        foreach (self::RAW_MONTH_KEYS as $month) {
            $value = (int) ($months[$month][$field] ?? 0);
            $band[$month] = $value;
            $total += $value;
            if ($value > 0) {
                $activeMonths++;
            }
        }

        $band['total'] = $total;
        $band['avg'] = $activeMonths > 0 ? round($total / $activeMonths, 2) : 0;

        return $band;
    }

    private function normalizeAttendanceBand(array $values): array
    {
        $band = [];

        foreach (self::RAW_MONTH_KEYS as $month) {
            $band[$month] = (int) ($values[$month] ?? 0);
        }

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

