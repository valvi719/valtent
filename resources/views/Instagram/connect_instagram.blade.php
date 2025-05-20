<!DOCTYPE html>
<html lang="en">
@extends('layouts.app')

@section('title', 'Wallet')

@section('content')
<body>
<div class="parent-container">
    <div class="wallet-container">
        <h2>Connect Instagram</h2>
        
        <a href="{{ url('/auth/instagram') }}">
            <button class="button">Login with Instagram</button>
        </a>
        
    </div>
  </div>
</div>
</body>
@endsection
</html>
