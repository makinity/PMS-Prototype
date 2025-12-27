<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::get('/employee-dashboard', function () {
    return view('employee.dashboard');
})->name('employee.dashboard');

Route::get('/employee-UWP', function () {
    return view('employee.uwp');
})->name('employee.uwp');

Route::get('/employee-task', function () {
    return view('employee.my-task');
})->name('employee.my-task');

Route::get('/employee-submit-output', function () {
    return view('employee.submit-output');
})->name('employee.submit-output');

Route::get('/employee-ORS', function () {
    return view('employee.ors');
})->name('employee.ors');

Route::get('/employee-OPCR', function () {
    return view('employee.opcr');
})->name('employee.opcr');

Route::get('/employee-IPCR', function () {
    return view('employee.ipcr');
})->name('employee.ipcr');

Route::get('/employee-IDP', function () {
    return view('employee.idp');
})->name('employee.idp');

Route::get('/employee-Profile', function () {
    return view('employee.profile');
})->name('employee.profile');




Route::get('/administrator-dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');    

Route::get('/administrator/users', function () {
    return view('admin.users');
})->name('admin.users');

Route::get('/administrator/roles', function () {
    return view('admin.roles');
})->name('admin.roles');

Route::get('/administrator/task-configuration', function () {
    return view('admin.task-config');
})->name('admin.task-config');

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

Route::get('/administrator/audit-trails', function () {
    return view('admin.audit-trail');
})->name('admin.audit-trail');

Route::get('/administrator/system-logs', function () {
    return view('admin.system-logs');
})->name('admin.system-logs');

Route::get('/administrator/profile', function () {
    return view('admin.profile');
})->name('admin.profile');




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

Route::get('/manager-performance-rating', function () {
    return view('manager.performance-rate');
})->name('manager.performance-rate');  

Route::get('/manager/ipcr-reports', function () {
    return view('manager.ipcr-reports');
})->name('manager.ipcr-reports');   

Route::get('/manager-Profile', function () {
    return view('manager.profile');
})->name('manager.profile');