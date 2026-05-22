<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AuditLogsController;
use App\Http\Controllers\Admin\DatabaseController;
use App\Http\Controllers\Admin\HrisIntegrationController;
use App\Http\Controllers\Admin\OfficeController;
use App\Http\Controllers\Admin\PerformancePeriodsController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Auth\ActivationController;
use App\Http\Controllers\Supervisor\UnitWorkPlanController;
use App\Http\Controllers\DeptHead\AccomplishmentReviewController;
use App\Http\Controllers\DeptHead\DashboardController as DeptHeadDashboardController;
use App\Http\Controllers\DeptHead\OpcrController as DeptHeadOpcrController;
use App\Http\Controllers\DeptHead\QarController;
use App\Http\Controllers\StageOne\Forms\IpcrExportController;
use App\Http\Controllers\StageOne\Forms\IpcrExcelExportController;
use App\Http\Controllers\StageOne\Forms\OpcrExportController;
use App\Http\Controllers\StageOne\Forms\OpcrExcelExportController;
use App\Http\Controllers\StageOne\Forms\UwpExportController;
use App\Http\Controllers\StageOne\Forms\UwpExcelExportController;
use App\Http\Controllers\DeptHead\UnitWorkPlanController as DeptHeadUnitWorkPlanController;
use App\Http\Controllers\Pmt\UnitWorkPlanController as PmtUnitWorkPlanController;
use App\Http\Controllers\StageThree\Forms\IpcrExportController as StageThreeFormsIpcrExportController;
use App\Http\Controllers\StageTwo\Commitement\OrsController;
use App\Http\Controllers\StageTwo\Commitement\MyTasksController;
use App\Http\Controllers\StageTwo\Forms\IpcrExportController as FormsIpcrExportController;
use App\Http\Controllers\StageTwo\Forms\OrsExportController;
use App\Http\Controllers\StageTwo\Forms\QarExportController;
use App\Http\Controllers\StageTwo\Forms\SmporExcelExportController;
use App\Http\Controllers\StageTwo\Monitoring\EmployeeAccomplishmentController;
use App\Http\Controllers\StageTwo\Monitoring\OrsMonitoringController;
use App\Http\Controllers\StageTwo\Monitoring\TeamTasksController;
use App\Http\Controllers\StageTwo\Mpor\MporController;
use App\Http\Controllers\StageTwo\Forms\MporExcelExportController;
use App\Http\Controllers\StageTwo\Planning\MporSubmissionController;
use App\Http\Controllers\StageTwo\Planning\PmtQarApprovalController;
use App\Http\Controllers\StageTwo\Planning\SupervisorMporEndorseController;
use App\Http\Controllers\Pmt\QarController as PmtQarController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Employee\IpcrTargetController;
use App\Http\Controllers\Employee\SmporIpcrAccomplishmentController;
use App\Http\Controllers\Pmt\AccomplishmentReviewController as PmtAccomplishmentReviewController;
use App\Http\Controllers\Pmt\DashboardController as PmtDashboardController;
use App\Http\Controllers\Pmt\OpcrController as PmtOpcrController;
use App\Http\Controllers\Supervisor\AccomplishmentController as SupervisorAccomplishmentController;
use App\Http\Controllers\Supervisor\DashboardController as SupervisorDashboardController;
use App\Http\Controllers\Supervisor\MporController as SupervisorMporController;
use App\Http\Controllers\Supervisor\OpcrController;

