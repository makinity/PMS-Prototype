# Design Document

## Feature: supervisor-uwp-success-indicator-page

### Overview

This feature extracts the Success Indicator modal from `supervisor/uwp.blade.php` into a dedicated full-page workspace. The new page is scoped to a single `UwpMfo` record and mirrors the existing modal's four-tab interface (Overview, Targets, Standards, Assignees). The MFO row "Success Indicator" button in the UWP editor is updated to navigate to the new page. The modal HTML and its JavaScript are removed from `uwp.blade.php` once the new page is in place.

The project is a **Laravel + Blade + vanilla JS** application. All code examples follow the existing project conventions.

---

## Architecture

### Component Map

```
routes/web.php
  └── GET  /uwp/{uwpId}/mfo/{mfoId}/success-indicators  → UnitWorkPlanController@showSuccessIndicators
  └── POST /uwp/{uwpId}/mfo/{mfoId}/success-indicators  → UnitWorkPlanController@saveSuccessIndicators

app/Http/Controllers/Supervisor/UnitWorkPlanController.php
  ├── showSuccessIndicators(Request, int $uwpId, int $mfoId) : View
  └── saveSuccessIndicators(Request, int $uwpId, int $mfoId) : RedirectResponse

resources/views/supervisor/uwp-success-indicators.blade.php
  └── @extends('layouts.supervisor')
      ├── Back link → route('supervisor.uwp', ['uwp_id' => $uwp->id])
      ├── Page header (title + MFO subtitle)
      ├── Four-tab workspace (Overview / Targets / Standards / Assignees)
      ├── Save & Return button → POST supervisor.uwp.success-indicators.save
      └── Inline JS (ported workspace logic, scoped to this page)

resources/views/supervisor/uwp.blade.php  [modified]
  ├── renderFunctions() MFO row: button → anchor tag using pre-computed route URLs
  ├── Remove #uwp-indicators-modal HTML block
  └── Remove openUwpIndicatorsModal / closeUwpIndicatorsModal JS
```

### Data Flow

```
GET /uwp/{uwpId}/mfo/{mfoId}/success-indicators
  ↓
showSuccessIndicators()
  ├── Load UnitWorkPlan (with returnedByUser)
  ├── Load UwpMfo (with successIndicators.qetStandards, successIndicators.assignments.employee)
  ├── Verify ownership (created_by === auth user)
  ├── Derive $canEdit, $status, $locked_at
  ├── Load $officeEmployees (role=employee, same office)
  └── Map $initialIndicators (JSON-serializable array)
  ↓
View: uwp-success-indicators.blade.php
  └── JS initializes workspace state from $initialIndicators

POST /uwp/{uwpId}/mfo/{mfoId}/success-indicators
  ↓
saveSuccessIndicators()
  ├── Verify ownership + canEdit
  ├── Decode indicators_payload JSON
  ├── DB::transaction {
  │     delete old indicators for $mfoId
  │     create new UwpSuccessIndicator records
  │     create UwpQetStandard records per indicator
  │     create UwpIndicatorAssignment records per indicator
  │   }
  └── Redirect → supervisor.uwp?uwp_id={uwpId} with success flash
```

---

## Components

### 1. Route Registration (`routes/web.php`)

Two new routes are added inside the existing `supervisor` prefix + `auth` middleware group, alongside the existing UWP routes:

```php
// Stage I - UWP Success Indicators (dedicated page)
Route::get('/uwp/{uwpId}/mfo/{mfoId}/success-indicators', [UnitWorkPlanController::class, 'showSuccessIndicators'])
    ->name('supervisor.uwp.success-indicators');
Route::post('/uwp/{uwpId}/mfo/{mfoId}/success-indicators', [UnitWorkPlanController::class, 'saveSuccessIndicators'])
    ->name('supervisor.uwp.success-indicators.save');
```

Both routes resolve to `App\Http\Controllers\Supervisor\UnitWorkPlanController`.

---

### 2. Controller Methods (`UnitWorkPlanController`)

#### 2.1 `showSuccessIndicators`

```php
public function showSuccessIndicators(Request $request, int $uwpId, int $mfoId): \Illuminate\View\View
{
    $user = $this->resolveSupervisorUser($request);

    $uwp = UnitWorkPlan::with(['returnedByUser'])->find($uwpId);
    if (!$uwp) {
        abort(404);
    }

    // Ownership check
    if ((int) $uwp->created_by !== (int) $user->id) {
        abort(403);
    }

    // Load MFO with full indicator tree
    $mfo = UwpMfo::with([
        'successIndicators' => fn ($q) => $q->orderBy('sort_order'),
        'successIndicators.qetStandards',
        'successIndicators.assignments.employee',
        'uwpFunction',
    ])->find($mfoId);

    // MFO must exist and belong to this UWP
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
            'id'              => $indicator->id,
            'text'            => (string) $indicator->indicator_text,
            'targetQuantity'  => $indicator->target_quantity,
            'targetTimeline'  => (string) ($indicator->target_timeline ?? ''),
            'sort_order'      => (int) $indicator->sort_order,
            'standards'       => $indicator->qetStandards
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
```

