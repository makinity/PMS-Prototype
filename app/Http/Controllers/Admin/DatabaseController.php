<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminDatabaseException;
use App\Services\AdminDatabaseService;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

class DatabaseController extends Controller
{
    public function __construct(
        private readonly AdminDatabaseService $databaseService,
        private readonly AuditLogService $auditLogs,
    ) {
    }

    public function index(Request $request): View
    {
        self::ensureAdmin($request);

        return view('admin.database', [
            'status' => $this->databaseService->environmentStatus(),
            'backups' => $this->databaseService->listBackups(),
            'confirmationPhrase' => $this->databaseService->confirmationPhrase(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        self::ensureAdmin($request);

        try {
            $backup = $this->databaseService->createBackup();
            $this->logAction($request, 'create_backup', $backup, 'success');

            return back()->with('success', 'Database backup created successfully.');
        } catch (AdminDatabaseException $e) {
            $this->logAction($request, 'create_backup', null, 'failed', $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

    public function download(Request $request, string $backup)
    {
        self::ensureAdmin($request);

        try {
            $resolved = $this->databaseService->resolveBackup($backup);
            $this->logAction($request, 'download_backup', $resolved, 'success');

            return Response::download(
                $this->databaseService->absoluteBackupPath($resolved['filename']),
                $resolved['filename'],
                ['Content-Type' => 'application/sql']
            );
        } catch (AdminDatabaseException $e) {
            $this->logAction($request, 'download_backup', ['filename' => $backup], 'failed', $e->getMessage());

            return redirect()->route('admin.database')->with('error', $e->getMessage());
        }
    }

    public function restore(Request $request, string $backup): RedirectResponse
    {
        self::ensureAdmin($request);

        $validated = $request->validate([
            'restore_confirmation' => ['required', 'string'],
        ]);

        try {
            $resolved = $this->databaseService->restoreBackup($backup, (string) $validated['restore_confirmation']);
            $this->logAction($request, 'restore_backup', $resolved, 'success');

            return back()->with('success', 'Database restore completed successfully.');
        } catch (AdminDatabaseException $e) {
            $this->logAction($request, 'restore_backup', ['filename' => $backup], 'failed', $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, string $backup): RedirectResponse
    {
        self::ensureAdmin($request);

        try {
            $resolved = $this->databaseService->deleteBackup($backup);
            $this->logAction($request, 'delete_backup', $resolved, 'success');

            return back()->with('success', 'Backup file deleted successfully.');
        } catch (AdminDatabaseException $e) {
            $this->logAction($request, 'delete_backup', ['filename' => $backup], 'failed', $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

    private function logAction(
        Request $request,
        string $actionKey,
        ?array $backup,
        string $status,
        ?string $message = null
    ): void {
        $actor = $request->user();

        $this->auditLogs->log([
            'actor_user_id' => $actor?->id,
            'actor_name' => $actor?->name,
            'actor_role' => $actor?->role,
            'action_key' => $actionKey,
            'module_key' => 'admin.database',
            'target_type' => 'database_backup',
            'target_id' => (string) ($backup['filename'] ?? 'database'),
            'route_name' => $request->route()?->getName(),
            'http_method' => strtoupper($request->method()),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => $status,
            'summary' => ucfirst(str_replace('_', ' ', $actionKey)) . ($status === 'failed' ? ' failed' : ' completed'),
            'metadata' => array_filter([
                'filename' => $backup['filename'] ?? null,
                'database' => $backup['database'] ?? null,
                'size_bytes' => $backup['size_bytes'] ?? null,
                'message' => $message,
            ], static fn ($value) => !($value === null || $value === '')),
        ]);
    }

    private static function ensureAdmin(Request $request): void
    {
        $actor = $request->user();
        abort_if(!$actor || strtolower((string) $actor->role) !== 'admin', 403, 'Unauthorized.');
    }
}
