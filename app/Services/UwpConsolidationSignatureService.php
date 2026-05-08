<?php

namespace App\Services;

use App\Models\UwpConsolidationSignature;
use App\Models\User;
use App\Exports\StageOne\UwpExcelExport;
use App\Models\UnitWorkPlan;
use App\Models\Opcr;
use App\Exports\StageOne\OpcrExcelExport;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class UwpConsolidationSignatureService
{
    public function __construct(
        private readonly UwpExcelPayloadService $payloadService,
    ) {
    }

    public function createSignedOpcrArtifact(Opcr $opcr, string $signatureDataUrl, bool $isPmt = false): array
    {
        $currentSignatureBinary = $this->decodeSignatureDataUrl($signatureDataUrl);
        
        $xlsxBinary = Excel::raw(
            new OpcrExcelExport($opcr),
            \Maatwebsite\Excel\Excel::XLSX
        );

        $disk = Storage::disk('public');
        $timestamp = now()->format('Ymd_His');
        $suffix = Str::lower(Str::random(8));

        $signaturePath = "signatures/opcr/opcr_{$opcr->id}_{$timestamp}_{$suffix}.png";
        $signedExcelPath = "opcr/signed/opcr_{$opcr->id}_{$timestamp}_{$suffix}.xlsx";

        $signatureAbsolutePath = $disk->path($signaturePath);
        $signedExcelAbsolutePath = $disk->path($signedExcelPath);

        File::ensureDirectoryExists(dirname($signatureAbsolutePath));
        File::ensureDirectoryExists(dirname($signedExcelAbsolutePath));

        $disk->put($signaturePath, $currentSignatureBinary);

        // Find existing signatures for this OPCR
        $existingSignatures = UwpConsolidationSignature::query()
            ->where('opcr_id', $opcr->id)
            ->with('signer')
            ->get();

        $tempWorkbookPath = tempnam(sys_get_temp_dir(), 'opcr-sign-');
        if ($tempWorkbookPath === false) {
            throw new \RuntimeException('Unable to allocate temporary workbook path.');
        }

        try {
            file_put_contents($tempWorkbookPath, $xlsxBinary);

            $spreadsheet = IOFactory::load($tempWorkbookPath);
            $worksheet = $spreadsheet->getActiveSheet();
            
            $signatureBaseRow = null;
            $searchLimit = min(2000, (int)$worksheet->getHighestRow());
            $searchLabel = $isPmt ? 'Assessed by:' : 'Discussed with and Agreed by:';
            
            for ($r = $searchLimit; $r >= 1; $r--) {
                $cellVal = trim((string)$worksheet->getCell("A{$r}")->getValue());
                // For PMT, it might be in Column G? Wait, let's check Column G too
                if ($isPmt) {
                    $cellValG = trim((string)$worksheet->getCell("G{$r}")->getValue());
                    if ($cellValG === $searchLabel) {
                        $signatureBaseRow = $r;
                        break;
                    }
                }

                if ($cellVal === $searchLabel) {
                    $signatureBaseRow = $r;
                    break;
                }
            }

            $targetSignatureRow = $signatureBaseRow ? ($signatureBaseRow + 1) : max(1, (int)$worksheet->getHighestRow() - 2);

            // Inject current signature
            $this->injectSignatureToWorksheet(
                $worksheet, 
                $signatureAbsolutePath, 
                $isPmt ? 'pmt-chairperson' : 'opcr-dept-head',
                $targetSignatureRow
            );

            // Inject existing signatures
            // Group by role and take only the latest one per role
            $latestSignaturesByRole = $existingSignatures
                ->sortByDesc('signed_at')
                ->unique(function ($s) {
                    return ($s->metadata['action'] ?? '') === 'dept_head_endorse_opcr' ? 'opcr-dept-head' : 'pmt-chairperson';
                });

            foreach ($latestSignaturesByRole as $existing) {
                $existingAction = $existing->metadata['action'] ?? '';
                $existingRole = $existingAction === 'dept_head_endorse_opcr' ? 'opcr-dept-head' : 'pmt-chairperson';
                $currentRole = $isPmt ? 'pmt-chairperson' : 'opcr-dept-head';

                // Avoid duplicating the role we just injected above
                if ($existingRole !== $currentRole) {
                    $absPath = $disk->path($existing->signature_image_path);
                    if (File::exists($absPath)) {
                        $this->injectSignatureToWorksheet($worksheet, $absPath, $existingRole, $targetSignatureRow);
                    }
                }
            }

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($signedExcelAbsolutePath);
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet, $writer);
        } finally {
            @unlink($tempWorkbookPath);
        }

        return [
            'signature_image_path' => $signaturePath,
            'signed_excel_path' => $signedExcelPath,
            'signature_hash' => hash('sha256', $currentSignatureBinary),
        ];
    }

    public function createSignedArtifact(UnitWorkPlan $uwp, string $signatureDataUrl): array
    {
        $currentSignatureBinary = $this->decodeSignatureDataUrl($signatureDataUrl);
        $payload = $this->payloadService->build($uwp);
        
        $xlsxBinary = Excel::raw(
            new UwpExcelExport($payload['uwp'], $payload['standards']),
            \Maatwebsite\Excel\Excel::XLSX
        );

        $disk = Storage::disk('public');
        $timestamp = now()->format('Ymd_His');
        $suffix = Str::lower(Str::random(8));

        $signaturePath = "signatures/uwp/uwp_{$uwp->id}_{$timestamp}_{$suffix}.png";
        $signedExcelPath = "uwp/signed/uwp_{$uwp->id}_{$timestamp}_{$suffix}.xlsx";

        $signatureAbsolutePath = $disk->path($signaturePath);
        $signedExcelAbsolutePath = $disk->path($signedExcelPath);

        File::ensureDirectoryExists(dirname($signatureAbsolutePath));
        File::ensureDirectoryExists(dirname($signedExcelAbsolutePath));

        $disk->put($signaturePath, $currentSignatureBinary);

        // Determine current signer role
        $currentUser = auth()->user();
        $isDeptHead = $currentUser && $currentUser->role === 'dept-head';

        // Find existing signatures for this UWP to include in the Excel
        $existingSignatures = UwpConsolidationSignature::query()
            ->where('unit_work_plan_id', $uwp->id)
            ->with('signer')
            ->get();

        $tempWorkbookPath = tempnam(sys_get_temp_dir(), 'uwp-sign-');
        if ($tempWorkbookPath === false) {
            throw new \RuntimeException('Unable to allocate temporary workbook path.');
        }

        try {
            file_put_contents($tempWorkbookPath, $xlsxBinary);

            $spreadsheet = IOFactory::load($tempWorkbookPath);
            $worksheet = $spreadsheet->getActiveSheet();
            $signatureBaseRow = null;
            $searchLimit = min(2000, (int)$worksheet->getHighestRow());
            for ($r = $searchLimit; $r >= 1; $r--) {
                $cellVal = trim((string)$worksheet->getCell("A{$r}")->getValue());
                if ($cellVal === 'Prepared by:') {
                    $signatureBaseRow = $r;
                    break;
                }
            }

            // Fallback if not found
            $targetSignatureRow = $signatureBaseRow ? ($signatureBaseRow + 1) : max(1, (int)$worksheet->getHighestRow() - 2);

            // 1. Inject current signature
            $this->injectSignatureToWorksheet(
                $worksheet, 
                $signatureAbsolutePath, 
                $isDeptHead ? 'dept-head' : 'supervisor',
                $targetSignatureRow
            );

            // 2. Inject existing signatures (if any and not the current one)
            // Group by role and take only the latest one per role
            $latestSignaturesByRole = $existingSignatures
                ->sortByDesc('signed_at')
                ->unique(fn($s) => $s->signer?->role);

            foreach ($latestSignaturesByRole as $existing) {
                $existingRole = $existing->signer?->role;
                $currentRole = $isDeptHead ? 'dept-head' : 'supervisor';

                if ($existingRole && $existingRole !== $currentRole) {
                    $absPath = $disk->path($existing->signature_image_path);
                    if (File::exists($absPath)) {
                        $this->injectSignatureToWorksheet($worksheet, $absPath, $existingRole, $targetSignatureRow);
                    }
                }
            }

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($signedExcelAbsolutePath);
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet, $writer);
        } finally {
            @unlink($tempWorkbookPath);
        }

        return [
            'signature_image_path' => $signaturePath,
            'signed_excel_path' => $signedExcelPath,
            'signature_hash' => hash('sha256', $currentSignatureBinary),
        ];
    }

    private function injectSignatureToWorksheet($worksheet, string $absPath, string $role, int $row): void
    {
        $drawing = new Drawing();
        $name = str_contains($role, 'dept-head') || str_contains($role, 'pmt') ? 'Head Signature' : 'Supervisor Signature';
        $drawing->setName($name);
        $drawing->setPath($absPath);

        // Define target widths in pixels based on actual Excel column widths (chars * ~7.5)
        // Supervisor (UWP A+B): 32+40=72 chars -> ~540px
        // Dept-head (UWP C+D): 18+30=48 chars -> ~360px
        // OPCR Dept-head (A+B+C+D): 35+40+12+18=105 chars -> ~780px
        // PMT Chairperson (OPCR G+H+I): 6+6+6=18 chars -> ~135px
        $targetWidth = 540;
        $anchor = "A{$row}";

        if ($role === 'dept-head') {
            $targetWidth = 360;
            $anchor = "C{$row}";
        } elseif ($role === 'opcr-dept-head') {
            $targetWidth = 780;
            $anchor = "A{$row}";
        } elseif ($role === 'pmt-chairperson') {
            $targetWidth = 135;
            $anchor = "G{$row}";
        }

        // Calculate centering using actual image dimensions
        $size = getimagesize($absPath);
        $origW = (float) ($size[0] ?? 1);
        $origH = (float) ($size[1] ?? 1);
        $maxH = 52.0;

        // Scale proportionally to fit max height
        $scaledW = ($origW / $origH) * $maxH;

        // Ensure it doesn't overflow the target width
        if ($scaledW > ($targetWidth - 20)) {
            $scaledW = (float) ($targetWidth - 20);
            $scaledH = ($origH / $origW) * $scaledW;
        } else {
            $scaledH = $maxH;
        }

        $drawing->setCoordinates($anchor);
        $drawing->setResizeProportional(true);
        $drawing->setHeight((int) $scaledH);
        $drawing->setWidth((int) $scaledW);

        // Center horizontally within the block
        $offsetX = (int) max(0, ($targetWidth - $scaledW) / 2);
        $drawing->setOffsetX($offsetX);
        $drawing->setOffsetY(-4);
        
        $drawing->setWorksheet($worksheet);
    }

    public function cleanupArtifact(array $artifact): void
    {
        $disk = Storage::disk('public');

        foreach (['signature_image_path', 'signed_excel_path'] as $key) {
            $path = trim((string) ($artifact[$key] ?? ''));
            if ($path !== '' && $disk->exists($path)) {
                $disk->delete($path);
            }
        }
    }

    public function decodeSignatureDataUrl(string $signatureDataUrl): string
    {
        $signatureDataUrl = trim($signatureDataUrl);

        if (!str_starts_with($signatureDataUrl, 'data:image/png;base64,')) {
            throw new \InvalidArgumentException('Signature must be a base64-encoded PNG data URL.');
        }

        $encoded = substr($signatureDataUrl, strlen('data:image/png;base64,'));
        if ($encoded === '') {
            throw new \InvalidArgumentException('Signature payload is empty.');
        }

        $decoded = base64_decode($encoded, true);
        if ($decoded === false || $decoded === '') {
            throw new \InvalidArgumentException('Signature payload is invalid.');
        }

        return $decoded;
    }
}
