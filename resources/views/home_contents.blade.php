@extends('layouts.app')

@section('title', 'Home')

@section('content')

<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-semibold text-center mb-6">Welcome to Your Feed</h1>

    @if($contents->isEmpty())
        <p class="text-center text-gray-600">No content available. Please follow some creators!</p>
    @else
        @foreach($contents as $content)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                <!-- Content Header -->
                <div class="flex items-center p-4 border-b">
                    <img src="{{ asset('storage/public/profile_photos/' . $content->creator->profile_photo) }}" alt="User Avatar" class="w-10 h-10 rounded-full mr-3">
                    <div class="flex flex-col">
                        <a href="#" class="font-semibold">{{ $content->name }}</a>
                        <p class="text-gray-500 text-sm">{{ $content->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                <div class="relative">
                    @if($content->type == 'Media')
                        @if(str_contains($content->value, '.mp4'))
                            <video class="w-full object-cover" controls>
                                <source src="{{ asset('storage/' . $content->value) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @else
                            <img class="w-full object-cover" src="{{ asset('storage/' . $content->value) }}" alt="{{ $content->name }}">
                        @endif
                    @else
                        <div class="w-full h-64 bg-gray-300 flex items-center justify-center">
                            <span class="text-white">NFT</span>
                        </div>
                    @endif
                </div>

                <div class="p-4">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex space-x-4">
                            <!-- Like Button -->
                            <button class="like-btn text-xl focus:outline-none" data-content-id="{{ $content->id }}">
                                <span class="like-text text-{{ in_array($content->id, $likedContents) ? 'green' : 'gray' }}-500">
                                    {{ in_array($content->id, $likedContents) ? '♥' : '♡' }}
                                </span>
                                <span class="like-count">{{ $content->likes()->count() }}</span> Likes
                            </button>
                        </div>
                    </div>

                    <p class="text-gray-700 mt-2">
                        <span class="font-semibold">{{ $content->name }}</span> {{ $content->name }}
                    </p>
                </div>
            </div>
        @endforeach
    @endif
</div>

<!-- AJAX script to handle like/unlike -->
<script>
    document.querySelectorAll('.like-btn').forEach(button => {
        button.addEventListener('click', function() {
            let contentId = this.getAttribute('data-content-id');
            let likeText = this.querySelector('.like-text');
            let likeCount = this.querySelector('.like-count');

            // Send AJAX request to like/unlike the content
            fetch(`/content/${contentId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({}),
            })
            .then(response => response.json())
            .then(data => {
                if (data.message === 'liked') {
                    likeText.textContent = '♥';  // Change icon to liked
                    likeText.classList.replace('text-gray-500', 'text-green-500');  // Change color to green
                } else {
                    likeText.textContent = '♡';  // Change icon to unliked
                    likeText.classList.replace('text-green-500', 'text-gray-500');  // Change color to gray
                }
                likeCount.textContent = data.like_count;  // Update like count
            })
            .catch(error => console.error('Error:', error));
        });
    });
</script>

@endsection
