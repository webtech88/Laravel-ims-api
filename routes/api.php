<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Registration and Forgot Password routes
Route::post('/register', 'Auth\RegisterController@register');
Route::post('/password/forgot', 'Auth\ForgotPasswordController@forgotPassword');

// Client Credentials Grant & Password Grant routes
Route::middleware(['client'])->group(function() {
    // Products
    Route::get('/products', 'ProductController@index');
    Route::get('/products/{sku}', 'ProductController@show');
		// Orders
		Route::post('/orders', 'OrderController@create');
		Route::get('/orders/{id}', 'OrderController@show')->where('id', '([A-Za-z]+)?[0-9]+');
});

// Password Grant routes
Route::middleware(['auth:api'])->group(function() {
    // Oauth & User
    Route::post('/password/reset', 'Auth\ResetPasswordController@resetPassword');
    Route::get('/logout', 'Auth\LoginController@logout');
    Route::get('/user', 'Auth\LoginController@getUser');
    Route::get('/users', 'UsersController');
		// Products
    Route::post('/products', 'ProductController@create');
    Route::put('/products/{sku}', 'ProductController@update');
    Route::delete('/products/{sku}', 'ProductController@delete');
    // Stock
		Route::post('/inventory/{sku}', 'ProductController@adjust');
    Route::get('/inventory/{sku}', 'ProductController@inventory');
		// Goods
    // TODO
		// Route::get('/supplies', 'SupplyController@index');
    // Route::post('/supplies', 'SupplyController@create');
    // Orders
    Route::get('/orders', 'OrderController@index');
		// Order Status
		Route::get('/orders/statuses', 'OrderController@statuses');
		Route::get('/orders/{id}/statuses', 'OrderController@orderStatusChanges')->where('id', '([A-Za-z]+)?[0-9]+');
    Route::post('/orders/{id}/status', 'OrderController@status')->where('id', '([A-Za-z]+)?[0-9]+');
    // Order Shipping
    Route::post('/orders/{order_id}/ship/{order_item_id}', 'OrderController@ship');
});
