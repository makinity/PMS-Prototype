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
        $sheet->getStyle($tableRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
    }

    private function writeManualHeader(Worksheet $sheet): void
    {
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'UNIT WORK PLAN (UWP)');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $period = $this->uwp['period'] ?? '';
        $period = preg_replace('/\s*[-–—]+\s*/u', ' ' . "\u{2013}" . ' ', $period);
        $sheet->setCellValue('E3', trim($period));
        $sheet->getStyle('E3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A5', 'Office / Unit:');
        $sheet->getStyle('A5')->getFont()->setBold(true);
        $sheet->setCellValue('B5', $this->uwp['office'] ?? '');

        $sheet->setCellValue('F5', 'Supervisor:');
        $sheet->getStyle('F5')->getFont()->setBold(true);
        $sheet->setCellValue('G5', $this->uwp['supervisor'] ?? '');

        $sheet->setCellValue('A6', 'Department Head:');
        $sheet->getStyle('A6')->getFont()->setBold(true);
        $sheet->setCellValue('D6', $this->uwp['dept_head'] ?? '');

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
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $sheet->setCellValue('D' . self::TABLE_HEADER_ROW, 'Standards per Success Indicator');
        $sheet->mergeCells('D' . self::TABLE_HEADER_ROW . ':H' . self::TABLE_HEADER_ROW);
        $sheet->getStyle('D' . self::TABLE_HEADER_ROW)->getFont()->setBold(true);
        $sheet->getStyle('D' . self::TABLE_HEADER_ROW)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        foreach (self::RATINGS as $rating) {
            $column = self::STANDARDS_COLUMNS[$rating];
            $cell = "{$column}" . self::TABLE_RATING_ROW;
            $sheet->setCellValue($cell, (string) $rating);
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
        $sheet->getStyle('D' . self::TABLE_RATING_ROW . ':H' . self::TABLE_RATING_ROW)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    private function writeSection(Worksheet $sheet, string $type, string $label, int $startRow): int
    {
        $sheet->setCellValue("A{$startRow}", $label);
        $sheet->mergeCells("A{$startRow}:H{$startRow}");
        $sheet->getStyle("A{$startRow}:H{$startRow}")->getFont()->setBold(true);
        $row = $startRow + 1;

        $outputs = Arr::where($this->uwp['outputs'] ?? [], fn ($row) => Str::contains(Str::lower($row['function']), $type));
        foreach ($outputs as $output) {
            $indicatorCount = count($output['success_indicators'] ?? []);
            $mfoStart = $row;
            foreach ($output['success_indicators'] ?? [] as $indicator) {
                $sheet->setCellValue("B{$row}", $indicator);
                $sheet->setCellValue("C{$row}", '');
                foreach (self::RATINGS as $rating) {
                    $sheet->setCellValue(self::STANDARDS_COLUMNS[$rating] . $row, $this->formatStandards($indicator, $rating));
                }
                $row++;
            }
            if ($indicatorCount > 0) {
                $sheet->mergeCells("A{$mfoStart}:A" . ($row - 1));
                $sheet->setCellValue("A{$mfoStart}", $output['mfo']);
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
}
