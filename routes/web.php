<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CreatorController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\RazorpayController;

// Route::get('/', function () {
//     return view('welcome');
// });

// Login Routes
Route::get('login', [CreatorController::class, 'showLoginForm'])->name('login');
Route::post('login', [CreatorController::class, 'login'])->name('login.submit');

Route::middleware('auth')->group(function () {
//creator 
Route::get('creator_landing/', [CreatorController::class, 'showForm'])->name('show.form');
Route::post('creator_form/', [CreatorController::class, 'submitForm'])->name('form.submit');

// OTP Verification Routes
Route::get('verify-otp/{id}', [CreatorController::class, 'showOtpForm'])->name('verify.otp');
Route::post('verify-otp/{id}', [CreatorController::class, 'verifyOtp'])->name('verify.otp');

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
Route::get('/', [ContentController::class, 'showall'])->name('content.showall');
Route::post('content/{content}/like', [ContentController::class, 'toggleLike'])->name('like.toggle');
Route::post('/content/{contentId}/extract', [ContentController::class, 'extract'])->name('content.extract');


// Route for viewing content
Route::get('/creator/{id}/content', [ContentController::class, 'index'])->name('creator.content');
Route::get('/modalcontent/{content_id}', [ContentController::class, 'modalContent'])->name('modalcontent');

//Razorpay 
 Route::get('payment-form', [RazorpayController::class, 'showPaymentForm']);
 Route::post('create-order', [RazorpayController::class, 'createOrder']);
 Route::post('payment-success', [RazorpayController::class, 'paymentSuccess']);
 Route::get('wallet', [RazorpayController::class, 'showwallet'])->name('wallet.show');
 Route::post('/create-razorpay-order', [RazorpayController::class, 'createRazorpayOrder']);
 Route::post('/process-razorpay-payment', [RazorpayController::class, 'processRazorpayPayment']);
 Route::post('/transfer-funds', [RazorpayController::class, 'transferFunds']);
});

