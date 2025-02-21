<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Creator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\OtpVerificationMail;

class CreatorController extends Controller
{
    public function showForm()
    {
        return view('creator_registration');
    }

    // Handle Form Submission and Send OTP
    public function submitForm(Request $request)
    {
        
        // Validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|unique:creators,email',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
        ]);

        // Create the user
        $user = Creator::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'address' => $validated['address'],
            'city' => $validated['city'],
        ]);

        // Generate OTP
        $otp = rand(100000, 999999); // Generate a 6-digit OTP
        $expiresAt = Carbon::now()->addMinutes(10); // OTP expires in 10 minutes

        // Save OTP to database
        $user->otp = $otp;
        $user->otp_expires_at = $expiresAt;
        $user->save();

        // Send OTP to user's email
        Mail::to($user->email)->send(new OtpVerificationMail($otp));

        // Encrypt user ID to pass to the next step
        $id = Crypt::encrypt($user->id);

        // Redirect to OTP verification page
        return redirect()->route('verify.otp', ['id' => $id])->with('success', 'You are registered successfully! Check your email for OTP.');
    }
    // Show OTP Verification Form
    public function showOtpForm($id)
    {
        return view('verify_otp', ['id' => $id]);
    }
     // Handle OTP Verification
    public function verifyOtp(Request $request, $id)
    {
        // Decrypt the user ID
        $userId = Crypt::decrypt($id);
        $user = Creator::findOrFail($userId);

        // Validate OTP
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        // Check if OTP matches and is not expired
        if ($user->otp == $request->input('otp') && $user->otp_expires_at > Carbon::now()) {
            // OTP verified successfully
            $user->email_verified_at = Carbon::now(); // Mark the email as verified
            $user->save();

            return redirect()->route('content.create', ['id' => $id])->with('success', 'Email verified successfully!');
        }

        return back()->withErrors(['otp' => 'Invalid or expired OTP']);
    }
    
    public function showLoginForm()
    {
        return view('creator_login');
    }
    
    public function login(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Check if the email exists
        $user = Creator::where('email', $validated['email'])->first();

        // If user exists and password is correct, log in
        if ($user && Hash::check($validated['password'], $user->password)) {
            Auth::login($user);
            $id = Crypt::encrypt($user->id);
            return redirect()->route('content.create', ['id' => $id]); // Redirect to a dashboard or home page after login
        }

        // If login fails
        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

}
