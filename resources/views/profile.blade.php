@extends('layouts.app')

@section('title', $creator->name . "'s Profile")

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Profile Section -->
    <div class="flex flex-col md:flex-row items-center md:items-start justify-center mb-10">
        <!-- Profile Photo -->
        <div class="w-32 h-32 md:w-40 md:h-40 rounded-full overflow-hidden border-4 border-green-500 mb-4 md:mb-0 md:mr-10">
            <img src="{{ asset('storage/public/profile_photos/' . $creator->profile_photo) }}" class="w-full h-full object-cover" alt="Profile Photo">
        </div>

        <!-- Profile Info -->
        <div class="text-center md:text-left">
            <h2 class="text-2xl font-bold">{{ $creator->name }}</h2>
            <div class="flex justify-center md:justify-start space-x-6 mt-2 text-gray-600">
                <span><strong>{{ $contents->count() }}</strong> contents</span>
                <button onclick="openFollowingModal('{{ $creator->id }}')" class="text-black-500">
                    <strong>{{ $creator->following()->count() }}</strong> following
                </button>
                <button onclick="openFollowersModal('{{ $creator->id }}')" class="text-black-500">
                    <strong>{{ $creator->followers()->count() }}</strong> followers
                </button>
            </div>
            
                <!-- Bio -->
                <p class="mt-3 text-sm">Singer</p>

                <!-- Follow/Unfollow Button -->
                @auth
                    @if(auth()->id() !== $creator->id)
                        <div class="mt-4">
                            <button id="follow-btn"
                                data-creator-id="{{ $creator->id }}"
                                class="px-4 py-1 rounded-full transition duration-300 
                                    {{ $isFollowing 
                                        ? 'bg-white text-black border border-black hover:bg-gray-100' 
                                        : 'bg-green-500 text-white hover:bg-green-600' }}">
                                {{ $isFollowing ? 'Unfollow' : 'Follow' }}
                            </button>
                        </div>
                    @endif
                @endauth
            
        </div>
    </div>

    <h1 class="text-3xl font-semibold text-center mb-6">{{ $creator->name }}'s Content</h1>

    @if($contents->isEmpty())
        <p class="text-center text-gray-600">No content available.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($contents as $content)
                <div class="flex flex-col bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <div class="relative h-48 w-full bg-gray-200 overflow-hidden cursor-pointer" data-content-id="{{ $content->id }}" onclick="openModal(this)">
                        <input type="hidden" name="content_id" value="{{ $content->id }}">
                        @if($content->type === 'Media')
                            @if(str_contains($content->value, '.mp4'))
                                <video class="absolute inset-0 w-full h-full object-cover rounded-md" muted autoplay loop playsinline>
                                    <source src="{{ asset('storage/' . $content->value) }}" type="video/mp4">
                                </video>
                            @else
                                <img src="{{ asset('storage/' . $content->value) }}" class="absolute inset-0 w-full h-full object-cover rounded-md" alt="{{ $content->name }}">
                            @endif
                        @else
                            <div class="absolute inset-0 flex items-center justify-center bg-gray-300 text-xl text-white">NFT</div>
                        @endif
                    </div>

                    <div class="p-4 flex flex-col justify-between flex-grow">
                        <div class="flex justify-between items-center mb-2">
                            <button class="like-btn text-xl focus:outline-none" data-content-id="{{ $content->id }}">
                                <span class="like-text text-{{ in_array($content->id, $likedContents) ? 'green' : 'gray' }}-500">
                                    {{ in_array($content->id, $likedContents) ? '♥' : '♡' }}
                                </span>
                                <span class="like-count">
                                    @php $likeCount = $content->likes()->count(); @endphp
                                    {{ $likeCount }} {{ $likeCount === 1 ? 'Like' : 'Likes' }}
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

<!-- Followers / Following Modal -->
<div id="followModal" class="fixed inset-0 hidden items-center justify-center z-50 bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden w-11/12 max-w-md relative">

        <div class="flex items-center justify-between p-3 border-b">
            <h2 id="followModalTitle" class="text-lg font-semibold"></h2>
            <button onclick="closeFollowModal()" class="text-white bg-red-500 hover:bg-red-600 rounded-full w-8 h-8 flex items-center justify-center text-xl">&times;</button>
        </div>

        <div id="followModalContent" class="p-4 max-h-96 overflow-y-auto">
            <!-- Follower or Following users will be loaded here -->
        </div>

    </div>
</div>

<script>
    window.baseUrl = '{{ url('/') }}'; 
    window.loggedInUserId = {{ Auth::id() }};
</script>

<!-- Modal Structure -->
<div id="contentModal" style="display: none;" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden w-11/12 max-w-md">
        <div class="flex items-center justify-between p-4 border-b">
            <div class="flex items-center gap-2">
                <img id="creatorProfilePhoto" src="" alt="Creator Profile" class="w-10 h-10 rounded-full">
                <span id="creatorUsername" class="text-sm font-semibold"></span>
            </div>
            <div class="flex items-center">
                <button id="moreOptions" class="text-2xl font-bold cursor-pointer mr-2 mb-3">&hellip;</button>
                <div id="moreMenu" class="hidden top-12 right-14 bg-white shadow-lg rounded-lg z-10">
                    <!-- Example dropdown content -->
                    <button class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">Report</button>
                    <button class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">Share</button>
                    <button id="deleteContent" class="text-red-500 hover:bg-gray-100 px-4 py-2 rounded transition" data-content-id="">
                Delete
            </button>
                </div>
                <button id="closeModal" class="text-white bg-red-500 hover:bg-red-600 rounded-full w-10 h-10 flex items-center justify-center text-xl cursor-pointer">&times;</button>
            </div>
        </div>
        <div id="modalContent" class="p-4">
        </div>
        <div class="p-4 flex justify-center border-t">
            
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const button = document.getElementById('follow-btn');

        if (button) {
            button.addEventListener('click', function () {
                const creatorId = button.getAttribute('data-creator-id');

                fetch(`/creator/${creatorId}/toggle-follow`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                })
                .then(response => response.json())
                .then(data => {
                    const followersBtn = document.querySelector('button[onclick^="openFollowersModal"]');

                    // Get current count from text like: "123 followers"
                    const followersText = followersBtn?.innerText.trim();
                    let [countStr] = followersText?.split(' ') ?? ['0'];
                    let currentCount = parseInt(countStr) || 0;

                    if (data.status === 'followed') {
                        button.className = 'px-4 py-1 rounded-full transition duration-300 bg-white text-black border border-black hover:bg-gray-100';
                        button.innerText = 'Unfollow';
                        currentCount += 1;
                    } else if (data.status === 'unfollowed') {
                        button.className = 'px-4 py-1 rounded-full transition duration-300 bg-green-500 text-white hover:bg-green-600';
                        button.innerText = 'Follow';
                        currentCount = Math.max(0, currentCount - 1);
                    }

                    if (followersBtn) {
                        followersBtn.innerHTML = `<strong>${currentCount}</strong> followers`;
                    }
                })
                .catch(error => console.error('Follow toggle error:', error));
            });
        }
    });
</script>
<script src="{{ asset('js/creator_content_and_profile.js') }}"></script>
@endsection
