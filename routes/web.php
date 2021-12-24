<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::view('/', 'index');

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // MESSENGER
    Route::get('/messenger', [MessageController::class, 'showUsers'])->name('users');
    Route::get('/messenger/{recipient}', [MessageController::class, 'getMessages'])->name('messenger');

    // GROUP
    Route::post('/group', [GroupController::class, 'createGroup'])->name('group-create');

    // AJAX
    Route::post('/message/send', [MessageController::class, 'sendMessage'])->name('message-send');
    Route::delete('/conversation', [MessageController::class, 'conversationDelete'])->name('conversation-delete');
});
