@extends('layouts.app')

@section('title', 'Home')

@section('content')

<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-semibold text-center mb-6">Welcome to Your Feed</h1>

    @if($contents->isEmpty())
        <p class="text-center text-gray-600">No content available. Please follow some creators!</p>
    @else
        @foreach($contents as $content)
            <!-- Card container: Aspect ratio 1:1 -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6 w-full sm:w-11/12 md:w-10/12 lg:w-8/12 xl:w-6/12 mx-auto">
                <!-- Content Header: User's Profile and post time -->
                <div class="flex items-center p-4 border-b">
                    <!-- Profile Image -->
                    <img src="{{ asset('storage/public/profile_photos/' . $content->creator->profile_photo) }}" alt="User Avatar" class="w-12 h-12 rounded-full mr-3">
                    <div class="flex flex-col flex-grow">
                        <a href="#" class="font-semibold text-lg">{{ $content->name }}</a>
                        <p class="text-gray-500 text-sm">{{ $content->created_at->diffForHumans() }}</p>
                    </div>
                    
                </div>
            
                <!-- Media Content (Image or Video) with Aspect Ratio 1:1 -->
                <div class="relative w-full" style="padding-top: 100%;"> <!-- 1:1 aspect ratio -->
                    @if($content->type == 'Media')
                        @if(str_contains($content->value, '.mp4'))
                            <video class="absolute top-0 left-0 w-full h-full object-cover" controls>
                                <source src="{{ asset('storage/' . $content->value) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @else
                            <img class="absolute top-0 left-0 w-full h-full object-cover" src="{{ asset('storage/' . $content->value) }}" alt="{{ $content->name }}">
                        @endif
                    @else
                        <div class="w-full h-full bg-gray-300 flex items-center justify-center">
                            <span class="text-white">NFT</span>
                        </div>
                    @endif
                </div>

                <!-- Content Footer: Like Button, Post Description -->
                <div class="p-4">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex space-x-4">
                            <!-- Like Button -->
                            <button class="like-btn text-xl focus:outline-none" data-content-id="{{ $content->id }}">
                                <span class="like-text text-{{ in_array($content->id, $likedContents) ? 'green' : 'gray' }}-500">
                                    {{ in_array($content->id, $likedContents) ? '♥' : '♡' }}
                                </span>
                                <span class="like-count">
                                    @if($content->likes()->count() > 1)
                                        {{ $content->likes()->count() }} Likes
                                    @elseif($content->likes()->count() == 1)
                                        1 Like
                                    @endif
                                </span>
                            </button>
                            @if (Auth::id() == $content->cre_id && $content->likes()->count() > 1)
                                <button class="extract-link" data-content-id="{{ $content->id }}">Extract</button>
                            @endif
                        </div>
                        
                    </div>

                    <!-- Post Description -->
                    <p class="text-gray-700 mt-2">
                        <span class="font-semibold">{{ $content->name }}</span> {{ $content->name }}
                    </p>
                </div>
            </div>
        @endforeach
    @endif
</div>
<script>
    // AJAX script to handle like/unlike
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
                 // Update the like count based on the response
                if (data.like_count === 0) {
                    likeCount.textContent = '';  // Hide the like count if it's 0
                } else if (data.like_count === 1) {
                    likeCount.textContent = '1 Like';  // Show "1 Like" when count is 1
                } else {
                    likeCount.textContent = `${data.like_count} Likes`;  // Show the count with "Likes" if greater than 1
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });

    // Extract button click handler
    document.querySelectorAll('.extract-link').forEach(button => {
        button.addEventListener('click', function() {
            let contentId = this.getAttribute('data-content-id');
            
            // Send AJAX request to the "extract" route
            fetch(`/content/${contentId}/extract`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({}),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Find the like-count element within the same card/container as the extract button
                    let likeCountElement = this.closest('.bg-white').querySelector('.like-count');
                    let likeText = this.closest('.bg-white').querySelector('.like-text');
                    if (likeCountElement) {
                        likeCountElement.textContent = '0'; // Set like count to 0
                        likeText.textContent = '♡';
                    }

                    // Optionally, hide the "Extract" button
                    this.style.display = 'none';  // Hide the extract button
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
</script>
<style>
        /* CSS to style the link as a button */
        .extract-link {
            display: inline-block;        /* Make it behave like a button */
            padding: 10px 20px;           /* Add padding to make it button-like */
            color: white;                 /* White text color */
            background-color: green;      /* Green background */
            text-decoration: none;        /* Remove underline */
            border-radius: 5px;           /* Rounded corners */
            font-weight: bold;            /* Make the text bold */
            text-align: center;           /* Center the text inside the button */
            font-size: 14px;              /* Set font size */
            cursor: pointer;             /* Change cursor to pointer on hover */
            transition: background-color 0.3s ease, transform 0.2s; /* Smooth transition for hover effect */
        }

        /* Optional: Hover effect for the button */
        .extract-link:hover {
            background-color: darkgreen;  /* Darker green on hover */
            transform: scale(1.05);        /* Slightly enlarge the button on hover */
        }
</style>
@endsection
