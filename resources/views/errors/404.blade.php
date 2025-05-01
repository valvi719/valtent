@extends('layouts.app')
@section('title', '404 error')
@section('content')
<div class="bg-white rounded-lg shadow-xl p-8 text-center">
        <div class="flex flex-col items-center mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-16 w-16 text-red-500 mb-4">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Oops! Page Not Found</h1>
            <p class="text-gray-600 mb-4">We can't seem to find the page you're looking for.</p>
        </div>
        <div class="space-x-4">
            <a href="{{ url('/') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-full focus:outline-none focus:shadow-outline">
                Go Home
            </a >
            <button onclick="window.history.back();" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-full focus:outline-none focus:shadow-outline">
                Go Back
            </button>
        </div>
    </div>
@endsection
