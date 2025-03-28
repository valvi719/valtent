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
                <div class="bg-white rounded-lg shadow-lg overflow-hidden" data-content-id="{{ $content->id }}" onclick="openModal(this)">
                    <input type="hidden" name="content_id" value="{{ $content->id }}">
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


// Initially hide the modal after page loads
window.addEventListener('load', function() {
    const modal = document.getElementById('contentModal');
    modal.style.display = 'none'; // Ensure modal is hidden on page load
});

// Open Modal when a post is clicked
function openModal(element) {
    const contentId = element.querySelector('input[name="content_id"]').value;

    // Show the modal
    const modal = document.getElementById('contentModal');
    modal.style.display = 'flex';  // Show the modal

    // Send AJAX request to fetch content details
    fetch(`/modalcontent/${contentId}`)
        .then(response => response.json())
        .then(data => {
            const modalContent = document.getElementById('modalContent');
            modalContent.innerHTML = '';
            console.log(data);
            // Depending on the content type (Image, Video, or NFT)
             // Depending on the content type (Image, Video, or NFT)
        if (data.type === 'Media') {
            if (data.value.includes('.mp4')) {
                modalContent.innerHTML = `
                    <video class="w-full h-auto object-cover" controls>
                        <source src="${data.url}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                `;
            } else {
                modalContent.innerHTML = `
                    <img class="w-full h-auto object-cover" src="${data.url}" alt="${data.name}">
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
                    <h3 class="text-xl font-semibold">${data.name}</h3>
                    <p class="text-gray-500">${data.type}</p>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error fetching content:', error);
        });
}

// Close Modal when close button is clicked
document.getElementById('closeModal').addEventListener('click', function() {
    const modal = document.getElementById('contentModal');
    modal.style.display = 'none';  // Hide modal by setting display to none
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
