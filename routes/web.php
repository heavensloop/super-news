<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return response('OK', 200);
})->name('home');

require __DIR__.'/settings.php';
