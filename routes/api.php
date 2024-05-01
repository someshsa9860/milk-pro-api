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
Route::post('/auth/users/verifyOTP', [AuthController::class, 'verifyOTP'])->middleware('security');
Route::get('/app/home', [HomeController::class, 'home']);


Route::post('/auth/custom/signup', [AuthController::class, 'signUpCustom'])->middleware('security');
Route::post('/auth/custom/signin', [AuthController::class, 'signInCustom'])->middleware('security');
Route::post('/auth/check/email', [AuthController::class, 'isEmailExists'])->middleware('security');
Route::post('/auth/users/platform-sign-in-check', [AuthController::class, 'verifyGAF'])->middleware('security');
Route::post('/auth/login', [AuthController::class, 'login'])->middleware('security');
Route::get('/logoutAt/{id}', [AuthController::class, 'logoutAt']);

Route::group(['middleware' => ['security', 'auth:sanctum']], function () {
    Route::post('/header-footer/save', [SettingsController::class, 'saveHeaderFooter']);
    Route::get('/header-footer/get/{location}', [SettingsController::class, 'fetchHeaderFooter']);
    Route::get('/data/fetch', [HomeController::class, 'fetch']);
    Route::post('/vehicle-types/save', [VehicleController::class, 'saveVehicleType']);
    Route::get('/vehicle-types/delete/{id}', [VehicleController::class, 'deleteVehicleType']);
    Route::post('/vehicle-rates/save', [VehicleController::class, 'saveVehicleRate']);
    Route::get('/vehicle-rates/delete/{id}', [VehicleController::class, 'deleteVehicleRate']);
    Route::post('/vehicle-parking/check-in', [ParkingController::class, 'checkInOutSlipEntry']);
    Route::post('/vehicle-parking/slip-save', [ParkingController::class, 'checkInOutSlipEntry']);
    Route::post('/vehicle-parking/entry-ticket', [ParkingController::class, 'checkInOutSlipEntry']);
    Route::post('/vehicle-parking/check-out', [ParkingController::class, 'checkInOutSlipEntry']);
    Route::post('/vehicle-parking/find', [ParkingController::class, 'find']);
    Route::post('/invoices', [ParkingController::class, 'genInvoice']);
    Route::post('/vehicle-parking/monthly-pass/save', [ParkingController::class, 'saveMonthlyPass']);
    Route::post('/vehicle-parking/monthly-pass/find', [ParkingController::class, 'findMonthlyPass']);
    Route::post('/vehicle-parking/monthly-pass/check-in', [ParkingController::class, 'checkInOutMonthlyPass']);
    Route::post('/vehicle-parking/monthly-pass/check-in/find', [ParkingController::class, 'findCheckedInMonthlyPass']);
    Route::post('/vehicle-parking/monthly-pass/check-out', [ParkingController::class, 'checkInOutMonthlyPass']);
    Route::post('/gst-data/save', [ParkingController::class, 'saveGSTData']);
    Route::post('/gst-data/find', [ParkingController::class, 'findGSTData']);
    Route::post('/vehicle-reports/fetch', [ReportController::class, 'getVehicleReports']);
    Route::post('/tbl-vehicle/delete', [ReportController::class, 'deleteTblVehicles']);
    Route::get('/locations', [ReportController::class, 'locations']);
    Route::get('/vehicle-types/get/{location}', [SettingsController::class, 'fetchVtypes']);
    Route::post('/vehicles/summary', [ReportController::class, 'summary']);
    Route::get('/vehicle-rates/get/{location}', [SettingsController::class, 'fetchVrates']);
    Route::post('/reports/mpass', [ReportController::class, 'getMpassReports']);
    Route::post('/reports/tblPass', [ReportController::class, 'getTblpassReports']);
    Route::post('/reports/gst', [ReportController::class, 'getGstReports']);
    Route::post('/reports/gst', [ReportController::class, 'getGstBillReports']);
    Route::post('/delete/monPass', [ReportController::class, 'deleteMonPass']);
    Route::post('/delete/tblPass', [ReportController::class, 'deleteTblPass']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('/postLogout', [AuthController::class, 'postLogout']);
    Route::get('/auth/status/check', [AuthController::class, 'test']);


    Route::post('users/password/change', [AuthController::class, 'changePassword']);
    Route::post('users/staffs/create', [UserController::class, 'create']);
    Route::get('users/staffs/status/{id}', [UserController::class, 'status']);
    Route::get('/locations/verify', [UserController::class, 'verifyLocation']);
 
    Route::get('users/staffs/fetch/{location}', [UserController::class, 'fetch']);
    
});
