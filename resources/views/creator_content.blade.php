@extends('layouts.app')
@section('title', 'Creator Content')
@section('content')

<div class="container mx-auto px-4 py-6">
    <!-- User Profile Section -->
<div class="flex flex-col md:flex-row items-center md:items-start justify-center mb-10">
    <!-- Profile Image -->
    <div class="w-32 h-32 md:w-40 md:h-40 rounded-full overflow-hidden border-4 border-green-500 mb-4 md:mb-0 md:mr-10">
        <img src="{{ asset('storage/public/profile_photos/' . Auth::user()->profile_photo) }}" class="w-full h-full object-cover" alt="Profile Photo">
    </div>

    <!-- User Info -->
    <div class="text-center md:text-left">
        <h2 class="text-2xl font-bold">{{ Auth::user()->name }}</h2>
        <div class="flex justify-center md:justify-start space-x-6 mt-2 text-gray-600">
            <span><strong>{{ $contents->count() }}</strong> posts</span>
            <span><strong>38.5M</strong> followers</span> <!-- Replace with real data -->
            <span><strong>180</strong> following</span> <!-- Replace with real data -->
        </div>
        <p class="mt-3 text-sm">Singer</p>
    </div>
</div>

    <h1 class="text-3xl font-semibold text-center mb-6">My Content</h1>

    <!-- Check if there are contents available -->
    @if($contents->isEmpty())
        <p class="text-center text-gray-600">No content available. Please add some content!</p>
    @else
        <!-- Grid Layout for displaying content -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($contents as $content)
            <div class="flex flex-col bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                
                <!-- Media Preview Area -->
                <div class="relative h-48 w-full bg-gray-200 overflow-hidden cursor-pointer" data-content-id="{{ $content->id }}" onclick="openModal(this)">
                    <input type="hidden" name="content_id" value="{{ $content->id }}">

                    @if($content->type == 'Media')
                        @if(str_contains($content->value, '.mp4'))
                            <video class="absolute inset-0 w-full h-full object-cover rounded-md" muted autoplay loop playsinline>
                                <source src="{{ asset('storage/' . $content->value) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @else
                            <img class="absolute inset-0 w-full h-full object-cover rounded-md" src="{{ asset('storage/' . $content->value) }}" alt="{{ $content->name }}">
                        @endif
                    @else
                        <div class="absolute inset-0 flex items-center justify-center text-xl text-gray-500 bg-gray-300 rounded-md">
                            NFT
                        </div>
                    @endif
                </div>

                <!-- Like + Description -->
                <div class="p-4 flex flex-col justify-between flex-grow">
                

                    <div class="flex justify-between items-center mb-2">
                        <button class="like-btn text-xl focus:outline-none" data-content-id="{{ $content->id }}">
                        <span class="like-text text-{{ in_array($content->id, $likedContents) ? 'green' : 'gray' }}-500">
                            {{ in_array($content->id, $likedContents) ? '♥' : '♡' }}
                        </span>
                        <span class="like-count">
                            @php $likeCount = $content->likes()->count(); @endphp
                            @if($likeCount > 1)
                                {{ $likeCount }} Likes
                            @elseif($likeCount === 1)
                                1 Like
                            @endif
                        </span>
                        </button>
                    </div>

                    <p class="text-gray-700 text-sm truncate">
                        <span class="font-semibold">{{ $content->name }}</span> {{ $content->name }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
     @endif
</div>
<!-- Modal structure -->
<div id="contentModal" class="modal">
    <div class="bg-white relative">
        <!-- Close button -->
        <span id="closeModal">&times;</span>

        <!-- Modal Content (populated dynamically) -->
        <div id="modalContent">
            <!-- Content will be loaded here dynamically -->
        </div>
    </div>
</div>

<script>
    window.addEventListener('load', function () {
        const modal = document.getElementById('contentModal');
        modal.style.display = 'none';
    });

    function openModal(element) {
        const contentId = element.querySelector('input[name="content_id"]').value;
        const modal = document.getElementById('contentModal');
        modal.style.display = 'flex';

        fetch(`/modalcontent/${contentId}`)
            .then(response => response.json())
            .then(data => {
                const modalContent = document.getElementById('modalContent');
                modalContent.innerHTML = '';

                if (data.type === 'Media') {
                    if (data.value.includes('.mp4')) {
                        modalContent.innerHTML = `
                            <video class="w-full h-auto object-contain rounded-lg" controls>
                                <source src="${data.url}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        `;
                    } else {
                        modalContent.innerHTML = `
                            <img class="w-full h-auto object-contain rounded-lg" src="${data.url}" alt="${data.name}">
                        `;
                    }
                } else {
                    modalContent.innerHTML = `
                        <div class="w-full h-full bg-gray-300 flex items-center justify-center">
                            <span class="text-white">NFT</span>
                        </div>
                    `;
                }

                modalContent.innerHTML += `
                    <div class="p-4">
                        <button class="modal-like-btn text-xl focus:outline-none" data-content-id="${data.id}">
                            <span class="like-text text-${data.likedContents.includes(data.id) ? 'green' : 'gray'}-500">
                                ${data.likedContents.includes(data.id) ? '♥' : '♡'}
                            </span>
                            <span class="like-count">
                                ${data.like_count > 1 ? data.like_count + ' Likes' : data.like_count === 1 ? '1 Like' : ''}
                            </span>
                        </button>
                        <h3 class="text-xl font-semibold">${data.name}</h3>
                        <p class="text-gray-500">${data.type}</p>
                    </div>
                `;
            })
            .catch(error => {
                console.error('Error fetching content:', error);
            });
    }

    document.addEventListener('click', function (e) {
        // Handle modal like button click
        if (e.target.closest('.modal-like-btn')) {
            const button = e.target.closest('.modal-like-btn');
            const contentId = button.getAttribute('data-content-id');
            const likeText = button.querySelector('.like-text');
            const likeCount = button.querySelector('.like-count');

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
                        likeText.textContent = '♥';
                        likeText.classList.replace('text-gray-500', 'text-green-500');
                    } else {
                        likeText.textContent = '♡';
                        likeText.classList.replace('text-green-500', 'text-gray-500');
                    }

                    likeCount.textContent =
                        data.like_count === 0
                            ? ''
                            : data.like_count === 1
                            ? '1 Like'
                            : `${data.like_count} Likes`;

                    // Sync main card like status if visible
                    const cardBtn = document.querySelector(`.like-btn[data-content-id="${contentId}"]`);
                    if (cardBtn) {
                        const cardLikeText = cardBtn.querySelector('.like-text');
                        const cardLikeCount = cardBtn.querySelector('.like-count');

                        cardLikeText.textContent = likeText.textContent;
                        cardLikeText.className = likeText.className;
                        cardLikeCount.textContent = likeCount.textContent;
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Handle main card like button
        if (e.target.closest('.like-btn')) {
            const button = e.target.closest('.like-btn');
            const contentId = button.getAttribute('data-content-id');
            const likeText = button.querySelector('.like-text');
            const likeCount = button.querySelector('.like-count');

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
                        likeText.textContent = '♥';
                        likeText.classList.replace('text-gray-500', 'text-green-500');
                    } else {
                        likeText.textContent = '♡';
                        likeText.classList.replace('text-green-500', 'text-gray-500');
                    }

                    likeCount.textContent =
                        data.like_count === 0
                            ? ''
                            : data.like_count === 1
                            ? '1 Like'
                            : `${data.like_count} Likes`;
                })
                .catch(error => console.error('Error:', error));
        }

        // Handle modal close
        if (e.target.id === 'closeModal') {
            document.getElementById('contentModal').style.display = 'none';
        }
    });
