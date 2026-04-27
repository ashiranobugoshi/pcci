<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\Api\LocalAuthController;
use App\Http\Controllers\Api\LeadershipController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\TreasurerProxyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::get('/v1/membership-types', function () {
    return response()->json([
        'data' => [
            [
                "id" => 1,
                "name" => "Micro",
                "price" => "500.00",
                "duration_in_months" => 12,
                "renewal_price" => null,
                "notes" => "1-year membership only",
                "created_at" => "2026-03-17 06:10:18"
            ],
            [
                "id" => 2,
                "name" => "Small Enterprises",
                "price" => "5000.00",
                "duration_in_months" => 12,
                "renewal_price" => "3000.00",
                "notes" => "Initial fee P5,000, renewal P3,000",
                "created_at" => "2026-03-17 06:10:18"   
            ]
        ]
    ]);
});

Route::get('/login', function () {
    return response()->json([
        'message' => 'Use POST /api/login with email and password.',
    ]);
});

Route::post('/login', [LocalAuthController::class, 'login']);

// Password OTP request proxy for Treasurer Security > Update Password
Route::post('/user/confirm-password-change', [TreasurerProxyController::class, 'confirmPasswordChange']);
Route::post('/user/verify-password-otp', [TreasurerProxyController::class, 'verifyPasswordOtp']);
Route::post('/user/request-password-change', [TreasurerProxyController::class, 'requestPasswordChange']);

Route::prefix('v1')->group(function () {

    Route::match(['post', 'put'], '/user/change-info', [TreasurerProxyController::class, 'changeInfo']);
    Route::post('/payments', [TreasurerProxyController::class, 'confirmApplicantPayment']);

    // Leadership Route
    Route::get('/leadership', [LeadershipController::class, 'index']);

    // --> NEW MEMBERS ROUTE <--
    Route::get('/members', [MemberController::class, 'index']);
    Route::post('/members', [MemberController::class, 'store']);

    // Add this to allow Admin to Approve/Reject via the Proxy Controller
    Route::put('/applicants/{id}', [TreasurerProxyController::class, 'updateTransaction']);

    // Route::get('/v1/business/{id}', [BusinessController::class, 'show']); // BusinessController not yet created
    // Your existing Event routes
    Route::get('/events', [EventController::class, 'index']);
    Route::post('/events', [EventController::class, 'store']);
    Route::post('/events/{id}', [EventController::class, 'update']);

    // Treasurer proxy route is in web.php
});