#### 2.2 `saveSuccessIndicators`

```php
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
            // Delete existing indicators (cascades to standards + assignments)
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
                    'uwp_mfo_id'       => $mfo->id,
                    'indicator_text'   => $indicatorText,
                    'target_quantity'  => isset($item['targetQuantity']) ? (int) $item['targetQuantity'] : null,
                    'target_timeline'  => trim((string) ($item['targetTimeline'] ?? '')),
                    'sort_order'       => (int) $sortOrder + 1,
                ]);

                // Persist QET standards
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

                // Persist employee assignments
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
```

---

### 3. Blade View (`uwp-success-indicators.blade.php`)

The view extends `layouts.supervisor` and reuses the same tab/panel structure from the modal, adapted as a standalone page.

**Key structural elements:**

```
@extends('layouts.supervisor')
@section('main-content')
  <!-- Back link -->
  <a href="{{ route('supervisor.uwp', ['uwp_id' => $uwp->id]) }}">← Back to UWP Editor</a>

  <!-- Page header -->
  <h1>Success Indicator Workspace</h1>
  <p>{{ $mfo->title }}</p>

  <!-- canEdit / status badges -->

  <!-- Four-tab workspace (same HTML structure as the modal panels) -->
  <div id="uwp-si-workspace">
    <!-- Tab bar: Overview | Targets | Standards | Assignees -->
    <!-- Left sidebar: indicator nav list -->
    <!-- Right panel: tab panels -->
  </div>

  <!-- Save & Return form -->
  <form method="POST" action="{{ route('supervisor.uwp.success-indicators.save', ...) }}">
    @csrf
    <input type="hidden" name="indicators_payload" id="si-indicators-payload">
    <button type="submit">Save & Return to UWP Editor</button>
  </form>
@endsection

@push('scripts')
  <script>
    // Workspace JS (ported from modal, scoped to this page)
    // Initialized from: const initialIndicators = @json($initialIndicators);
    // Assigned employees from: const assignedData = @json($assignedData);
  </script>
@endpush
```

**PHP variables passed to the view:**

| Variable | Type | Description |
|---|---|---|
| `$uwp` | `UnitWorkPlan` | The parent UWP record |
| `$mfo` | `UwpMfo` | The target MFO record |
| `$status` | `string` | UWP status string |
| `$locked_at` | `datetime\|null` | UWP lock timestamp |
| `$canEdit` | `bool` | `($isDraft \|\| $isReturned) && !$isLocked` |
| `$officeEmployees` | `Collection` | Employees in the UWP's office |
| `$initialIndicators` | `array` | JSON-serializable indicator array |

---

### 4. UWP Editor Button Update (`uwp.blade.php`)

#### 4.1 Pre-computing Route URLs in PHP

Since `renderFunctions()` builds HTML via JS string interpolation, route URLs for each MFO must be pre-computed in PHP and passed to JS. The approach is to embed a PHP-generated URL map as a JS variable:

```php
@php
    // Build a map of mfo DB id → success indicators page URL
    // This is used by renderFunctions() to build anchor hrefs
    $mfoSuccessIndicatorUrls = [];
    if ($uwp && $initialFunctions) {
        foreach ($uwp->uwpFunctions as $func) {
            foreach ($func->mfos as $mfo) {
                $mfoSuccessIndicatorUrls[$mfo->id] = route(
                    'supervisor.uwp.success-indicators',
                    ['uwpId' => $uwp->id, 'mfoId' => $mfo->id]
                );
            }
        }
    }
@endphp
<script>
    const mfoSuccessIndicatorUrls = @json($mfoSuccessIndicatorUrls);
    const uwpSuccessIndicatorsBaseUrl = '{{ $uwp ? url("/supervisor/uwp/{$uwp->id}/mfo") : "" }}';
</script>
```

Each MFO object in `uwpState` carries its DB `id` (populated from `$initialFunctions`). The `renderFunctions()` JS uses `mfoSuccessIndicatorUrls[mfo.id]` to build the anchor href. For new (unsaved) MFOs that have no DB id yet, the button falls back to a disabled state with a tooltip indicating the UWP must be saved first.

#### 4.2 MFO Row Button Change

