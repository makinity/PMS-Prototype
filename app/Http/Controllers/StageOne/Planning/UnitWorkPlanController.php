<?php

namespace App\Http\Controllers\StageOne\Planning;

use App\Http\Controllers\Controller;
use App\Models\UnitWorkPlan;
use App\Models\User;
use App\Models\Office;
use App\Models\PerformancePeriod;
use App\Models\UwpFunction;
use App\Models\UwpIndicatorAssignment;
use App\Models\UwpQetStandard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class UnitWorkPlanController extends Controller
{
    public function uwpList(){
        $lists = UnitWorkPlan::get();
        $offices = Office::orderBy('name')->get();

        return view('supervisor.uwp-list', compact('lists', 'offices'));
    }
    public function index(Request $request)
    {
        $offices = Office::orderBy('name')->get();
        $periods = PerformancePeriod::orderBy('start_date', 'desc')->get();

        return view('supervisor.uwp', compact('offices', 'periods'));
    }

    public function create(Request $request)
    {
        $this->ensureRole($request->user()->role, ['supervisor']);

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

        return redirect()->with('success', 'UWP created (Draft).');
    }


    public function preview($id)
    {
        $uwp = UnitWorkPlan::with([
            'office',
            'performancePeriod',
            'creator',
            'departmentHead',
            'uwpFunctions' => function($query) {
                $query->with([
                    'mfos' => function($query) {
                        $query->with([
                            'successIndicators' => function($query) {
                                $query->with([
                                    'qetStandards',
                                    'assignments' => function($query) {
                                        $query->with('employee');
                                    }
                                ]);
                            }
                        ]);
                    }
                ]);
            }
        ])->findOrFail($id);

        return response()->json($uwp);
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

        return redirect()->route('supervisor.uwp-page')->with('success', 'UWP draft saved.');
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
            return response()->json([
                'success' => false,
                'error' => 'Only Draft UWP can be submitted.'
            ], 422);
        }

        if ($uwp->mfos()->count() === 0) {
            return response()->json([
                'success' => false,
                'error' => 'Cannot submit: no MFOs found.'
            ], 422);
        }

        if ($this->countIndicatorAssignments($uwp) === 0) {
            return response()->json([
                'success' => false,
                'error' => 'Cannot submit: no assigned employees.'
            ], 422);
        }

        DB::transaction(function () use ($uwp) {
            $uwp->update([
                'status' => UnitWorkPlan::STATUS_SUBMITTED,
                'submitted_at' => now(),
                'locked_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'UWP submitted successfully. Now read-only.'
        ]);
    }

    public function submitForApproval(Request $request, $id)
    {
        try {
            $user = $this->resolveSupervisorUser($request);

            // Find the UWP with its relationships
            $uwp = UnitWorkPlan::with([
                'mfos.successIndicators.assignments.employee',
                'office',
                'office.head',
                'creator'
            ])->findOrFail($id);

            $isCreator = $uwp->creator_id === $user->id;
            $isOfficeSupervisor = $uwp->office && $uwp->office->head_id === $user->id;
            $isSameOffice = $uwp->office && $uwp->office->id === $user->office_id;
            $isSupervisor = $user->role === 'supervisor';

            $isAuthorized = $isCreator || $isOfficeSupervisor || ($isSupervisor && $isSameOffice);

            Log::info('UWP Submission Permission Check', [
                'uwp_id' => $uwp->id,
                'uwp_office_id' => $uwp->office->id ?? null,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
                'user_office_id' => $user->office_id,
                'office_head_id' => $uwp->office->head_id ?? null,
                'is_creator' => $isCreator,
                'is_office_supervisor' => $isOfficeSupervisor,
                'is_same_office' => $isSameOffice,
                'is_supervisor' => $isSupervisor,
                'is_authorized' => $isAuthorized
            ]);

            if (!$isAuthorized) {
                $errorMessage = 'You do not have permission to submit this UWP. ';

                if ($user->role !== 'supervisor') {
                    $errorMessage .= 'Only supervisors can submit UWPs.';
                } else if (!$isSameOffice) {
                    $errorMessage .= 'You must be assigned to the same office as this UWP.';
                } else {
                    $errorMessage .= 'Please contact your office supervisor.';
                }

                return response()->json([
                    'success' => false,
                    'error' => $errorMessage
                ], 403);
            }

            // Check if UWP is in draft status
            if (!$uwp->isDraft()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Only Draft UWP can be submitted. Current status: ' . $uwp->status
                ], 422);
            }

            if ($uwp->mfos()->count() === 0) {
                return response()->json([
                    'success' => false,
                    'error' => 'Cannot submit: No MFOs/PPAs found in this UWP.'
                ], 422);
            }

            $assignmentCount = $this->countIndicatorAssignments($uwp);

            if ($assignmentCount === 0) {
                return response()->json([
                    'success' => false,
                    'error' => 'Cannot submit: No employees assigned to success indicators.'
                ], 422);
            }

            DB::transaction(function () use ($uwp) {
                $uwp->update([
                    'status' => UnitWorkPlan::STATUS_SUBMITTED,
                    'submitted_at' => now(),
                    'locked_at' => now(),
                ]);

                Log::info('UWP submitted for department head review', [
                    'uwp_id' => $uwp->id,
                    'submitted_by' => auth()->id(),
                    'submitted_at' => now()
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'UWP submitted successfully for Department Head review. The plan is now locked.',
                'status' => 'submitted',
                'submitted_at' => now()->toDateTimeString()
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'UWP not found.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('UWP Submission Error: ' . $e->getMessage(), [
                'uwp_id' => $id ?? null,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'An error occurred while submitting the UWP. Please try again.'
            ], 500);
        }
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
            // Allow supervisors, department heads, and admins
            $this->ensureRole($user->role, ['supervisor', 'dept-head', 'admin', 'pmt']);
            return $user;
        }

        // For development/demo - get the first supervisor
        $demoSupervisor = User::query()
            ->where('role', 'supervisor')
            ->orderBy('id')
            ->first();

        if (!$demoSupervisor) {
            abort(403, 'Unauthorized - No supervisor found.');
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
