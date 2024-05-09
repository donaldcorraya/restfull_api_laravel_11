<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');


Route::post('/store', [ApiController::class, 'store'])->name('store');
Route::post('/login', [ApiController::class, 'login'])->name('login');

Route::middleware(['auth:api'])->group(function(){

    Route::get('/index', [ApiController::class, 'index'])->name('index');
    Route::post('/update/{id}', [ApiController::class, 'update'])->name('update');
    Route::get('/destroy/{id}', [ApiController::class, 'destroy'])->name('destroy');
    Route::get('/logout', [ApiController::class, 'logout'])->name('logout');

});