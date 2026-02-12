<?php

namespace App\Http\Controllers\StageOne\Planning;

use App\Http\Controllers\Controller;
use App\Models\UnitWorkPlan;
use App\Models\User;
use App\Models\UwpFunction;
use App\Models\UwpIndicatorAssignment;
use App\Models\UwpQetStandard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class UnitWorkPlanController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = UnitWorkPlan::query()
            ->with(['office', 'performancePeriod'])
            ->orderByDesc('id');

        if ($user->role === 'supervisor') {
            $query->where('created_by', $user->id);
        }

        $uwps = $query->paginate(10);

        return view('stages.stage1.uwp.index', compact('uwps'));
    }

    public function create(Request $request)
    {
        $this->ensureRole($request->user()->role, ['supervisor']);

        // Your blade will likely load offices/periods via dropdown
        return view('stages.stage1.uwp.create');
    }

    public function store(Request $request)
    {
        $this->ensureRole($request->user()->role, ['supervisor']);

        $data = $request->validate([
            'office_id' => ['required', 'integer', 'exists:offices,id'],
            'performance_period_id' => ['required', 'integer', 'exists:performance_periods,id'],
        ]);

        $user = $request->user();

        $uwp = UnitWorkPlan::create([
            'office_id' => $data['office_id'],
            'performance_period_id' => $data['performance_period_id'],
            'created_by' => $user->id,
            'status' => UnitWorkPlan::STATUS_DRAFT,
        ]);

        return redirect()
            ->route('stage1.uwp.show', $uwp->id)
            ->with('success', 'UWP created (Draft).');
    }

    public function show(Request $request, int $id)
    {
        $uwp = UnitWorkPlan::with([
            'office',
            'performancePeriod',
            'creator',
            'uwpFunctions.mfos.successIndicators.qetStandards',
            'uwpFunctions.mfos.successIndicators.assignments.employee',
            'opcr',
        ])->findOrFail($id);

        $this->ensureCanViewUwp($request->user(), $uwp);

        return view('stages.stage1.uwp.show', compact('uwp'));
    }

    public function edit(Request $request, int $id)
    {
        $this->ensureRole($request->user()->role, ['supervisor']);

        $uwp = UnitWorkPlan::findOrFail($id);
        $this->ensureCanViewUwp($request->user(), $uwp);

        if (!$uwp->isDraft()) {
            return back()->with('error', 'UWP is read-only once submitted.');
        }

        return view('stages.stage1.uwp.edit', compact('uwp'));
    }

    public function update(Request $request, int $id)
    {
        $this->ensureRole($request->user()->role, ['supervisor']);

        $uwp = UnitWorkPlan::findOrFail($id);
        $this->ensureCanViewUwp($request->user(), $uwp);

        if (!$uwp->isDraft()) {
            return back()->with('error', 'UWP is read-only once submitted.');
        }

        $data = $request->validate([
            'office_id' => ['required', 'integer', 'exists:offices,id'],
            'performance_period_id' => ['required', 'integer', 'exists:performance_periods,id'],
        ]);

        $uwp->update($data);

        return redirect()
            ->route('stage1.uwp.show', $uwp->id)
            ->with('success', 'UWP updated.');
    }

    public function saveDraftData(Request $request)
    {
        $user = $this->resolveSupervisorUser($request);

        [$data, $functionsPayload, $assignmentsPayload] = $this->parseUwpPayload($request);

        $this->persistUwpFromPayload(
            $user,
            (int) $data['office_id'],
            (int) $data['performance_period_id'],
            $functionsPayload,
            $assignmentsPayload
        );

        return redirect()->back()->with('success', 'UWP draft saved.');
    }


    public function submitData(Request $request)
    {
        $user = $this->resolveSupervisorUser($request);

        [$data, $functionsPayload, $assignmentsPayload] = $this->parseUwpPayload($request);

        $uwp = $this->persistUwpFromPayload(
            $user,
            (int) $data['office_id'],
            (int) $data['performance_period_id'],
            $functionsPayload,
            $assignmentsPayload
        );

        if (!$uwp->isDraft()) {
            return back()->with('error', 'Only Draft UWP can be submitted.');
        }

        if ($uwp->mfos()->count() === 0) {
            return back()->with('error', 'Cannot submit: no MFOs found.');
        }

        if ($this->countIndicatorAssignments($uwp) === 0) {
            return back()->with('error', 'Cannot submit: no assigned employees.');
        }

        DB::transaction(function () use ($uwp) {
            $uwp->update([
                'status' => UnitWorkPlan::STATUS_SUBMITTED,
                'submitted_at' => now(),
                'locked_at' => now(),
            ]);
        });

        return redirect()
            ->back()
            ->with('success', 'UWP submitted. Now read-only.');
    }

    public function submit(Request $request, int $id)
    {
        $this->ensureRole($request->user()->role, ['supervisor']);

        $uwp = UnitWorkPlan::with(['uwpFunctions.mfos.successIndicators.assignments'])->findOrFail($id);
        $this->ensureCanViewUwp($request->user(), $uwp);

        if (!$uwp->isDraft()) {
            return back()->with('error', 'Only Draft UWP can be submitted.');
        }

        if ($uwp->mfos()->count() === 0) {
            return back()->with('error', 'Cannot submit: no MFOs found.');
        }
        if ($this->countIndicatorAssignments($uwp) === 0) {
            return back()->with('error', 'Cannot submit: no assigned employees.');
        }

        DB::transaction(function () use ($uwp) {
            $uwp->update([
                'status' => UnitWorkPlan::STATUS_SUBMITTED,
                'submitted_at' => now(),
                'locked_at' => now(),
            ]);
        });

        return redirect()
            ->route('stage1.uwp.show', $uwp->id)
            ->with('success', 'UWP submitted. Now read-only.');
    }

    private function parseUwpPayload(Request $request): array
    {
        $data = $request->validate([
            'office_id' => ['required', 'integer', 'exists:offices,id'],
            'performance_period_id' => ['required', 'integer', 'exists:performance_periods,id'],
            'functions_payload' => ['nullable', 'string'],
            'mfos_payload' => ['nullable', 'string'],
            'assignments_payload' => ['nullable', 'string'],
        ]);

        $functionsPayload = [];

        if (!empty($data['functions_payload'])) {
            $rawFunctions = $this->decodeJsonPayload($data['functions_payload'], 'functions_payload');
            $functionsPayload = $this->normalizeFunctionsPayload($rawFunctions);
        } elseif (!empty($data['mfos_payload'])) {
            $rawMfos = $this->decodeJsonPayload($data['mfos_payload'], 'mfos_payload');
            $functionsPayload = $this->normalizeFunctionsPayload($rawMfos);
        }

        if (empty($functionsPayload)) {
            throw ValidationException::withMessages([
                'functions_payload' => 'Missing or invalid functions payload.',
            ]);
        }

        $assignmentsPayload = [];
        if (!empty($data['assignments_payload'])) {
            $assignmentsPayload = $this->decodeJsonPayload($data['assignments_payload'], 'assignments_payload');
        }

        return [$data, $functionsPayload, $assignmentsPayload];
    }

    private function decodeJsonPayload(string $payload, string $field): array
    {
        try {
            $decoded = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            throw ValidationException::withMessages([$field => 'Invalid JSON payload.']);
        }

        if (!is_array($decoded)) {
            throw ValidationException::withMessages([$field => 'Invalid JSON payload.']);
        }

        return $decoded;
    }

    private function normalizeFunctionsPayload(array $payload): array
    {
        $payload = array_values($payload);

        $first = $payload[0] ?? null;
        if (!is_array($first)) {
            return [];
        }

        if (array_key_exists('mfos', $first) || array_key_exists('type', $first) || array_key_exists('function_type', $first)) {
            return $this->normalizeFunctionsFromFunctionsPayload($payload);
        }

        return $this->normalizeFunctionsFromMfosPayload($payload);
    }

    private function normalizeFunctionsFromFunctionsPayload(array $payload): array
    {
        $functions = [];
        $sortOrder = 1;

        foreach ($payload as $functionPayload) {
            if (!is_array($functionPayload)) {
                continue;
            }

            $name = trim((string) ($functionPayload['title'] ?? $functionPayload['name'] ?? ''));
            if ($name === '') {
                $name = 'Custom Function';
            }

            $type = strtolower(trim((string) ($functionPayload['type'] ?? $functionPayload['function_type'] ?? 'custom')));
            if (!in_array($type, ['core', 'support', 'custom'], true)) {
                $type = 'custom';
            }

            $weight = (float) ($functionPayload['weight'] ?? $functionPayload['weight_percent'] ?? 0);
            $mfos = $this->normalizeMfosFromFunctionsPayload($functionPayload['mfos'] ?? []);

            $functions[] = [
                'name' => $name,
                'function_type' => $type,
                'weight_percent' => $weight,
                'sort_order' => (int) ($functionPayload['sort_order'] ?? $sortOrder),
                'mfos' => $mfos,
            ];

            $sortOrder++;
        }

        return $functions;
    }

    private function normalizeMfosFromFunctionsPayload(array $mfosPayload): array
    {
        $mfos = [];
        $sortOrder = 1;

        foreach ($mfosPayload as $mfoPayload) {
            if (!is_array($mfoPayload)) {
                continue;
            }

            $title = trim((string) ($mfoPayload['title'] ?? ''));
            if ($title === '') {
                continue;
            }

            $indicators = $this->normalizeIndicatorsPayload($mfoPayload['indicators'] ?? $mfoPayload['success_indicators'] ?? []);

            $mfos[] = [
                'title' => $title,
                'target_timeline' => (string) ($mfoPayload['target'] ?? $mfoPayload['target_timeline'] ?? ''),
                'weight_percent' => isset($mfoPayload['weight_percent']) ? (float) $mfoPayload['weight_percent'] : null,
                'sort_order' => (int) ($mfoPayload['sort_order'] ?? $sortOrder),
                'indicators' => $indicators,
            ];

            $sortOrder++;
        }

        return $mfos;
    }

    private function normalizeFunctionsFromMfosPayload(array $payload): array
    {
        $functions = [];
        $functionMap = [];
        $functionOrder = 1;

        foreach ($payload as $mfoPayload) {
            if (!is_array($mfoPayload)) {
                continue;
            }

            $functionCode = strtolower(trim((string) ($mfoPayload['function_code'] ?? 'custom')));
            if (!in_array($functionCode, ['core', 'support', 'custom'], true)) {
                $functionCode = 'custom';
            }

            if (!isset($functionMap[$functionCode])) {
                $functionMap[$functionCode] = $functionOrder++;
                $functions[$functionCode] = [
                    'name' => $functionCode === 'core' ? 'Core Functions' : ($functionCode === 'support' ? 'Support Functions' : 'Custom Functions'),
                    'function_type' => $functionCode,
                    'weight_percent' => (float) ($mfoPayload['weight'] ?? 0),
                    'sort_order' => $functionMap[$functionCode],
                    'mfos' => [],
                ];
            }

            $title = trim((string) ($mfoPayload['title'] ?? ''));
            if ($title === '') {
                continue;
            }

            $indicators = $this->normalizeIndicatorsPayload($mfoPayload['success_indicators'] ?? []);

            $functions[$functionCode]['mfos'][] = [
                'title' => $title,
                'target_timeline' => (string) ($mfoPayload['target_summary'] ?? ''),
                'weight_percent' => isset($mfoPayload['weight']) ? (float) $mfoPayload['weight'] : null,
                'sort_order' => (int) ($mfoPayload['sort_order'] ?? count($functions[$functionCode]['mfos']) + 1),
                'indicators' => $indicators,
            ];
        }

        return array_values($functions);
    }

    private function normalizeIndicatorsPayload(array $indicatorsPayload): array
    {
        $indicators = [];
        $sortOrder = 1;

        foreach ($indicatorsPayload as $indicatorPayload) {
            if (!is_array($indicatorPayload)) {
                continue;
            }

            $text = trim((string) ($indicatorPayload['indicator_text'] ?? $indicatorPayload['description'] ?? $indicatorPayload['text'] ?? ''));
            if ($text === '') {
                continue;
            }

            $standards = $this->normalizeStandardsPayload($indicatorPayload['standards'] ?? []);

            $indicators[] = [
                'indicator_text' => $text,
                'sort_order' => (int) ($indicatorPayload['sort_order'] ?? $sortOrder),
                'standards' => $standards,
                'assignees' => $indicatorPayload['assignees'] ?? [],
            ];

            $sortOrder++;
        }

        return $indicators;
    }

    private function normalizeStandardsPayload(array $standardsPayload): array
    {
        $standards = [];

        foreach ($standardsPayload as $standardPayload) {
            if (!is_array($standardPayload)) {
                continue;
            }

            $dimension = $this->normalizeDimension((string) ($standardPayload['dimension'] ?? ''));
            $rating = (int) ($standardPayload['rating'] ?? $standardPayload['rating_level'] ?? 0);
            $text = trim((string) ($standardPayload['standard_text'] ?? $standardPayload['standard'] ?? $standardPayload['text'] ?? ''));

            if (!$dimension || $rating < 1 || $rating > 5) {
                continue;
            }

            $standards[] = [
                'dimension' => $dimension,
                'rating' => $rating,
                'standard_text' => $text,
            ];
        }

        return $standards;
    }

    private function persistUwpFromPayload(User $user, int $officeId, int $periodId, array $functionsPayload, array $assignmentsPayload): UnitWorkPlan
    {
        return DB::transaction(function () use ($user, $officeId, $periodId, $functionsPayload, $assignmentsPayload) {
            $uwp = UnitWorkPlan::query()
                ->where('office_id', $officeId)
                ->where('performance_period_id', $periodId)
                ->where('created_by', $user->id)
                ->first();

            if ($uwp && (!$uwp->isDraft() || $uwp->locked_at)) {
                throw ValidationException::withMessages([
                    'status' => 'UWP is read-only once submitted.',
                ]);
            }

            if (!$uwp) {
                $uwp = UnitWorkPlan::create([
                    'office_id' => $officeId,
                    'performance_period_id' => $periodId,
                    'created_by' => $user->id,
                    'status' => UnitWorkPlan::STATUS_DRAFT,
                ]);
            }

            $uwp->uwpFunctions()->delete();

            $fallbackAssignmentIds = $this->resolveAssignmentEmployeeIds($assignmentsPayload, $officeId);

            $functionOrder = 1;
            foreach ($functionsPayload as $functionPayload) {
                if (!is_array($functionPayload)) {
                    continue;
                }

                $name = trim((string) ($functionPayload['name'] ?? ''));
                if ($name === '') {
                    continue;
                }

                $type = strtolower(trim((string) ($functionPayload['function_type'] ?? 'custom')));
                if (!in_array($type, ['core', 'support', 'custom'], true)) {
                    $type = 'custom';
                }

                $function = $uwp->uwpFunctions()->create([
                    'name' => $name,
                    'function_type' => $type,
                    'weight_percent' => (float) ($functionPayload['weight_percent'] ?? 0),
                    'sort_order' => (int) ($functionPayload['sort_order'] ?? $functionOrder),
                ]);

                $functionOrder++;

                $mfoOrder = 1;
                $mfos = is_array($functionPayload['mfos'] ?? null) ? $functionPayload['mfos'] : [];

                foreach ($mfos as $mfoPayload) {
                    if (!is_array($mfoPayload)) {
                        continue;
                    }

                    $title = trim((string) ($mfoPayload['title'] ?? ''));
                    if ($title === '') {
                        continue;
                    }

                    $mfo = $function->mfos()->create([
                        'title' => $title,
                        'target_timeline' => (string) ($mfoPayload['target_timeline'] ?? ''),
                        'weight_percent' => isset($mfoPayload['weight_percent']) ? (float) $mfoPayload['weight_percent'] : null,
                        'sort_order' => (int) ($mfoPayload['sort_order'] ?? $mfoOrder),
                    ]);

                    $mfoOrder++;

                    $indicatorOrder = 1;
                    $indicators = is_array($mfoPayload['indicators'] ?? null)
                        ? $mfoPayload['indicators']
                        : [];

                    foreach ($indicators as $indicatorPayload) {
                        if (!is_array($indicatorPayload)) {
                            continue;
                        }

                        $text = trim((string) ($indicatorPayload['indicator_text'] ?? ''));
                        if ($text === '') {
                            continue;
                        }

                        $indicator = $mfo->successIndicators()->create([
                            'indicator_text' => $text,
                            'sort_order' => (int) ($indicatorPayload['sort_order'] ?? $indicatorOrder),
                        ]);

                        $indicatorOrder++;

                        $standards = is_array($indicatorPayload['standards'] ?? null)
                            ? $indicatorPayload['standards']
                            : [];

                        foreach ($standards as $standardPayload) {
                            if (!is_array($standardPayload)) {
                                continue;
                            }

                            $dimension = $this->normalizeDimension((string) ($standardPayload['dimension'] ?? ''));
                            $rating = (int) ($standardPayload['rating'] ?? 0);
                            $standardText = trim((string) ($standardPayload['standard_text'] ?? ''));

                            if (!$dimension || $rating < 1 || $rating > 5 || $standardText === '') {
                                continue;
                            }

                            $indicator->qetStandards()->create([
                                'dimension' => $dimension,
                                'rating' => $rating,
                                'standard_text' => $standardText,
                            ]);
                        }

                        $assignees = $indicatorPayload['assignees'] ?? [];
                        $employeeIds = $this->resolveAssignmentEmployeeIds(is_array($assignees) ? $assignees : [$assignees], $officeId);
                        if (empty($employeeIds) && !empty($fallbackAssignmentIds)) {
                            $employeeIds = $fallbackAssignmentIds;
                        }

                        foreach (array_unique($employeeIds) as $employeeId) {
                            $indicator->assignments()->create([
                                'employee_id' => $employeeId,
                                'assigned_by' => $user->id,
                                'assigned_at' => now(),
                            ]);
                        }
                    }
                }
            }

            return $uwp;
        });
    }

    private function countIndicatorAssignments(UnitWorkPlan $uwp): int
    {
        return UwpIndicatorAssignment::query()
            ->whereHas('successIndicator.uwpMfo.uwpFunction', function ($query) use ($uwp) {
                $query->where('unit_work_plan_id', $uwp->id);
            })
            ->count();
    }

    private function resolveAssignmentEmployeeIds(array $assignmentsPayload, int $officeId): array
    {
        $ids = [];

        foreach ($assignmentsPayload as $entry) {
            if (is_numeric($entry)) {
                $ids[] = (int) $entry;
                continue;
            }

            if (is_string($entry) && trim($entry) !== '') {
                $user = User::query()->where('name', trim($entry))->first();
                if ($user) {
                    $ids[] = $user->id;
                }
            }

            if (is_array($entry)) {
                if (isset($entry['employee_id']) && is_numeric($entry['employee_id'])) {
                    $ids[] = (int) $entry['employee_id'];
                }

                if (isset($entry['id']) && is_numeric($entry['id'])) {
                    $ids[] = (int) $entry['id'];
                }

                if (!empty($entry['employee_ids']) && is_array($entry['employee_ids'])) {
                    foreach ($entry['employee_ids'] as $employeeId) {
                        if (is_numeric($employeeId)) {
                            $ids[] = (int) $employeeId;
                        }
                    }
                }

                if (isset($entry['name']) && is_string($entry['name'])) {
                    $user = User::query()->where('name', trim($entry['name']))->first();
                    if ($user) {
                        $ids[] = $user->id;
                    }
                }
            }
        }

        $ids = array_values(array_unique(array_filter($ids)));

        if (empty($ids) && !empty($assignmentsPayload)) {
            $fallback = $this->fallbackAssignmentIds($officeId);
            $ids = $fallback;
        }

        return $ids;
    }

    private function fallbackAssignmentIds(int $officeId): array
    {
        $query = User::query();

        if (Schema::hasColumn('users', 'office_id')) {
            $query->where('office_id', $officeId);
        } else {
            $query->where('role', 'employee');
        }

        $user = $query->orderBy('id')->first();

        return $user ? [$user->id] : [];
    }

    private function normalizeDimension(string $dimension): ?string
    {
        $value = strtolower(trim($dimension));

        return match ($value) {
            'quality', 'q' => UwpQetStandard::DIM_QUALITY,
            'efficiency', 'e' => UwpQetStandard::DIM_EFFICIENCY,
            'timeliness', 't' => UwpQetStandard::DIM_TIMELINESS,
            default => null,
        };
    }

    private function resolveSupervisorUser(Request $request): User
    {
        $user = $request->user();

        if ($user) {
            $this->ensureRole($user->role, ['supervisor']);
            return $user;
        }

        $demoSupervisor = User::query()
            ->where('role', 'supervisor')
            ->orderBy('id')
            ->first();

        if (!$demoSupervisor) {
            abort(403, 'Unauthorized.');
        }

        return $demoSupervisor;
    }

    private function ensureRole(string $role, array $allowed): void
    {
        if (!in_array($role, $allowed, true)) {
            abort(403, 'Unauthorized.');
        }
    }

    private function ensureCanViewUwp($user, UnitWorkPlan $uwp): void
    {
        if (in_array($user->role, ['admin', 'pmt', 'dept-head'], true)) {
            return;
        }

        if ($user->role === 'supervisor' && $uwp->created_by === $user->id) {
            return;
        }

        abort(403, 'Unauthorized.');
    }
}
