<?php

namespace App\Exports\StageThree;

use App\Models\Opcr;
use App\Models\Ipcr;
use App\Services\PerformanceRatingService;
use Illuminate\Support\Arr;
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

    private const BASE_ROW_HEIGHT = 22;
    private const LINE_HEIGHT = 14;
    private const CHARS_PER_LINE = 42;

    private Opcr $opcrModel;
    private array $opcrData = [];
    private array $standards = [];
    private array $sectionLabels = [];
    private array $sectionMeta = [];
    private PerformanceRatingService $ratingService;

    public function __construct(Opcr $opcr)
    {
        $this->opcrModel = $opcr;
        $this->ratingService = app(PerformanceRatingService::class);
        $this->hydrateFromModel();
    }

    public function array(): array { return []; }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 35, // Output
            'B' => 40, // Indicators
            'C' => 12, // Budget
            'D' => 25, // Individual Accountable
            'E' => 30, // Accomplishment Summary
            'F' => 6,  // Q
            'G' => 6,  // E
            'H' => 6,  // T
            'I' => 6,  // A
            'J' => 14, // Remarks
            'K' => 30, 'L' => 30, 'M' => 30, 'N' => 30, 'O' => 30, // Standards
        ];
    }

    public function title(): string { return 'OPCR Evaluation'; }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $this->populateTemplate($event->sheet->getDelegate());
            },
        ];
    }

    private function hydrateFromModel(): void
    {
        // 1. Only aggregate IPCRs that are officially Calibrated (Approved or Adjusted)
        $ipcrs = Ipcr::where('opcr_id', $this->opcrModel->id)
            ->whereIn('status', [Ipcr::STATUS_APPROVED_BY_PMT, Ipcr::STATUS_ADJUSTED_BY_PMT, Ipcr::STATUS_RELEASED_BY_PMT])
            ->get();
        $ipcrMaps = [];
        foreach ($ipcrs as $ipcr) {
            [$ratingsByOutput, $ratingsByIndicator] = $this->ratingService->buildRatedIpcrPerformanceMaps($ipcr);
            $ipcrMaps[$ipcr->id] = [
                'employee' => $ipcr->employee?->name ?? 'Unknown',
                'by_output' => $ratingsByOutput,
            ];
        }

        // 2. Load OPCR structure (MFOs)
        $sources = $this->opcrModel->sourceUnitWorkPlans();
        if ($sources->isEmpty()) return;

        foreach ($sources as $uwp) {
            $uwp->loadMissing(['uwpFunctions.mfos.successIndicators.qetStandards']);
            
            foreach ($uwp->uwpFunctions as $function) {
                $bucket = $this->normalizeFunctionType((string) ($function->function_type ?? ''));
                
                if (!isset($this->sectionMeta[$bucket])) {
                    $this->sectionMeta[$bucket] = [
                        'weight_percent' => (float) ($function->weight_percent ?? 0),
                        'sort_order' => $function->sort_order ?? 1000,
                        'outputs' => []
                    ];
                }

                foreach ($function->mfos as $mfo) {
                    $mfoTitle = trim((string) ($mfo->title ?? ''));
                    $indicators = [];
                    foreach ($mfo->successIndicators as $indicator) {
                        $indicators[] = (string) ($indicator->indicator_text ?? '');
                        foreach (self::RATINGS as $r) {
                            $this->standards[$indicator->indicator_text][$r] = ['q' => [], 'e' => [], 't' => []];
                            foreach ($indicator->qetStandards as $std) {
                                if ((int)$std->rating === $r) {
                                    $dim = strtolower(substr((string)$std->dimension, 0, 1));
                                    if (in_array($dim, ['q','e','t'])) $this->standards[$indicator->indicator_text][$r][$dim][] = $std->standard_text;
                                }
                            }
                        }
                    }

                    // 3. Aggregate accomplishments for this MFO Title
                    $qty = 0; $qSum = 0; $eSum = 0; $tSum = 0; $aSum = 0; $ratingCount = 0;
                    $accountableList = [];
                    
                    foreach ($ipcrMaps as $map) {
                        if (isset($map['by_output'][$mfoTitle])) {
                            $r = $map['by_output'][$mfoTitle];
                            $qty += (float)($r['qty'] ?? 0);
                            $qSum += (float)($r['q'] ?? 0);
                            $eSum += (float)($r['e'] ?? 0);
                            $tSum += (float)($r['t'] ?? 0);
                            $aSum += (float)($r['a'] ?? 0);
                            $ratingCount++;
                            $accountableList[] = $map['employee'] . " (" . ($r['a'] ?? 0) . ")";
                        }
                    }

                    $this->opcrData[$bucket][] = [
                        'mfo' => $mfoTitle,
                        'indicators' => $indicators,
                        'target' => ($mfo->target_quantity !== null ? $mfo->target_quantity . ' ' : '') . ($mfo->target_summary ?? ''),
                        'accountable' => implode("\n", $accountableList),
                        'actual_qty' => $qty,
                        'q' => $ratingCount > 0 ? round($qSum / $ratingCount, 2) : 0,
                        'e' => $ratingCount > 0 ? round($eSum / $ratingCount, 2) : 0,
                        't' => $ratingCount > 0 ? round($tSum / $ratingCount, 2) : 0,
                        'a' => $ratingCount > 0 ? round($aSum / $ratingCount, 2) : 0,
                    ];
                }
            }
        }
    }

    private function populateTemplate(Worksheet $sheet): void
    {
        $this->setupPage($sheet);
        $this->writeManualHeader($sheet);
        $this->writeTableHeader($sheet);

        $row = self::TABLE_START_ROW;
        foreach ($this->buildSectionDefinitions() as $section) {
            $row = $this->writeSection($sheet, $section['type'], $section['label'], $row);
        }

        $lastRow = $row - 1;
        $sheet->getStyle('A' . self::TABLE_HEADER_ROW . ':O' . $lastRow)->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getStyle('A' . self::TABLE_HEADER_ROW . ':O' . self::TABLE_SUBHEADER_ROW)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        for ($i = self::TABLE_START_ROW; $i <= $lastRow; $i++) {
            foreach (range('A', 'O') as $col) {
                $sheet->getStyle($col . $i)->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle($col . $i)->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
            }
        }

        $this->writeFooterBlock($sheet, $lastRow + 1);
    }

    private function setupPage(Worksheet $sheet): void
    {
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_LEGAL);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1);
    }

    private function writeManualHeader(Worksheet $sheet): void
    {
        $sheet->mergeCells('A1:O1');
        $sheet->setCellValue('A1', 'OFFICE PERFORMANCE COMMITMENT AND REVIEW (OPCR) EVALUATION');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $period = $this->opcrModel->performancePeriod?->name ?? 'N/A';
        $sheet->mergeCells('A2:O2');
        $sheet->setCellValue('A2', "Period: $period");
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A4', 'Office / Unit:'); $sheet->setCellValue('B4', $this->opcrModel->office?->name ?? 'N/A');
        $sheet->setCellValue('A5', 'Department Head:'); $sheet->setCellValue('B5', $this->opcrModel->office?->head?->name ?? 'N/A');
    }

    private function writeTableHeader(Worksheet $sheet): void
    {
        $h = self::TABLE_HEADER_ROW; $sh = self::TABLE_SUBHEADER_ROW;
        $headers = [
            'A' => 'MFOs / PPAs', 'B' => 'Success Indicators', 'C' => 'Budget', 'D' => 'Individual Accountable',
            'E' => 'Actual Accomplishment', 'J' => 'Remarks'
        ];
        foreach ($headers as $col => $txt) {
            $sheet->setCellValue($col.$h, $txt);
            $sheet->mergeCells($col.$h.':'.$col.$sh);
        }

        $sheet->mergeCells('F'.$h.':I'.$h); $sheet->setCellValue('F'.$h, 'Rating');
        $sheet->setCellValue('F'.$sh, 'Q'); $sheet->setCellValue('G'.$sh, 'E'); $sheet->setCellValue('H'.$sh, 'T'); $sheet->setCellValue('I'.$sh, 'A');

        $sheet->mergeCells('K'.$h.':O'.$h); $sheet->setCellValue('K'.$h, 'Standards');
        foreach (self::RATINGS as $r) { $sheet->setCellValue(self::STANDARDS_COLUMNS[$r].$sh, $r); }

        $sheet->getStyle("A$h:O$sh")->getFont()->setBold(true);
        $sheet->getStyle("A$h:O$sh")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A$h:O$sh")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D1D5DB');
    }

    private function writeSection(Worksheet $sheet, string $type, string $label, int $startRow): int
    {
        $sheet->setCellValue("A$startRow", $label);
        $sheet->mergeCells("A$startRow:O$startRow");
        $sheet->getStyle("A$startRow:O$startRow")->getFont()->setBold(true);
        $sheet->getStyle("A$startRow:O$startRow")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E5E7EB');
        
        $row = $startRow + 1;
        foreach ($this->opcrData[$type] ?? [] as $item) {
            $mfoStart = $row;
            $indicatorCount = count($item['indicators']);
            
            foreach ($item['indicators'] as $idx => $ind) {
                if ($idx === 0) {
                    $sheet->setCellValue("A$row", $item['mfo']);
                    $sheet->setCellValue("D$row", $item['accountable']);
                    $sheet->setCellValue("E$row", $item['actual_qty'] . " units achieved\nTarget: " . $item['target']);
                    $sheet->setCellValue("F$row", $item['q']); $sheet->setCellValue("G$row", $item['e']); $sheet->setCellValue("H$row", $item['t']); $sheet->setCellValue("I$row", $item['a']);
                }
                $sheet->setCellValue("B$row", $ind);
                foreach (self::RATINGS as $r) {
                    $sheet->setCellValue(self::STANDARDS_COLUMNS[$r].$row, $this->formatStdBlock($ind, $r));
                }
                $row++;
            }
            if ($indicatorCount > 1) {
                $sheet->mergeCells("A$mfoStart:A".($row-1));
                $sheet->mergeCells("D$mfoStart:D".($row-1));
                $sheet->mergeCells("E$mfoStart:E".($row-1));
                $sheet->mergeCells("F$mfoStart:F".($row-1));
                $sheet->mergeCells("G$mfoStart:G".($row-1));
                $sheet->mergeCells("H$mfoStart:H".($row-1));
                $sheet->mergeCells("I$mfoStart:I".($row-1));
            }
        }
        return $row;
    }

    private function formatStdBlock(string $ind, int $r): string
    {
        $e = $this->standards[$ind][$r] ?? ['q'=>[],'e'=>[],'t'=>[]];
        return "Q: ".implode('; ',$e['q'])."\nE: ".implode('; ',$e['e'])."\nT: ".implode('; ',$e['t']);
    }

    private function buildSectionDefinitions(): array
    {
        $sections = [];
        $priority = ['core' => 'A', 'support' => 'B', 'custom' => 'C'];
        foreach ($this->sectionMeta as $type => $meta) {
            $sections[] = ['type' => $type, 'label' => ($priority[$type] ?? 'D') . ". " . strtoupper($type) . " FUNCTIONS (" . $meta['weight_percent'] . "%)"];
        }
        usort($sections, fn($a, $b) => $a['label'] <=> $b['label']);
        return $sections;
    }

    private function normalizeFunctionType(string $t): string { $t = strtolower(trim($t)); return in_array($t, ['core','support']) ? $t : 'custom'; }

    private function writeFooterBlock(Worksheet $sheet, int $row): void
    {
        $sheet->mergeCells("A$row:J$row"); $sheet->setCellValue("A$row", "FINAL OVERALL RATING");
        $finalScore = \App\Models\Ipcr::where('opcr_id', $this->opcrModel->id)->whereNotNull('final_score')->avg('final_score') ?? 0;
        $sheet->setCellValue("I$row", round($finalScore, 2));
        $sheet->getStyle("A$row:O$row")->getFont()->setBold(true);
        $sheet->getStyle("A$row:O$row")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }
}
