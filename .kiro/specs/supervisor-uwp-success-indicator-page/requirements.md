# Requirements Document

## Introduction

This feature transforms the Success Indicator modal in the Supervisor UWP editor page (`supervisor.uwp`) into a dedicated, full-page workspace. The new page is scoped to a specific MFO within a UWP and mirrors the existing modal's four-tab interface (Overview, Targets, Standards, Assignees) with full editing support when the UWP is in Draft or Returned status. The MFO row "Success Indicator" button in the UWP editor is updated to navigate to the new page instead of opening the modal. The modal HTML and its associated JavaScript are removed from `uwp.blade.php` after the new page is in place.

## Glossary

- **Success Indicator Page**: The new dedicated Blade view at `resources/views/supervisor/uwp-success-indicators.blade.php` that replaces the modal.
- **UWP**: Unit Work Plan — the top-level planning document managed by a Supervisor.
- **UwpMfo**: Major Final Output — a child record of `UwpFunction`, identified by `id` in the `uwp_mfos` table.
- **UwpSuccessIndicator**: A success indicator record belonging to a `UwpMfo`, stored in `uwp_success_indicators`.
- **UwpQetStandard**: A Quality/Efficiency/Timeliness standard record belonging to a `UwpSuccessIndicator`, stored in `uwp_qet_standards`.
- **UwpIndicatorAssignment**: An employee assignment record linking a `UwpSuccessIndicator` to a `User` with role `employee`.
- **canEdit**: The boolean condition `($isDraft || $isReturned) && !$isLocked`, where `$isDraft` and `$isReturned` are derived from the UWP's `status` field and `$isLocked` is derived from the presence of `locked_at`.
- **Supervisor Layout**: The shared Blade layout `layouts.supervisor` used by all supervisor views.
- **UnitWorkPlanController**: `App\Http\Controllers\Supervisor\UnitWorkPlanController` — the controller that handles all UWP-related HTTP actions for supervisors.
- **Success Indicator Route (GET)**: Named route `supervisor.uwp.success-indicators` at `/supervisor/uwp/{uwpId}/mfo/{mfoId}/success-indicators`.
- **Success Indicator Route (POST)**: Named route `supervisor.uwp.success-indicators.save` at `/supervisor/uwp/{uwpId}/mfo/{mfoId}/success-indicators`.

## Requirements

### Requirement 1: New Dedicated Success Indicator Page

**User Story:** As a Supervisor, I want a dedicated page for managing success indicators of a specific MFO, so that I have a focused workspace without the constraints of a modal overlay.

#### Acceptance Criteria

1. THE Success Indicator Page SHALL extend `layouts.supervisor` and render within the standard supervisor shell.
2. WHEN a Supervisor navigates to `/supervisor/uwp/{uwpId}/mfo/{mfoId}/success-indicators`, THE Success Indicator Page SHALL display the success indicator workspace for the specified MFO.
3. THE Success Indicator Page SHALL display a page title of "Success Indicator Workspace" and a subtitle showing the MFO title.
4. THE Success Indicator Page SHALL display a "← Back to UWP Editor" link that navigates to `route('supervisor.uwp', ['uwp_id' => $uwp->id])`.
5. THE Success Indicator Page SHALL display the same four tabs as the former modal: Overview, Targets, Standards, and Assignees.
6. WHILE `canEdit` is `true`, THE Success Indicator Page SHALL render all four tabs in editable mode, allowing the Supervisor to add, edit, and remove indicators, targets, standards, and assignees.
7. WHILE `canEdit` is `false`, THE Success Indicator Page SHALL render all four tabs in read-only mode, hiding all add/edit/remove controls.

### Requirement 2: GET Route and Controller Action

**User Story:** As a Supervisor, I want the success indicator page to load with all existing data for the MFO, so that I can review and edit without re-entering information.

#### Acceptance Criteria

1. THE UnitWorkPlanController SHALL expose a `showSuccessIndicators(Request $request, int $uwpId, int $mfoId)` method that handles the GET request for the Success Indicator Route.
2. WHEN the GET route is requested, THE UnitWorkPlanController SHALL load the `UnitWorkPlan` record identified by `$uwpId` with its `returnedByUser` relationship.
3. WHEN the GET route is requested, THE UnitWorkPlanController SHALL load the `UwpMfo` record identified by `$mfoId` with its `successIndicators`, `successIndicators.qetStandards`, and `successIndicators.assignments.employee` relationships.
4. IF the `UnitWorkPlan` record does not exist, THEN THE UnitWorkPlanController SHALL abort with HTTP 404.
5. IF the `UwpMfo` record does not exist or its `uwp_function.unit_work_plan_id` does not match `$uwpId`, THEN THE UnitWorkPlanController SHALL abort with HTTP 404.
6. IF the authenticated user's `id` does not match the UWP's `created_by` field, THEN THE UnitWorkPlanController SHALL abort with HTTP 403.
7. WHEN the GET route is requested, THE UnitWorkPlanController SHALL pass `$uwp`, `$mfo`, `$status`, `$locked_at`, `$canEdit`, `$officeEmployees`, and `$initialIndicators` to the Success Indicator Page view.
8. THE UnitWorkPlanController SHALL derive `$initialIndicators` as a JSON-serializable array mapping each `UwpSuccessIndicator` to its `id`, `indicator_text`, `target_quantity`, `target_timeline`, `sort_order`, `qetStandards`, and `assignments`.

