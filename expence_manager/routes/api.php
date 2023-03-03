<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountUsersController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\FlareClient\Api;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(UserController::class)->prefix('user')->group(function () {
        Route::post('/changePassword', 'changePassword');
        Route::get('/user-profile', 'getUserProfile');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(AccountController::class)->prefix('account')->group(function () {
        Route::post('create', 'create');
        Route::get('list', 'list');
        Route::get('show/{id}', 'get');
        Route::put('update/{id}', 'update');
        Route::delete('delete/{id}', 'delete');
    });

    Route::controller(AccountUsersController::class)->prefix('accountUser')->group(function () {
        Route::post('create', 'create');
        Route::get('list', 'list');
        Route::get('show/{id}', 'get');
        Route::put('update/{id}', 'update');
        Route::delete('delete/{id}', 'delete');
    });

    Route::controller(TransactionController::class)->prefix('transaction')->group(function () {
        Route::post('create', 'create');
        Route::get('list', 'list');
        Route::get('show/{id}', 'get');
        Route::put('update/{id}', 'update');
        Route::delete('delete/{id}', 'delete');
    });
});

Route::controller(AuthController::class)->prefix('user')->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::get('verifyAccount/{token}', 'verifyUser');
    Route::post('forgotPassword', 'forgotPassword');
    Route::post('resetPassword', 'resetPassword');
});
