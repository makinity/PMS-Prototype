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




Route::get('/manager-dashboard', function () {
    return view('manager.dashboard');
})->name('manager.dashboard');    
Route::get('/manager-task-monitoring', function () {
    return view('manager.task-monitoring');
})->name('manager.task-monitoring');   