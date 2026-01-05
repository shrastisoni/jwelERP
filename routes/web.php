<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/login', function () {
//     return response()->json([
//         'message' => 'API only. Use /api/login'
//     ], 401);
// })->name('login');