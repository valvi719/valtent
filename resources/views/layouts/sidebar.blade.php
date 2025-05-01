<!-- Desktop Sidebar -->
<aside class="hidden md:flex fixed top-0 left-0 h-full w-60 bg-green-600 text-white flex-col shadow-lg z-40" role="navigation" aria-label="Main Sidebar">
    <div class="text-2xl font-bold text-center py-6 border-b border-green-600 hidden md:block">
        <a href="/" class="hover:text-green-300">Valtent</a>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-4" role="menu">
        <a href="/" class="block px-4 py-2 rounded hover:bg-green-700" role="menuitem">🏠 Home</a>
        <button id="openSearch" class="block px-4 py-2 rounded hover:bg-green-700 w-full text-left">🔍 Search</button>
        <a href="/wallet" class="block px-4 py-2 rounded hover:bg-green-700" role="menuitem">💰 Wallet</a>
        <a href="{{ route('content.create', ['id' => Crypt::encrypt(Auth::id())]) }}" class="block px-4 py-2 rounded hover:bg-green-700" role="menuitem">➕ Create</a>
        <a href="#" class="block px-4 py-2 rounded hover:bg-green-700" role="menuitem">🔗 Connect</a>
    </nav>

    @auth
    <div class="px-4 py-4 border-t border-green-600">
        <a href="{{ route('creator.content', ['username' => Auth::user()->username]) }}"
           class="bg-green-600 text-white p-4 rounded-full text-2xl shadow-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 flex items-center space-x-2">
            @if (Auth::user()->profile_photo)
                <img src="{{ asset('storage/public/profile_photos/' . Auth::user()->profile_photo) }}" alt="Profile" class="w-8 h-8 rounded-full object-cover">
            @else
                <div class="w-8 h-8 rounded-full bg-green-800 text-white flex items-center justify-center text-sm font-bold">
                    {{ strtoupper(substr(Auth::user()->username, 0, 1)) }}
                </div>
            @endif
            <span class="text-base font-semibold">Profile</span>
        </a>
    </div>
    @endauth
</aside>

<!-- Mobile Header -->
<header class="md:hidden bg-green-600 text-white flex items-center justify-between px-4 py-3 relative z-50" role="banner">
    <button id="toggleSidebar" class="text-white text-2xl focus:outline-none focus:ring-2 focus:ring-white">
        <i class="fas fa-bars"></i>
    </button>

    <div class="text-xl font-bold absolute left-0 right-0 top-1/2 transform -translate-y-1/2 text-center pointer-events-none">
        Valtent
    </div>
</header>

<!-- Mobile Sidebar -->
<aside id="mobileSidebar" class="fixed top-0 left-0 h-full w-60 bg-green-600 text-white shadow-lg z-[100] transform -translate-x-full transition-transform duration-300 md:hidden pt-16" role="dialog" aria-label="Mobile Sidebar" aria-hidden="true">
    <div class="flex flex-col h-full">
        <div class="flex items-center justify-end px-4 py-4 border-b border-green-700">
            <button id="closeSidebar" class="text-white text-2xl bg-red-500 p-2 rounded focus:outline-none focus:ring-2 focus:ring-white">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-4" role="menu">
            <a href="/" class="block px-4 py-2 rounded hover:bg-green-700" role="menuitem">🏠 Home</a>
            <button id="openSearchMobileInside" class="block px-4 py-2 rounded hover:bg-green-700 w-full text-left">🔍 Search</button>
            <a href="/wallet" class="block px-4 py-2 rounded hover:bg-green-700" role="menuitem">💰 Wallet</a>
            <a href="{{ route('content.create', ['id' => Crypt::encrypt(Auth::id())]) }}" class="block px-4 py-2 rounded hover:bg-green-700" role="menuitem">➕ Create</a>
            <a href="#" class="block px-4 py-2 rounded hover:bg-green-700" role="menuitem">🔗 Connect</a>
        </nav>

        @auth
        <div class="px-4 py-4 border-t border-green-600">
            <a href="{{ route('creator.content', ['username' => Auth::user()->username]) }}"
               class="bg-green-600 text-white p-4 rounded-full text-2xl shadow-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 flex items-center space-x-2">
                @if (Auth::user()->profile_photo)
                    <img src="{{ asset('storage/public/profile_photos/' . Auth::user()->profile_photo) }}" alt="Profile" class="w-8 h-8 rounded-full object-cover">
                @else
                    <div class="w-8 h-8 rounded-full bg-green-800 text-white flex items-center justify-center text-sm font-bold">
                        {{ strtoupper(substr(Auth::user()->username, 0, 1)) }}
                    </div>
                @endif
                <span class="text-base font-semibold">Profile</span>
            </a>
        </div>
        @endauth
    </div>