/*
|--------------------------------------------------------------------------
| Public / Auth
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('main-page'))->name('main-page');

Route::get('/login', fn () => redirect('/'))->name('login');

Route::get('/whoami', function (Request $request) {
    $user = $request->user();

    return response()->json([
        'id' => $user?->id,
        'name' => $user?->name,
        'email' => $user?->email,
        'role' => $user?->role,
    ]);
})->middleware('auth');

Route::get('/logout', function (Request $request) {
    $actor = $request->user();
    if ($actor) {
        $request->attributes->set('audit_force', true);
        $request->attributes->set('audit_actor_snapshot', [
            'id' => $actor->id,
            'name' => $actor->name,
            'role' => $actor->role,
        ]);
    }

    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
})->name('logout');

Route::get('/send/id', [ActivationController::class, 'index']);
Route::post('/activate/verify', [ActivationController::class, 'verify']);
Route::post('/activate/complete', [ActivationController::class, 'complete']);

/*
|--------------------------------------------------------------------------
| Dashboard Role Router
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    $user = auth()->user();
    if (! $user) {
        return redirect()->route('login');
    }

    return match ($user->role) {
        'employee'   => redirect()->route('employee.dashboard'),
        'supervisor' => redirect()->route('supervisor.dashboard'),
        'dept-head'  => redirect()->route('dept-head.dashboard'),
        'pmt'        => redirect()->route('pmt.dashboard'),
        'admin'      => redirect()->route('admin.dashboard'),
        default      => abort(403, 'Unauthorized role'),
    };
})->middleware('auth');

/*
|--------------------------------------------------------------------------
| Employee Routes
|--------------------------------------------------------------------------
*/

Route::prefix('employee')->middleware('auth')->group(function () {
    // Views
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])
    ->name('employee.dashboard');
    Route::get('/task', [MyTasksController::class, 'index'])->name('employee.my-task');
    Route::get('/stage2/my-tasks/{orsEntry}/evidences', [MyTasksController::class, 'evidences'])
        ->name('stage2.my_tasks.evidences');
    Route::get('/stage2/my-tasks/{orsEntry}/evidences/{evidence}/view', [MyTasksController::class, 'viewEvidence'])
        ->name('stage2.my_tasks.evidences.view');
    Route::get('/stage2/my-tasks/{orsEntry}/evidences/{evidence}/download', [MyTasksController::class, 'downloadEvidence'])
        ->name('stage2.my_tasks.evidences.download');
    Route::get('/ors', [OrsController::class, 'index'])->name('employee.ors');
    Route::post('/ors', [OrsController::class, 'store'])->name('stage2.ors.store');
    Route::post('/ors/{orsEntry}/start', [OrsController::class, 'start'])->name('stage2.ors.start');
    Route::post('/ors/{orsEntry}/pause', [OrsController::class, 'pause'])->name('stage2.ors.pause');
    Route::post('/ors/{orsEntry}/resume', [OrsController::class, 'resume'])->name('stage2.ors.resume');
    Route::post('/ors/{orsEntry}/stop', [OrsController::class, 'stop'])->name('stage2.ors.stop');
    Route::post('/ors/{orsEntry}/submit', [OrsController::class, 'submit'])->name('stage2.ors.submit');
    Route::post('/ors/{orsEntry}/evidence', [OrsController::class, 'uploadEvidence'])->name('stage2.ors.evidence.store');
    Route::delete('/ors/{orsEntry}/evidence/{evidence}', [OrsController::class, 'destroyEvidence'])->name('stage2.ors.evidence.destroy');
    Route::get('/mpor', [MporController::class, 'index'])->name('employee.mpor');
    Route::post('/mpor/generate', [MporController::class, 'employeeGenerate'])->name('employee.mpor.generate');
    Route::post('/mpor/{mpor}/attach', [MporController::class, 'employeeAttachRatedOrs'])->name('employee.mpor.attach');
    Route::post('/mpor/{mpor}/detach', [MporController::class, 'employeeDetachRatedOrs'])->name('employee.mpor.detach');
    Route::get('/MPOR', fn () => redirect()->route('employee.mpor'));
    Route::get('/IPCR-Target', [IpcrTargetController::class, 'index'])
        ->name('employee.ipcr-target');
    Route::post('/stage1/ipcr/commit', [IpcrTargetController::class, 'commit'])
    ->name('stage1.ipcr.commit');
    Route::get('/Profile', fn () => view('employee.profile'))->name('employee.profile');

    // Stage II - Accomplishment Submission
    Route::get('/accomplishment-submission', [SmporIpcrAccomplishmentController::class, 'index'])->name('employee.accomplishment-submission');
    Route::get('/accomplishment-submission/smpor-preview', [SmporIpcrAccomplishmentController::class, 'showSmporPreview'])
        ->name('employee.accomplishment.smpor-preview');
    Route::get('/accomplishment-submission/ipcr-preview', [SmporIpcrAccomplishmentController::class, 'showIpcrPreview'])
        ->name('employee.accomplishment.ipcr-preview');
    Route::post('/accomplishment/submit', [SmporIpcrAccomplishmentController::class, 'submit'])
        ->name('employee.accomplishment.submit');

    // Stage II - MPOR Submit
    Route::post('/mpor/submit', [MporController::class, 'submitMpor'])
        ->name('employee.mpor.submit');

    // Exports - SMPOR and IPCR Excel
    Route::get('/smpor/export', [SmporIpcrAccomplishmentController::class, 'exportExcel'])
        ->name('smpor.export.excel');
    Route::get('ipcr/export-excel', [SmporIpcrAccomplishmentController::class, 'exportIpcrExcel'])
        ->name('ipcr.export.excel');

    // Exports - ORS PDF
    Route::get('/ors/export/pdf', [OrsExportController::class, 'preview'])
        ->name('employee.ors.export.pdf');
    Route::get('/ors/export/pdf/download', [OrsExportController::class, 'exportPdf'])
        ->name('employee.ors.export.pdf.download');

    // Exports - MPOR Excel
    Route::get('/mpor/export/excel', [MporExcelExportController::class, 'exportExcel'])
        ->name('employee.mpor.export.excel');
    Route::get('/mpor/preview/excel', [MporExcelExportController::class, 'previewExcel'])
        ->name('employee.mpor.preview.excel');

    // Exports - IPCR PDF (Stage II Forms)
    Route::get('/ipcr/export/pdf', [FormsIpcrExportController::class, 'exportPdf'])
        ->name('stage2.ipcr.export.pdf');

    // Exports - IPCR Excel (Stage I Forms) - kept as-is
    Route::get('/ipcr/export/excel', [IpcrExcelExportController::class, 'exportExcel'])
        ->name('stage1.ipcr.export.excel');
});

