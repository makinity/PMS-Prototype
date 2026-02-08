<?php

use App\Http\Controllers\Auth\ActivationController;
use App\Http\Controllers\StageOne\Forms\IpcrExportController;
use App\Http\Controllers\StageOne\Forms\OpcrExportController;
use App\Http\Controllers\StageOne\Forms\UwpExportController;
use App\Http\Controllers\StageOne\Forms\UwpExcelExportController;
use App\Http\Controllers\StageThree\Forms\IpcrExportController as StageThreeFormsIpcrExportController;
use App\Http\Controllers\StageTwo\Forms\IpcrExportController as FormsIpcrExportController;
use App\Http\Controllers\StageTwo\Forms\MporExportController;
use App\Http\Controllers\StageTwo\Forms\OrsExportController;
use App\Http\Controllers\StageTwo\Forms\QarExportController;
use App\Http\Controllers\StageTwo\Forms\SmporExportController;
use App\Http\Controllers\StageOne\Forms\OpcrExcelExportController;
use App\Http\Controllers\StageOne\Forms\IpcrExcelExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::post('/activate/verify', [ActivationController::class, 'verify']);
Route::post('/activate/complete', [ActivationController::class, 'complete']);

Route::fallback(function(){
    return view('no-page');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    if (! $user) {
        return redirect()->route('login');
    }

    return match ($user->role) {
        'employee'   => redirect()->route('employee.dashboard'),
        'supervisor' => redirect()->route('supervisor.dashboard'),
        default      => abort(403, 'Unauthorized role'),
    };
})->middleware('auth');


Route::prefix('employee')->group(function(){
    Route::get('/dashboard', function () {
        return view('employee.dashboard');
    })->name('employee.dashboard');

    // Route::get('/employee-UWP', function () {
    //     return view('employee.uwp');
    // })->name('employee.uwp');

    Route::get('/task', function () {
        return view('employee.my-task');
    })->name('employee.my-task');

    Route::get('/accomplishment-submission', function () {
        return view('employee.accomplishment-submission');
    })->name('employee.accomplishment-submission');

    Route::get('/submit-output', function () {
        return view('employee.submit-output');
    })->name('employee.submit-output');

    Route::get('/ORS', function () {
        return view('employee.ors');
    })->name('employee.ors');

    Route::get('/MPOR', function () {
        return view('employee.mpor');
    })->name('employee.mpor');

    Route::get('/SMPOR', function () {
        return view('employee.smpor');
    })->name('employee.smpor');

    Route::get('/IPCR-Target', function () {
        return view('employee.ipcr-target');
    })->name('employee.ipcr-target');

    Route::get('/IPCR', function () {
        return view('employee.ipcr');
    })->name('employee.ipcr');

    Route::get('/final-ratings', function () {
        return view('employee.final-ratings');
    })->name('employee.final-ratings');

    Route::get('/IDP', function () {
        return view('employee.idp');
    })->name('employee.idp');

    Route::get('/Profile', function () {
        return view('employee.profile');
    })->name('employee.profile');

    Route::get('/smpor/export/pdf', [SmporExportController::class, 'exportPdf'])
        ->name('stage2.smpor.export.pdf');

    Route::get('/ors/export/pdf', [OrsExportController::class, 'exportPdf'])
        ->name('employee.ors.export.pdf');

    Route::get('/employee/mpor/export/pdf', [MporExportController::class, 'exportPdf'])
        ->name('employee.mpor.export.pdf');

    Route::get('/ipcr/export/pdf', [FormsIpcrExportController::class, 'exportPdf'])
        ->name('stage2.ipcr.export.pdf');

    Route::get('/ipcr/export/excel', [IpcrExcelExportController::class, 'exportExcel'])
        ->name('stage1.ipcr.export.excel');
});





