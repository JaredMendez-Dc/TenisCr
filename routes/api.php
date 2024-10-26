<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\TeniController;
use App\Http\Controllers\AuthController;
/*
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');*/

Route::post('auth/register', [AuthController::class, 'create']);
Route::post('auth/login', [AuthController::class, 'login']);


Route::middleware(['auth:sanctum'])->group(function (){

Route::resource('marcas', MarcaController::class);
Route::resource('tenis', TeniController::class);
Route::get('tenisall',[TeniController::class, 'all']);
Route::get('tenisbymarca',[TeniController::class, 'TenisByMarca']);
Route::get('auth/logout', [AuthController::class, 'logout']);


});