/*
|--------------------------------------------------------------------------
| Dept Head Routes
|--------------------------------------------------------------------------
*/

Route::prefix('dept-head')->middleware('auth')->group(function () {
    // Views
    Route::get('/dashboard', [DeptHeadDashboardController::class, 'index'])->name('dept-head.dashboard');
    Route::get('/profile', fn () => view('dept-head.profile'))->name('dept-head.profile');

    // Stage I - UWP Review
    Route::get('/uwp', [DeptHeadUnitWorkPlanController::class, 'index'])->name('dept-head.uwp');
    Route::get('/uwp/index', [DeptHeadUnitWorkPlanController::class, 'index'])->name('dept-head.uwp.index');
    Route::get('/uwp/{id}/show', [DeptHeadUnitWorkPlanController::class, 'show'])->name('dept-head.uwp.show');
    Route::post('/uwp/review', [DeptHeadUnitWorkPlanController::class, 'review'])->name('dept-head.uwp.review');
    Route::post('/uwp/return', [DeptHeadUnitWorkPlanController::class, 'returnUwp'])->name('dept-head.uwp.return');

    // Stage I - OPCR Review
    Route::get('/opcr', [DeptHeadOpcrController::class, 'index'])->name('dept-head.opcr');
    Route::get('/opcr/index', [DeptHeadOpcrController::class, 'index'])->name('dept-head.opcr.index');
    Route::get('/opcr/{opcr}/mfo/{mfoId}/success-indicators', [DeptHeadOpcrController::class, 'showSuccessIndicators'])->name('dept-head.opcr.success-indicators');
    Route::get('/opcr/accomplishment', [DeptHeadOpcrController::class, 'accomplishment'])->name('dept-head.opcr.accomplishment');
    Route::post('/opcr/{opcr}/endorse', [DeptHeadOpcrController::class, 'endorse'])->name('dept-head.opcr.endorse');
    Route::post('/opcr/{opcr}/submit-calibration', [DeptHeadOpcrController::class, 'submitCalibration'])->name('dept-head.opcr.submit-calibration');
    Route::post('/opcr/{opcr}/return', [DeptHeadOpcrController::class, 'returnOpcr'])->name('dept-head.opcr.return');
    Route::post('/opcr/review', [DeptHeadOpcrController::class, 'review'])->name('dept-head.opcr.review');

    // Stage II - QAR
    Route::get('/qar', [QarController::class, 'index'])->name('dept-head.qar');
    Route::get('/qar/mpor/{mpor}/show', [QarController::class, 'showMpor'])->name('dept-head.qar.mpor.show');
    Route::post('/qar/generate', [QarController::class, 'generate'])->name('dept-head.qar.generate');
    Route::post('/qar/endorse', [QarController::class, 'endorse'])->name('dept-head.qar.endorse');
    Route::post('/qar/reset', [QarController::class, 'reset'])->name('dept-head.qar.reset');

    // Exports
    Route::get('/qar/export/pdf', [QarExportController::class, 'exportPdf'])
        ->name('stage2.qar.export.pdf');

    Route::get('/ipcr/export/pdf', [IpcrExportController::class, 'exportPdf'])
        ->name('stage1.ipcr.export.pdf');

    Route::get('/accomplishment-review', [AccomplishmentReviewController::class, 'index'])->name('dept-head.acc-review');
    Route::post('/accomplishment-review/{id}', [AccomplishmentReviewController::class, 'endorseToPmt'])->name('dept-head.acc-review.endorse');
    
    // Stage III - Export
    Route::get('/opcr/export-stage3', [\App\Http\Controllers\StageThree\Forms\OpcrExcelExportController::class, 'exportExcel'])
        ->name('dept-head.opcr.export-stage3');
});

