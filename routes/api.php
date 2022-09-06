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
use Spatie\Activitylog\Models\Activity;

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
Route::get('/', function () {
    return Activity::all();
});
Route::controller(RegisterController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
});
Route::apiResource('role', RoleController::class);
Route::get('user/approval_request', [UserController::class, 'approvalList']);
Route::get('user/deactivated_account', [UserController::class, 'deactivatedAccount']);
Route::post('user/{requestor}/approve', [UserController::class, 'approveAccount']);
Route::post('user/{requestor}/disapprove', [UserController::class, 'disapproveAccount']);
Route::get('user/disapproved_account', [UserController::class, 'disapprovedList']);
Route::apiResource('company', CompanyController::class);
Route::apiResource('category', CategoryController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [RegisterController::class, 'logout']);
    Route::put('user/{user}/change_role', [UserController::class, 'changeRole']);
    Route::put('user/{user}/activate', [UserController::class, 'activate']);
    Route::put('user/{user}/deactivate', [UserController::class, 'deactivate']);
    Route::get('user/{user}/admin_details', [UserController::class, 'getUserById']);
    Route::get('user/admin_list', [UserController::class, 'adminList']);
    Route::get('user/employee_list', [UserController::class, 'employeeList']);
//    Route::put('admin_updateProfile', [UserController::class, 'updateProfile']);
//    Route::put('employee_updateProfile', [EmployeeController::class, 'update']);
    Route::apiResource('unit', UnitController::class);
    Route::apiResource('status', StatusController::class);
    Route::apiResource('spec', SpecificationController::class);
    Route::apiResource('remark', RemarkController::class);
});
