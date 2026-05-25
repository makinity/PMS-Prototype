<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\UnitWorkPlan;
use App\Models\User;
use App\Models\Office;
use App\Models\PerformancePeriod;
use App\Models\UwpFunction;
use App\Models\UwpMfo;
use App\Models\UwpIndicatorAssignment;
use App\Models\UwpQetStandard;
use App\Models\UwpSuccessIndicator;
use App\Models\UwpConsolidationSignature;
use App\Notifications\WorkflowEventNotification;
use App\Services\WorkflowNotificationDispatcher;
use App\Services\UwpConsolidationSignatureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class UnitWorkPlanController extends Controller
{
    public function __construct(
        private readonly UwpConsolidationSignatureService $signatureService
    ) {
    }
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
            ->where('created_by', $user->id)
            ->first();

        if ($existing) {

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


    public function showJson(Request $request, int $id)
    {
        $uwp = UnitWorkPlan::find($id);
        if (!$uwp) {
            return response()->json([
                'message' => 'UWP not found.',
            ], 404);
        }

        return $this->buildUwpShowResponse($request, $uwp);
    }

    public function previewJson(Request $request, int $id)
    {
        $uwp = UnitWorkPlan::find($id);
        if (!$uwp) {
            return response()->json([
                'message' => 'UWP not found.',
            ], 404);
        }

        return $this->buildUwpShowResponse($request, $uwp);
    }

    // Backward-compatible aliases during rollout
    public function show(Request $request, int $id)
    {
        return $this->showJson($request, $id);
    }

    public function preview(Request $request, int $id)
    {
        return $this->previewJson($request, $id);
    }

    public function showPage(Request $request, int $id)
    {
        $uwp = UnitWorkPlan::find($id);
        if (!$uwp) {
            return redirect()->route('supervisor.uwp-page')->with('error', 'UWP not found.');
        }

        try {
            $payload = $this->buildUwpShowPayload($request, $uwp);

            $status = strtolower((string) ($payload['status'] ?? ''));
            $isEditable = in_array($status, [UnitWorkPlan::STATUS_DRAFT, UnitWorkPlan::STATUS_RETURNED], true)
                && empty($payload['locked_at']);
            $canSubmit = $isEditable;

            return view('supervisor.uwp-show', [
                'uwp' => $payload,
                'canSubmit' => $canSubmit,
                'isEditable' => $isEditable,
            ]);
        } catch (\Throwable $e) {
            return redirect()->route('supervisor.uwp-page')->with('error', $e->getMessage() ?: 'Unable to open UWP preview.');
        }
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
            // Build a flat ordered list of MFO IDs so the client can
            // construct the success-indicators URL right after saving,
            // without a full page reload.
            $mfoIds = [];
            $uwp->load('uwpFunctions.mfos');
            foreach ($uwp->uwpFunctions->sortBy('sort_order') as $func) {
                foreach ($func->mfos->sortBy('sort_order') as $mfo) {
                    $mfoIds[] = $mfo->id;
                }
            }

            return response()->json([
                'success' => true,
                'uwp_id' => $uwp->id,
                'status' => $uwp->status,
                'message' => 'UWP draft saved.',
                'mfo_ids' => $mfoIds,
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

        DB::transaction(function () use ($uwp, $request) {
            $uwp->update([
                'status' => UnitWorkPlan::STATUS_SUBMITTED,
                'submitted_at' => now(),
                'locked_at' => now(),
            ]);

            // If a signature is provided, create the signed artifact and record
            if ($request->filled('signature')) {
                $signedArtifact = $this->signatureService->createSignedArtifact(
                    $uwp,
                    $request->input('signature')
                );

                UwpConsolidationSignature::query()->create([
                    'unit_work_plan_id' => $uwp->id,
                    'opcr_id' => null, // Not yet consolidated into an OPCR
                    'signed_by' => auth()->id(),
                    'signature_image_path' => $signedArtifact['signature_image_path'],
                    'signed_excel_path' => $signedArtifact['signed_excel_path'],
                    'signature_hash' => $signedArtifact['signature_hash'],
                    'signed_at' => now(),
                    'metadata' => [
                        'action' => 'supervisor_submit',
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ],
                ]);
            }
        });

        $uwp->loadMissing(['office.head', 'performancePeriod']);
        $departmentHead = $uwp->office?->head;
        if ($departmentHead) {
            app(WorkflowNotificationDispatcher::class)->notifyUser(
                $departmentHead,
                new WorkflowEventNotification(
                    title: 'UWP Submitted',
                    body: "{$user->name} submitted a Unit Work Plan for review.",
                    url: route('dept-head.uwp.show', ['id' => $uwp->id]),
                    type: 'info',
                    meta: [
                        'event' => 'uwp.submitted',
                        'uwp_id' => $uwp->id,
                        'office_id' => $uwp->office_id,
                        'performance_period_id' => $uwp->performance_period_id,
                        'status' => UnitWorkPlan::STATUS_SUBMITTED,
                        'source_role' => 'supervisor',
                    ],
                )
            );
        }

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
        // Read signature directly from JSON or form input — do NOT rely on validate()
        // to cache JSON body since large base64 payloads can be silently dropped.
        $signatureInput = $request->input('signature');

        Log::info('UWP submitForApproval called', [
            'uwp_id'           => $id,
            'has_signature'    => !empty($signatureInput),
            'content_type'     => $request->header('Content-Type'),
            'is_json'          => $request->isJson(),
        ]);

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

            DB::transaction(function () use ($uwp, $request, $signatureInput) {
                $uwp->update([
                    'status'       => UnitWorkPlan::STATUS_SUBMITTED,
                    'submitted_at' => now(),
                    'locked_at'    => now(),
                ]);

                // Process signature if provided
                if (!empty($signatureInput)) {
                    Log::info('UWP submitForApproval: processing signature', ['uwp_id' => $uwp->id]);

                    $signedArtifact = $this->signatureService->createSignedArtifact(
                        $uwp,
                        $signatureInput
                    );

                    UwpConsolidationSignature::query()->create([
                        'unit_work_plan_id'    => $uwp->id,
                        'opcr_id'              => null,
                        'signed_by'            => auth()->id(),
                        'signature_image_path' => $signedArtifact['signature_image_path'],
                        'signed_excel_path'    => $signedArtifact['signed_excel_path'],
                        'signature_hash'       => $signedArtifact['signature_hash'],
                        'signed_at'            => now(),
                        'metadata'             => [
                            'action'     => 'supervisor_submit',
                            'ip_address' => $request->ip(),
                            'user_agent' => $request->userAgent(),
                        ],
                    ]);

                    Log::info('UWP submitForApproval: signature record saved', ['uwp_id' => $uwp->id]);
                } else {
                    Log::warning('UWP submitForApproval: no signature provided, skipping artifact creation', ['uwp_id' => $uwp->id]);
                }

                Log::info('UWP submitted for department head review', [
                    'uwp_id'       => $uwp->id,
                    'submitted_by' => auth()->id(),
                    'submitted_at' => now()
                ]);
            });

            $uwp->loadMissing(['office.head', 'performancePeriod']);
            $departmentHead = $uwp->office?->head;
            if ($departmentHead) {
                app(WorkflowNotificationDispatcher::class)->notifyUser(
                    $departmentHead,
                    new WorkflowEventNotification(
                        title: 'UWP Submitted',
                        body: "{$user->name} submitted a Unit Work Plan for review.",
                        url: route('dept-head.uwp.show', ['id' => $uwp->id]),
                        type: 'info',
                        meta: [
                            'event' => 'uwp.submitted',
                            'uwp_id' => $uwp->id,
                            'office_id' => $uwp->office_id,
                            'performance_period_id' => $uwp->performance_period_id,
                            'status' => UnitWorkPlan::STATUS_SUBMITTED,
                            'source_role' => 'supervisor',
                        ],
                    )
                );
            }

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

        if ($request->boolean('redirect_to_list') && $success) {
            return redirect()->route('supervisor.uwp-page')->with('success', $message);
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
            'signature' => ['nullable', 'string'],
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

            $type = strtolower(trim((string) ($functionPayload['type'] ?? $functionPayload['function_type'] ?? 'core')));
            if (!in_array($type, ['core', 'support'], true)) {
                $type = 'core';
            }

            if ($name === '') {
                $name = $type === 'core' ? 'Core Functions' : 'Support Functions';
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

            $fallbackTargetQuantity = $this->normalizeTargetQuantity(
                $mfoPayload['target_quantity'] ?? $mfoPayload['targetQuantity'] ?? null
            );
            $fallbackTargetTimeline = (string) ($mfoPayload['target'] ?? $mfoPayload['target_timeline'] ?? '');
            $indicators = $this->normalizeIndicatorsPayload(
                $mfoPayload['indicators'] ?? $mfoPayload['success_indicators'] ?? [],
                $fallbackTargetQuantity,
                $fallbackTargetTimeline
            );
            $derivedTargets = $this->deriveMfoTargetsFromIndicators(
                $indicators,
                $fallbackTargetQuantity,
                $fallbackTargetTimeline
            );

            $mfos[] = [
                'title' => $title,
                'target_quantity' => $derivedTargets['target_quantity'],
                'target_timeline' => $derivedTargets['target_timeline'],
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

            $functionCode = strtolower(trim((string) ($mfoPayload['function_code'] ?? 'core')));
            if (!in_array($functionCode, ['core', 'support'], true)) {
                $functionCode = 'core';
            }

            if (!isset($functionMap[$functionCode])) {
                $functionMap[$functionCode] = $functionOrder++;
                $functions[$functionCode] = [
                    'name' => $functionCode === 'core' ? 'Core Functions' : 'Support Functions',
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

            $fallbackTargetQuantity = $this->normalizeTargetQuantity(
                $mfoPayload['target_quantity'] ?? $mfoPayload['targetQuantity'] ?? null
            );
            $fallbackTargetTimeline = (string) ($mfoPayload['target_summary'] ?? $mfoPayload['target_timeline'] ?? '');
            $indicators = $this->normalizeIndicatorsPayload(
                $mfoPayload['success_indicators'] ?? [],
                $fallbackTargetQuantity,
                $fallbackTargetTimeline
            );
            $derivedTargets = $this->deriveMfoTargetsFromIndicators(
                $indicators,
                $fallbackTargetQuantity,
                $fallbackTargetTimeline
            );

            $functions[$functionCode]['mfos'][] = [
                'title' => $title,
                'target_quantity' => $derivedTargets['target_quantity'],
                'target_timeline' => $derivedTargets['target_timeline'],
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

    private function deriveMfoTargetsFromIndicators(
        array $indicators,
        mixed $fallbackTargetQuantity = null,
        ?string $fallbackTargetTimeline = null
    ): array {
        $quantities = [];
        $timelines = [];

        foreach ($indicators as $indicator) {
            $quantity = $this->normalizeTargetQuantity($indicator['target_quantity'] ?? null);
            if ($quantity !== null) {
                $quantities[] = $quantity;
            }

            $timeline = trim((string) ($indicator['target_timeline'] ?? ''));
            if ($timeline !== '') {
                $timelines[] = $timeline;
            }
        }

        $targetQuantity = !empty($quantities)
            ? array_sum($quantities)
            : $this->normalizeTargetQuantity($fallbackTargetQuantity);

        $uniqueTimelines = array_values(array_unique($timelines));
        if (count($uniqueTimelines) === 1) {
            $targetTimeline = $uniqueTimelines[0];
        } elseif (!empty($quantities) || !empty($timelines)) {
            $targetTimeline = 'Per success indicator';
        } else {
            $targetTimeline = trim((string) ($fallbackTargetTimeline ?? ''));
        }

        return [
            'target_quantity' => $targetQuantity,
            'target_timeline' => $targetTimeline,
        ];
    }

    private function normalizeIndicatorsPayload(
        array $indicatorsPayload,
        mixed $fallbackTargetQuantity = null,
        ?string $fallbackTargetTimeline = null
    ): array
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
            $targetQuantity = $this->normalizeTargetQuantity(
                $indicatorPayload['target_quantity'] ?? $indicatorPayload['targetQuantity'] ?? $fallbackTargetQuantity
            );
            $targetTimeline = trim((string) (
                $indicatorPayload['target_timeline']
                ?? $indicatorPayload['target']
                ?? $fallbackTargetTimeline
                ?? ''
            ));

            $indicators[] = [
                'indicator_text' => $text,
                'target_quantity' => $targetQuantity,
                'target_timeline' => $targetTimeline,
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
                    ->where('created_by', $user->id)
                    ->first();

                if ($uwp) {

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
                ->where('created_by', $user->id)
                ->where('id', '!=', $uwp->id)
                ->first();

            if ($conflict) {
                throw ValidationException::withMessages([
                    'office_id' => 'You already have a Unit Work Plan for the selected Office/Unit and Performance Period.',
                ]);
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

                $type = strtolower(trim((string) ($functionPayload['function_type'] ?? 'core')));
                if (!in_array($type, ['core', 'support'], true)) {
                    $type = 'core';
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

                    $fallbackTargetQuantity = $this->normalizeTargetQuantity(
                        $mfoPayload['target_quantity'] ?? $mfoPayload['targetQuantity'] ?? null
                    );
                    $fallbackTargetTimeline = (string) ($mfoPayload['target_timeline'] ?? $mfoPayload['target'] ?? '');
                    $indicators = is_array($mfoPayload['indicators'] ?? null)
                        ? $this->normalizeIndicatorsPayload(
                            $mfoPayload['indicators'],
                            $fallbackTargetQuantity,
                            $fallbackTargetTimeline
                        )
                        : [];
                    $derivedTargets = $this->deriveMfoTargetsFromIndicators(
                        $indicators,
                        $fallbackTargetQuantity,
                        $fallbackTargetTimeline
                    );

                    $mfo = $function->mfos()->create([
                        'title' => $title,
                        'target_quantity' => $derivedTargets['target_quantity'],
                        'target_timeline' => $derivedTargets['target_timeline'],
                        'weight_percent' => isset($mfoPayload['weight_percent']) ? (float) $mfoPayload['weight_percent'] : null,
                        'sort_order' => (int) ($mfoPayload['sort_order'] ?? $mfoOrder),
                    ]);

                    $mfoOrder++;

                    $indicatorOrder = 1;
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
                            'target_quantity' => $this->normalizeTargetQuantity($indicatorPayload['target_quantity'] ?? null),
                            'target_timeline' => trim((string) ($indicatorPayload['target_timeline'] ?? '')),
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

            // Child records can change without mutating the parent row directly.
            // Touch the UWP so downstream preview logic can distinguish a freshly
            // saved draft from untouched legacy seeded data.
            $uwp->touch();

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

    private function legacySeedStandardsMap(): array
    {
        return [
            'All e-bank transactions scanned and encoded daily' => [
                5 => ['q' => ['No errors; accurate encoding'], 'e' => ['100% processed'], 't' => ['Same working day']],
                4 => ['q' => ['Minor errors'], 'e' => ['100% processed'], 't' => ['Same working day']],
                3 => ['q' => ['Few minor errors'], 'e' => ['95–99% processed'], 't' => ['End of working day']],
                2 => ['q' => ['Multiple errors'], 'e' => ['<95% processed'], 't' => ['Beyond working day']],
                1 => ['q' => ['Major errors/missing'], 'e' => ['Majority unprocessed'], 't' => ['Not within acceptable time']],
            ],
            'Indexing complete with no missing pages' => [
                5 => ['q' => ['Indexing fully verified, zero gaps'], 'e' => ['100% pages indexed'], 't' => ['Same day']],
                4 => ['q' => ['Indexing minor rechecks'], 'e' => ['100% pages indexed'], 't' => ['Same day']],
                3 => ['q' => ['Occasional missing indexes fixed'], 'e' => ['95–99% indexed'], 't' => ['Within 24 hours']],
                2 => ['q' => ['Frequent missing pages'], 'e' => ['<95% indexed'], 't' => ['Beyond 24 hours']],
                1 => ['q' => ['Indexing largely incomplete'], 'e' => ['Major gaps'], 't' => ['Unacceptable']],
            ],
            'Audit trail maintained within 24 hours' => [
                5 => ['q' => ['Complete trail, no errors'], 'e' => ['100% entries captured'], 't' => ['Within 24 hours']],
                4 => ['q' => ['Minor corrections only'], 'e' => ['100% entries captured'], 't' => ['Within 24 hours']],
                3 => ['q' => ['Some gaps corrected'], 'e' => ['95–99% entries captured'], 't' => ['Within 48 hours']],
                2 => ['q' => ['Multiple missing logs'], 'e' => ['<95% captured'], 't' => ['Beyond 48 hours']],
                1 => ['q' => ['Trail missing'], 'e' => ['Majority uncaptured'], 't' => ['Unacceptable']],
            ],
            'Same-day verification of OTC transactions' => [
                5 => ['q' => ['Verified without discrepancies'], 'e' => ['100% OTC verified'], 't' => ['Same working day']],
                4 => ['q' => ['Minor verifications pending'], 'e' => ['100% OTC verified'], 't' => ['Same working day']],
                3 => ['q' => ['Few pending verifications'], 'e' => ['95–99% verified'], 't' => ['End of working day']],
                2 => ['q' => ['Several unverified'], 'e' => ['<95% verified'], 't' => ['Beyond working day']],
                1 => ['q' => ['Verification not done'], 'e' => ['Majority unverified'], 't' => ['Unacceptable']],
            ],
            '95% encoded within the business day' => [
                5 => ['q' => ['Encodings error-free'], 'e' => ['100% encoded'], 't' => ['Same business day']],
                4 => ['q' => ['Minor corrections'], 'e' => ['100% encoded'], 't' => ['Same business day']],
                3 => ['q' => ['Few delays'], 'e' => ['95–99% encoded'], 't' => ['By end of day']],
                2 => ['q' => ['Multiple delays'], 'e' => ['<95% encoded'], 't' => ['Next day']],
                1 => ['q' => ['Encoding largely incomplete'], 'e' => ['Major backlog'], 't' => ['Unacceptable']],
            ],
            'OR validation completed daily' => [
                5 => ['q' => ['All ORs validated error-free'], 'e' => ['100% validated'], 't' => ['Daily']],
                4 => ['q' => ['Minor issues corrected same day'], 'e' => ['100% validated'], 't' => ['Daily']],
                3 => ['q' => ['Some validations late'], 'e' => ['95–99% validated'], 't' => ['Within 48 hours']],
                2 => ['q' => ['Frequent late validations'], 'e' => ['<95% validated'], 't' => ['Beyond 48 hours']],
                1 => ['q' => ['Validations mostly missing'], 'e' => ['Majority unvalidated'], 't' => ['Unacceptable']],
            ],
            'Weekly filing updated and retrievable' => [
                5 => ['q' => ['Zero retrieval issues'], 'e' => ['100% weekly updates'], 't' => ['Within week']],
                4 => ['q' => ['Minor retrieval fixes'], 'e' => ['100% weekly updates'], 't' => ['Within week']],
                3 => ['q' => ['Some items late'], 'e' => ['95–99% updates'], 't' => ['Within next week']],
                2 => ['q' => ['Many late updates'], 'e' => ['<95% updates'], 't' => ['Beyond next week']],
                1 => ['q' => ['Updates not done'], 'e' => ['Major gaps'], 't' => ['Unacceptable']],
            ],
            'Digital backups synced monthly' => [
                5 => ['q' => ['Backups verified'], 'e' => ['100% synced'], 't' => ['Within month']],
                4 => ['q' => ['Minor sync corrections'], 'e' => ['100% synced'], 't' => ['Within month']],
                3 => ['q' => ['Some delays'], 'e' => ['95–99% synced'], 't' => ['Within following week']],
                2 => ['q' => ['Frequent delays'], 'e' => ['<95% synced'], 't' => ['Beyond following week']],
                1 => ['q' => ['Backups largely missing'], 'e' => ['Major gaps'], 't' => ['Unacceptable']],
            ],
            'Retrieval logs maintained for audits' => [
                5 => ['q' => ['Logs complete and audit-ready'], 'e' => ['100% requests logged'], 't' => ['Same day']],
                4 => ['q' => ['Minor log gaps corrected'], 'e' => ['100% requests logged'], 't' => ['Same day']],
                3 => ['q' => ['Some gaps'], 'e' => ['95–99% logged'], 't' => ['Within 48 hours']],
                2 => ['q' => ['Many gaps'], 'e' => ['<95% logged'], 't' => ['Beyond 48 hours']],
                1 => ['q' => ['Logs largely missing'], 'e' => ['Majority unlogged'], 't' => ['Unacceptable']],
            ],
        ];
    }

    private function normalizeLegacySeedText(string $value): string
    {
        $normalized = str_replace(['–', '—', 'â€“', 'â€”'], '-', $value);
        $normalized = preg_replace('/\s+/', ' ', trim($normalized)) ?? trim($normalized);

        return mb_strtolower($normalized);
    }

    private function isLegacySeedDraft(UnitWorkPlan $uwp): bool
    {
        if (!in_array((string) $uwp->status, [UnitWorkPlan::STATUS_DRAFT, UnitWorkPlan::STATUS_RETURNED], true)) {
            return false;
        }

        if (!$uwp->created_at || !$uwp->updated_at) {
            return true;
        }

        return abs($uwp->updated_at->getTimestamp() - $uwp->created_at->getTimestamp()) < 1;
    }

    private function isLegacySeededIndicator(UnitWorkPlan $uwp, $indicator): bool
    {
        if (!$this->isLegacySeedDraft($uwp)) {
            return false;
        }

        $seed = $this->legacySeedStandardsMap()[(string) ($indicator->indicator_text ?? '')] ?? null;
        if (!$seed) {
            return false;
        }

        $actual = [];
        foreach ($indicator->qetStandards ?? [] as $standard) {
            $rating = (int) ($standard->rating ?? 0);
            $dimension = (string) ($standard->dimension ?? '');
            $actual[$rating][$dimension][] = $this->normalizeLegacySeedText((string) ($standard->standard_text ?? ''));
        }

        foreach ([5, 4, 3, 2, 1] as $rating) {
            foreach (['q', 'e', 't'] as $dimension) {
                $actualValues = $actual[$rating][$dimension] ?? [];
                $seedValues = array_map(
                    fn ($value) => $this->normalizeLegacySeedText((string) $value),
                    $seed[$rating][$dimension] ?? []
                );

                sort($actualValues);
                sort($seedValues);

                if ($actualValues !== $seedValues) {
                    return false;
                }
            }
        }

        return true;
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
                    'isCustom' => false,
                    'mfos' => $function->mfos
                        ->sortBy('sort_order')
                        ->values()
                        ->map(function ($mfo) {
                            return [
                                'id' => $mfo->id,
                                'title' => (string) $mfo->title,
                                'targetQuantity' => $this->normalizeTargetQuantity($mfo->target_quantity),
                                'target' => (string) ($mfo->target_timeline ?? ''),
                                'indicators' => $mfo->successIndicators
                                    ->sortBy('sort_order')
                                    ->values()
                                    ->map(function ($indicator) {
                                        return [
                                            'text' => (string) $indicator->indicator_text,
                                            'targetQuantity' => $this->normalizeTargetQuantity($indicator->target_quantity),
                                            'targetTimeline' => (string) ($indicator->target_timeline ?? ''),
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
                                                ->map(fn ($assignment) => $assignment->employee?->id)
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
            $payload = $this->buildUwpShowPayload($request, $uwp);
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

    private function buildUwpShowPayload(Request $request, UnitWorkPlan $uwp): array
    {
        $user = $request->user();
        if (!$user) {
            throw new \RuntimeException('Unauthorized.');
        }

        $isPrivileged = in_array($user->role, ['admin', 'pmt', 'dept-head'], true);
        $isOwner = (int) $uwp->created_by === (int) $user->id;
        $isSupervisorSameOffice = $user->role === 'supervisor' && (int) $user->office_id === (int) $uwp->office_id;

        if (!$isPrivileged && !$isOwner && !$isSupervisorSameOffice) {
            throw new \RuntimeException('You are not allowed to view this UWP.');
        }

        $uwp->load([
            'office.head',
            'performancePeriod',
            'creator',
            'returnedByUser',
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

        return [
            'id' => $uwp->id,
            'status' => $uwp->status,
            'created_at' => optional($uwp->created_at)->toDateTimeString(),
            'submitted_at' => optional($uwp->submitted_at)->toDateTimeString(),
            'locked_at' => optional($uwp->locked_at)->toDateTimeString(),
            'updated_at' => optional($uwp->updated_at)->toDateTimeString(),
            'returned_at' => optional($uwp->returned_at)->toDateTimeString(),
            'return_remarks' => (string) ($uwp->return_remarks ?? ''),
            'returned_by_user' => [
                'id' => $uwp->returnedByUser?->id,
                'name' => $uwp->returnedByUser?->name,
            ],
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
                                    'target_quantity' => $this->normalizeTargetQuantity($indicator->target_quantity),
                                    'target_timeline' => $indicator->target_timeline,
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
                                                'profile_photo_url' => $assignment->employee?->profile_photo_url,
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
    }

    public function showSuccessIndicators(Request $request, int $uwpId, int $mfoId): \Illuminate\View\View
    {
        $user = $this->resolveSupervisorUser($request);

        $uwp = UnitWorkPlan::with(['returnedByUser'])->find($uwpId);
        if (!$uwp) {
            abort(404);
        }

        if ((int) $uwp->created_by !== (int) $user->id) {
            abort(403);
        }

        $mfo = UwpMfo::with([
            'successIndicators' => fn ($q) => $q->orderBy('sort_order'),
            'successIndicators.qetStandards',
            'successIndicators.assignments.employee',
            'uwpFunction',
        ])->find($mfoId);

        if (!$mfo || (int) ($mfo->uwpFunction->unit_work_plan_id ?? 0) !== $uwpId) {
            abort(404);
        }

        $statusKey  = strtolower((string) $uwp->status);
        $isDraft    = $statusKey === UnitWorkPlan::STATUS_DRAFT;
        $isReturned = $statusKey === UnitWorkPlan::STATUS_RETURNED;
        $isLocked   = !is_null($uwp->locked_at);
        $canEdit    = ($isDraft || $isReturned) && !$isLocked;
        $status     = $uwp->status;
        $locked_at  = $uwp->locked_at;

        $officeEmployees = User::query()
            ->where('office_id', $uwp->office_id)
            ->where('role', 'employee')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'office_id']);

        $initialIndicators = $mfo->successIndicators
            ->map(fn ($indicator) => [
                'id'             => $indicator->id,
                'text'           => (string) $indicator->indicator_text,
                'targetQuantity' => $indicator->target_quantity,
                'targetTimeline' => (string) ($indicator->target_timeline ?? ''),
                'sort_order'     => (int) $indicator->sort_order,
                'standards'      => $indicator->qetStandards
                    ->sortByDesc('rating')
                    ->values()
                    ->map(fn ($s) => [
                        'rating'    => (int) $s->rating,
                        'dimension' => (string) $s->dimension,
                        'text'      => (string) $s->standard_text,
                    ])
                    ->all(),
                'assignees' => $indicator->assignments
                    ->map(fn ($a) => $a->employee?->id)
                    ->filter()
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();

        return view('supervisor.uwp-success-indicators', compact(
            'uwp', 'mfo', 'status', 'locked_at', 'canEdit',
            'officeEmployees', 'initialIndicators'
        ));
    }

    public function saveSuccessIndicators(Request $request, int $uwpId, int $mfoId): \Illuminate\Http\RedirectResponse
    {
        $user = $this->resolveSupervisorUser($request);

        $uwp = UnitWorkPlan::find($uwpId);
        if (!$uwp) {
            abort(404);
        }

        if ((int) $uwp->created_by !== (int) $user->id) {
            abort(403);
        }

        $statusKey  = strtolower((string) $uwp->status);
        $isDraft    = $statusKey === UnitWorkPlan::STATUS_DRAFT;
        $isReturned = $statusKey === UnitWorkPlan::STATUS_RETURNED;
        $isLocked   = !is_null($uwp->locked_at);
        $canEdit    = ($isDraft || $isReturned) && !$isLocked;

        if (!$canEdit) {
            return back()
                ->withInput()
                ->with('error', 'This UWP is read-only and cannot be edited.');
        }

        $mfo = UwpMfo::with(['uwpFunction'])->find($mfoId);
        if (!$mfo || (int) ($mfo->uwpFunction->unit_work_plan_id ?? 0) !== $uwpId) {
            abort(404);
        }

        $rawPayload = $request->input('indicators_payload', '[]');
        $indicators = json_decode($rawPayload, true);
        if (!is_array($indicators)) {
            return back()->withInput()->with('error', 'Invalid indicators payload.');
        }

        try {
            DB::transaction(function () use ($mfo, $indicators, $user) {
                foreach ($mfo->successIndicators()->with(['qetStandards', 'assignments'])->get() as $old) {
                    $old->assignments()->delete();
                    $old->qetStandards()->delete();
                }
                $mfo->successIndicators()->delete();

                foreach ($indicators as $sortOrder => $item) {
                    $indicatorText = trim((string) ($item['text'] ?? ''));
                    if ($indicatorText === '') {
                        continue;
                    }

                    $indicator = UwpSuccessIndicator::create([
                        'uwp_mfo_id'      => $mfo->id,
                        'indicator_text'  => $indicatorText,
                        'target_quantity' => isset($item['targetQuantity']) ? (int) $item['targetQuantity'] : null,
                        'target_timeline' => trim((string) ($item['targetTimeline'] ?? '')),
                        'sort_order'      => (int) $sortOrder + 1,
                    ]);

                    foreach ($item['standards'] ?? [] as $standard) {
                        $text = trim((string) ($standard['text'] ?? ''));
                        if ($text === '') {
                            continue;
                        }
                        UwpQetStandard::create([
                            'uwp_success_indicator_id' => $indicator->id,
                            'dimension'                => (string) ($standard['dimension'] ?? 'q'),
                            'rating'                   => (int) ($standard['rating'] ?? 3),
                            'standard_text'            => $text,
                        ]);
                    }

                    $assignedIds = array_unique(array_filter(
                        array_map('intval', $item['assignees'] ?? [])
                    ));
                    foreach ($assignedIds as $employeeId) {
                        UwpIndicatorAssignment::create([
                            'uwp_success_indicator_id' => $indicator->id,
                            'employee_id'              => $employeeId,
                            'assigned_by'              => $user->id,
                            'assigned_at'              => now(),
                        ]);
                    }
                }
            });
        } catch (\Throwable $e) {
            Log::error('saveSuccessIndicators failed', [
                'uwp_id' => $uwpId,
                'mfo_id' => $mfoId,
                'error'  => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'Failed to save indicators. Please try again.');
        }

        return redirect()
            ->route('supervisor.uwp', ['uwp_id' => $uwpId])
            ->with('success', 'Success indicators saved successfully.');
    }
}
