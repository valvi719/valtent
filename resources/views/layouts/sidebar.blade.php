<!-- Desktop Sidebar -->
<aside class="hidden md:flex fixed top-0 left-0 h-full w-60 bg-green-600 text-white flex-col shadow-lg z-40" role="navigation" aria-label="Main Sidebar">
    <div class="text-2xl font-bold text-center py-6 border-b border-green-600 hidden md:block" >
        <a href="/" class="hover:text-green-300">Valtent</a>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-4" role="menu">
        <a href="/" class="block px-4 py-2 rounded hover:bg-green-700" role="menuitem">🏠 Home</a>
        <a href="/wallet" class="block px-4 py-2 rounded hover:bg-green-700" role="menuitem">💰 Wallet</a>
        <a href="{{ route('content.create', ['id' => Crypt::encrypt(Auth::id())]) }}" class="block px-4 py-2 rounded hover:bg-green-700" role="menuitem">➕ Create</a>
        <a href="#" class="block px-4 py-2 rounded hover:bg-green-700" role="menuitem">🔗 Connect</a>
    </nav>

    @auth
    <div class="px-4 py-4 border-t border-green-600">
        <a href="{{ route('creator.content', ['id' => Crypt::encrypt(Auth::user()->id)]) }}"
        class="bg-green-600 text-white p-4 rounded-full text-2xl shadow-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 flex items-center space-x-2">
            
            {{-- Profile Photo --}}
            <img src="{{ Auth::user()->profile_photo ? asset('storage/public/profile_photos/' . Auth::user()->profile_photo) : asset('default-avatar.png') }}" alt="Profile" class="w-8 h-8 rounded-full object-cover">
             <span class="text-base font-semibold">Profile</span>
        </a>
    </div>
    @endauth
</aside>

<!-- Mobile Header -->
<header class="md:hidden bg-green-600 text-white flex items-center justify-between px-4 py-3 relative z-50" role="banner">
    <button id="toggleSidebar" aria-controls="mobileSidebar" aria-expanded="false" aria-label="Toggle sidebar"
        class="text-white text-2xl focus:outline-none focus:ring-2 focus:ring-white">
        <i class="fas fa-bars"></i>
    </button>

    <div class="text-xl font-bold absolute left-0 right-0 top-1/2 transform -translate-y-1/2 text-center pointer-events-none">
        Valtent
    </div>
</header>

<!-- Mobile Sidebar -->
<aside id="mobileSidebar" class="fixed top-0 left-0 h-full w-60 bg-green-600 text-white shadow-lg z-40 transform -translate-x-full transition-transform duration-300 md:hidden"
       role="dialog" aria-label="Mobile Sidebar" aria-hidden="true">
    <div class="flex flex-col h-full">
        <!-- Close Button -->
        <div class="flex items-center justify-end px-4 py-4 border-b border-green-700">
            <button id="closeSidebar" aria-label="Close sidebar" class="text-white text-2xl focus:outline-none focus:ring-2 focus:ring-white">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-4" role="menu">
            <a href="/" class="block px-4 py-2 rounded hover:bg-green-700" role="menuitem">🏠 Home</a>
            <a href="/wallet" class="block px-4 py-2 rounded hover:bg-green-700" role="menuitem">💰 Wallet</a>
            <a href="{{ route('content.create', ['id' => Crypt::encrypt(Auth::id())]) }}" class="block px-4 py-2 rounded hover:bg-green-700" role="menuitem">➕ Create</a>
            <a href="#" class="block px-4 py-2 rounded hover:bg-green-700" role="menuitem">🔗 Connect</a>
        </nav>

        @auth
        <div class="px-4 py-4 border-t border-green-600">
            <a href="{{ route('creator.content', ['id' => Crypt::encrypt(Auth::user()->id)]) }}"
               class="bg-green-600 text-white p-4 rounded-full text-2xl shadow-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 flex items-center space-x-2">
               <img src="{{ Auth::user()->profile_photo ? asset('storage/public/profile_photos/' . Auth::user()->profile_photo) : asset('default-avatar.png') }}" alt="Profile" class="w-8 h-8 rounded-full object-cover">
                <span class="text-base font-semibold">Profile</span>
            </a>
        </div>
        @endauth
    </div>
</aside>

<!-- Toggle Script -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('toggleSidebar');
        const closeBtn = document.getElementById('closeSidebar');
        const mobileSidebar = document.getElementById('mobileSidebar');

        toggleBtn.addEventListener('click', () => {
            mobileSidebar.classList.remove('-translate-x-full');
            mobileSidebar.setAttribute('aria-hidden', 'false');
            toggleBtn.setAttribute('aria-expanded', 'true');
        });

        closeBtn.addEventListener('click', () => {
            mobileSidebar.classList.add('-translate-x-full');
            mobileSidebar.setAttribute('aria-hidden', 'true');
            toggleBtn.setAttribute('aria-expanded', 'false');
        });

        // Optional: close when clicking outside
        document.addEventListener('click', function (event) {
            if (!mobileSidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
                mobileSidebar.classList.add('-translate-x-full');
                mobileSidebar.setAttribute('aria-hidden', 'true');
                toggleBtn.setAttribute('aria-expanded', 'false');
            }
        });
    });
</script>

<!-- Content offset for desktop -->
<style>
    main {
        padding-left: 15rem;
    }

    @media (max-width: 768px) {
        main {
            padding-left: 0;
        }
    }
</style>
