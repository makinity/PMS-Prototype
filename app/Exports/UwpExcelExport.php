<?php

namespace App\Exports;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTemplate;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UwpExcelExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    use Exportable;

    private const RATINGS = [5, 4, 3, 2, 1];
    private const TEMPLATE_PATH = 'templates/uwp_template.xlsx';
    private const TOTAL_COLUMNS = 8;
    private const STANDARDS_COLUMNS = [
        5 => 'D',
        4 => 'E',
        3 => 'F',
        2 => 'G',
        1 => 'H',
    ];

    private array $uwp;
    private array $standards;
    private array $rows = [];
    private bool $templateAvailable;

    public function __construct(array $uwp, array $standards)
    {
        $this->uwp = $uwp;
        $this->standards = $standards;
        $this->templateAvailable = file_exists(resource_path(self::TEMPLATE_PATH));
        $this->buildRows();
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
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

    public function template(): string
    {
        return $this->templateAvailable ? resource_path(self::TEMPLATE_PATH) : '';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                if ($this->templateAvailable) {
                     $sheet->removeRow(5, 13);
                    $this->populateTemplate($sheet);
                } else {
                    $sheet->fromArray($this->rows, null, 'A1', true);
                }
            },
        ];
    }

    private function buildRows(): void
    {
        $this->addRow(['UNIT WORK PLAN (UWP)']);
        $this->addRow(['Performance Period:', $this->uwp['period']]);
        $this->addRow(['Office / Unit:', $this->uwp['office']]);
        $this->addRow([
            'Supervisor:', $this->uwp['supervisor'],
            'Department Head:', $this->uwp['dept_head'],
        ]);
        $this->addRow([]);
        $header = [
            'PPA / MFO',
            'Success Indicator',
            'Allotted Budget',
            'Rating 5 – Standards (Q / E / T)',
            'Rating 4 – Standards (Q / E / T)',
            'Rating 3 – Standards (Q / E / T)',
            'Rating 2 – Standards (Q / E / T)',
            'Rating 1 – Standards (Q / E / T)',
        ];
        $this->addRow($header);
        $this->addSection('core', 'A. CORE FUNCTIONS (80%)');
        $this->addSection('support', 'B. SUPPORT FUNCTIONS (20%)');
    }

    private function addSection(string $type, string $label): void
    {
        $outputs = Arr::where($this->uwp['outputs'] ?? [], fn ($row) => Str::contains(Str::lower($row['function']), $type));
        if (empty($outputs)) {
            return;
        }
        $this->addRow([$label]);
        foreach ($outputs as $output) {
            foreach ($output['success_indicators'] ?? [] as $indicator) {
                $row = [
                    $output['mfo'],
                    $indicator,
                    '',
                ];
                foreach (self::RATINGS as $rating) {
                    $row[] = $this->formatStandards($indicator, $rating);
                }
                $this->addRow($row);
            }
        }
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

    private function addRow(array $row): void
    {
        $this->rows[] = array_pad($row, self::TOTAL_COLUMNS, null);
    }

    private function populateTemplate(Worksheet $sheet): void
    {
        $sheet->setCellValue('E3', $this->uwp['period']);
        $sheet->setCellValue('B5', $this->uwp['office']);
        $sheet->setCellValue('G5', $this->uwp['supervisor']);
        $sheet->setCellValue('D6', $this->uwp['dept_head']);

        $currentRow = 19;

        $currentRow = $this->writeSection($sheet, 'core', 'A. CORE FUNCTIONS (80%)', $currentRow);
        $currentRow = $this->writeSection($sheet, 'support', 'B. SUPPORT FUNCTIONS (20%)', $currentRow);

        $tableRange = "A19:H{$currentRow}";
        $sheet->getStyle($tableRange)->getAlignment()
            ->setWrapText(true)
            ->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
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
}