In `renderFunctions()`, the "Success Indicator" button cell changes from:

```js
// BEFORE
`<button type="button" data-action="view-indicators" data-function-index="${funcIndex}" data-mfo-index="${mfoIndex}" ...>
    ${indicatorCount} indicator${indicatorCount === 1 ? '' : 's'}
</button>`
```

To:

```js
// AFTER
const siUrl = mfo.id ? (mfoSuccessIndicatorUrls[mfo.id] || null) : null;
const siCell = siUrl
    ? `<a href="${siUrl}" class="inline-flex items-center rounded-full border border-slate-700 bg-slate-900 px-3 py-1 text-xs font-semibold text-slate-300 transition hover:bg-slate-700/40 hover:border-slate-400/60 focus:outline-none focus:ring-2 focus:ring-cyan-500/60">
           ${indicatorCount} indicator${indicatorCount === 1 ? '' : 's'}
       </a>`
    : `<span class="inline-flex items-center rounded-full border border-slate-700 bg-slate-900/40 px-3 py-1 text-xs font-semibold text-slate-500 cursor-not-allowed" title="Save the UWP first to manage indicators">
           ${indicatorCount} indicator${indicatorCount === 1 ? '' : 's'}
       </span>`;
```

The `data-action="view-indicators"` click handler in the `functionsWrapper` event listener is removed.

#### 4.3 Modal Removal

The following blocks are removed from `uwp.blade.php`:
- The `#uwp-indicators-modal` div (lines ~168–482)
- The `#uwp-standards-modal-legacy` div
- The `#uwp-assigned-employees-modal-legacy` div
- All JS functions: `openUwpIndicatorsModal`, `closeUwpIndicatorsModal`, and the `view-indicators` click handler in the `functionsWrapper` event listener
- All workspace state variables (`activeFunctionIndex`, `activeMfoIndex`, `activeIndicators`, `activeIndicatorIndex`, `activeWorkspaceTab`, `activeEditingIndicatorIndex`, `standardsEditTarget`) and all workspace render functions (`renderEditorWorkspaceDetail`, `renderIndicatorWorkspaceNav`, `renderWorkspaceOverview`, `renderTargetsPanel`, `renderStandardsPanel`, `renderAssigned`, `setEditorWorkspaceTab`, etc.)

---

## Data Models

### Existing Models (unchanged)

| Model | Table | Key Fields |
|---|---|---|
| `UnitWorkPlan` | `unit_work_plans` | `id`, `status`, `locked_at`, `created_by`, `office_id` |
| `UwpFunction` | `uwp_functions` | `id`, `unit_work_plan_id`, `function_type`, `weight_percent` |
| `UwpMfo` | `uwp_mfos` | `id`, `uwp_function_id`, `title`, `target_quantity`, `target_timeline` |
| `UwpSuccessIndicator` | `uwp_success_indicators` | `id`, `uwp_mfo_id`, `indicator_text`, `target_quantity`, `target_timeline`, `sort_order` |
| `UwpQetStandard` | `uwp_qet_standards` | `id`, `uwp_success_indicator_id`, `dimension`, `rating`, `standard_text` |
| `UwpIndicatorAssignment` | `uwp_indicator_assignments` | `id`, `uwp_success_indicator_id`, `employee_id`, `assigned_by`, `assigned_at` |

### `$initialIndicators` Array Shape

```json
[
  {
    "id": 42,
    "text": "All e-bank transactions scanned and encoded daily",
    "targetQuantity": 100,
    "targetTimeline": "Monthly",
    "sort_order": 1,
    "standards": [
      { "rating": 5, "dimension": "q", "text": "No errors; accurate encoding" },
      { "rating": 5, "dimension": "e", "text": "100% processed" }
    ],
    "assignees": [7, 12, 15]
  }
]
```

### `indicators_payload` POST Field Shape

Same structure as `$initialIndicators`, serialized as JSON. The `id` field is optional (present for existing indicators, absent for new ones — though the save action performs a full replace, so IDs are not used for upsert logic).

---

## Interfaces

### GET `/supervisor/uwp/{uwpId}/mfo/{mfoId}/success-indicators`

**Request:** Authenticated GET, no body.

**Response (success):** HTTP 200, renders `supervisor.uwp-success-indicators` view.

**Response (errors):**
- HTTP 404 — UWP or MFO not found, or MFO does not belong to UWP.
- HTTP 403 — Authenticated user is not the UWP creator.
- HTTP 302 → login — Unauthenticated request (auth middleware).

### POST `/supervisor/uwp/{uwpId}/mfo/{mfoId}/success-indicators`

**Request body (form):**

