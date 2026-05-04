<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AuditLogsController extends Controller
{
    public function index(Request $request)
    {
        self::ensureAdmin($request);

        return view('admin.audit-logs', self::buildIndexPayload($request));
    }

    public static function buildIndexPayload(Request $request): array
    {
        self::ensureAdmin($request);

        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'actor_user_id' => trim((string) $request->query('actor_user_id', '')),
            'role' => trim((string) $request->query('role', '')),
            'module' => trim((string) $request->query('module', '')),
            'action' => trim((string) $request->query('action', '')),
            'status' => trim((string) $request->query('status', '')),
            'date_from' => trim((string) $request->query('date_from', '')),
            'date_to' => trim((string) $request->query('date_to', '')),
        ];

        $baseQuery = AuditLog::query()->with('actor:id,name,email,role');
        $filteredQuery = self::applyFilters(clone $baseQuery, $filters);

        $logs = (clone $filteredQuery)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $summaryQuery = clone $filteredQuery;
        $totalCount = (clone $summaryQuery)->count();
        $successCount = (clone $summaryQuery)->where('status', 'success')->count();
        $failedCount = (clone $summaryQuery)->where('status', 'failed')->count();
        $uniqueActorsCount = (clone $summaryQuery)->whereNotNull('actor_user_id')->distinct('actor_user_id')->count('actor_user_id');

        return [
            'logs' => $logs,
            'filters' => $filters,
            'actors' => self::actorOptions(),
            'roles' => self::distinctColumnValues('actor_role'),
            'modules' => self::distinctColumnValues('module_key'),
            'actions' => self::distinctColumnValues('action_key'),
            'summary' => [
                'total' => $totalCount,
                'success' => $successCount,
                'failed' => $failedCount,
                'unique_actors' => $uniqueActorsCount,
            ],
        ];
    }

    private static function applyFilters(Builder $query, array $filters): Builder
    {
        if ($filters['search'] !== '') {
            $search = $filters['search'];
            $query->where(function (Builder $inner) use ($search) {
                $inner->where('actor_name', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%")
                    ->orWhere('module_key', 'like', "%{$search}%")
                    ->orWhere('action_key', 'like', "%{$search}%")
                    ->orWhere('target_id', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($filters['actor_user_id'] !== '' && ctype_digit($filters['actor_user_id'])) {
            $query->where('actor_user_id', (int) $filters['actor_user_id']);
        }

        if ($filters['role'] !== '') {
            $query->where('actor_role', $filters['role']);
        }

        if ($filters['module'] !== '') {
            $query->where('module_key', $filters['module']);
        }

        if ($filters['action'] !== '') {
            $query->where('action_key', $filters['action']);
        }

        if (in_array($filters['status'], ['success', 'failed'], true)) {
            $query->where('status', $filters['status']);
        }

        if ($filters['date_from'] !== '') {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if ($filters['date_to'] !== '') {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query;
    }

    private static function actorOptions(): array
    {
        return AuditLog::query()
            ->select(['actor_user_id', 'actor_name'])
            ->whereNotNull('actor_user_id')
            ->whereNotNull('actor_name')
            ->groupBy('actor_user_id', 'actor_name')
            ->orderBy('actor_name')
            ->get()
            ->map(static fn (AuditLog $log): array => [
                'id' => (int) $log->actor_user_id,
                'name' => (string) $log->actor_name,
            ])
            ->values()
            ->all();
    }

    private static function distinctColumnValues(string $column): array
    {
        return AuditLog::query()
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->orderBy($column)
            ->distinct()
            ->pluck($column)
            ->all();
    }

    private static function ensureAdmin(Request $request): void
    {
        $actor = $request->user();
        abort_if(!$actor || strtolower((string) $actor->role) !== 'admin', 403, 'Unauthorized.');
    }
}
