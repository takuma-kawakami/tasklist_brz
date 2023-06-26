<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\UsersController;

use resources\auth;

require __DIR__.'/auth.php';

Route::resource('users', UsersController::class, ['show']);

Route::group(['middleware' => ['guest']], function () {
    Route::get('/', function () {return view('dashboard');});
    Route::get('/dashboard', function () {return view('dashboard');})->middleware(['auth'])->name('dashboard');
    
    Route::resource('users', UsersController::class, ['only' => ['index', 'show']]);
});

Route::group(['middleware' => ['auth']], function () {
    Route::resource('tasks', TasksController::class);
    Route::resource('users', UsersController::class, ['only' => ['index', 'show']]);
    Route::get('/', [TasksController::class, 'index'])->name('tasks.index');
    Route::get('tasks/{id}', [TasksController::class, 'show']);
});