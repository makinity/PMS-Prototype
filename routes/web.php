<?php

use App\Http\Controllers\StageOne\Forms\IpcrExportController;
use App\Http\Controllers\StageOne\Forms\OpcrExportController;
use App\Http\Controllers\StageOne\Forms\UwpExportController;
use App\Http\Controllers\StageThree\Forms\IpcrExportController as StageThreeFormsIpcrExportController;
use App\Http\Controllers\StageTwo\Forms\IpcrExportController as FormsIpcrExportController;
use App\Http\Controllers\StageTwo\Forms\MporExportController;
use App\Http\Controllers\StageTwo\Forms\OrsExportController;
use App\Http\Controllers\StageTwo\Forms\QarExportController;
use App\Http\Controllers\StageTwo\Forms\SmporExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::get('/employee-dashboard', function () {
    return view('employee.dashboard');
})->name('employee.dashboard');

// Route::get('/employee-UWP', function () {
//     return view('employee.uwp');
// })->name('employee.uwp');

Route::get('/employee-task', function () {
    return view('employee.my-task');
})->name('employee.my-task');

Route::get('/employee/accomplishment-submission', function () {
    return view('employee.accomplishment-submission');
})->name('employee.accomplishment-submission');

Route::get('/employee-submit-output', function () {
    return view('employee.submit-output');
})->name('employee.submit-output');

Route::get('/employee-ORS', function () {
    return view('employee.ors');
})->name('employee.ors');

Route::get('/employee-MPOR', function () {
    return view('employee.mpor');
})->name('employee.mpor');

Route::get('/employee-SMPOR', function () {
    return view('employee.smpor');
})->name('employee.smpor');

Route::get('/employee/IPCR-Target', function () {
    return view('employee.ipcr-target');
})->name('employee.ipcr-target');

Route::get('/employee-IPCR', function () {
    return view('employee.ipcr');
})->name('employee.ipcr');

Route::get('/employee/final-ratings', function () {
    return view('employee.final-ratings');
})->name('employee.final-ratings');

Route::get('/employee-IDP', function () {
    return view('employee.idp');
})->name('employee.idp');

Route::get('/employee-Profile', function () {
    return view('employee.profile');
})->name('employee.profile');

Route::get('/employee/smpor/export/pdf', [SmporExportController::class, 'exportPdf'])
    ->name('stage2.smpor.export.pdf');

Route::get('/employee/ipcr/export/pdf', [FormsIpcrExportController::class, 'exportPdf'])
    ->name('stage2.ipcr.export.pdf');



Route::get('/dept-head/dashboard', function(){
    return view('dept-head.dashboard');
})->name('dept-head.dashboard');

Route::get('/dept-head/uwp', function () {
    return view('dept-head.uwp');
})->name('dept-head.uwp');

Route::get('/dept-head/opcr', function () {
    return view('dept-head.opcr');
})->name('dept-head.opcr');

Route::get('/dept-head/qar/export/pdf', [QarExportController::class, 'exportPdf'])
    ->name('stage2.qar.export.pdf');

Route::get('/dept-head/qar', function () {
    return view('dept-head.qar');
})->name('dept-head.qar');

Route::get('/dept-head/smpor', function () {
    return view('dept-head.smpor');
})->name('dept-head.smpor');

Route::get('/dept-head/IPCR', function () {
    return view('dept-head.ipcr');
})->name('dept-head.ipcr');

Route::get('/dept-head/IPCRTARGET', function () {
    return view('dept-head.ipcr-target');
})->name('dept-head.ipcr-target');

Route::get('/dept-head/ipcr/export/pdf', [IpcrExportController::class, 'exportPdf'])
    ->name('stage1.ipcr.export.pdf');

Route::get('/dept-head/idp', function () {
    return view('dept-head.idp');
})->name('dept-head.idp');

Route::get('/dept-head/profile', function () {
    return view('dept-head.profile');
})->name('dept-head.profile');





Route::get('/supervisor-dashboard', function () {
    return view('supervisor.dashboard');
})->name('supervisor.dashboard');

Route::get('/supervisor/uwp-page', function () {
    return view('supervisor.uwp-list');
})->name('supervisor.uwp-page');

Route::get('/supervisor/uwp', function () {
    return view('supervisor.uwp');
})->name('supervisor.uwp');

Route::get('/supervisor/team-tasks', function () {
    return view('supervisor.team-tasks');
})->name('supervisor.team-tasks');

Route::get('/supervisor/ipcr', function () {
    return view('supervisor.ipcr');
})->name('supervisor.ipcr');

Route::get('/supervisor/ipcr-target', function () {
    return view('supervisor.ipcr-target');
})->name('supervisor.ipcr-target');

Route::get('/supervisor/opcr', function () {
    return view('supervisor.opcr');
})->name('supervisor.opcr');

Route::get('/supervisor/mpor', function () {
    return view('supervisor.mpor');
})->name('supervisor.mpor');

Route::get('/supervisor/mpor/export/pdf', [MporExportController::class, 'exportPdf'])
    ->name('supervisor.mpor.export.pdf');

Route::get('/supervisor/mpor-validation', function () {
    return view('supervisor.mpor-validation');
})->name('supervisor.mpor-validation');

Route::get('/supervisor/ors-monitoring', function () {
    return view('supervisor.ors-monitoring');
})->name('supervisor.ors-monitoring');

Route::get('/supervisor/ors/export/pdf', [OrsExportController::class, 'exportPdf'])
    ->name('supervisor.ors.export.pdf');

Route::get('/supervisor/overdue-alerts', function () {
    return view('supervisor.overdue-alerts');
})->name('supervisor.overdue-alerts');