</script>
 

<style>
/* Modal background to cover the entire screen */
.modal{
    display: none;  /* Initially hidden */
    position: fixed;  /* Fixed position to cover the entire screen */
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.7);  /* Dark background to dim the page */
    z-index: 9999;  /* Ensures the modal is above everything else */
    justify-content: center;  /* Center modal horizontally */
    align-items: center;  /* Center modal vertically */
}

/* Modal content styling */
.modal .bg-white {
    width: 40%;  /* Adjust the width of the modal to 40% of the screen */
    height: auto;  /* Auto height, so content can adjust */
    max-width: 800px;  /* Set a max-width to prevent it from being too wide */
    padding: 15px;  /* Reduced padding for a more compact look */
    border-radius: 8px;  /* Rounded corners */
    background-color: white;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);  /* Light shadow for visual separation */
}

/* Close button */
#closeModal {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: #f44336;
    color: white;
    border-radius: 50%;
    padding: 10px;
    cursor: pointer;
    z-index: 10000; 
}

/* Ensure modal content fits inside */
#modalContent {
    max-height: 400px;  /* Limit the height of content to prevent overflow */
    overflow-y: auto;  /* Make content scrollable if it exceeds the max height */
    z-index: 999;  /* Ensure video content is placed correctly */
    width: 100%;  /* Full width of the modal */
    height: auto;
}
</style>

@endsection
