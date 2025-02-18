<!DOCTYPE html>
<html lang="en">
@extends('layouts.app')
@section('title', 'Verify OTP')
@section('content')
<body class="bg-gray-100 p-8">
    <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-center text-green-600 mb-6">Verify OTP</h2>

        <form action="{{ route('verify.otp', ['id' => $id]) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="otp" class="block text-sm font-medium text-gray-700">Enter OTP</label>
                <input type="text" id="otp" name="otp" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600" required>
                @error('otp') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="w-full bg-green-600 text-white p-3 rounded-md hover:bg-green-700 focus:outline-none">Verify OTP</button>
        </form>
    </div>
</body>
@endsection
</html>
