<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\QuotationController;

// Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Customer routes
    Route::post('/add-prescriptions', [PrescriptionController::class, 'store']);
    Route::get('/quotations', [QuotationController::class, 'getQuotations']);
    Route::get('/quotations/{id}', [QuotationController::class, 'ViewQuotation']);
    Route::put('/quotations/{id}/accept', [QuotationController::class, 'accept']);
    Route::put('/quotations/{id}/reject', [QuotationController::class, 'reject']);
    
    // Pharmacy routes
    Route::prefix('pharmacy')->group(function () {
        Route::get('/prescriptions', [PrescriptionController::class, 'getPrescriptions']);
        Route::get('/get-images/{id}', [PrescriptionController::class, 'getPrescriptionImages']);
        Route::get('/get-user/{id}', [PrescriptionController::class, 'getUser']);
        Route::post('/create-quotation', [QuotationController::class, 'store']);
        Route::get('/quotations', [QuotationController::class, 'getQuotations']);
        Route::get('/quotations/{id}', [QuotationController::class, 'ViewQuotation']);
    });


});
