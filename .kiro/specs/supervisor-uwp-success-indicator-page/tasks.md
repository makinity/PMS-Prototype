# Implementation Plan: supervisor-uwp-success-indicator-page

## Overview

Transform the Success Indicator modal in `supervisor/uwp.blade.php` into a dedicated full-page workspace scoped to a single `UwpMfo`. The implementation proceeds in four discrete steps: register routes, add controller methods, create the new Blade view, then update `uwp.blade.php` to replace the modal button with an anchor and strip all modal HTML/JS.

## Tasks

- [x] 1. Register the two new Success Indicator routes in `routes/web.php`
  - Inside the existing `supervisor` prefix + `auth` middleware group, add a GET and POST route at `/uwp/{uwpId}/mfo/{mfoId}/success-indicators` pointing to `UnitWorkPlanController@showSuccessIndicators` and `UnitWorkPlanController@saveSuccessIndicators`
  - Name them `supervisor.uwp.success-indicators` and `supervisor.uwp.success-indicators.save`
  - Place them alongside the existing Stage I UWP routes block
  - _Requirements: 4.1, 4.2, 4.3_

- [x] 2. Implement `showSuccessIndicators` and `saveSuccessIndicators` in `UnitWorkPlanController`
  - [x] 2.1 Implement `showSuccessIndicators(Request $request, int $uwpId, int $mfoId)`
    - Load `UnitWorkPlan` with `returnedByUser`; abort 404 if not found
    - Abort 403 if `$uwp->created_by !== auth user id`
    - Load `UwpMfo` with `successIndicators` (ordered by `sort_order`), `successIndicators.qetStandards`, `successIndicators.assignments.employee`, and `uwpFunction`; abort 404 if not found or if `uwpFunction.unit_work_plan_id !== $uwpId`
    - Derive `$canEdit`, `$status`, `$locked_at` from UWP status and `locked_at`
    - Load `$officeEmployees` (role=employee, same office_id as UWP, is_active=true)
    - Build `$initialIndicators` as a JSON-serializable array per the design's array shape (id, text, targetQuantity, targetTimeline, sort_order, standards, assignees)
    - Return `view('supervisor.uwp-success-indicators', compact(...))`
    - Add `use App\Models\UwpMfo;` import if not already present
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 2.8, 6.1, 6.3_

  - [ ]* 2.2 Write property test for `showSuccessIndicators` — Property 1: Page renders correct MFO data
    - **Property 1: Page renders correct MFO data**
    - For any valid UWP and any UwpMfo belonging to it, when the authenticated creator requests the page, the response returns HTTP 200 and the rendered HTML contains the MFO's title text
    - **Validates: Requirements 1.2, 1.3**

  - [ ]* 2.3 Write property test for `showSuccessIndicators` — Property 2: Initial indicators are fully loaded
    - **Property 2: Initial indicators are fully loaded**
    - For any UwpMfo with any number of UwpSuccessIndicator records (each with any number of qetStandards and assignments), `$initialIndicators` contains one entry per indicator with all required fields
    - **Validates: Requirements 2.3, 2.8**

  - [ ]* 2.4 Write property test for access control — Property 3: Non-creator is denied access
    - **Property 3: Non-creator is denied access**
    - For any UWP created by user A, when a different authenticated supervisor (user B) requests either the GET or POST route, the controller responds with HTTP 403
    - **Validates: Requirements 2.6, 3.2, 6.1**

  - [ ]* 2.5 Write property test for access control — Property 4: Invalid UWP/MFO combinations return 404
    - **Property 4: Invalid UWP/MFO combinations return 404**
    - For any request where (a) `$uwpId` does not exist, (b) `$mfoId` does not exist, or (c) the MFO's parent UwpFunction belongs to a different UWP, the controller responds with HTTP 404
    - **Validates: Requirements 2.4, 2.5, 6.3**

  - [x] 2.6 Implement `saveSuccessIndicators(Request $request, int $uwpId, int $mfoId)`
    - Load UWP; abort 404 if not found, abort 403 if not creator
    - Derive `$canEdit`; return redirect back with error flash + input if `canEdit` is false
    - Load MFO with `uwpFunction`; abort 404 if not found or belongs to different UWP
    - Decode `indicators_payload` JSON; return redirect back with error flash if invalid
    - Wrap all DB writes in `DB::transaction`: delete existing indicators/standards/assignments for the MFO, then create new `UwpSuccessIndicator`, `UwpQetStandard`, and `UwpIndicatorAssignment` records from the payload
    - On success redirect to `route('supervisor.uwp', ['uwp_id' => $uwpId])` with success flash
    - Catch `\Throwable`, log error, redirect back with error flash + input
    - Add `use App\Models\UwpSuccessIndicator;` import if not already present
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 3.8, 3.9_

  - [ ]* 2.7 Write property test for `saveSuccessIndicators` — Property 5: Save is a full round-trip
    - **Property 5: Save is a full round-trip for indicators, standards, and assignments**
    - For any UwpMfo in an editable UWP and any array of indicator objects, posting that array results in the DB containing exactly those indicators, standards, and assignments — and no others
    - **Validates: Requirements 3.3, 3.5, 3.6, 3.7**

  - [ ]* 2.8 Write property test for `saveSuccessIndicators` — Property 6: Non-editable UWP rejects save
    - **Property 6: Non-editable UWP rejects save**
    - For any UWP where `canEdit` is false (submitted/locked), posting to the save route does NOT modify any indicator/standard/assignment records and returns an error response
    - **Validates: Requirements 3.3**