/*
|--------------------------------------------------------------------------
| Supervisor Routes
|--------------------------------------------------------------------------
*/

Route::prefix('supervisor')->middleware('auth')->group(function () {
    // Views
    Route::get('/dashboard', [SupervisorDashboardController::class, 'index'])->name('supervisor.dashboard');
    Route::get('/team-tasks', [TeamTasksController::class, 'index'])->name('supervisor.team-tasks');
    Route::get('/team-tasks/{orsEntry}/monitor', [OrsMonitoringController::class, 'show'])->name('supervisor.team-tasks.monitor');
    Route::post('/team-tasks/{orsEntry}/monitor', [OrsMonitoringController::class, 'store'])->name('supervisor.team-tasks.monitor.store');

    Route::get('/submissions', [SupervisorAccomplishmentController::class, 'index'])
        ->name('supervisor.employee-submissions');
    Route::post('/submissions/{id}/endorse', [SupervisorAccomplishmentController::class, 'endorseToDeptHead'])
        ->name('supervisor.submissions.endorse');


    Route::get('/ors-monitoring', [OrsMonitoringController::class, 'index'])->name('supervisor.ors-monitoring');
    Route::post('/ors-monitoring/{orsEntry}/monitor', [OrsMonitoringController::class, 'store'])->name('supervisor.ors-monitoring.store');
    Route::get('/profile', fn () => view('supervisor.profile'))->name('supervisor.profile');

    // Stage I - UWP
    Route::get('/uwp-page', [UnitWorkPlanController::class, 'uwpList'])->name('supervisor.uwp-page');
    Route::get('/stage-one/planning/uwp', [UnitWorkPlanController::class, 'uwpList'])->name('supervisor.uwp.index');
    Route::get('/stage-one/planning/uwp/{id}', [UnitWorkPlanController::class, 'showJson'])->name('supervisor.uwp.show');
    Route::get('/uwp/{id}/preview', [UnitWorkPlanController::class, 'previewJson'])->name('supervisor.uwp.preview');
    Route::get('/uwp/{id}/show', [UnitWorkPlanController::class, 'showPage'])->name('supervisor.uwp.show.page');
    Route::get('/uwp', [UnitWorkPlanController::class, 'index'])->name('supervisor.uwp');

    Route::get('/uwp/{uwp}/excel', [UwpExcelExportController::class, 'excelExport'])->name('uwp.excel.export');

    Route::post('/uwp/save-draft', [UnitWorkPlanController::class, 'saveDraftData'])
        ->name('supervisor.uwp.saveDraftData');
    Route::post('/uwp/{id}/save-draft', [UnitWorkPlanController::class, 'saveDraftDataById'])
        ->name('supervisor.uwp.saveDraftData.byId');
    Route::post('/uwp/submit', [UnitWorkPlanController::class, 'submitData'])
        ->name('supervisor.uwp.submitData');
    Route::post('/uwp/{id}/submit', [UnitWorkPlanController::class, 'submitDataForUwp'])
        ->name('supervisor.uwp.submitData.byId');
    Route::delete('/uwp/{id}', [UnitWorkPlanController::class, 'destroy'])
        ->name('supervisor.uwp.destroy');

    Route::post('/stage-one/planning/uwp/{id}/submit', [UnitWorkPlanController::class, 'submitForApproval'])
        ->name('supervisor.uwp.submit');
    Route::post('/stage1/uwp/{id}/submit-legacy', [UnitWorkPlanController::class, 'submitForApproval'])
        ->name('supervisor.uwp.submit.legacy');

    // Stage I - UWP Success Indicators (dedicated page)
    Route::get('/uwp/{uwpId}/mfo/{mfoId}/success-indicators', [UnitWorkPlanController::class, 'showSuccessIndicators'])
        ->name('supervisor.uwp.success-indicators');
    Route::post('/uwp/{uwpId}/mfo/{mfoId}/success-indicators', [UnitWorkPlanController::class, 'saveSuccessIndicators'])
        ->name('supervisor.uwp.success-indicators.save');

    Route::get('/supervisor/uwp/{id}/preview', [UnitWorkPlanController::class, 'previewJson'])
        ->name('supervisor.uwp.preview.legacy');

    // Stage I - OPCR
    // Stage II - MPOR Review
    Route::get('/mpor', [SupervisorMporController::class, 'index'])->name('supervisor.mpor');
    Route::get('/mpor/{mpor}', [SupervisorMporController::class, 'show'])->name('supervisor.mpor.show');
    Route::get('/mpor/{mpor}/preview-json', [SupervisorMporController::class, 'previewJson'])->name('supervisor.mpor.preview.json');
    Route::post('/mpor/{mpor}/approve', [SupervisorMporController::class, 'approve'])->name('supervisor.mpor.approve');
    Route::post('/mpor/{mpor}/return', [SupervisorMporController::class, 'return'])->name('supervisor.mpor.return');
    Route::post('/mpor/{mpor}/endorse', [SupervisorMporController::class, 'endorse'])->name('supervisor.mpor.endorse');

    // Exports - OPCR
    Route::get('/opcr/export/pdf', [OpcrExportController::class, 'exportPdf'])
        ->name('stage1.opcr.export.pdf');
    Route::get('/opcr/export/excel', [OpcrExcelExportController::class, 'exportExcel'])
        ->name('stage1.opcr.export.excel');
});