</aside>

<!-- Search Popup -->
<div id="searchPopup" class="fixed inset-0 z-[110] bg-black bg-opacity-50 flex justify-center items-start pt-32 hidden">
    <div class="bg-white w-full max-w-md rounded-lg p-4 shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Search Creator</h2>
            <button id="closeSearchPopup" class="text-red-600 text-lg font-bold">&times;</button>
        </div>
        <input type="text" id="creatorSearchInput" class="w-full border px-4 py-2 rounded focus:outline-none" placeholder="Search by username...">
        <ul id="creatorSearchResults" class="mt-4 space-y-2 max-h-60 overflow-y-auto"></ul>
    </div>
</div>

<script>
    window.baseUrl = '{{ url('/') }}'; 
</script>

<!-- Script -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('toggleSidebar');
    const closeBtn = document.getElementById('closeSidebar');
    const mobileSidebar = document.getElementById('mobileSidebar');

    toggleBtn?.addEventListener('click', () => {
        mobileSidebar.classList.remove('-translate-x-full');
        mobileSidebar.setAttribute('aria-hidden', 'false');
    });

    closeBtn?.addEventListener('click', () => {
        mobileSidebar.classList.add('-translate-x-full');
        mobileSidebar.setAttribute('aria-hidden', 'true');
    });

    // Optional: Click outside to close
    document.addEventListener('click', (event) => {
        if (!mobileSidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
            mobileSidebar.classList.add('-translate-x-full');
        }
    });

    // Search popup toggle
    const openSearchBtns = [
        document.getElementById('openSearch'),
        document.getElementById('openSearchMobile'),
        document.getElementById('openSearchMobileInside')
    ];
    const searchPopup = document.getElementById('searchPopup');
    const closeSearchPopup = document.getElementById('closeSearchPopup');
    const searchInput = document.getElementById('creatorSearchInput');
    const searchResults = document.getElementById('creatorSearchResults');

    openSearchBtns.forEach(btn => {
        btn?.addEventListener('click', () => {
            searchPopup.classList.remove('hidden');
            searchInput.focus();
        });
    });

    closeSearchPopup?.addEventListener('click', () => {
        searchPopup.classList.add('hidden');
        searchResults.innerHTML = '';
        searchInput.value = '';
    });

    // Fetch and display search results
    searchInput?.addEventListener('input', async function () {
        const query = this.value;
        if (query.length < 2) {
            searchResults.innerHTML = '';
            return;
        }

        try {
            const response = await fetch(`/search/creators?q=${encodeURIComponent(query)}`);
            const creators = await response.json();
            searchResults.innerHTML = '';
            if (creators.length === 0) {
                searchResults.innerHTML = '<li class="text-sm text-gray-500">No creators found.</li>';
                return;
            }
            creators.forEach(creator => {
                const li = document.createElement('li');

                if(creator.id === creator.loggedInUserId)
                {
                    li.innerHTML = `
                    <a href="${window.baseUrl}/me/${creator.username}" class="flex items-center space-x-3 hover:bg-gray-100 p-2 rounded">
                        <img src="${creator.profile_photo}" alt="${creator.username}" class="w-8 h-8 rounded-full object-cover">
                        <span class="text-sm font-medium">${creator.username}</span>
                    </a>
                `;
                    
                }else{

                    li.innerHTML = `
                    <a href="/${creator.username}" class="flex items-center space-x-3 hover:bg-gray-100 p-2 rounded">
                        <img src="${creator.profile_photo}" alt="${creator.username}" class="w-8 h-8 rounded-full object-cover">
                        <span class="text-sm font-medium">${creator.username}</span>
                    </a>
                `;
                }
                searchResults.appendChild(li);
            });
        } catch (err) {
            console.error('Search error:', err);
        }
    });
});
// Close search popup on outside click
document.addEventListener('click', function (e) {
        const popup = document.getElementById('searchPopup');
        const popupContent = popup?.querySelector('.bg-white');

        if (
            popup &&
            !popup.classList.contains('hidden') &&
            !popupContent.contains(e.target) &&
            !e.target.closest('#openSearch') &&
            !e.target.closest('#openSearchMobile') &&
            !e.target.closest('#openSearchMobileInside')
        ) {
            popup.classList.add('hidden');
            searchResults.innerHTML = '';
            searchInput.value = '';
        }
});
</script>

<!-- Optional: Style offset for main content -->
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
