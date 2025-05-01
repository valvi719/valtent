@extends('layouts.app')
@section('title', 'Edit Profile')
@section('content')

<div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-lg mt-6">
    <div class="text-center mb-6">
        <img src="{{ asset('images/valtent_logo.jpeg') }}" alt="App Logo" class="mx-auto h-16">
    </div>
    <h2 class="text-2xl font-bold text-center text-green-600 mb-6">Edit Profile</h2>

    <form action="{{ route('creator.updateProfile') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="profile_photo" class="block text-sm font-medium text-gray-700">Change Profile Photo</label>
            <input type="file" name="profile_photo" accept="image/*"
                class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600">
            @error('profile_photo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label for="relationship_status" class="block text-sm font-medium text-gray-700">Relationship Status</label>
            <select name="relationship_status" id="relationship_status"
                class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600">
                <option value="">Select</option>
                <option value="Single" {{ $creator->relationship_status == 'Single' ? 'selected' : '' }}>Single</option>
                <option value="In a Relationship With" {{ $creator->relationship_status == 'In a Relationship With' ? 'selected' : '' }}>In a Relationship With</option>
                <option value="Engaged To" {{ $creator->relationship_status == 'Engaged To' ? 'selected' : '' }}>Engaged To</option>
                <option value="Married To" {{ $creator->relationship_status == 'Married To' ? 'selected' : '' }}>Married To</option>
                <option value="It's Complicated" {{ $creator->relationship_status == "It's Complicated" ? 'selected' : '' }}>It's Complicated</option>
                <option value="In an Open Relationship With" {{ $creator->relationship_status == 'In an Open Relationship With' ? 'selected' : '' }}>In an Open Relationship With</option>
            </select>
            @error('relationship_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4" id="relationship_with_container" style="display: none;">
            <label for="relationship_with" class="block text-sm font-medium text-gray-700">Relationship With (Username)</label>
            <div class="relative">
                <input type="text" name="relationship_with" id="relationship_with"
                       class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600"
                       value="{{ $creator->relationship_with }}" placeholder="Search Username">
                <div id="search_results" class="absolute z-10 w-full bg-white border rounded shadow-md mt-1" style="display: none;">
                    </div>
            </div>
            @error('relationship_with') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4" id="relationship_since_container" style="display: none;">
            <label for="relationship_status_since" class="block text-sm font-medium text-gray-700">Since</label>
            <input type="date" name="relationship_status_since"
                   class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600"
                   value="{{ $creator->relationship_status_since ? $creator->relationship_status_since->format('Y-m-d') : '' }}">
            @error('relationship_status_since') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
            <textarea name="bio" rows="3"
                class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600">{{ $creator->bio }}</textarea>
            @error('bio') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <button type="submit"
            class="w-full bg-green-600 text-white p-3 rounded-md hover:bg-green-700 focus:outline-none">Update Profile</button>
    </form>
</div>

<script>
    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const relationshipStatusSelect = document.getElementById('relationship_status');
        const relationshipWithContainer = document.getElementById('relationship_with_container');
        const relationshipSinceContainer = document.getElementById('relationship_since_container');
        const relationshipWithInput = document.getElementById('relationship_with');
        const searchResultsContainer = document.getElementById('search_results');
        const relationshipSinceInput = document.querySelector('input[name="relationship_status_since"]');
        const relationshipWithLabel = document.querySelector('label[for="relationship_with"]');
        const usernameValidationMessage = document.createElement('p');
        usernameValidationMessage.classList.add('text-red-500', 'text-sm', 'mt-1');
        relationshipWithInput.parentNode.insertBefore(usernameValidationMessage, relationshipWithInput.nextSibling);

        // Block future dates in the date picker
        const today = new Date();
        const todayFormatted = today.toISOString().split('T')[0]; // Get<\ctrl3348>-MM-DD format
        relationshipSinceInput.setAttribute('max', todayFormatted);

        function updateVisibility() {
            const selectedStatus = relationshipStatusSelect.value;
            const showWithField = selectedStatus && (selectedStatus.includes('With') || selectedStatus.includes('To'));

            relationshipWithContainer.style.display = showWithField ? 'block' : 'none';
            relationshipSinceContainer.style.display = selectedStatus ? 'block' : 'none';

            // Update the label text
            if (selectedStatus === 'In a Relationship With') {
                relationshipWithLabel.textContent = 'In a Relationship With';
            } else if (selectedStatus === 'Engaged To') {
                relationshipWithLabel.textContent = 'Engaged To';
            } else if (selectedStatus === 'Married To') {
                relationshipWithLabel.textContent = 'Married To';
            } else if (selectedStatus === 'In an Open Relationship With') {
                relationshipWithLabel.textContent = 'In an Open Relationship';
            } else {
                relationshipWithLabel.textContent = 'Relationship With (Username)'; // Default label
            }

            // Clear any previous validation message and hide search results
            usernameValidationMessage.textContent = '';
            searchResultsContainer.style.display = 'none';
        }

        relationshipStatusSelect.addEventListener('change', updateVisibility);
        updateVisibility(); // Initial visibility on load

        relationshipWithInput.addEventListener('input', function() {
            const query = this.value.trim();
            searchResultsContainer.innerHTML = ''; // Clear previous search results
            usernameValidationMessage.textContent = ''; // Clear previous validation message
            searchResultsContainer.style.display = 'none';

            if (query.length >= 2) {
                fetch(`/creator/search?query=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            data.forEach(user => {
                                const div = document.createElement('div');
                                div.classList.add('flex', 'items-center', 'p-2', 'hover:bg-gray-100', 'cursor-pointer');
                                const profilePhotoUrl = user.profile_photo
                                    ? `/storage/public/profile_photos/${user.profile_photo}`
                                    : `/images/default-profile.png`; // Fallback

                                div.innerHTML = `
                                    <div class="w-8 h-8 rounded-full overflow-hidden mr-2">
                                        <img src="${profilePhotoUrl}" alt="${user.name}" class="w-full h-full object-cover rounded-full">
                                    </div>
                                    <span>${user.name} (${user.username})</span>
                                `;
                                div.addEventListener('click', function() {
                                    relationshipWithInput.value = user.username;
                                    searchResultsContainer.style.display = 'none';
                                    usernameValidationMessage.textContent = ''; // Clear message on selection
                                });
                                searchResultsContainer.appendChild(div);
                            });
                            searchResultsContainer.style.display = 'block';
                        } else if (query.length >= 2) {
                            usernameValidationMessage.textContent = 'User not found.';
                        }
                    })
                    .catch(error => {
                        console.error('Error searching creators:', error);
                    });
            } else if (query.length < 2) {
                usernameValidationMessage.textContent = ''; // Clear message if query is too short
            }
        });

        document.addEventListener('click', function(event) {
            if (!relationshipWithContainer.contains(event.target)) {
                searchResultsContainer.style.display = 'none';
            }
        });
    });
</script>
@endsection