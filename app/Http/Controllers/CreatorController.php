<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Creator;
use App\Models\Follower;
use App\Models\Following;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
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
        // Validate the form inputs
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:creators,username',
            'phone' => 'required|string|max:15',
            'account_number' => 'required|string|max:50',
            'ifsc_code' => 'required|string|max:20',
            'email' => 'required|email|unique:creators,email',
            'password' => 'required|string|min:8|confirmed',
            // 'address' => 'required|string|max:255',
            // 'city' => 'required|string|max:255',
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate the profile photo
            'birthday' => ['required', 'date', function ($attribute, $value, $fail) {
                if (\Carbon\Carbon::parse($value)->diffInYears(\Carbon\Carbon::now()) < 13) {
                    $fail('You must be at least 13 years old to register.');
                }
            }],
        ]);

        // Handle file upload
        $profilePhotoPath = null;
        if ($request->hasFile('profile_photo') && $request->file('profile_photo')->isValid()) {
            // Handle the file upload
            $profilePhoto = $request->file('profile_photo');
            $profilePhotoName = time() . '-' . $profilePhoto->getClientOriginalName();
            
            // Store the photo in 'public/profile_photos' and get the file path
            $profilePhotoPath = $profilePhoto->storeAs('public/profile_photos', $profilePhotoName);
        }

        // Create the user
        $user = Creator::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'account_number' => $validated['account_number'],
            'ifsc_code' => $validated['ifsc_code'],
            'password' => Hash::make($validated['password']),
            // 'address' => $validated['address'],
            // 'city' => $validated['city'],
            'profile_photo' => basename($profilePhotoPath),  // Save only the file name (basename) to the database
            'birthday' => $validated['birthday'],
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

    public function checkUsername(Request $request)
    {
        $exists = Creator::where('username', $request->username)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function checkEmail(Request $request)
    {
        $exists = Creator::where('email', $request->email)->exists();
        return response()->json(['exists' => $exists]);
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
            // Redirect to the creator's content page using the encrypted ID
            return redirect()->route('creator.content', ['username' => Auth::user()->username]); // Redirect to the content route
        }

        // If login fails
        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    }
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    public function showForgotPasswordForm()
    {
        return view('creator_forgot_password');  // Return the Forgot Password view
    }
    
    public function sendResetLink(Request $request)
    {
        // Validate the email
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        // Normalize the email (trim spaces and convert to lowercase)
        $email = strtolower(trim($validated['email']));

        // Log the normalized email for debugging
        Log::info('Password reset attempt for email: ' . $email);

        // Check if the email exists in the database with a case-insensitive query
        $user = DB::table('creators')->whereRaw('LOWER(email) = ?', [$email])->first();

        if ($user) {
            // Send the password reset link to the email
            $response = Password::sendResetLink([
                'email' => $email,
            ]);
            Log::error('Password reset link response: ' . $response);

            // Check the response from the password reset link
            if ($response == Password::RESET_LINK_SENT) {
                return back()->with('status', 'We have sent you a password reset link!');
            } else {
                return back()->withErrors(['email' => 'An error occurred while sending the password reset link.']);
            }
        } else {
            return back()->withErrors(['email' => 'We could not find a user with that email address.']);
        }
    }
    public function showResetPasswordForm($token)
    {
        return view('creator_reset_password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|confirmed|min:8',
            'token' => 'required',
        ]);

        // Reset the user's password
        $response = Password::reset($validated, function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        });

        if ($response == Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Your password has been reset successfully!');
        } else {
            return back()->withErrors(['email' => 'We could not reset your password. Please try again.']);
        }
    }

    //Edit Profile
    public function editProfile()
    {
        $creator = auth()->user();
        return view('edit-profile', compact('creator'));
    } 

    public function updateProfile(Request $request)
    {
        $rules = [
            'relationship_status' => 'nullable|in:Single,In a Relationship With,Engaged To,Married To,It\'s Complicated,In an Open Relationship With',
            'relationship_status_since' => 'nullable|date|before_or_equal:today',
            'bio' => 'nullable|string|max:1000',
            'profile_photo' => 'nullable|image|max:2048',
        ];

        // Add validation for relationship_with only if a relationship status that implies one is selected
        if (in_array($request->relationship_status, ['In a Relationship With', 'Engaged To', 'Married To', 'In an Open Relationship With']) && $request->filled('relationship_with')) {
            $rules['relationship_with'] = [
                'required',
                'string',
                'max:255',
                Rule::exists('creators', 'username')->whereNot('id', Auth::id()), // Ensure username exists and is not the current user's
            ];
        } else {
            $rules['relationship_with'] = 'nullable|string|max:255'; // Allow nullable if no relationship selected
        }

        $request->validate($rules);

        $creator = auth()->user();

        // Only handle the profile photo if uploaded
        if ($request->hasFile('profile_photo') && $request->file('profile_photo')->isValid()) {
            $profilePhoto = $request->file('profile_photo');
            $profilePhotoName = time() . '-' . $profilePhoto->getClientOriginalName();
            $profilePhotoPath = $profilePhoto->storeAs('public/profile_photos', $profilePhotoName);
            $creator->profile_photo = basename($profilePhotoPath);
        }

        $creator->relationship_status = $request->relationship_status;
        $creator->relationship_status_since = $request->relationship_status_since;
        $creator->relationship_with = $request->relationship_with;
        $creator->bio = $request->bio;
        $creator->save();

        return redirect()->route('creator.content', ['username' => Auth::user()->username])
            ->with('success', 'Profile updated successfully!');
    }
    
    //Search Creators
    public function searchCreators(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return response()->json([]);
        }

        $loggedInUserId = Auth::id();
        

        $creators = Creator::where('username', 'like', '%' . $query . '%')
            ->orWhere('name', 'like', '%' . $query . '%')
            ->limit(10)
            ->get()
            ->map(function ($creator) use ($loggedInUserId) {
                return [
                    'id' => $creator->id,
                    'username' => $creator->username,
                    'loggedInUserId' => $loggedInUserId,
                    'profile_photo' => $creator->profile_photo
                        ? asset('storage/public/profile_photos/' . $creator->profile_photo)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($creator->username) . '&background=random&color=fff',
                        
                ];
            });

        return response()->json($creators);
    }

    public function searchCreators2(Request $request)
    {
        $query = $request->input('query');
        $loggedInUserId = Auth::id(); // Get the ID of the currently logged-in user

        $creators = Creator::where('id', '!=', $loggedInUserId) // Exclude the logged-in user
            ->where(function ($q) use ($query) {
                $q->where('username', 'like', '%' . $query . '%')
                    ->orWhere('name', 'like', '%' . $query . '%');
            })
            ->limit(10)
            ->get(['id', 'username', 'name','profile_photo']);

        return response()->json($creators);
    }

    public function fetchFollowers($creatorId, Request $request)
    {
        $query = Follower::where('cre_id', $creatorId)
            ->with('followerUser:id,username,name,profile_photo');

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->whereHas('followerUser', function ($q) use ($searchTerm) {
                $q->where('username', 'like', "%{$searchTerm}%")
                ->orWhere('name', 'like', "%{$searchTerm}%");
            });
        }

        $followers = $query->limit(50)
            ->get()
            ->pluck('followerUser');

        return response()->json($followers);
    }

    public function fetchFollowing($creatorId, Request $request)
    {
        $query = Following::where('cre_id', $creatorId)
            ->with('followingUser:id,username,name,profile_photo');

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->whereHas('followingUser', function ($q) use ($searchTerm) {
                $q->where('username', 'like', "%{$searchTerm}%")
                ->orWhere('name', 'like', "%{$searchTerm}%");
            });
        }

        $following = $query->limit(50)
            ->get()
            ->pluck('followingUser');

        return response()->json($following);
    }

}
