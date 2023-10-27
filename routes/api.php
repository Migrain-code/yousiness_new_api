<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\PaymentController;
use \App\Http\Controllers\Api\DetailSetupController;
use \App\Http\Controllers\Api\BusinessServiceController;
use \App\Http\Controllers\Api\PersonalController;
use App\Http\Controllers\Api\AuthController;
use \App\Http\Controllers\Api\SetupController;
use \App\Http\Controllers\Api\PackageSaleController;
use \App\Http\Controllers\Api\CustomerController;
use \App\Http\Controllers\Api\BusinessPackageController;
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
require_once "guards/personal.php";
Route::prefix('business')->group(function (){
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('check-phone', [AuthController::class, 'register']);
        Route::post('verify', [AuthController::class, 'verify']);
    });

    Route::middleware('auth:business')->group(function () {

        Route::get('user', [AuthController::class, 'user']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('/packages', [BusinessPackageController::class, 'index']);
        Route::controller(SetupController::class)->prefix('setup')->group(function (){
            Route::get('/get', 'get');
            Route::post('/update', 'update');
        });
        /*Route::controller(OfficialCreditCardController::class)->prefix('cart')->group(function (){
            Route::get('/', 'index');
            Route::post('/get', 'get');
            Route::post('/delete', 'delete');
            Route::post('/save', 'store');
            Route::post('/update', 'update');
        });*/
        Route::controller(PaymentController::class)->prefix('payment')->group(function (){
            Route::get('/', 'index');
            Route::post('/pay', 'pay');
        });

        Route::controller(DetailSetupController::class)->prefix('detail-setup')->group(function (){
            Route::get('/step-1/get', 'index');
            Route::post('/step-1/update', 'step1');
        });

        Route::controller(BusinessServiceController::class)->prefix('business-service')->group(function (){
            Route::get('/', 'step2Get');
            Route::post('/get', 'step2GetService');
            Route::post('/add', 'step2AddService');
            Route::post('/update', 'step2UpdateService');
            Route::post('/delete', 'step2DeleteService');
            /*Route::post('/update/logo', 'updateLogo');*/
        });

        Route::controller(PersonalController::class)->prefix('personal')->group(function (){
            Route::get('/', 'step3Get');
            Route::post('/get', 'step3GetPersonal');
            Route::post('/add', 'step3AddPersonal');
            Route::post('/update', 'step3UpdatePersonal');
            Route::post('/delete', 'step3DeletePersonal');
        });

        Route::controller(PackageSaleController::class)->prefix('package-sale')->group(function (){
           Route::get('/', 'index');
           Route::get('/create-packet', 'createPacket');
           Route::post('/payments', 'payments');
           Route::post('/usages', 'usages');
           Route::post('/add-packet', 'addPacket');
           Route::post('/add-payment', 'addPayment');
           Route::post('/add-usage', 'addUsage');
        });

        Route::controller(CustomerController::class)->prefix('customer')->group(function (){
            Route::get('/', 'index');
            Route::post('/create', 'create');
            Route::post('/edit', 'edit');
            Route::post('/update', 'update');
            Route::post('/delete', 'destroy');
        });


    });

});


Route::prefix('city')->group(function (){
    Route::get('list', [CityController::class, 'index']);
    Route::post('get', [CityController::class, 'get']);
});

