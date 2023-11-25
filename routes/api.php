<?php

use App\Http\Controllers\AssistantController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CologneController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::middleware('auth:api')->group(function () {
    
});


Route::post("login", [UserController::class, 'login']);
Route::get("user/list", [UserController::class, 'list']);
Route::post("user/register", [UserController::class, 'register']);
Route::post("user/update/{id}", [UserController::class, 'editAccount']);
Route::post("user/changePassword/{id}", [UserController::class, 'updatePassword']);
Route::post("user/updateStatus/{id}", [UserController::class, 'updateStatus']);
Route::delete("user/delete/{id}", [UserController::class, 'delete']);

// Services clients
Route::get("service/list", [ServiceController::class, 'index']);
Route::post("service/add", [ServiceController::class, 'store']);
Route::post("service/evidences", [ServiceController::class, 'evidences']);
Route::post("service/update/{id}", [ServiceController::class, 'update']);
Route::get("service/delete/{id}", [ServiceController::class, 'destroy']);
Route::get("service/calendar", [ServiceController::class, 'calendar']);
Route::get("service/cancelOrder/{id}", [ServiceController::class, 'cancelOrder']);
Route::get("service/updateStatus/{id}/{status}", [ServiceController::class, 'updateStatus']);
Route::post("service/filter", [ServiceController::class, 'filter']);

// Services clients
Route::get("client/list", [ClientController::class, 'index']);
Route::get("client/getCombo", [ClientController::class, 'getData']);
Route::get("client/show/{id}", [ClientController::class, 'show']);
Route::post("client/add", [ClientController::class, 'store']);
Route::post("client/update/{id}", [ClientController::class, 'update']);
Route::get("client/delete/{id}", [ClientController::class, 'destroy']);


// Services drivers
Route::get("driver/list", [DriverController::class, 'index']);
Route::get("driver/getCombo", [DriverController::class, 'getData']);
Route::get("driver/show/{id}", [DriverController::class, 'show']);
Route::post("driver/add", [DriverController::class, 'store']);
Route::post("driver/update/{id}", [DriverController::class, 'update']);
Route::get("driver/delete/{id}", [DriverController::class, 'destroy']);

// Services assistant
Route::get("assistant/list", [AssistantController::class, 'index']);
Route::get("assistant/getCombo", [AssistantController::class, 'getData']);
Route::get("assistant/show/{id}", [AssistantController::class, 'show']);
Route::post("assistant/add", [AssistantController::class, 'store']);
Route::post("assistant/update/{id}", [AssistantController::class, 'update']);
Route::get("assistant/delete/{id}", [AssistantController::class, 'destroy']);

// Services clients
Route::get("unit/list", [UnitController::class, 'index']);
Route::get("unit/getCombo", [UnitController::class, 'getData']);
Route::get("unit/show/{id}", [UnitController::class, 'show']);
Route::post("unit/add", [UnitController::class, 'store']);
Route::post("unit/update/{id}", [UnitController::class, 'update']);
Route::get("unit/delete/{id}", [UnitController::class, 'destroy']);

// get all postal code
Route::get("code_postal/{code}", [CologneController::class, 'getCodes']);

// get data status
Route::get("status/", [StatusController::class, 'getData']);

// uplaod files
Route::post("upload/evidences/{id}/{status}", [ServiceController::class, 'uploadimage']);


