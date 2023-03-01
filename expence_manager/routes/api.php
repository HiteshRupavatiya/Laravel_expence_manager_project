<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountUsersController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [UserController::class, 'register']);

Route::post('/login', [UserController::class, 'login']);

Route::post('/logout', [UserController::class, 'logout']);

Route::get('/verifyAccount/{token}', [UserController::class, 'verify_user']);

Route::group(['account', 'middleware:sanctum'], function () {
    Route::post('/account', [AccountController::class, 'add_account']);
    Route::get('/account', [AccountController::class, 'show_all_accounts']);
    Route::get('/account/{id}', [AccountController::class, 'show_account']);
    Route::put('/account/{id}/edit', [AccountController::class, 'edit_account']);
    Route::delete('/account/{id}', [AccountController::class, 'destroy_account']);
});

Route::group(['account_user', 'middleware:sanctum'], function () {
    Route::post('/account_user', [AccountUsersController::class, 'add_account_user']);
    Route::get('/account_user', [AccountUsersController::class, 'show_all_account_user']);
    Route::get('/account_user/{id}', [AccountUsersController::class, 'show_account_user']);
    Route::put('/account_user/{id}/edit', [AccountUsersController::class, 'edit_account_user']);
    Route::delete('/account_user/{id}', [AccountUsersController::class, 'destroy_account_user']);
});

Route::group(['transaction', 'middleware:sanctum'], function () {
    Route::post('/transaction', [TransactionController::class, 'add_transaction']);
    Route::get('/transaction', [TransactionController::class, 'show_all_transaction']);
    Route::get('/transaction/{id}', [TransactionController::class, 'show_transaction']);
    Route::put('/transaction/{id}/edit', [TransactionController::class, 'edit_transaction']);
    Route::delete('/transaction/{id}', [TransactionController::class, 'destroy_transaction']);
});