### Requirement 3: POST Route and Save Action

**User Story:** As a Supervisor, I want to save changes to success indicators and return to the UWP editor, so that my edits are persisted without losing my place in the workflow.

#### Acceptance Criteria

1. THE UnitWorkPlanController SHALL expose a `saveSuccessIndicators(Request $request, int $uwpId, int $mfoId)` method that handles the POST request for the Success Indicator Route.
2. WHEN the POST route is requested, THE UnitWorkPlanController SHALL validate that the authenticated user is the creator of the UWP identified by `$uwpId`.
3. WHEN the POST route is requested, THE UnitWorkPlanController SHALL validate that `canEdit` is `true` for the UWP; IF `canEdit` is `false`, THEN THE UnitWorkPlanController SHALL return an error response with HTTP 422.
4. WHEN the POST route is requested, THE UnitWorkPlanController SHALL accept an `indicators_payload` field containing a JSON-encoded array of indicator objects.
5. WHEN the POST route is requested, THE UnitWorkPlanController SHALL persist all indicator records (create, update, delete) for the specified `UwpMfo` within a single database transaction.
6. WHEN the POST route is requested, THE UnitWorkPlanController SHALL persist all `UwpQetStandard` records associated with each indicator within the same transaction.
7. WHEN the POST route is requested, THE UnitWorkPlanController SHALL persist all `UwpIndicatorAssignment` records associated with each indicator within the same transaction.
8. WHEN the save operation succeeds, THE UnitWorkPlanController SHALL redirect to `route('supervisor.uwp', ['uwp_id' => $uwpId])` with a success flash message.
9. IF the save operation fails due to a database error, THEN THE UnitWorkPlanController SHALL redirect back with an error flash message and preserve the submitted input.

### Requirement 4: Route Registration

**User Story:** As a developer, I want the new routes registered under the supervisor middleware group, so that they are protected by authentication and role checks consistent with all other supervisor routes.

#### Acceptance Criteria

1. THE `routes/web.php` file SHALL register a GET route at `/uwp/{uwpId}/mfo/{mfoId}/success-indicators` named `supervisor.uwp.success-indicators` within the `supervisor` prefix and `auth` middleware group.
2. THE `routes/web.php` file SHALL register a POST route at `/uwp/{uwpId}/mfo/{mfoId}/success-indicators` named `supervisor.uwp.success-indicators.save` within the `supervisor` prefix and `auth` middleware group.
3. WHEN a request is made to either route, THE routing layer SHALL resolve both routes to the `UnitWorkPlanController`.

### Requirement 5: UWP Editor Button Update

**User Story:** As a Supervisor, I want the "Success Indicator" button on each MFO row in the UWP editor to navigate to the new page, so that I no longer interact with the modal overlay.

#### Acceptance Criteria

1. THE `uwp.blade.php` view SHALL replace the `onclick="openUwpIndicatorsModal(funcIdx, mfoIdx)"` call on the MFO row "Success Indicator" button with a navigation link to `route('supervisor.uwp.success-indicators', ['uwpId' => $uwp->id, 'mfoId' => $mfo->id])`.
2. WHILE `canEdit` is `false`, THE `uwp.blade.php` view SHALL render the "Success Indicator" button as a read-only navigation link to the same route.
3. THE `uwp.blade.php` view SHALL remove the `#uwp-indicators-modal` HTML block and all associated modal JavaScript (`openUwpIndicatorsModal`, `closeUwpIndicatorsModal`, and related handlers) after the new page is confirmed functional.

### Requirement 6: Access Control

**User Story:** As a system, I want to ensure only the UWP creator can access the success indicator page for their UWP, so that data integrity and privacy are maintained.

#### Acceptance Criteria

1. WHEN an authenticated user who is not the creator of the UWP requests the Success Indicator Route, THE UnitWorkPlanController SHALL abort with HTTP 403.
2. WHEN an unauthenticated user requests the Success Indicator Route, THE routing layer SHALL redirect the user to the login page via the `auth` middleware.
3. IF the `UwpMfo` record's parent `UwpFunction` does not belong to the UWP identified by `$uwpId`, THEN THE UnitWorkPlanController SHALL abort with HTTP 404.
