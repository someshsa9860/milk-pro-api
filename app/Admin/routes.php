<?php

use App\Admin\Controllers\CollectionSummaryController;
use App\Admin\Controllers\LedgerController;
use App\Admin\Controllers\LocationController;
use App\Admin\Controllers\OrderController;
use App\Admin\Controllers\OrderItemController;
use App\Admin\Controllers\RateController;
use App\Admin\Controllers\RetailerController;
use App\Admin\Controllers\UserController;
use App\Http\Controllers\OrderController as ControllersOrderController;
use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    // $router->get('/', function (){
    //     return redirect('/admin/orders/create');
    // })->name('home');
    $router->get('/', 'HomeController@index')->name('home');

    $router->resource('users', UserController::class);
    $router->resource('auth/users', UserController::class);
    $router->resource('farmers', RetailerController::class);
    $router->resource('rates', RateController::class);
    $router->resource('orders', OrderController::class);
    $router->resource('reports/ledger', LedgerController::class);
    $router->resource('reports/collection/summary', CollectionSummaryController::class);
    
    $router->resource('locations', LocationController::class);
    $router->get('/import/rates', [RateController::class,'import']);
    $router->get('/reports/export', [OrderController::class,'export']);




});
