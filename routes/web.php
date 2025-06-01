<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CreatorController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\RazorpayController;
use App\Http\Controllers\InstagramController;
use Illuminate\Http\Request;


// Route::get('/', function () {
//     return view('welcome');
// });

// Login Routes
Route::get('login', [CreatorController::class, 'showLoginForm'])->name('login');
Route::post('login', [CreatorController::class, 'login'])->name('login.submit');

//creator
Route::get('creator_landing/', [CreatorController::class, 'showForm'])->name('show.form');
Route::post('creator_form/', [CreatorController::class, 'submitForm'])->name('form.submit');
// AJAX check for username
Route::post('check-username', [CreatorController::class, 'checkUsername'])->name('check.username');
// AJAX check for email
Route::post('check-email', [CreatorController::class, 'checkEmail'])->name('check.email');


// OTP Verification Routes
Route::get('verify-otp/{id}', [CreatorController::class, 'showOtpForm'])->name('verify.otp');
Route::post('verify-otp/{id}', [CreatorController::class, 'verifyOtp'])->name('verify.otp');

// Forgot Password Routes
Route::get('forgot-password', [CreatorController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('forgot-password', [CreatorController::class, 'sendResetLink'])->name('password.email');
Route::get('reset-password/{token}', [CreatorController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('reset-password', [CreatorController::class, 'resetPassword'])->name('password.update');

Route::middleware('auth')->group(function () {
     
    // Logout Route
    Route::post('logout', [CreatorController::class, 'logout'])->name('logout');

    //Razorpay 
    Route::get('payment-form', [RazorpayController::class, 'showPaymentForm']);
    Route::post('create-order', [RazorpayController::class, 'createOrder']);
    Route::post('payment-success', [RazorpayController::class, 'paymentSuccess']);
    Route::get('wallet', [RazorpayController::class, 'showwallet'])->name('wallet.show');
    Route::post('/create-razorpay-order', [RazorpayController::class, 'createRazorpayOrder']);
    Route::post('/process-razorpay-payment', [RazorpayController::class, 'processRazorpayPayment']);
    Route::post('/transfer-funds', [RazorpayController::class, 'transferFunds']);

    //content
    Route::get('/content/create/{id}', [ContentController::class, 'create'])->name('content.create');
    Route::post('/content/store/{id}', [ContentController::class, 'store'])->name('content.store');
    Route::get('/', [ContentController::class, 'showall'])->name('content.showall');
    Route::post('content/{content}/like', [ContentController::class, 'toggleLike'])->name('like.toggle');
    Route::post('/content/{contentId}/extract', [ContentController::class, 'extract'])->name('content.extract');
    Route::post('/donate', [ContentController::class, 'donate']);
    Route::get('/content/{id}/donors/search', [ContentController::class, 'searchDonors']);
    Route::post('/moderation-callback', [ContentController::class, 'handle'])->name('moderation.callback');


    // Route for viewing content
    Route::get('/me/{username}', [ContentController::class, 'index'])->name('creator.content');
    Route::get('/modalcontent/{content_id}', [ContentController::class, 'modalContent'])->name('modalcontent');
    Route::delete('/content/{id}', [ContentController::class, 'destroy'])->name('content.destroy');
    Route::get('/{username}', [ContentController::class, 'showProfile'])->name('creator.profile');
    Route::post('/creator/{creator}/toggle-follow', [ContentController::class, 'toggleFollow'])->name('creator.toggleFollow');
    Route::post('/follow/{creator}', [ContentController::class, 'follow'])->name('follow');
    Route::post('/unfollow/{creator}', [ContentController::class, 'unfollow'])->name('unfollow');

    //Instagram
    Route::get('/connect/instagram', [InstagramController::class, 'connectInstagramIndex']);
    Route::get('/auth/instagram', [InstagramController::class, 'redirectToFacebook']);
    Route::get('/auth/instagram/callback', [InstagramController::class, 'handleCallback']);
    
    //  Route::get('/instagram/media', [InstagramController::class, 'getInstagramMedia']);

    Route::get('/instagram/webhook', function (Request $request) {
        $verify_token = env('INSTAGRAM_VERIFY_TOKEN');

        if ($request->hub_verify_token === $verify_token) {
            return response($request->hub_challenge, 200);
        }
        
        return response('Verification failed.', 403);
    });

    //Edit Profile
    
    Route::get('/creator/edit-profile', [CreatorController::class, 'editProfile'])->name('creator.editProfile');
    Route::put('/creator/update-profile', [CreatorController::class, 'updateProfile'])->name('creator.updateProfile');

    //Search
    Route::get('/search/creators', [CreatorController::class, 'searchCreators']);
    Route::get('/creator/search', [CreatorController::class, 'searchCreators2'])->name('creator.search');
    

    //followers and following
    Route::get('/creator/{creatorId}/followers', [CreatorController::class, 'fetchFollowers']);
    Route::get('/creator/{creatorId}/following', [CreatorController::class, 'fetchFollowing']);

});
Route::get('/privacy-policy', [InstagramController::class, 'privacypolicy']);


