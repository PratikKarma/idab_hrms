<?php

use App\Http\Controllers\AttendanceEmployeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceRequestController;
use App\Http\Controllers\Auth\SsoController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\EmployeeController;

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

Route::post('sso-login', [SsoController::class, 'ssoLogin'])->name('sso.login');
Route::post('attendance-requests', [AttendanceRequestController::class, 'store'])->middleware(['auth:sanctum'])->name('attendance-requests.store');
Route::post('attendanceemployee/attendance', [AttendanceEmployeeController::class, 'attendance'])->name('attendanceemployee.attendance')->middleware(['auth', 'XSS', 'throttle:1,1']);
Route::post('company/register', [RegisteredUserController::class, 'companyStore'])->name('company.register');
Route::post('employee/register', [RegisteredUserController::class, 'employeeStore'])->name('employee.register');
Route::post('getemployee', [EmployeeController::class, 'getEmployeesApi'])->middleware(['auth:sanctum'])->name('getemployee');
Route::post('/break-requests', [AttendanceEmployeeController::class, 'handleBreak'])->middleware(['auth:sanctum']);
Route::post('passcode-verification', [AttendanceEmployeeController::class, 'passcodeVerify'])->middleware(['auth:sanctum']);
// Get current break status
Route::get('/break-status', [AttendanceEmployeeController::class, 'getBreakStatus'])->middleware(['auth:sanctum']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