/*
|--------------------------------------------------------------------------
| PMT Routes
|--------------------------------------------------------------------------
*/

Route::prefix('pmt')->middleware('auth')->group(function () {
    // Views
    Route::get('/dashboard', [PmtDashboardController::class, 'index'])->name('pmt.dashboard');
    Route::get('/profile', fn () => view('pmt.profile'))->name('pmt.profile');

    // Stage I - OPCR only
    Route::get('/UWP', fn () => redirect()->route('pmt.opcr.review.index'))->name('pmt.uwp');
    Route::post('/uwp/review', fn () => redirect()->route('pmt.opcr.review.index'))->name('pmt.uwp.review');
    Route::post('/uwp/return', fn () => redirect()->route('pmt.opcr.review.index'))->name('pmt.uwp.return');

    Route::get('/opcr-review', [PmtOpcrController::class, 'index'])->name('pmt.opcr.review.index');
    Route::get('/opcr/{opcr}/review', [PmtOpcrController::class, 'show'])->name('pmt.opcr.review.show');
    Route::post('/opcr-review/action', [PmtOpcrController::class, 'review'])->name('pmt.opcr.review.action');
    Route::get('/opcr-review/{opcr}/export', [PmtOpcrController::class, 'export'])->name('pmt.opcr.review.export');

    // Stage II - QAR
    Route::get('/qar', [PmtQarController::class, 'index'])
        ->name('pmt.qar');
    Route::get('/qar/{qarHeader}/show', [PmtQarController::class, 'show'])
        ->name('pmt.qar.show');
    Route::get('/qar/{qarHeader}/preview-pdf', [PmtQarController::class, 'previewPdf'])
        ->name('pmt.qar.previewPdf');
    Route::post('/qar/{qarHeader}/approve', [PmtQarController::class, 'approve'])
        ->name('pmt.qar.approve');
    Route::post('/qar/{qarHeader}/return', [PmtQarController::class, 'return'])
        ->name('pmt.qar.return');

    // Exports - UWP
    Route::get('/uwp/export/pdf', [UwpExportController::class, 'exportPdf'])
        ->name('stage1.uwp.export.pdf');
    Route::get('/uwp/preview/pdf', [UwpExportController::class, 'preview'])
        ->name('stage1.uwp.preview.pdf');

    // Legacy excel route redirect kept as-is
    Route::get('/uwp/export/excel', function (Request $request) {
        $uwpId = (int) $request->query('uwp');
        if ($uwpId <= 0) {
            abort(403, 'UWP export now requires a PMT-approved UWP id.');
        }

        return redirect()->route('uwp.export', ['uwp' => $uwpId]);
    })->name('stage1.uwp.export.excel');

    // Exports - Stage III IPCR PDF
    Route::get('/ipcr/export/pdf', [StageThreeFormsIpcrExportController::class, 'exportPdf'])
        ->name('stage3.ipcr.export.pdf');

    Route::get('/acc-review', [PmtAccomplishmentReviewController::class, 'index'])->name('pmt.acc-review');
    Route::post('/acc-review/{id}/approve', [PmtAccomplishmentReviewController::class, 'approve'])->name('pmt.acc-review.approve');
    Route::post('/acc-review/{id}/return', [PmtAccomplishmentReviewController::class, 'returnSubmission'])->name('pmt.acc-review.return');

    // Stage III - Final Calibration
    Route::prefix('employee-calibration')->name('pmt.employee-calibration.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Pmt\EmployeeCalibrationController::class, 'index'])->name('index');
        Route::post('/{ipcr}/adjust', [\App\Http\Controllers\Pmt\EmployeeCalibrationController::class, 'adjust'])->name('adjust');
        Route::post('/{ipcr}/approve', [\App\Http\Controllers\Pmt\EmployeeCalibrationController::class, 'approve'])->name('approve');
        Route::post('/{ipcr}/release', [\App\Http\Controllers\Pmt\EmployeeCalibrationController::class, 'release'])->name('release');
        Route::post('/{ipcr}/return', [\App\Http\Controllers\Pmt\EmployeeCalibrationController::class, 'returnIpcr'])->name('return');
    });

    Route::prefix('office-calibration')->name('pmt.office-calibration.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Pmt\OfficeCalibrationController::class, 'index'])->name('index');
        Route::get('/{opcr}', [\App\Http\Controllers\Pmt\OfficeCalibrationController::class, 'show'])->name('show');
        Route::post('/{opcr}/adjust', [\App\Http\Controllers\Pmt\OfficeCalibrationController::class, 'adjust'])->name('adjust');
        Route::post('/{opcr}/approve', [\App\Http\Controllers\Pmt\OfficeCalibrationController::class, 'approve'])->name('approve');
        Route::post('/{opcr}/release', [\App\Http\Controllers\Pmt\OfficeCalibrationController::class, 'release'])->name('release');
        Route::post('/{opcr}/return', [\App\Http\Controllers\Pmt\OfficeCalibrationController::class, 'returnOpcr'])->name('return');
    });

    Route::get('/top-performers', [\App\Http\Controllers\Pmt\TopPerformersController::class, 'index'])
        ->name('pmt.top-performers.index');
    Route::get('/top-performers/preview-pdf', [\App\Http\Controllers\Pmt\TopPerformersController::class, 'previewPdf'])
        ->name('pmt.top-performers.preview-pdf');

    Route::prefix('development-planning')->name('pmt.development-planning.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Pmt\DevelopmentPlanningController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Pmt\DevelopmentPlanningController::class, 'store'])->name('store');
        Route::get('/{developmentPlan}', [\App\Http\Controllers\Pmt\DevelopmentPlanningController::class, 'show'])->name('show');
        Route::post('/{developmentPlan}/status', [\App\Http\Controllers\Pmt\DevelopmentPlanningController::class, 'updateStatus'])->name('status');
    });
});

