<?php

use Illuminate\Support\Facades\Route;

Route::any('/{any}', fn () => view('welcome'))->where('any', '^(?!api).*$');
