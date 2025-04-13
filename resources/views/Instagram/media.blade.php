<!DOCTYPE html>
<html lang="en">
@extends('layouts.app')

@section('title', 'Wallet')

@section('content')
<body>
<div class="parent-container">
    <div class="wallet-container">
        <h2>Instagram Media</h2>
        
        @foreach ($media['data'] as $item)
            <div>
                <p>{{ $item['caption'] ?? 'No caption' }}</p>
                @if ($item['media_type'] === 'VIDEO')
                    <video width="300" controls>
                        <source src="{{ $item['media_url'] }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                @else
                    <img src="{{ $item['media_url'] }}" width="300" />
                @endif
                <hr>
            </div>
        @endforeach
        
    </div>
  </div>
</div>
</body>
@endsection
</html>