/*
|--------------------------------------------------------------------------
| Shared Auth Routes (kept)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/stage-one/pmt/uwp', fn () => redirect()->route('pmt.opcr.review.index'))->name('pmt.uwp.index');
    Route::post('/stage-one/pmt/uwp/approve', fn () => redirect()->route('pmt.opcr.review.index'))->name('pmt.uwp.approve');
    Route::get('/stage-one/uwp/{uwp}/export', [PmtUnitWorkPlanController::class, 'exportExcel'])->name('uwp.export');
});

/*
|--------------------------------------------------------------------------
| Administrator Routes
|--------------------------------------------------------------------------
*/

Route::prefix('administrator')->middleware('auth')->group(function () {
    // Views
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/profile', fn () => view('admin.profile'))->name('admin.profile');
    Route::get('/audit-logs', [AuditLogsController::class, 'index'])->name('admin.audit-logs');
    Route::get('/database', [DatabaseController::class, 'index'])->name('admin.database');
    Route::post('/database/backups', [DatabaseController::class, 'store'])->name('admin.database.backups.store');
    Route::get('/database/backups/{backup}/download', [DatabaseController::class, 'download'])->name('admin.database.backups.download');
    Route::post('/database/backups/{backup}/restore', [DatabaseController::class, 'restore'])->name('admin.database.backups.restore');
    Route::delete('/database/backups/{backup}', [DatabaseController::class, 'destroy'])->name('admin.database.backups.destroy');
    Route::get('/reports', [ReportsController::class, 'index'])->name('admin.reports');
    Route::get('/reports/{report}/preview', [ReportsController::class, 'preview'])->name('admin.reports.preview');
    Route::get('/reports/{report}/download', [ReportsController::class, 'download'])->name('admin.reports.download');
    Route::get('/performance-period', [PerformancePeriodsController::class, 'index'])->name('admin.performance-period');
    Route::post('/performance-period', [PerformancePeriodsController::class, 'store'])->name('admin.performance-periods.store');
    Route::put('/performance-period/{period}', [PerformancePeriodsController::class, 'update'])->name('admin.performance-periods.update');
    Route::patch('/performance-period/{period}', [PerformancePeriodsController::class, 'update']);
    Route::post('/performance-period/{period}/activate', [PerformancePeriodsController::class, 'activate'])->name('admin.performance-periods.activate');
    Route::post('/performance-period/{period}/deactivate', [PerformancePeriodsController::class, 'deactivate'])->name('admin.performance-periods.deactivate');
    Route::delete('/performance-period/{period}', [PerformancePeriodsController::class, 'destroy'])->name('admin.performance-periods.destroy');

    Route::get('/users', [UsersController::class, 'index'])->name('admin.users');
    Route::put('/users/{user}', [UsersController::class, 'update'])->name('admin.users.update');
    Route::patch('/users/{user}', [UsersController::class, 'update']);
    Route::post('/users/{user}/toggle-active', [UsersController::class, 'toggleActive'])->name('admin.users.toggle');
    Route::post('/users/{user}/reset-password', [UsersController::class, 'resetPassword'])->name('admin.users.reset-password');
    Route::post('/users/{user}/send-code', [UsersController::class, 'sendEmployeeCode'])->name('admin.users.send-code');

    Route::get('/offices', [OfficeController::class, 'index'])->name('admin.office');
    Route::post('/offices/create', [OfficeController::class, 'store'])->name('admin.office.create');
    Route::post('/offices/{id}', [OfficeController::class, 'update'])->name('admin.office.update');
    Route::post('/offices/{id}/delete', [OfficeController::class, 'destroy'])->name('admin.office.delete');

    Route::get('/HRIS-integration', [HrisIntegrationController::class, 'index'])->name('admin.hris');
    Route::post('/HRIS-integration', [HrisIntegrationController::class, 'update'])->name('admin.hris.update');
    Route::post('/HRIS-integration/test', [HrisIntegrationController::class, 'testConnection'])->name('admin.hris.test');
    Route::post('/HRIS-integration/sync', [HrisIntegrationController::class, 'syncEmployees'])->name('admin.hris.sync');
    Route::post('/HRIS-integration/pms-api/regenerate', [HrisIntegrationController::class, 'regeneratePmsApiToken'])->name('admin.hris.pms-api.regenerate');
});

/*
|--------------------------------------------------------------------------
| Fallback (keep last)
|--------------------------------------------------------------------------
*/

Route::fallback(fn () => view('no-page'));