- [x] 3. Checkpoint — Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 4. Create the new Blade view `resources/views/supervisor/uwp-success-indicators.blade.php`
  - [ ] 4.1 Build the page shell, header, and back link
    - `@extends('layouts.supervisor')`, `@section('main-content')`
    - Back link: `← Back to UWP Editor` pointing to `route('supervisor.uwp', ['uwp_id' => $uwp->id])`
    - Page title "Success Indicator Workspace" and subtitle showing `$mfo->title`
    - Status badge (Draft / Returned / Submitted) and canEdit/locked indicator, matching the styling in `uwp.blade.php`
    - _Requirements: 1.1, 1.2, 1.3, 1.4_

  - [~] 4.2 Build the four-tab workspace HTML (Overview, Targets, Standards, Assignees)
    - Port the tab bar (`data-editor-workspace-tab`) and four panel divs (`data-editor-workspace-panel`) from the modal in `uwp.blade.php` verbatim, adapting IDs to avoid conflicts (prefix with `si-`)
    - Left sidebar: indicator nav list (`#si-workspace-indicator-nav`), indicator count badge, and "Add Indicator" button (shown only when `$canEdit`)
    - Overview panel: summary stats cards (function type, weight, indicator count, QET cell count, assignee count) and linked indicators list with "Add Indicator" button
    - Targets panel: quantity input (`#si-targets-quantity`) and timeline input (`#si-targets-timeline`); read-only note when `!$canEdit`
    - Standards panel: QET standards table (`#si-standards-list`), add-standard form (rating select, dimension select, textarea, "Save to Table" and "Reset" buttons) shown only when `$canEdit`; read-only note otherwise
    - Assignees panel: employee assignment table (`#si-assigned-list`) with columns for name, office, status, indicator; Action column with assign/unassign button shown only when `$canEdit`
    - _Requirements: 1.5, 1.6, 1.7_

  - [~] 4.3 Add the Save & Return form and wire the submit payload
    - `<form method="POST" action="{{ route('supervisor.uwp.success-indicators.save', ['uwpId' => $uwp->id, 'mfoId' => $mfo->id]) }}">`
    - `@csrf`, hidden `<input type="hidden" name="indicators_payload" id="si-indicators-payload">`
    - "Save & Return to UWP Editor" submit button (disabled / hidden when `!$canEdit`)
    - On form `submit` event, serialize the JS workspace state into `#si-indicators-payload` before the form posts
    - _Requirements: 3.4_

  - [~] 4.4 Port and scope the workspace JavaScript to this page
    - In a `@push('scripts')` block, declare `const initialIndicators = @json($initialIndicators);` and `const assignedData = @json($assignedData);` (build `$assignedData` in the controller or view from `$officeEmployees`)
    - Port all workspace state variables and render functions from the modal JS in `uwp.blade.php` (renamed with `si` prefix to avoid global conflicts): `siState`, `renderSiWorkspace`, `renderSiIndicatorNav`, `renderSiOverview`, `renderSiTargetsPanel`, `renderSiStandardsPanel`, `renderSiAssigned`, `setSiWorkspaceTab`, `addSiIndicator`, `removeSiIndicator`, `selectSiIndicator`, `addSiStandard`, `removeSiStandard`, `toggleSiAssignee`
    - Initialize state from `initialIndicators` on `DOMContentLoaded`
    - Respect `canEdit = {{ $canEdit ? 'true' : 'false' }}` — all mutating actions must be gated
    - _Requirements: 1.5, 1.6, 1.7_

