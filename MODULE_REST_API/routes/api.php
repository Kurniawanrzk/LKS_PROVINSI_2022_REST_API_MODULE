<?php

use App\Http\Controllers\API\RequestConsultation;
use App\Http\Controllers\API\SocAuthController;
use App\Http\Controllers\API\SpotController;
use App\Http\Controllers\API\VaccinationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix("v1")->group(function() {
    Route::prefix("auth")->group(function() {
        Route::post("login", [SocAuthController::class, "login"]);
        Route::post("logout/{token?}", [SocAuthController::class, "logout"]);
    });
Route::middleware("tokenMiddleware")->group(function() {
    Route::prefix("consultations")->group(function() {
        Route::post("/{token?}", [RequestConsultation::class, "CreateConsultation"]);
        Route::get("/{token?}", [RequestConsultation::class, "GetConsultation"]);
    });

    Route::prefix("spots")->group(function(){
        Route::get("/{spot_id}/{token?}/{date?}", [SpotController::class, "GetDetailSpot"]);
        Route::get("/{token?}", [SpotController::class, "GetSpot"]);
    });

    Route::prefix("vaccinations")->group(function(){
        Route::post("/{token?}", [VaccinationController::class, "RegisterVaccination"]);
        Route::get("/{token?}", [VaccinationController::class, "GetAllVaccination"]);
    });
});
});