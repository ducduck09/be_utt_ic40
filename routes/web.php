<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('react');
});

// Catch all routes for React Router (SPA)
Route::get('/{any}', function () {
    return view('react');
})->where('any', '.*');
