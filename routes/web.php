<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CreatorController;
use App\Http\Controllers\ContentController;

Route::get('/', function () {
    return view('welcome');
});

//creator 
Route::get('creator_landing/', [CreatorController::class, 'showForm'])->name('show.form');
Route::post('creator_form/', [CreatorController::class, 'submitForm'])->name('form.submit');

// OTP Verification Routes
Route::get('verify-otp/{id}', [CreatorController::class, 'showOtpForm'])->name('verify.otp');
Route::post('verify-otp/{id}', [CreatorController::class, 'verifyOtp'])->name('verify.otp');

// Login Routes
Route::get('login', [CreatorController::class, 'showLoginForm'])->name('login');
Route::post('login', [CreatorController::class, 'login'])->name('login.submit');

// Logout Route
Route::post('logout', [CreatorController::class, 'logout'])->name('logout');

// Forgot Password Routes
Route::get('forgot-password', [CreatorController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('forgot-password', [CreatorController::class, 'sendResetLink'])->name('password.email');
Route::get('reset-password/{token}', [CreatorController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('reset-password', [CreatorController::class, 'resetPassword'])->name('password.update');

//content
Route::get('/content/create/{id}', [ContentController::class, 'create'])->name('content.create');
Route::post('/content/store/{id}', [ContentController::class, 'store'])->name('content.store');

// Route for viewing content
Route::get('/creator/{id}/content', [ContentController::class, 'index'])->name('creator.content');
