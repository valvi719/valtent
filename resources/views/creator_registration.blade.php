<!DOCTYPE html>
<html lang="en">
@extends('layouts.app')
@section('title', 'Creator - Signup')
@section('content')
<body class="bg-gray-100 p-8">
    <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-lg">
        <!-- Logo Section -->
        <div class="text-center mb-6">
            <img src="{{ asset('images/valtent_logo.jpeg') }}" alt="App Logo" class="mx-auto h-16">
        </div>
        <h2 class="text-2xl font-bold text-center text-green-600 mb-6">Sign Up</h2>

        <form action="{{ route('form.submit') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" id="name" name="name" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600" required>
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                <input type="text" id="phone" name="phone" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600" required>
                @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600" required>
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
             
            <div class="mb-4">
                <label for="account_number" class="block text-sm font-medium text-gray-700">Account Number</label>
                <input type="text" id="account_number" name="account_number" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600" required>
                @error('account_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="ifsc_code" class="block text-sm font-medium text-gray-700">IFSC Code</label>
                <input type="text" id="ifsc_code" name="ifsc_code" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600" required>
                @error('ifsc_code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="profile_photo" class="block text-sm font-medium text-gray-700">Profile Photo</label>
                <input type="file" id="profile_photo" name="profile_photo" accept="image/*" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600">
                @error('profile_photo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

             <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600" required>
                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600" required>
                @error('password_confirmation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                <input type="text" id="address" name="address" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600" required>
                @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                <input type="text" id="city" name="city" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600" required>
                @error('city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="w-full bg-green-600 text-white p-3 rounded-md hover:bg-green-700 focus:outline-none">Sign Up</button>
        </form>
    </div>
</body>
@endsection
</html>
