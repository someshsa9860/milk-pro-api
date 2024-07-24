<?php

use App\Admin\Controllers\OrderController;
use App\Admin\Controllers\OrderItemController;
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

    $router->get('/', function (){
        return redirect('/admin/orders/create');
    })->name('home');
    $router->resource('users', UserController::class);
    $router->resource('retailers', RetailerController::class);
    $router->resource('orders', OrderController::class);
    $router->resource('order-items', OrderItemController::class);



});