- [~] 5. Checkpoint — Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 6. Update `uwp.blade.php` to replace the modal button and remove modal code
  - [~] 6.1 Pre-compute `$mfoSuccessIndicatorUrls` PHP map and expose it as a JS variable
    - In the `@php` block at the top of `uwp.blade.php`, build `$mfoSuccessIndicatorUrls` as an array keyed by MFO DB id mapping to the route URL (only when `$uwp` and `$initialFunctions` are set)
    - Emit `<script>const mfoSuccessIndicatorUrls = @json($mfoSuccessIndicatorUrls); const uwpSuccessIndicatorsBaseUrl = '...';</script>` before the main script block
    - _Requirements: 5.1, 5.2_

  - [ ]* 6.2 Write property test for the MFO row anchor — Property 7: MFO row anchor href matches expected route
    - **Property 7: MFO row anchor href matches expected route**
    - For any UwpMfo with a persisted DB id, the HTML rendered by `renderFunctions()` for that MFO's "Success Indicator" cell is an `<a>` tag whose `href` equals `route('supervisor.uwp.success-indicators', ['uwpId' => $uwp->id, 'mfoId' => $mfo->id])`
    - **Validates: Requirements 5.1, 5.2**

  - [~] 6.3 Replace the `data-action="view-indicators"` button with an anchor tag in `renderFunctions()`
    - In the `renderFunctions()` JS function, locate the "Success Indicator" button cell
    - Replace it with the anchor/span pattern from the design: use `mfoSuccessIndicatorUrls[mfo.id]` for the href when the MFO has a DB id; fall back to a disabled `<span>` with tooltip "Save the UWP first to manage indicators" for unsaved MFOs
    - Remove the `data-action="view-indicators"` click handler from the `functionsWrapper` event listener
    - _Requirements: 5.1, 5.2, 5.3_

  - [~] 6.4 Remove the `#uwp-indicators-modal` HTML block and all associated modal JS
    - Delete the `#uwp-indicators-modal` div and its entire contents from `uwp.blade.php`
    - Delete the `#uwp-standards-modal-legacy` div
    - Delete the `#uwp-assigned-employees-modal-legacy` div (if present)
    - Remove all modal JS: `openUwpIndicatorsModal`, `closeUwpIndicatorsModal`, and all workspace state variables and render functions (`activeFunctionIndex`, `activeMfoIndex`, `activeIndicators`, `activeIndicatorIndex`, `activeWorkspaceTab`, `activeEditingIndicatorIndex`, `standardsEditTarget`, `renderEditorWorkspaceDetail`, `renderIndicatorWorkspaceNav`, `renderWorkspaceOverview`, `renderTargetsPanel`, `renderStandardsPanel`, `renderAssigned`, `setEditorWorkspaceTab`, etc.)
    - _Requirements: 5.3_

- [~] 7. Final checkpoint — Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- The design document contains full PHP and JS code samples for all controller methods and the JS anchor pattern — use them as the authoritative implementation reference
- Property tests (tasks 2.2–2.8, 6.2) should use Laravel's built-in `TestCase` with factories or database seeders; PestPHP or PHPUnit are both acceptable
- The `UwpMfo` model must have a `uwpFunction` relationship returning `UwpFunction` — verify this exists before implementing task 2.1
- `$assignedData` for the new view should be built the same way as in `uwp.blade.php`: map `$officeEmployees` to `['id', 'name', 'office_id', 'unit']`
- Checkpoints ensure incremental validation after each major phase

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1"] },
    { "id": 1, "tasks": ["2.1"] },
    { "id": 2, "tasks": ["2.2", "2.3", "2.4", "2.5", "2.6"] },
    { "id": 3, "tasks": ["2.7", "2.8", "4.1"] },
    { "id": 4, "tasks": ["4.2", "4.3"] },
    { "id": 5, "tasks": ["4.4"] },
    { "id": 6, "tasks": ["6.1"] },
    { "id": 7, "tasks": ["6.2", "6.3"] },
    { "id": 8, "tasks": ["6.4"] }
  ]
}
```
