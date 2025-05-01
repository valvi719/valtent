<!DOCTYPE html>
<html lang="en">
@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<body class="bg-gray-100 p-8">
    <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-lg">
        <!-- Logo Section -->
        <div class="text-center mb-6">
            <img src="{{ asset('images/valtent_logo.jpeg') }}" alt="App Logo" class="mx-auto h-16">
        </div>
        <h2 class="text-2xl font-bold text-center text-green-600 mb-6">Reset Your Password</h2>

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600" required>
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password" id="password" name="password" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600" required>
                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600" required>
                <div id="password-match-status" class="text-sm mt-1"></div>
                @error('password_confirmation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <button type="submit" class="w-full bg-green-600 text-white p-3 rounded-md hover:bg-green-700 focus:outline-none">
                    Reset Password
                </button>
            </div>
        </form>
    </div>
    <script>
            // Password match check
            $('#password_confirmation').on('input', function() {
                var password = $('#password').val();
                var confirmPassword = $(this).val();
                if (confirmPassword.length > 0) {
                    if (password === confirmPassword) {
                        if ($('#password-match-status').length == 0) {
                            $('<div id="password-match-status" class="text-sm mt-1"></div>').insertAfter('#password_confirmation');
                        }
                        $('#password-match-status').text('Password matched ✅').css('color', 'green');
                    } else {
                        if ($('#password-match-status').length == 0) {
                            $('<div id="password-match-status" class="text-sm mt-1"></div>').insertAfter('#password_confirmation');
                        }
                        $('#password-match-status').text('Password does not match ❌').css('color', 'red');
                    }
                } else {
                    $('#password-match-status').text('');
                }
            });
    </script>
</body>

@endsection
</html>
