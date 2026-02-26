<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OfficeController;
use App\Http\Controllers\Admin\PerformancePeriodsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Auth\ActivationController;
use App\Http\Controllers\DeptHead\QarController;
use App\Http\Controllers\StageOne\Forms\IpcrExportController;
use App\Http\Controllers\StageOne\Forms\IpcrExcelExportController;
use App\Http\Controllers\StageOne\Forms\OpcrExportController;
use App\Http\Controllers\StageOne\Forms\OpcrExcelExportController;
use App\Http\Controllers\StageOne\Forms\UwpExportController;
use App\Http\Controllers\StageOne\Forms\UwpExcelExportController;
use App\Http\Controllers\StageOne\Planning\DeptHeadOpcrReviewController;
use App\Http\Controllers\StageOne\Planning\IpcrTargetController;
use App\Http\Controllers\StageOne\Planning\OpcrPmtReviewController;
use App\Http\Controllers\StageOne\Planning\SuperVisorOpcrController;
use App\Http\Controllers\StageOne\Planning\UnitWorkPlanController;
use App\Http\Controllers\StageOne\Planning\UwpDeptHeadReviewController;
use App\Http\Controllers\StageOne\Planning\UwpPmtReviewController;
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
use App\Http\Controllers\Employee\SmporIpcrAccomplishmentController;
use App\Http\Controllers\Supervisor\DashboardController as SupervisorDashboardController;
use App\Http\Controllers\Supervisor\MporController as SupervisorMporController;

