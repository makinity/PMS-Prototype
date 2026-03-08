<?php

namespace App\Http\Controllers\Supervisor;

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
    public function uwpList(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'supervisor') {
            abort(403, 'Unauthorized.');
        }

        $office = null;
        if (!empty($user->office_id)) {
            $office = Office::query()->find($user->office_id);
        }

        $lists = collect();
        if ($office) {
            $lists = UnitWorkPlan::query()
                ->with(['office.head', 'performancePeriod', 'creator'])
                ->where('office_id', $office->id)
                ->where('created_by', $user->id)
                ->orderByDesc('submitted_at')
                ->orderByDesc('id')
                ->get();
        }

        return view('supervisor.uwp-list', compact('lists', 'office'));
    }
    public function index(Request $request)
    {
        $user = $this->resolveSupervisorUser($request);

        $offices = Office::orderBy('name')->get();
        $periods = PerformancePeriod::orderBy('start_date', 'desc')->get();

        $selectedUwpId = (int) $request->query('uwp_id', 0);
        $uwp = null;
        $status = UnitWorkPlan::STATUS_DRAFT;
        $locked_at = null;
        $selectedPerformancePeriodId = null;
        $selectedOfficeId = (int) ($user->office_id ?? 0);
        $initialFunctions = null;

        if ($selectedUwpId > 0) {
            $uwp = UnitWorkPlan::query()
                ->with([
                    'returnedByUser',
                    'uwpFunctions' => function ($query) {
                        $query->orderBy('sort_order')->with([
                            'mfos' => function ($mfoQuery) {
                                $mfoQuery->orderBy('sort_order')->with([
                                    'successIndicators' => function ($indicatorQuery) {
                                        $indicatorQuery->orderBy('sort_order')->with([
                                            'qetStandards',
                                            'assignments.employee',
                                        ]);
                                    },
                                ]);
                            },
                        ]);
                    },
                ])
                ->findOrFail($selectedUwpId);

            $this->ensureCanViewUwp($user, $uwp);
            if ((int) $uwp->created_by !== (int) $user->id) {
                abort(403, 'Unauthorized.');
            }

            $status = (string) $uwp->status;
            $locked_at = $uwp->locked_at;
            $selectedPerformancePeriodId = (int) $uwp->performance_period_id;
            $selectedOfficeId = (int) $uwp->office_id;
            $initialFunctions = $this->mapUwpToEditorFunctions($uwp);
        }

        $selectedOfficeId = (int) ($selectedOfficeId ?? $user->office_id);

        $officeEmployees = User::query()
            ->where('office_id', $selectedOfficeId)
            ->where('role', 'employee')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'office_id']);

        return view('supervisor.uwp', compact(
            'offices',
            'officeEmployees',
            'periods',
            'uwp',
            'status',
            'locked_at',
            'selectedOfficeId',
            'selectedPerformancePeriodId',
            'initialFunctions'
        ));
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

        $existing = UnitWorkPlan::query()
            ->where('office_id', $data['office_id'])
            ->where('performance_period_id', $data['performance_period_id'])
            ->first();

        if ($existing) {
            if ((int) $existing->created_by !== (int) $user->id) {
                return back()->with('error', 'A Unit Work Plan already exists for the selected Office/Unit and Performance Period.');
            }

            if (!$existing->isEditableBySupervisor()) {
                return back()->with('error', 'UWP is read-only at this stage.');
            }

            return redirect()->route('supervisor.uwp', ['uwp_id' => $existing->id]);
        }

        $uwp = UnitWorkPlan::create([
            'office_id' => $data['office_id'],
            'performance_period_id' => $data['performance_period_id'],
            'created_by' => $user->id,
            'status' => UnitWorkPlan::STATUS_DRAFT,
            'submitted_at' => null,
            'locked_at' => null,
        ]);

        return redirect()
            ->route('supervisor.uwp', ['uwp_id' => $uwp->id])
            ->with('success', 'UWP created (Draft).');
    }


    public function show(Request $request, int $id)
    {
        $uwp = UnitWorkPlan::find($id);
        if (!$uwp) {
            return response()->json([
                'message' => 'UWP not found.',
            ], 404);
        }

        return $this->buildUwpShowResponse($request, $uwp);
    }

    public function preview(Request $request, $id)
    {
        $uwp = UnitWorkPlan::find($id);
        if (!$uwp) {
            return response()->json([
                'message' => 'UWP not found.',
            ], 404);
        }

        return $this->buildUwpShowResponse($request, $uwp);
    }

    public function edit(Request $request, int $id)
    {
        $this->ensureRole($request->user()->role, ['supervisor']);

        $uwp = UnitWorkPlan::findOrFail($id);
        $this->ensureCanViewUwp($request->user(), $uwp);

        if (!$uwp->isEditableBySupervisor()) {
            return back()->with('error', 'UWP is read-only at this stage.');
        }

        return view('stages.stage1.uwp.edit', compact('uwp'));
    }

    public function update(Request $request, int $id)
    {
        $this->ensureRole($request->user()->role, ['supervisor']);

        $uwp = UnitWorkPlan::findOrFail($id);
        $this->ensureCanViewUwp($request->user(), $uwp);

        if (!$uwp->isEditableBySupervisor()) {
            return back()->with('error', 'UWP is read-only at this stage.');
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

    public function destroy(Request $request, int $id)
    {
        $user = $this->resolveSupervisorUser($request);

        $uwp = UnitWorkPlan::query()
            ->with([
                'uwpFunctions.mfos.successIndicators.assignments',
                'uwpFunctions.mfos.successIndicators.qetStandards',
            ])
            ->find($id);

        if (!$uwp) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'error' => 'UWP not found.'], 404);
            }

            return redirect()->route('supervisor.uwp-page')->with('error', 'UWP not found.');
        }

        $sameOffice = (int) ($user->office_id ?? 0) === 0 || (int) $uwp->office_id === (int) $user->office_id;
        if ((int) $uwp->created_by !== (int) $user->id || !$sameOffice) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'error' => 'You do not have permission to delete this UWP.'], 403);
            }

            return redirect()->route('supervisor.uwp-page')->with('error', 'You do not have permission to delete this UWP.');
        }

        $status = strtolower((string) $uwp->status);
        if (!in_array($status, [UnitWorkPlan::STATUS_DRAFT, UnitWorkPlan::STATUS_RETURNED], true) || !is_null($uwp->locked_at)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'error' => 'Only Draft/Returned & unlocked UWP can be deleted.'], 422);
            }

            return redirect()->route('supervisor.uwp-page')->with('error', 'Only Draft/Returned & unlocked UWP can be deleted.');
        }

        DB::transaction(function () use ($uwp) {
            foreach ($uwp->uwpFunctions as $function) {
                foreach ($function->mfos as $mfo) {
                    foreach ($mfo->successIndicators as $indicator) {
                        $indicator->assignments()->delete();
                        $indicator->qetStandards()->delete();
                    }
                    $mfo->successIndicators()->delete();
                }
                $function->mfos()->delete();
            }

            $uwp->uwpFunctions()->delete();
            $uwp->delete();
        });

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'UWP deleted successfully.']);
        }

        return redirect()->route('supervisor.uwp-page')->with('success', 'UWP deleted successfully.');
    }


    public function saveDraftData(Request $request)
    {
        return $this->handleSaveDraftData($request, null);
    }

    public function saveDraftDataById(Request $request, int $id)
    {
        return $this->handleSaveDraftData($request, $id);
    }

    public function saveDraftDataForUwp(Request $request, int $id)
    {
        return $this->handleSaveDraftData($request, $id);
    }

    public function submitData(Request $request)
    {
        return $this->handleSubmitData($request, null);
    }

    public function submitDataForUwp(Request $request, int $id)
    {
        if (!$request->filled('functions_payload') && !$request->filled('mfos_payload')) {
            return $this->submitForApproval($request, $id);
        }

        return $this->handleSubmitData($request, $id);
    }

    private function handleSaveDraftData(Request $request, ?int $forcedUwpId)
    {
        $user = $this->resolveSupervisorUser($request);

        [$data, $functionsPayload, $assignmentsPayload] = $this->parseDraftPayload($request);
        $uwpId = $forcedUwpId ?? (isset($data['uwp_id']) ? (int) $data['uwp_id'] : null);

        $uwp = $this->persistUwpFromPayload(
            $user,
            (int) $data['office_id'],
            (int) $data['performance_period_id'],
            $functionsPayload,
            $assignmentsPayload,
            $uwpId
        );

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'uwp_id' => $uwp->id,
                'status' => $uwp->status,
                'message' => 'UWP draft saved.',
            ]);
        }

        return redirect()->route('supervisor.uwp-page');
    }

    private function handleSubmitData(Request $request, ?int $forcedUwpId)
    {
        $user = $this->resolveSupervisorUser($request);

        [$data, $functionsPayload, $assignmentsPayload] = $this->parseUwpPayload($request);
        $uwpId = $forcedUwpId ?? (isset($data['uwp_id']) ? (int) $data['uwp_id'] : null);

        $uwp = $this->persistUwpFromPayload(
            $user,
            (int) $data['office_id'],
            (int) $data['performance_period_id'],
            $functionsPayload,
            $assignmentsPayload,
            $uwpId
        );

        if ($uwp->isLocked() || !in_array($uwp->status, [UnitWorkPlan::STATUS_DRAFT, UnitWorkPlan::STATUS_RETURNED], true)) {
            if (!($request->expectsJson() || $request->ajax())) {
                return back()->with('error', 'Only editable Draft/Returned UWP can be submitted.');
            }

            return response()->json([
                'success' => false,
                'error' => 'Only editable Draft/Returned UWP can be submitted.'
            ], 422);
        }

        if ($uwp->mfos()->count() === 0) {
            if (!($request->expectsJson() || $request->ajax())) {
                return back()->with('error', 'Cannot submit: no MFOs found.');
            }

            return response()->json([
                'success' => false,
                'error' => 'Cannot submit: no MFOs found.'
            ], 422);
        }

        if ($this->countIndicatorAssignments($uwp) === 0) {
            if (!($request->expectsJson() || $request->ajax())) {
                return back()->with('error', 'Cannot submit: no assigned employees.');
            }

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

        if (!($request->expectsJson() || $request->ajax())) {
            return redirect()
                ->route('supervisor.uwp-page')
                ->with('success', 'UWP submitted successfully. Now read-only.');
        }

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

            $isCreator = (int) $uwp->created_by === (int) $user->id;
            if (!$isCreator) {
                return $this->respondSubmitForApproval(
                    $request,
                    false,
                    'You do not have permission to submit this UWP.',
                    403
                );
            }

            if ($uwp->isLocked() || !in_array($uwp->status, [UnitWorkPlan::STATUS_DRAFT, UnitWorkPlan::STATUS_RETURNED], true)) {
                return $this->respondSubmitForApproval(
                    $request,
                    false,
                    'Only editable Draft/Returned UWP can be submitted. Current status: ' . $uwp->status,
                    422
                );
            }

            if ($uwp->mfos()->count() === 0) {
                return $this->respondSubmitForApproval(
                    $request,
                    false,
                    'Cannot submit: No MFOs/PPAs found in this UWP.',
                    422
                );
            }

            $assignmentCount = $this->countIndicatorAssignments($uwp);

            if ($assignmentCount === 0) {
                return $this->respondSubmitForApproval(
                    $request,
                    false,
                    'Cannot submit: No employees assigned to success indicators.',
                    422
                );
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

            return $this->respondSubmitForApproval(
                $request,
                true,
                'UWP submitted successfully for Department Head review. The plan is now locked.',
                200
            );

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->respondSubmitForApproval($request, false, 'UWP not found.', 404);
        } catch (\Exception $e) {
            Log::error('UWP Submission Error: ' . $e->getMessage(), [
                'uwp_id' => $id ?? null,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->respondSubmitForApproval(
                $request,
                false,
                'An error occurred while submitting the UWP. Please try again.',
                500
            );
        }
    }

    private function respondSubmitForApproval(Request $request, bool $success, string $message, int $statusCode)
    {
        if ($request->expectsJson() || $request->ajax()) {
            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'status' => UnitWorkPlan::STATUS_SUBMITTED,
                    'submitted_at' => now()->toDateTimeString(),
                ], $statusCode);
            }

            return response()->json([
                'success' => false,
                'error' => $message,
            ], $statusCode);
        }

        if ($success) {
            return back()->with('success', $message);
        }

        return back()->with('error', $message);
    }


    private function parseUwpPayload(Request $request): array
    {
        $data = $request->validate([
            'uwp_id' => ['nullable', 'integer', 'exists:unit_work_plans,id'],
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

    private function parseDraftPayload(Request $request): array
    {
        $data = $request->validate([
            'uwp_id' => ['nullable', 'integer', 'exists:unit_work_plans,id'],
            'office_id' => ['required', 'integer', 'exists:offices,id'],
            'performance_period_id' => ['required', 'integer', 'exists:performance_periods,id'],
            'functions_payload' => ['nullable', 'string'],
            'mfos_payload' => ['nullable', 'string'],
            'assignments_payload' => ['nullable', 'string'],
        ]);

        $functionsPayload = [];
        if (!empty($data['functions_payload'])) {
            $rawFunctions = $this->safeJsonDecode($data['functions_payload'], []);
            $functionsPayload = $this->normalizeFunctionsPayload($rawFunctions);
        } elseif (!empty($data['mfos_payload'])) {
            $rawMfos = $this->safeJsonDecode($data['mfos_payload'], []);
            $functionsPayload = $this->normalizeFunctionsPayload($rawMfos);
        }

        $assignmentsPayload = [];
        if (!empty($data['assignments_payload'])) {
            $assignmentsPayload = $this->safeJsonDecode($data['assignments_payload'], []);
        }

        return [$data, $functionsPayload, $assignmentsPayload];
    }

    private function safeJsonDecode(?string $payload, array $default = []): array
    {
        if (!is_string($payload) || trim($payload) === '') {
            return $default;
        }

        $decoded = json_decode($payload, true);
        return is_array($decoded) ? $decoded : $default;
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
            $targetQuantity = $this->normalizeTargetQuantity(
                $mfoPayload['target_quantity'] ?? $mfoPayload['targetQuantity'] ?? null
            );

            $mfos[] = [
                'title' => $title,
                'target_quantity' => $targetQuantity,
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
            $targetQuantity = $this->normalizeTargetQuantity(
                $mfoPayload['target_quantity'] ?? $mfoPayload['targetQuantity'] ?? null
            );

            $functions[$functionCode]['mfos'][] = [
                'title' => $title,
                'target_quantity' => $targetQuantity,
                'target_timeline' => (string) ($mfoPayload['target_summary'] ?? ''),
                'weight_percent' => isset($mfoPayload['weight']) ? (float) $mfoPayload['weight'] : null,
                'sort_order' => (int) ($mfoPayload['sort_order'] ?? count($functions[$functionCode]['mfos']) + 1),
                'indicators' => $indicators,
            ];
        }

        return array_values($functions);
    }

    private function normalizeTargetQuantity(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            return null;
        }

        return max(0, (int) $value);
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

    private function persistUwpFromPayload(
        User $user,
        int $officeId,
        int $periodId,
        array $functionsPayload,
        array $assignmentsPayload,
        ?int $uwpId = null
    ): UnitWorkPlan
    {
        return DB::transaction(function () use ($user, $officeId, $periodId, $functionsPayload, $assignmentsPayload, $uwpId) {
            $uwp = null;
            $wasCreated = false;

            if ($uwpId) {
                $uwp = UnitWorkPlan::query()->find($uwpId);

                if (!$uwp) {
                    throw ValidationException::withMessages([
                        'uwp_id' => 'Selected UWP was not found.',
                    ]);
                }

                if ((int) $uwp->created_by !== (int) $user->id) {
                    abort(403, 'Unauthorized.');
                }

                if (!$uwp->isEditableBySupervisor()) {
                    throw ValidationException::withMessages([
                        'status' => 'UWP is read-only at this stage.',
                    ]);
                }
            } else {
                $uwp = UnitWorkPlan::query()
                    ->where('office_id', $officeId)
                    ->where('performance_period_id', $periodId)
                    ->first();

                if ($uwp) {
                    if ((int) $uwp->created_by !== (int) $user->id) {
                        throw ValidationException::withMessages([
                            'office_id' => 'A Unit Work Plan already exists for the selected Office/Unit and Performance Period.',
                        ]);
                    }

                    if (!$uwp->isEditableBySupervisor()) {
                        throw ValidationException::withMessages([
                            'status' => 'UWP is read-only at this stage.',
                        ]);
                    }
                }
            }

            if (!$uwp) {
                $uwp = UnitWorkPlan::create([
                    'office_id' => $officeId,
                    'performance_period_id' => $periodId,
                    'created_by' => $user->id,
                    'status' => UnitWorkPlan::STATUS_DRAFT,
                    'submitted_at' => null,
                    'locked_at' => null,
                ]);
                $wasCreated = true;
            }

            $conflict = UnitWorkPlan::query()
                ->where('office_id', $officeId)
                ->where('performance_period_id', $periodId)
                ->where('id', '!=', $uwp->id)
                ->first();

            if ($conflict) {
                if ((int) $conflict->created_by !== (int) $user->id) {
                    throw ValidationException::withMessages([
                        'office_id' => 'A Unit Work Plan already exists for the selected Office/Unit and Performance Period.',
                    ]);
                }
            }

            $payloadUpdate = [
                'created_by' => $user->id,
                'office_id' => $officeId,
                'performance_period_id' => $periodId,
                'submitted_at' => null,
                'locked_at' => null,
            ];

            if ($wasCreated) {
                $payloadUpdate['status'] = UnitWorkPlan::STATUS_DRAFT;
            }

            $uwp->update($payloadUpdate);

            $uwp->uwpFunctions()->delete();

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

                    $targetQuantity = $this->normalizeTargetQuantity(
                        $mfoPayload['target_quantity'] ?? $mfoPayload['targetQuantity'] ?? null
                    );

                    $mfo = $function->mfos()->create([
                        'title' => $title,
                        'target_quantity' => $targetQuantity,
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

            if (
                in_array((string) $uwp->status, [UnitWorkPlan::STATUS_DRAFT, UnitWorkPlan::STATUS_RETURNED], true)
                && !$uwp->uwpFunctions()->exists()
            ) {
                $this->ensureDefaultUwpFunctions($uwp);
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

    private function ensureDefaultUwpFunctions(UnitWorkPlan $uwp): void
    {
        if ($uwp->uwpFunctions()->exists()) {
            return;
        }

        $uwp->uwpFunctions()->createMany([
            [
                'name' => 'Core Functions',
                'function_type' => 'core',
                'weight_percent' => 80.00,
                'sort_order' => 1,
            ],
            [
                'name' => 'Support Functions',
                'function_type' => 'support',
                'weight_percent' => 20.00,
                'sort_order' => 2,
            ],
        ]);
    }

    private function mapUwpToEditorFunctions(UnitWorkPlan $uwp): array
    {
        return $uwp->uwpFunctions
            ->sortBy('sort_order')
            ->values()
            ->map(function (UwpFunction $function) {
                return [
                    'title' => (string) $function->name,
                    'type' => (string) $function->function_type,
                    'weight' => (float) ($function->weight_percent ?? 0),
                    'isCustom' => $function->function_type === 'custom',
                    'mfos' => $function->mfos
                        ->sortBy('sort_order')
                        ->values()
                        ->map(function ($mfo) {
                            return [
                                'title' => (string) $mfo->title,
                                'targetQuantity' => $this->normalizeTargetQuantity($mfo->target_quantity),
                                'target' => (string) ($mfo->target_timeline ?? ''),
                                'indicators' => $mfo->successIndicators
                                    ->sortBy('sort_order')
                                    ->values()
                                    ->map(function ($indicator) {
                                        return [
                                            'text' => (string) $indicator->indicator_text,
                                            'standards' => $indicator->qetStandards
                                                ->sortBy([['rating', 'desc'], ['dimension', 'asc']])
                                                ->values()
                                                ->map(function ($standard) {
                                                    return [
                                                        'rating' => (int) $standard->rating,
                                                        'dimension' => (string) $standard->dimension,
                                                        'text' => (string) $standard->standard_text,
                                                    ];
                                                })
                                                ->all(),
                                            'assignees' => $indicator->assignments
                                                ->map(fn ($assignment) => $assignment->employee?->name)
                                                ->filter()
                                                ->values()
                                                ->all(),
                                        ];
                                    })
                                    ->all(),
                            ];
                        })
                        ->all(),
                ];
            })
            ->all();
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

        if (!$user) {
            abort(403, 'Unauthorized.');
        }

        $this->ensureRole($user->role, ['supervisor']);
        return $user;
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

    private function buildUwpShowResponse(Request $request, UnitWorkPlan $uwp)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'message' => 'Unauthorized.',
                ], 403);
            }

            $isPrivileged = in_array($user->role, ['admin', 'pmt', 'dept-head'], true);
            $isOwner = (int) $uwp->created_by === (int) $user->id;
            $isSupervisorSameOffice = $user->role === 'supervisor' && (int) $user->office_id === (int) $uwp->office_id;

            if (!$isPrivileged && !$isOwner && !$isSupervisorSameOffice) {
                return response()->json([
                    'message' => 'You are not allowed to view this UWP.',
                ], 403);
            }

            $uwp->load([
                'office.head',
                'performancePeriod',
                'creator',
                'uwpFunctions' => function ($query) {
                    $query->orderBy('sort_order')->with([
                        'mfos' => function ($mfoQuery) {
                            $mfoQuery->orderBy('sort_order')->with([
                                'successIndicators' => function ($siQuery) {
                                    $siQuery->orderBy('sort_order')->with([
                                        'qetStandards',
                                        'assignments.employee.office',
                                    ]);
                                },
                            ]);
                        },
                    ]);
                },
            ]);

            $payload = [
                'id' => $uwp->id,
                'status' => $uwp->status,
                'submitted_at' => optional($uwp->submitted_at)->toDateTimeString(),
                'locked_at' => optional($uwp->locked_at)->toDateTimeString(),
                'office' => [
                    'id' => $uwp->office?->id,
                    'name' => $uwp->office?->name,
                ],
                'performance_period' => [
                    'id' => $uwp->performancePeriod?->id,
                    'name' => $uwp->performancePeriod?->name,
                ],
                'creator' => [
                    'id' => $uwp->creator?->id,
                    'name' => $uwp->creator?->name,
                ],
                'department_head' => [
                    'id' => $uwp->office?->head?->id,
                    'name' => $uwp->office?->head?->name,
                ],
                'uwp_functions' => $uwp->uwpFunctions->map(function ($function) {
                    return [
                        'id' => $function->id,
                        'name' => $function->name,
                        'function_type' => $function->function_type,
                        'weight_percent' => $function->weight_percent,
                        'mfos' => $function->mfos->map(function ($mfo) {
                            return [
                                'id' => $mfo->id,
                                'title' => $mfo->title,
                                'target_quantity' => $this->normalizeTargetQuantity($mfo->target_quantity),
                                'target_timeline' => $mfo->target_timeline,
                                'weight_percent' => $mfo->weight_percent,
                                'success_indicators' => $mfo->successIndicators->map(function ($indicator) {
                                    return [
                                        'id' => $indicator->id,
                                        'indicator_text' => $indicator->indicator_text,
                                        'qet_standards' => $indicator->qetStandards->map(function ($standard) {
                                            return [
                                                'id' => $standard->id,
                                                'dimension' => $standard->dimension,
                                                'rating' => $standard->rating,
                                                'standard_text' => $standard->standard_text,
                                            ];
                                        })->values()->all(),
                                        'assignments' => $indicator->assignments->map(function ($assignment) {
                                            return [
                                                'id' => $assignment->id,
                                                'assigned_at' => optional($assignment->assigned_at)->toDateTimeString(),
                                                'employee' => [
                                                    'id' => $assignment->employee?->id,
                                                    'name' => $assignment->employee?->name,
                                                    'office' => [
                                                        'id' => $assignment->employee?->office?->id,
                                                        'name' => $assignment->employee?->office?->name,
                                                    ],
                                                ],
                                            ];
                                        })->values()->all(),
                                    ];
                                })->values()->all(),
                            ];
                        })->values()->all(),
                    ];
                })->values()->all(),
            ];

            return response()->json($payload);
        } catch (\Throwable $e) {
            Log::error('Failed to load UWP preview data.', [
                'uwp_id' => $uwp->id ?? null,
                'user_id' => $request->user()?->id,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Unable to load UWP details right now.',
            ], 500);
        }
    }
}
