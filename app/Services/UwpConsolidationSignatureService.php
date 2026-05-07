<?php

namespace App\Services;

use App\Models\UwpConsolidationSignature;
use App\Models\User;
use App\Exports\StageOne\UwpExcelExport;
use App\Models\UnitWorkPlan;
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
            foreach ($existingSignatures as $existing) {
                $role = $existing->signer?->role;
                if ($role && $existing->signed_by !== $currentUser?->id) {
                    $absPath = $disk->path($existing->signature_image_path);
                    if (File::exists($absPath)) {
                        $this->injectSignatureToWorksheet($worksheet, $absPath, $role, $targetSignatureRow);
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
        $name = $role === 'dept-head' ? 'Department Head Signature' : 'Supervisor Signature';
        $drawing->setName($name);
        $drawing->setDescription($name);
        $drawing->setPath($absPath);
        
        // Supervisor is block 1 (A:B), Dept Head is block 2 (C:D)
        $column = $role === 'dept-head' ? 'C' : 'A';
        
        $drawing->setCoordinates("{$column}{$row}");
        $drawing->setOffsetX(15); 
        $drawing->setOffsetY(5);
        $drawing->setHeight(75);
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