/*
|--------------------------------------------------------------------------
| Public / Auth
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => view('landing'));

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
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
})->name('logout');

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
        'manager'    => redirect()->route('manager.dashboard'),
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
    Route::get('/submit-output', fn () => view('employee.submit-output'))->name('employee.submit-output');
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
    Route::get('/SMPOR', fn () => view('employee.smpor'))->name('employee.smpor');
    Route::get('/IPCR-Target', [IpcrTargetController::class, 'index'])
        ->name('employee.ipcr-target');
    Route::post('/stage1/ipcr/commit', [IpcrTargetController::class, 'commit'])
    ->name('stage1.ipcr.commit');

    Route::get('/IPCR', fn () => view('employee.ipcr'))->name('employee.ipcr');
    Route::get('/final-ratings', fn () => view('employee.final-ratings'))->name('employee.final-ratings');
    Route::get('/IDP', fn () => view('employee.idp'))->name('employee.idp');
    Route::get('/Profile', fn () => view('employee.profile'))->name('employee.profile');

    // Stage II - Accomplishment Submission
    Route::get('/accomplishment-submission', [SmporIpcrAccomplishmentController::class, 'index'])->name('employee.accomplishment-submission');
    Route::post('/accomplishment/submit', [EmployeeAccomplishmentController::class, 'submit'])
        ->name('stage2.employee.accomplishment.submit');

    // Stage II - MPOR Submit
    Route::post('/mpor/submit', [MporController::class, 'submitMpor'])
        ->name('employee.mpor.submit');

    // Exports - SMPOR Excel
    Route::get('/stage-two/forms/smpor/export-excel', [SmporExcelExportController::class, 'exportExcel'])
        ->name('stage2.smpor.export.excel');
    Route::get('/stage-two/forms/smpor/preview-excel', [SmporExcelExportController::class, 'previewExcel'])
        ->name('stage2.smpor.preview.excel');

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

    Route::get('/stage-two/forms/ipcr/export-excel', [\App\Http\Controllers\StageTwo\Forms\IpcrExcelExportController::class, 'exportExcel'])
        ->name('stage2.ipcr.export.excel');
    Route::get('/stage-two/forms/ipcr/preview-excel', [\App\Http\Controllers\StageTwo\Forms\IpcrExcelExportController::class, 'previewExcel'])
        ->name('stage2.ipcr.preview.excel');

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
    Route::get('/dashboard', fn () => view('dept-head.dashboard'))->name('dept-head.dashboard');
    Route::get('/smpor', fn () => view('dept-head.smpor'))->name('dept-head.smpor');
    Route::get('/smpor-ipcr-review', fn () => view('dept-head.smpor-ipcr-review'))->name('dept-head.smpor-ipcr-review');
    Route::get('/IPCR', fn () => view('dept-head.ipcr'))->name('dept-head.ipcr');
    Route::get('/IPCRTARGET', fn () => view('dept-head.ipcr-target'))->name('dept-head.ipcr-target');
    Route::get('/idp', fn () => view('dept-head.idp'))->name('dept-head.idp');
    Route::get('/profile', fn () => view('dept-head.profile'))->name('dept-head.profile');

    // Stage I - UWP Review
    Route::get('/uwp', [UwpDeptHeadReviewController::class, 'index'])->name('dept-head.uwp');
    Route::get('/uwp/index', [UwpDeptHeadReviewController::class, 'index'])->name('dept-head.uwp.index');
    Route::post('/uwp/review', [UwpDeptHeadReviewController::class, 'review'])->name('dept-head.uwp.review');
    Route::post('/uwp/return', [UwpDeptHeadReviewController::class, 'returnUwp'])->name('dept-head.uwp.return');

    // Stage I - OPCR Review
    Route::get('/opcr', [DeptHeadOpcrReviewController::class, 'index'])->name('dept-head.opcr');
    Route::get('/opcr/index', [DeptHeadOpcrReviewController::class, 'index'])->name('dept-head.opcr.index');
    Route::post('/opcr/{opcr}/endorse', [DeptHeadOpcrReviewController::class, 'endorse'])->name('dept-head.opcr.endorse');
    Route::post('/opcr/{opcr}/return', [DeptHeadOpcrReviewController::class, 'returnOpcr'])->name('dept-head.opcr.return');
    Route::post('/opcr/review', [DeptHeadOpcrReviewController::class, 'review'])->name('dept-head.opcr.review');

    // Stage II - QAR
    Route::get('/qar', [QarController::class, 'index'])->name('dept-head.qar');
    Route::post('/qar/generate', [QarController::class, 'generate'])->name('dept-head.qar.generate');
    Route::post('/qar/endorse', [QarController::class, 'endorse'])->name('dept-head.qar.endorse');
    Route::post('/qar/reset', [QarController::class, 'reset'])->name('dept-head.qar.reset');

    // Exports
    Route::get('/qar/export/pdf', [QarExportController::class, 'exportPdf'])
        ->name('stage2.qar.export.pdf');

    Route::get('/ipcr/export/pdf', [IpcrExportController::class, 'exportPdf'])
        ->name('stage1.ipcr.export.pdf');
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
    Route::get('/ipcr', fn () => view('supervisor.ipcr'))->name('supervisor.ipcr');
    Route::get('/smpor-ipcr-review', fn () => view('supervisor.smpor-ipcr-review'))->name('supervisor.smpor-ipcr-review');
    Route::get('/ipcr-target', fn () => view('supervisor.ipcr-target'))->name('supervisor.ipcr-target');


    Route::get('/mpor-validation', fn () => view('supervisor.mpor-validation'))->name('supervisor.mpor-validation');
    Route::get('/ors-monitoring', [OrsMonitoringController::class, 'index'])->name('supervisor.ors-monitoring');
    Route::post('/ors-monitoring/{orsEntry}/monitor', [OrsMonitoringController::class, 'store'])->name('supervisor.ors-monitoring.store');
    Route::get('/overdue-alerts', fn () => view('supervisor.overdue-alerts'))->name('supervisor.overdue-alerts');
    Route::get('/task-validation', fn () => view('supervisor.task-validation'))->name('supervisor.task-validation');
    Route::get('/team-productivity', fn () => view('supervisor.team-productivity'))->name('supervisor.team-productivity');
    Route::get('/bottleneck-reports', fn () => view('supervisor.bottleneck-reports'))->name('supervisor.bottleneck-reports');
    Route::get('/recommendations', fn () => view('supervisor.recommendations'))->name('supervisor.recommendations');
    Route::get('/reports', fn () => view('supervisor.reports'))->name('supervisor.reports');
    Route::get('/profile', fn () => view('supervisor.profile'))->name('supervisor.profile');

    // Stage I - UWP
    Route::get('/uwp-page', [UnitWorkPlanController::class, 'uwpList'])->name('supervisor.uwp-page');
    Route::get('/stage-one/planning/uwp', [UnitWorkPlanController::class, 'uwpList'])->name('supervisor.uwp.index');
    Route::get('/stage-one/planning/uwp/{id}', [UnitWorkPlanController::class, 'show'])->name('supervisor.uwp.show');
    Route::get('/uwp', [UnitWorkPlanController::class, 'index'])->name('supervisor.uwp');

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

    Route::get('/supervisor/uwp/{id}/preview', [UnitWorkPlanController::class, 'preview'])
        ->name('supervisor.uwp.preview');

    // Stage I - OPCR
    Route::get('/opcr', [SuperVisorOpcrController::class, 'index'])->name('supervisor.opcr');
    Route::get('/stage-one/planning/opcr', [SuperVisorOpcrController::class, 'index'])->name('stage1.opcr.index');
    Route::post('/stage-one/planning/opcr/generate', [SuperVisorOpcrController::class, 'generate'])->name('stage1.opcr.generate');
    Route::post('/stage-one/planning/opcr/{opcr}/submit', [SuperVisorOpcrController::class, 'submit'])->name('stage1.opcr.submit');

    // Stage II - MPOR Review
    Route::get('/mpor', [SupervisorMporController::class, 'index'])->name('supervisor.mpor');
    Route::get('/mpor/{mpor}', [SupervisorMporController::class, 'show'])->name('supervisor.mpor.show');
    Route::post('/mpor/{mpor}/approve', [SupervisorMporController::class, 'approve'])->name('supervisor.mpor.approve');
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
    Route::get('/dashboard', fn () => view('pmt.dashboard'))->name('pmt.dashboard');
    Route::get('/OPCR', fn () => view('pmt.opcr'))->name('pmt.opcr');
    Route::get('/OPCR/approval', fn () => view('pmt.opcr-app-view'))->name('pmt.opcr-app-view');
    Route::get('/ipcr', fn () => view('pmt.ipcr'))->name('pmt.ipcr');
    Route::get('/smpor-ipcr-review', fn () => view('pmt.smpor-ipcr-review'))->name('pmt.smpor-ipcr-review');
    Route::get('/ipcr-overview', fn () => view('pmt.ipcr-calib-overview'))->name('pmt.ipcr-calib-overview');
    Route::get('/ipcr-calibration', fn () => view('pmt.ipcr-calib'))->name('pmt.ipcr-calib');
    Route::get('/final-calibration', fn () => view('pmt.final-calibration'))->name('pmt.final-calib');
    Route::get('/final-calibration/office', fn () => view('pmt.final-calibration-office'))->name('pmt.final-calibration-office');
    Route::get('/rewards-development', fn () => view('pmt.rewards'))->name('pmt.rewards');
    Route::get('/smpor', fn () => view('pmt.smpor'))->name('pmt.smpor');
    Route::get('/performance-reports', fn () => view('pmt.pr'))->name('pmt.pr');
    Route::get('/profile', fn () => view('pmt.profile'))->name('pmt.profile');

    // Stage I - UWP / OPCR
    Route::get('/UWP', [UwpPmtReviewController::class, 'index'])->name('pmt.uwp');
    Route::post('/uwp/review', [UwpPmtReviewController::class, 'review'])->name('pmt.uwp.review');
    Route::post('/uwp/return', [UwpPmtReviewController::class, 'returnUwp'])->name('pmt.uwp.return');

    Route::get('/opcr-review', [OpcrPmtReviewController::class, 'index'])
        ->name('pmt.opcr.review.index');
    Route::post('/opcr-review/action', [OpcrPmtReviewController::class, 'review'])
        ->name('pmt.opcr.review.action');
    Route::get('/opcr-review/{opcr}/export', [OpcrPmtReviewController::class, 'export'])
        ->name('pmt.opcr.review.export');

    // Stage II - QAR
    Route::get('/qar', [PmtQarController::class, 'index'])
        ->name('pmt.qar');
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
});

/*
|--------------------------------------------------------------------------
| Shared Auth Routes (kept)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/stage-one/pmt/uwp', [UwpPmtReviewController::class, 'index'])->name('pmt.uwp.index');
    Route::post('/stage-one/pmt/uwp/approve', [UwpPmtReviewController::class, 'approve'])->name('pmt.uwp.approve');
    Route::get('/stage-one/uwp/{uwp}/export', [UwpExcelExportController::class, 'exportExcel'])->name('uwp.export');
});

/*
|--------------------------------------------------------------------------
| Administrator Routes
|--------------------------------------------------------------------------
*/

