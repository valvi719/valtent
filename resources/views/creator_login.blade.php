<!DOCTYPE html>
<html lang="en">
@extends('layouts.app')

@section('title', 'Login')

@section('content')
<body class="bg-gray-100 p-8">
    <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-lg">
        <!-- Logo Section -->
        <div class="text-center mb-6">
            <img src="{{ asset('images/valtent_logo.jpeg') }}" alt="App Logo" class="mx-auto h-16">
        </div>
        <h2 class="text-2xl font-bold text-center text-green-600 mb-6">Login</h2>

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf
            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600" required>
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600" required>
                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <button type="submit" class="w-full bg-green-600 text-white p-3 rounded-md hover:bg-green-700 focus:outline-none">
                    Log In
                </button>
            </div>

            

            @error('email')
                <div class="mt-4 text-center text-sm text-red-600">{{ $message }}</div>
            @enderror
        </form>
    </div>
</body>
@endsection
</html>