Route::prefix('dept-head')->group(function(){
    Route::get('/dashboard', function(){
        return view('dept-head.dashboard');
    })->name('dept-head.dashboard');

    Route::get('/uwp', function () {
        return view('dept-head.uwp');
    })->name('dept-head.uwp');

    Route::get('/opcr', function () {
        return view('dept-head.opcr');
    })->name('dept-head.opcr');

    Route::get('/qar/export/pdf', [QarExportController::class, 'exportPdf'])
        ->name('stage2.qar.export.pdf');

    Route::get('/qar', function () {
        return view('dept-head.qar');
    })->name('dept-head.qar');

    Route::get('/smpor', function () {
        return view('dept-head.smpor');
    })->name('dept-head.smpor');

    Route::get('/IPCR', function () {
        return view('dept-head.ipcr');
    })->name('dept-head.ipcr');

    Route::get('/IPCRTARGET', function () {
        return view('dept-head.ipcr-target');
    })->name('dept-head.ipcr-target');

    Route::get('/ipcr/export/pdf', [IpcrExportController::class, 'exportPdf'])
        ->name('stage1.ipcr.export.pdf');

    Route::get('/idp', function () {
        return view('dept-head.idp');
    })->name('dept-head.idp');

    Route::get('/profile', function () {
        return view('dept-head.profile');
    })->name('dept-head.profile');
});





Route::prefix('supervisor')->group(function(){
    Route::get('/dashboard', function () {
        return view('supervisor.dashboard');
    })->name('supervisor.dashboard');

    Route::get('/uwp-page', function () {
        return view('supervisor.uwp-list');
    })->name('supervisor.uwp-page');

    Route::get('/uwp', function () {
        return view('supervisor.uwp');
    })->name('supervisor.uwp');

    Route::get('/team-tasks', function () {
        return view('supervisor.team-tasks');
    })->name('supervisor.team-tasks');

    Route::get('/ipcr', function () {
        return view('supervisor.ipcr');
    })->name('supervisor.ipcr');

    Route::get('/ipcr-target', function () {
        return view('supervisor.ipcr-target');
    })->name('supervisor.ipcr-target');

    Route::get('/opcr', function () {
        return view('supervisor.opcr');
    })->name('supervisor.opcr');

    Route::get('/mpor', function () {
        return view('supervisor.mpor');
    })->name('supervisor.mpor');

    Route::get('/mpor-validation', function () {
        return view('supervisor.mpor-validation');
    })->name('supervisor.mpor-validation');

    Route::get('/ors-monitoring', function () {
        return view('supervisor.ors-monitoring');
    })->name('supervisor.ors-monitoring');

    Route::get('/overdue-alerts', function () {
        return view('supervisor.overdue-alerts');
    })->name('supervisor.overdue-alerts');

    Route::get('/task-validation', function () {
        return view('supervisor.task-validation');
    })->name('supervisor.task-validation');

    Route::get('/team-productivity', function () {
        return view('supervisor.team-productivity');
    })->name('supervisor.team-productivity');

    Route::get('/bottleneck-reports', function () {
        return view('supervisor.bottleneck-reports');
    })->name('supervisor.bottleneck-reports');

    Route::get('/recommendations', function () {
        return view('supervisor.recommendations');
    })->name('supervisor.recommendations');

    Route::get('/reports', function () {
        return view('supervisor.reports');
    })->name('supervisor.reports');

    Route::get('/profile', function () {
        return view('supervisor.profile');
    })->name('supervisor.profile');

});







