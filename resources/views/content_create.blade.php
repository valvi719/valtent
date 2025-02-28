<!DOCTYPE html>
<html lang="en">
@extends('layouts.app')
@section('title', 'Create Content')
 @section('content')
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg w-96">
            <h2 class="text-2xl font-semibold text-center text-green-600 mb-4">Create Content</h2>

            @if (session('success'))
                <div class="text-green-600 mb-4 p-2 bg-green-100 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="text-red-600 mb-4 p-2 bg-red-100 rounded">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
             
            <form action="{{ route('content.store',['id' => $id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="cre_id" value="{{$id}}">

                <!-- Name Field -->
                <div class="mb-4">
                    <label for="name" class="block text-gray-700">Name</label>
                    <input type="text" id="name" name="name" class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500" value="{{ old('name') }}" required>
                </div>

               <!-- Type Field -->
                <div class="mb-4">
                    <label for="type" class="block text-gray-700">Type</label>
                    <select id="type" name="type" class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500" required>
                        <option value="NFT" {{ old('type') == 'NFT' ? 'selected' : '' }}>NFT</option>
                        <option value="Media" {{ old('type') == 'Media' ? 'selected' : '' }}>Media</option>
                    </select>
                </div>

                <!-- Value Field (File Upload for Media) -->
                <div id="value-field" class="mb-4 hidden">
                    <label for="value" class="block text-gray-700">Upload Media (Video or Image)</label>
                    <!-- Adding 'accept' attribute to allow MP4 video files -->
                    <input type="file" id="value" name="value" class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500" accept="video/mp4, image/*">
                    <small class="text-gray-500">Upload a video or image</small>

                    <!-- Progress bar for upload -->
                    <div class="mt-4">
                        <progress id="upload-progress" value="0" max="100" class="w-full h-2 rounded bg-gray-200"></progress>
                        <p id="progress-text" class="text-gray-500 mt-2">0% Uploaded</p>
                    </div>
                </div>


                <!-- Submit Button -->
                <div class="mb-4">
                    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        Create Content
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Add Content Button with + Sign -->
    <div class="fixed bottom-10 right-10">
        <a href="{{ route('creator.content', ['id' => Crypt::encrypt(Auth::user()->id)]) }}" class="bg-green-600 text-white p-4 rounded-full text-2xl shadow-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
            Contents
        </a>
    </div>
    <div class="fixed bottom-6 right-15">
        <a href="{{ route('content.showall') }}" class="inline-block bg-green-600 text-white p-4 rounded-full shadow-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
            <i class="fas fa-home text-2xl"></i> <!-- Font Awesome Home icon -->
        </a>
    </div>
    
    <script>
        // JavaScript to show the "Value" field when "Media" type is selected
        document.getElementById('type').addEventListener('change', function() {
            const valueField = document.getElementById('value-field');
            if (this.value === 'Media') {
                valueField.classList.remove('hidden');
            } else {
                valueField.classList.add('hidden');
            }
        });

        // Initial check for the value if it's already selected as Media
        if (document.getElementById('type').value === 'Media') {
            document.getElementById('value-field').classList.remove('hidden');
        }
    </script>
    <script>
    const fileInput = document.getElementById('value');
    const progressBar = document.getElementById('upload-progress');
    const progressText = document.getElementById('progress-text');
    let cre_id = document.getElementById('cre_id').value;
    
    // Removed the file size limit to accept any size
    const fileSizeLimit = Infinity; // No file size limit

    let csrfToken;
    
    // Get the CSRF token from the meta tag
    window.addEventListener('load', function() {
        const csrfTokenInput = document.querySelector('input[name="_token"]');

        if (csrfTokenInput) {
            csrfToken = csrfTokenInput.value;
            console.log(csrfToken);  // Prints the token value to the console
        } else {
            console.error('CSRF token input element not found!');
        }
    });

    fileInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        
        if (file) {
            // Check file type
            if (file.type !== 'video/mp4' && !file.type.startsWith('image/')) {
                alert('Please upload a valid MP4 video or image file');
                fileInput.value = ''; // Reset file input
                return;
            }

            // Removed file size check (now accepts any file size)
            // If you want to set a different limit, you can modify fileSizeLimit as needed
            if (file.size > fileSizeLimit) {
                alert('File size exceeds the limit');
                fileInput.value = ''; // Reset file input
                return;
            }

            // Start the upload process
            uploadFile(file);
        }
    });

    function uploadFile(file) {
        const formData = new FormData();
        formData.append('file', file);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/content/store/' + cre_id, true);  // Replace '/upload-endpoint' with your actual upload URL

        // Add the CSRF token to the request header
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);

        // Set up the progress event listener to update the progress bar
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percent = (e.loaded / e.total) * 100;
                progressBar.value = percent;
                progressText.textContent = `${Math.round(percent)}% Uploaded`;
            }
        });

        // Success or failure handling
        xhr.addEventListener('load', function() {
            if (xhr.status === 200) {
                progressText.textContent = 'Upload Complete!';
            } else {
                progressText.textContent = 'Upload Failed!';
            }
        });

        // Handle errors
        xhr.addEventListener('error', function() {
            progressText.textContent = 'Upload Error!';
        });

        // Send the file to the server
        xhr.send(formData);
    }
</script>

</body>
@endsection
</html>
