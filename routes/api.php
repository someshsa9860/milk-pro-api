<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
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





Route::post('/auth/login', [AuthController::class, 'login'])->middleware('security');

Route::group(['middleware' => ['security', 'auth:sanctum']], function () {
   
    Route::post('/logout', [AuthController::class, 'logout']);


    Route::post('users/password/change', [AuthController::class, 'changePassword']);
    Route::post('users/staffs/create', [UserController::class, 'create']);
    Route::get('users/staffs/delete/{id}', [UserController::class, 'delete']);
    Route::get('users/staffs/status/{id}', [UserController::class, 'status']);
 
    Route::get('users/staffs/fetch', [UserController::class, 'fetch']);
    Route::get('user', [UserController::class, 'user']);
    Route::post('/user/logout', [UserController::class, 'userLogout']);
    Route::post('validateUser', [UserController::class, 'validateUser']);

    

    Route::get('home', [HomeController::class, 'fetch']);
    Route::get('/rates/sync', [HomeController::class, 'getRates']);
    Route::get('customers/all', [CustomerController::class, 'fetch']);
    Route::post('customers/create', [CustomerController::class, 'create']);
    Route::get('customers/delete', [CustomerController::class, 'delete']);
    Route::get('customers/status/{id}', [CustomerController::class, 'status']);

    Route::post('/order/place', [OrderController::class, 'place']);
    Route::post('/payment/entry', [OrderController::class, 'makePaymentEntry']);
    Route::get('/orders', [OrderController::class, 'orders']);
    Route::get('/orders/delete/{id}', [OrderController::class, 'delete']);
    Route::get('/reports/ledger/print', [OrderController::class, 'printLedger']);
    
});
Route::post('/location/customers', [HomeController::class, 'customers']);