| Field | Type | Required | Description |
|---|---|---|---|
| `_token` | string | yes | CSRF token |
| `indicators_payload` | string (JSON) | yes | JSON array of indicator objects |

**Response (success):** HTTP 302 → `supervisor.uwp?uwp_id={uwpId}` with `success` flash.

**Response (errors):**
- HTTP 302 → back with `error` flash — canEdit is false (HTTP 422 for JSON clients).
- HTTP 302 → back with `error` flash + input — DB transaction failure.
- HTTP 404 — UWP or MFO not found.
- HTTP 403 — Not the UWP creator.

---

## Error Handling

| Scenario | Handling |
|---|---|
| UWP not found | `abort(404)` in both controller methods |
| MFO not found or belongs to different UWP | `abort(404)` in both controller methods |
| User is not UWP creator | `abort(403)` in both controller methods |
| UWP is not editable (`canEdit = false`) | Redirect back with error flash in `saveSuccessIndicators` |
| Invalid JSON in `indicators_payload` | Redirect back with error flash |
| DB transaction failure | Caught by `\Throwable`, logged, redirect back with error flash + input |
| Unauthenticated request | Handled by `auth` middleware, redirect to login |

---

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system — essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property Reflection

Before writing properties, redundant criteria are consolidated:

- Requirements 2.6 and 6.1 both state "non-creator gets 403" — merged into **Property 3**.
- Requirements 3.2 and 6.1 both cover POST 403 for non-creators — covered by **Property 3**.
- Requirements 3.5, 3.6, and 3.7 all describe the same round-trip persistence transaction — merged into **Property 5**.
- Requirements 2.4 and 2.5 and 6.3 all describe 404 conditions — merged into **Property 4**.
- Requirements 1.2 and 1.3 both describe what the page displays for any MFO — merged into **Property 1**.

### Property 1: Page renders correct MFO data

*For any* valid `UnitWorkPlan` and any `UwpMfo` belonging to it, when the authenticated creator requests the Success Indicator Page, the response SHALL return HTTP 200 and the rendered HTML SHALL contain the MFO's title text.

**Validates: Requirements 1.2, 1.3**

### Property 2: Initial indicators are fully loaded

*For any* `UwpMfo` with any number of `UwpSuccessIndicator` records, each having any number of `UwpQetStandard` and `UwpIndicatorAssignment` records, the `$initialIndicators` array passed to the view SHALL contain one entry per indicator, and each entry SHALL include the indicator's `id`, `text`, `targetQuantity`, `targetTimeline`, `sort_order`, `standards` array, and `assignees` array.

**Validates: Requirements 2.3, 2.8**

### Property 3: Non-creator is denied access

*For any* `UnitWorkPlan` created by user A, when a different authenticated supervisor (user B) requests either the GET or POST Success Indicator Route for that UWP, the controller SHALL respond with HTTP 403.

**Validates: Requirements 2.6, 3.2, 6.1**

### Property 4: Invalid UWP/MFO combinations return 404

*For any* request where (a) the `$uwpId` does not correspond to an existing `UnitWorkPlan`, or (b) the `$mfoId` does not correspond to an existing `UwpMfo`, or (c) the `UwpMfo`'s parent `UwpFunction` belongs to a different `UnitWorkPlan` than `$uwpId`, the controller SHALL respond with HTTP 404.

**Validates: Requirements 2.4, 2.5, 6.3**

### Property 5: Save is a full round-trip for indicators, standards, and assignments

*For any* `UwpMfo` in an editable UWP, and *for any* array of indicator objects (each with any combination of `standards` and `assignees`), posting that array as `indicators_payload` to the save route SHALL result in the database containing exactly those indicators for the MFO — with exactly those QET standards and employee assignments — and no others.

**Validates: Requirements 3.3, 3.5, 3.6, 3.7**

### Property 6: Non-editable UWP rejects save

*For any* `UnitWorkPlan` where `canEdit` is `false` (status is not draft/returned, or `locked_at` is set), posting to the save route SHALL NOT modify any `UwpSuccessIndicator`, `UwpQetStandard`, or `UwpIndicatorAssignment` records, and SHALL return an error response.

**Validates: Requirements 3.3**

### Property 7: MFO row anchor href matches expected route

*For any* `UwpMfo` with a persisted database `id`, the HTML rendered by `renderFunctions()` in `uwp.blade.php` for that MFO's "Success Indicator" cell SHALL be an anchor tag (`<a>`) whose `href` attribute equals `route('supervisor.uwp.success-indicators', ['uwpId' => $uwp->id, 'mfoId' => $mfo->id])`.

**Validates: Requirements 5.1, 5.2**
