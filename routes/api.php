<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\InvestorController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\DataController;
use App\Http\Controllers\Api\ReportsController;
use App\Http\Controllers\Api\LicenseReportsController;

Route::get('/stats', [ApiController::class, 'stats']);
Route::get('/investment-packages', [ApiController::class, 'investmentPackages']);

// Data endpoints to mirror original FastAPI
Route::post('/users', [DataController::class, 'createUser']);
Route::get('/users', [DataController::class, 'getUsers']);

Route::post('/investments', [DataController::class, 'createInvestment']);
Route::get('/investments', [DataController::class, 'getInvestments']);
Route::get('/investments/user/{user_id}', [DataController::class, 'getUserInvestments']);

Route::post('/business-licenses', [DataController::class, 'createBusinessLicense']);
Route::get('/business-licenses', [DataController::class, 'getBusinessLicenses']);

Route::post('/register-investor', [InvestorController::class, 'register']);
Route::post('/upload-document', [UploadController::class, 'upload']);

// Admin reports
Route::get('/admin/reports', [ReportsController::class, 'index']);
Route::get('/admin/license-reports', [LicenseReportsController::class, 'index']);
