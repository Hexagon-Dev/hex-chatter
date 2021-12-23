<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::view('/', 'index');

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/messenger', [MessageController::class, 'showUsers'])->name('users');
    Route::get('/messenger/{recipient}', [MessageController::class, 'writeMessage'])->name('messenger');
    Route::post('/message/send', [MessageController::class, 'send'])->name('message-send');
});
