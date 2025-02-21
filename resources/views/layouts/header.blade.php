<header class="bg-green-600 text-white py-4">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/valtent_logo.jpeg') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
        <!-- Logo -->
        <a href="/" class="text-3xl font-bold">
            Valtent
        </a>

        <!-- Navigation Menu -->
        <nav>
            <ul class="flex space-x-6">
                <li><a href="/" class="hover:text-green-200">Home</a></li>
                <li><a href="#about" class="hover:text-green-200">About</a></li>
                <li><a href="#services" class="hover:text-green-200">Services</a></li>
                <li><a href="#contact" class="hover:text-green-200">Contact</a></li>
            </ul>
        </nav>

        <!-- Profile and Logout Dropdown -->
        <div class="relative">
            <!-- Conditionally display profile icon only if authenticated and not on registration or OTP pages -->
            @auth
                @if (!in_array(Route::currentRouteName(), ['show.form','verify.otp','login','password.request','password.reset']))
                    <button id="profileButton" class="w-10 h-10 rounded-full overflow-hidden border-2 border-white">
                        <!-- Display user's profile image or initials -->
                        @if(Auth::user()->profile_photo)
                            <!-- Display Profile Image -->
                            <img src="{{ asset('storage/public/profile_photos/' . Auth::user()->profile_photo) }}" alt="Profile" class="w-full h-full object-cover">
                        @else
                            <!-- Display Initials if no Profile Image -->
                            <span class="flex justify-center items-center w-full h-full text-white text-lg">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                        @endif
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="profileDropdown" class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg hidden">
                        <div class="p-2">
                            <p class="text-gray-700 font-semibold">{{ Auth::user()->name }}</p>
                            <p class="text-gray-500 text-sm">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="border-t border-gray-200">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">
                                    Log out
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @else
                <!-- If no user is authenticated, show nothing or show login/signup options -->
            @endauth
        </div>
    </div>

    <!-- TailwindCSS and custom JS for dropdown -->
    <script>
        // Toggle the profile dropdown on click
        document.getElementById('profileButton').addEventListener('click', function() {
            let dropdown = document.getElementById('profileDropdown');
            dropdown.classList.toggle('hidden');
        });

        // Close the dropdown if clicked outside
        document.addEventListener('click', function(event) {
            let dropdown = document.getElementById('profileDropdown');
            let profileButton = document.getElementById('profileButton');
            
            if (!profileButton.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</header>