Route::prefix('pmt')->group(function(){
    Route::get('/dashboard', function () {
        return view('pmt.dashboard');
    })->name('pmt.dashboard');

    Route::get('/UWP', function () {
        return view('pmt.uwp');
    })->name('pmt.uwp');

    Route::get('/OPCR', function () {
        return view('pmt.opcr');
    })->name('pmt.opcr');

    Route::get('/OPCR/approval', function () {
        return view('pmt.opcr-app-view');
    })->name('pmt.opcr-app-view');

    Route::get('/ipcr', function () {
        return view('pmt.ipcr');
    })->name('pmt.ipcr');

    Route::get('/ipcr-overview', function () {
        return view('pmt.ipcr-calib-overview');
    })->name('pmt.ipcr-calib-overview');

    Route::get('/ipcr-calibration', function () {
        return view('pmt.ipcr-calib');
    })->name('pmt.ipcr-calib');

    Route::get('/final-calibration', function () {
        return view('pmt.final-calibration');
    })->name('pmt.final-calib');

    Route::get('/final-calibration/office', function () {
        return view('pmt.final-calibration-office');
    })->name('pmt.final-calibration-office');

    Route::get('/rewards-development', function () {
        return view('pmt.rewards');
    })->name('pmt.rewards');

    Route::get('/smpor', function () {
        return view('pmt.smpor');
    })->name('pmt.smpor');

    Route::get('/performance-reports', function () {
        return view('pmt.pr');
    })->name('pmt.pr');

    Route::get('/profile', function () {
        return view('pmt.profile');
    })->name('pmt.profile');

    Route::get('/uwp/export/pdf', [UwpExportController::class, 'exportPdf'])
        ->name('stage1.uwp.export.pdf');

    Route::get('/uwp/preview/pdf', [UwpExportController::class, 'preview'])
        ->name('stage1.uwp.preview.pdf');

    Route::get('/uwp/export/excel', [UwpExcelExportController::class, 'exportExcel'])
        ->name('stage1.uwp.export.excel');

    Route::get('/uwp/preview/excel', [UwpExcelExportController::class, 'previewExcel'])
        ->name('stage1.uwp.preview.excel');

    Route::get('/ipcr/export/pdf', [StageThreeFormsIpcrExportController::class, 'exportPdf'])
    ->name('stage3.ipcr.export.pdf');
});






Route::prefix('administrator')->group(function(){
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/users', function () {
        return view('admin.users');
    })->name('admin.users');

    Route::get('/roles', function () {
        return view('admin.roles');
    })->name('admin.roles');

    Route::get('/opcr', function () {
        return view('admin.opcr');
    })->name('admin.opcr');

    Route::get('/opcr-accomplishment', function () {
        return view('admin.opcr-acc');
    })->name('admin.opcr-acc');

    Route::get('/opcr-accomplishment/show', function () {
        return view('admin.opcr-acc-view');
    })->name('admin.opcr-acc-view');

    Route::get('/task-configuration', function () {
        return view('admin.task-config');
    })->name('admin.task-config');

    Route::get('/uwp-monitoring', function () {
        return view('admin.uwp-monitoring');
    })->name('admin.uwp-monitoring');

    Route::get('/performance-metrics', function () {
        return view('admin.performance-metrics');
    })->name('admin.performance-metrics');

    Route::get('/system-settings', function () {
        return view('admin.system');
    })->name('admin.system');

    Route::get('/HRIS-integration', function () {
        return view('admin.hris');
    })->name('admin.hris');

    Route::get('/data-export', function () {
        return view('admin.data');
    })->name('admin.data');

    Route::get('/semestral-pr', function () {
        return view('admin.semestral-pr');
    })->name('admin.semestral-pr');

    Route::get('/audit-trails', function () {
        return view('admin.audit-trail');
    })->name('admin.audit-trail');

    Route::get('/system-logs', function () {
        return view('admin.system-logs');
    })->name('admin.system-logs');

    Route::get('/profile', function () {
        return view('admin.profile');
    })->name('admin.profile');

    Route::get('/opcr/export/pdf', [OpcrExportController::class, 'exportPdf'])
        ->name('stage1.opcr.export.pdf');

    Route::get('/opcr/export/excel', [OpcrExcelExportController::class, 'exportExcel'])
        ->name('stage1.opcr.export.excel');
});






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
