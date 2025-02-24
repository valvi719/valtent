@extends('layouts.app')
@section('title', 'Creator Content')
@section('content')

<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-semibold text-center mb-6">My Content</h1>

    <!-- Check if there are contents available -->
    @if($contents->isEmpty())
        <p class="text-center text-gray-600">No content available. Please add some content!</p>
    @else
        <!-- Grid Layout for displaying content -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($contents as $content)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="relative">
                        @if($content->type == 'Media')
                            <!-- If the content is media (image or video) -->
                            @if(str_contains($content->value, '.mp4'))
                                <!-- Video Content -->
                                <video class="w-full h-48 object-cover" controls>
                                    <source src="{{ asset('storage/' . $content->value) }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            @else
                                <!-- Image Content -->
                                <img class="w-full h-48 object-cover" src="{{ asset('storage/' . $content->value) }}" alt="{{ $content->name }}">
                            @endif
                        @else
                            <!-- Handle NFT Content (for example, display a placeholder) -->
                            <div class="w-full h-48 bg-gray-300 flex items-center justify-center">
                                <span class="text-white">NFT</span>
                            </div>
                        @endif
                    </div>

                    <div class="p-4">
                        <h3 class="text-xl font-semibold">{{ $content->name }}</h3>
                        <p class="text-gray-500">{{ $content->type }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Add Content Button with + Sign -->
<div class="fixed bottom-10 right-10">
    <a href="{{ route('content.create', ['id' => Crypt::encrypt(Auth::user()->id)]) }}" class="bg-green-600 text-white p-4 rounded-full text-2xl shadow-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
        +
    </a>
</div>

@endsection
