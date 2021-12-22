<?php

use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

Route::get('/messenger', [MessageController::class, 'index'])->middleware(['auth'])->name('messenger');

Route::get('/dashboard', static function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('test-broadcast', function(){
    broadcast(new \App\Events\NewMessageNotification('хуй'));
    return true;
});

require __DIR__.'/auth.php';