Route::get('/supervisor/task-validation', function () {
    return view('supervisor.task-validation');
})->name('supervisor.task-validation');

Route::get('/supervisor/team-productivity', function () {
    return view('supervisor.team-productivity');
})->name('supervisor.team-productivity');

Route::get('/supervisor/bottleneck-reports', function () {
    return view('supervisor.bottleneck-reports');
})->name('supervisor.bottleneck-reports');

Route::get('/supervisor/recommendations', function () {
    return view('supervisor.recommendations');
})->name('supervisor.recommendations');

Route::get('/supervisor/reports', function () {
    return view('supervisor.reports');
})->name('supervisor.reports');

Route::get('/supervisor/profile', function () {
    return view('supervisor.profile');
})->name('supervisor.profile');





Route::get('/pmt-dashboard', function () {
    return view('pmt.dashboard');
})->name('pmt.dashboard');

Route::get('/pmt/UWP', function () {
    return view('pmt.uwp');
})->name('pmt.uwp');

Route::get('/pmt/OPCR', function () {
    return view('pmt.opcr');
})->name('pmt.opcr');

Route::get('/pmt/OPCR/approval', function () {
    return view('pmt.opcr-app-view');
})->name('pmt.opcr-app-view');

Route::get('/pmt/ipcr', function () {
    return view('pmt.ipcr');
})->name('pmt.ipcr');

Route::get('/pmt/ipcr-overview', function () {
    return view('pmt.ipcr-calib-overview');
})->name('pmt.ipcr-calib-overview');

Route::get('/pmt/ipcr-calibration', function () {
    return view('pmt.ipcr-calib');
})->name('pmt.ipcr-calib');

Route::get('/pmt/final-calibration', function () {
    return view('pmt.final-calibration');
})->name('pmt.final-calib');

Route::get('/pmt/final-calibration/office', function () {
    return view('pmt.final-calibration-office');
})->name('pmt.final-calibration-office');

Route::get('/pmt/rewards-development', function () {
    return view('pmt.rewards');
})->name('pmt.rewards');

Route::get('/pmt/smpor', function () {
    return view('pmt.smpor');
})->name('pmt.smpor');

Route::get('/pmt/performance-reports', function () {
    return view('pmt.pr');
})->name('pmt.pr');

Route::get('/pmt/profile', function () {
    return view('pmt.profile');
})->name('pmt.profile');

Route::get('/pmt/uwp/export/pdf', [UwpExportController::class, 'exportPdf'])
    ->name('stage1.uwp.export.pdf');

Route::get('/pmt/ipcr/export/pdf', [StageThreeFormsIpcrExportController::class, 'exportPdf'])
->name('stage3.ipcr.export.pdf');




Route::get('/administrator-dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/administrator/users', function () {
    return view('admin.users');
})->name('admin.users');

Route::get('/administrator/roles', function () {
    return view('admin.roles');
})->name('admin.roles');

Route::get('/administrat/opcr', function () {
    return view('admin.opcr');
})->name('admin.opcr');

Route::get('/administrat/opcr-accomplishment', function () {
    return view('admin.opcr-acc');
})->name('admin.opcr-acc');

Route::get('/administrat/opcr-accomplishment/show', function () {
    return view('admin.opcr-acc-view');
})->name('admin.opcr-acc-view');

Route::get('/administrator/task-configuration', function () {
    return view('admin.task-config');
})->name('admin.task-config');

Route::get('/administrator/uwp-monitoring', function () {
    return view('admin.uwp-monitoring');
})->name('admin.uwp-monitoring');

Route::get('/administrator/performance-metrics', function () {
    return view('admin.performance-metrics');
})->name('admin.performance-metrics');

Route::get('/administrator/system-settings', function () {
    return view('admin.system');
})->name('admin.system');

Route::get('/administrator/HRIS-integration', function () {
    return view('admin.hris');
})->name('admin.hris');

Route::get('/administrator/data-export', function () {
    return view('admin.data');
})->name('admin.data');

Route::get('/administrator/semestral-pr', function () {
    return view('admin.semestral-pr');
})->name('admin.semestral-pr');

Route::get('/administrator/audit-trails', function () {
    return view('admin.audit-trail');
})->name('admin.audit-trail');

Route::get('/administrator/system-logs', function () {
    return view('admin.system-logs');
})->name('admin.system-logs');

Route::get('/administrator/profile', function () {
    return view('admin.profile');
})->name('admin.profile');

Route::get('/administrator/opcr/export/pdf', [OpcrExportController::class, 'exportPdf'])
    ->name('stage1.opcr.export.pdf');





Route::get('/manager-dashboard', function () {
    return view('manager.dashboard');
})->name('manager.dashboard');

Route::get('/manager-team', function () {
    return view('manager.my-team');
})->name('manager.my-team');

Route::get('/manager-task-monitoring', function () {
    return view('manager.task-monitoring');
})->name('manager.task-monitoring');

Route::get('/manager-productivity-analysis', function () {
    return view('manager.productivity');
})->name('manager.productivity');

Route::get('/manager-bottleneck-analysis', function () {
    return view('manager.bottleneck');
})->name('manager.bottleneck');

Route::get('/manager-predictive-analytics', function () {
    return view('manager.predictive-analytics');
})->name('manager.predictive-analytics');

Route::get('/manager-performance-rating', function () {
    return view('manager.performance-rate');
})->name('manager.performance-rate');

Route::get('/manager/ipcr-reports', function () {
    return view('manager.ipcr-reports');
})->name('manager.ipcr-reports');

Route::get('/manager-Profile', function () {
    return view('manager.profile');
})->name('manager.profile');
