<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::get('/employee-dashboard', function () {
    return view('employee.dashboard');
})->name('employee.dashboard');

Route::get('/administrator-dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');    

Route::get('/manager-dashboard', function () {
    return view('manager.dashboard');
})->name('manager.dashboard');    