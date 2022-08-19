<?php

use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\CompanyController;
use App\Http\Controllers\api\RegisterController;
use App\Http\Controllers\api\RoleController;
use App\Http\Controllers\api\StatusController;
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
    Route::post('/register', 'register');
    Route::post('/login', 'login');

});

Route::middleware('auth:sanctum')->group( function () {
    Route::post('/logout', [RegisterController::class, 'logout']);
    Route::post('/approved/{requests}', [UserController::class, 'registerAccount']);
    Route::delete('/disapproved/{requests}', [UserController::class, 'disapprove']);
    Route::put('/change_role/{user}', [UserController::class, 'changeRole']);
    Route::get('/admin_list', [UserController::class, 'adminList']);
    Route::get('/admin_details/{id}', [UserController::class, 'getAdminById']);
    Route::resource('/role', RoleController::class);
    Route::resource('/unit', UnitController::class);
    Route::resource('/company', CompanyController::class);
    Route::resource('/category', CategoryController::class);
    Route::resource('/status', StatusController::class);
});