Route::prefix('administrator')->middleware('auth')->group(function () {
    // Views
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
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

    Route::get('/offices', [OfficeController::class, 'index'])->name('admin.office');
    Route::post('/offices/create', [OfficeController::class, 'store'])->name('admin.office.create');
    Route::post('/offices/{id}', [OfficeController::class, 'update'])->name('admin.office.update');
    Route::post('/offices/{id}/delete', [OfficeController::class, 'destroy'])->name('admin.office.delete');

    Route::get('/roles', fn () => view('admin.roles'))->name('admin.roles');
    Route::get('/opcr', fn () => view('admin.opcr'))->name('admin.opcr');
    Route::get('/opcr-accomplishment', fn () => view('admin.opcr-acc'))->name('admin.opcr-acc');
    Route::get('/opcr-accomplishment/show', fn () => view('admin.opcr-acc-view'))->name('admin.opcr-acc-view');
    Route::get('/task-configuration', fn () => view('admin.task-config'))->name('admin.task-config');
    Route::get('/uwp-monitoring', fn () => view('admin.uwp-monitoring'))->name('admin.uwp-monitoring');
    Route::get('/performance-metrics', fn () => view('admin.performance-metrics'))->name('admin.performance-metrics');
    Route::get('/system-settings', fn () => view('admin.system'))->name('admin.system');
    Route::get('/HRIS-integration', fn () => view('admin.hris'))->name('admin.hris');
    Route::get('/data-export', fn () => view('admin.data'))->name('admin.data');
    Route::get('/semestral-pr', fn () => view('admin.semestral-pr'))->name('admin.semestral-pr');
    Route::get('/audit-trails', fn () => view('admin.audit-trail'))->name('admin.audit-trail');
    Route::get('/system-logs', fn () => view('admin.system-logs'))->name('admin.system-logs');
    Route::get('/profile', fn () => view('admin.profile'))->name('admin.profile');
});

/*
|--------------------------------------------------------------------------
| Manager Routes
|--------------------------------------------------------------------------
*/

Route::prefix('manager')->middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => view('manager.dashboard'))->name('manager.dashboard');
    Route::get('/team', fn () => view('manager.my-team'))->name('manager.my-team');
    Route::get('/task-monitoring', fn () => view('manager.task-monitoring'))->name('manager.task-monitoring');
    Route::get('/productivity-analysis', fn () => view('manager.productivity'))->name('manager.productivity');
    Route::get('/bottleneck-analysis', fn () => view('manager.bottleneck'))->name('manager.bottleneck');
    Route::get('/predictive-analytics', fn () => view('manager.predictive-analytics'))->name('manager.predictive-analytics');
    Route::get('/performance-rating', fn () => view('manager.performance-rate'))->name('manager.performance-rate');
    Route::get('/ipcr-reports', fn () => view('manager.ipcr-reports'))->name('manager.ipcr-reports');
    Route::get('/profile', fn () => view('manager.profile'))->name('manager.profile');
});

/*
|--------------------------------------------------------------------------
| Fallback (keep last)
|--------------------------------------------------------------------------
*/

Route::fallback(fn () => view('no-page'));
