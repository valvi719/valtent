@extends('layouts.app')
@section('title', 'Creator Content')
@section('content')

<div class="container mx-auto px-4 py-6">
    <!-- Profile Section Wrapper -->
    <div class="w-full flex justify-center">
        <div class="w-full max-w-5xl px-4 py-8">

            <!-- Profile Header Row -->
            <div class="flex flex-col md:flex-row items-center md:items-start md:space-x-10">

                <!-- Profile Photo: Classic Circle -->
                <div class="w-40 h-40 rounded-full overflow-hidden">
                    <img src="{{ asset('storage/public/profile_photos/' . Auth::user()->profile_photo) }}" class="w-full h-full object-cover rounded-full">
                </div>

                <!-- Profile Info -->
                <div class="flex-1 mt-4 md:mt-0 text-center md:text-left">

                    <div class="flex items-center justify-center md:justify-start space-x-4 mb-5">
                        <h2 class="text-2xl font-bold">{{ Auth::user()->username }}</h2>
                        @if(Auth::id() === $creator->id)
                        <a href="{{ route('creator.editProfile') }}" class="text-sm px-4 py-1 border rounded hover:bg-gray-100 transition mb-4">Edit Profile</a>
                        @endif
                    </div>

                    <div class="flex justify-center md:justify-start space-x-6 text-sm text-gray-700 mb-2">
                        <span>
                            <strong>{{ $contents->count() }}</strong> contents
                        </span>
                        <button onclick="openFollowingModal('{{ $creator->id }}')" class="text-black-500">
                            <strong>{{ $creator->following()->count() }}</strong> following
                        </button>

                        <button onclick="openFollowersModal('{{ $creator->id }}')" class="text-black-500">
                            <strong>{{ $creator->followers()->count() }}</strong> followers
                        </button>
                    </div>

                    <div class="text-sm text-gray-800">
                        <div class="font-semibold">{{ Auth::user()->name }}</div>
                        <div>
                            Relationship :
                            @if($creator->relationship_status)
                                {{ ucfirst($creator->relationship_status) }}
                                @if(in_array($creator->relationship_status, ['In a Relationship With', 'Engaged To', 'Married To', 'In an Open Relationship With']) && $creator->relationshipWithUser)
                                <a href="{{ route('creator.profile', ['username' => $creator->relationshipWithUser->username]) }}">
                                    <strong>{{ $creator->relationshipWithUser->name }}</strong>
                                </a>
                                @endif
                                @if($creator->relationship_status_since)
                                    Since {{ $creator->relationship_status_since->format('F j, Y') }}
                                @endif
                            @else
                                Unspecified
                            @endif
                        </div>
                        <div>{{ $creator->bio}}</div>
                    </div>

                </div>
            </div>

            <div class="mt-10 text-center">
                <h3 class="text-xl font-semibold">My Content</h3>
            </div>

        </div>
    </div>

    @if($contents->isEmpty())
    <p class="text-center text-gray-600">No content available. Please add some content!</p>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-6">
        @foreach($contents as $content)
        <div class="flex flex-col bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">

            <div class="relative h-48 w-full bg-gray-200 overflow-hidden cursor-pointer" data-content-id="{{ $content->id }}" onclick="openModal(this)">
                <input type="hidden" name="content_id" value="{{ $content->id }}">
                @if($content->type == 'Media')
                     @if(str_contains($content->value, '.mp4'))
                        <video class="absolute inset-0 w-full h-full object-cover rounded-md" muted autoplay loop playsinline>
                            <source src="{{ asset('storage/' . $content->value) }}" type="video/mp4">
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

            <div class="p-4 flex flex-col justify-between flex-grow space-y-2">
                <div class="flex items-center justify-between">
                    <button class="like-btn flex items-center space-x-1 text-xl focus:outline-none" data-content-id="{{ $content->id }}">
                        <span class="like-text text-{{ in_array($content->id, $likedContents) ? 'green' : 'gray' }}-500">
                            {{ in_array($content->id, $likedContents) ? '♥' : '♡' }}
                        </span>
                        <span class="like-count text-sm text-gray-600">
                            @php $likeCount = $content->likes()->count(); @endphp
                            {{ $likeCount }} {{ Str::plural('Like', $likeCount) }}
                        </span>
                    </button>
                </div>

                <p class="text-gray-800 text-sm font-medium truncate">
                    {{ \Illuminate\Support\Str::limit($content->name, 60) }}
                </p>
            </div>


        </div>
        @endforeach
    </div>
    @endif
</div>

<script>
    window.baseUrl = '{{ url('/') }}'; 
</script>

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
<script src="{{ asset('js/creator_content_and_profile.js') }}"></script>
@endsection
