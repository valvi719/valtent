<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Creator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class CreatorController extends Controller
{
    public function showForm()
    {
        return view('creator_registration');
    }

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
         $id=Crypt::encrypt($user->id);
        // Redirect or return a response
        return redirect()->route('content.create',['id' => $id])->with('success', 'You are registered successfully!');
        }
    
}
