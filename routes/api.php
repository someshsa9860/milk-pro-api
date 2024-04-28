<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ParkingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/auth/login', [AuthController::class, 'login'])->middleware('security');

Route::group(['middleware' => ['security', 'auth:sanctum']], function () {
    Route::post('/header-footer/save', [SettingsController::class, 'saveHeaderFooter']);
    Route::get('/header-footer/get/{location}', [SettingsController::class, 'fetchHeaderFooter']);
    Route::get('/data/fetch', [HomeController::class, 'fetch']);
    
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('/postLogout', [AuthController::class, 'postLogout']);
    Route::get('/auth/status/check', [AuthController::class, 'test']);


    Route::post('users/password/change', [AuthController::class, 'changePassword']);
    Route::post('users/staffs/create', [UserController::class, 'create']);
    Route::get('users/staffs/status/{id}', [UserController::class, 'status']);
    Route::get('/locations/verify', [UserController::class, 'verifyLocation']);
 
    Route::get('users/staffs/fetch/{location}', [UserController::class, 'fetch']);
    
});
