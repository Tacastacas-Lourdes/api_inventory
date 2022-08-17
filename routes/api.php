<?php

use App\Http\Controllers\api\CompanyController;
use App\Http\Controllers\api\RegisterController;
use App\Http\Controllers\api\UnitController;
use App\Http\Controllers\api\UserController;
use Illuminate\Support\Facades\Route;

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
Route::controller(RegisterController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');

});

Route::middleware('auth:sanctum')->group( function () {
    Route::resource('unit', UnitController::class);
    Route::resource('company', CompanyController::class);
    Route::post('logout', [RegisterController::class, 'logout']);
    Route::post('approved/{requests}', [UserController::class, 'registerAccount']);
    Route::delete('disapproved/{requests}', [UserController::class, 'disapprove']);
    Route::put('change_role/{user}', [UserController::class, 'changeRole']);
});

