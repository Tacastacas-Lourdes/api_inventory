<?php

use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\CompanyController;
use App\Http\Controllers\api\RegisterController;
use App\Http\Controllers\api\RemarkController;
use App\Http\Controllers\api\RoleController;
use App\Http\Controllers\api\SpecificationController;
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
Route::controller(RegisterController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
});
Route::apiResource('role', RoleController::class);
Route::get('approval_list', [UserController::class, 'approvalList']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [RegisterController::class, 'logout']);
    Route::post('approved/{requests}', [UserController::class, 'registerAccount']);
    Route::delete('disapproved/{requests}', [UserController::class, 'disapprove']);
    Route::put('change_role/{user}', [UserController::class, 'changeRole']);
    Route::get('admin_details/{user}', [UserController::class, 'getAdminById']);
    Route::get('admin_list', [UserController::class, 'adminList']);
    Route::apiResource('unit', UnitController::class);
    Route::apiResource('company', CompanyController::class);
    Route::apiResource('category', CategoryController::class);
    Route::apiResource('status', StatusController::class);
    Route::apiResource('spec', SpecificationController::class);
    Route::apiResource('remark', RemarkController::class);
});
