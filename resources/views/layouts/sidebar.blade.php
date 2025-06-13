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
        <a href="#" id="openMoments" class="block px-4 py-2 rounded hover:bg-green-700" role="menuitem">📸 Moments</a>
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
            <a href="#" id="openMomentsMobile" class="block px-4 py-2 rounded hover:bg-green-700" role="menuitem">📸 Moments</a>
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
            <button id="closeSearchPopup" class="mb-6 text-red-600 text-lg font-bold">&times;</button>
        </div>

        <div class="relative">
            <input type="text" id="creatorSearchInput" class="w-full border px-4 py-2 pr-10 rounded focus:outline-none" placeholder="Search by username...">
            <button type="button" id="clearSearchIcon" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 hidden text-2xl">
                &times;
            </button>
        </div>
        <ul id="creatorSearchResults" class="mt-4 space-y-2 max-h-60 overflow-y-auto"></ul>
    </div>
</div>

<!-- Moments Modal -->
<div id="momentsModal" class="hidden fixed inset-0 z-[120] bg-black bg-opacity-50 flex justify-center items-start pt-24">
    <div class="bg-white w-full max-w-md max-h-[60vh] rounded-md shadow-lg overflow-y-auto">
        <!-- Modal Header -->
        <div class="flex justify-between items-center px-4 py-2 border-b sticky top-0 bg-white z-10">
            <h2 class="text-base font-semibold">Moments</h2>
            <button id="closeMomentsModal" class="text-gray-500 hover:text-gray-700 text-xl">&times;</button>
        </div>

        <!-- Modal Body -->
        <div id="momentsContent" class="px-3 py-2 space-y-3">
            <!-- Moments will be dynamically loaded here -->
        </div>
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

                const profileLink = creator.id === creator.loggedInUserId
                    ? `${window.baseUrl}/me/${creator.username}`
                    : `/${creator.username}`;

                li.innerHTML = `
                    <a href="${profileLink}" class="flex items-center space-x-3 hover:bg-gray-100 p-2 rounded">
                        <img src="${creator.profile_photo}" alt="${creator.username}" class="w-8 h-8 rounded-full object-cover">
                        <span class="text-sm font-medium flex items-center gap-2">
                            ${creator.username}
                            ${creator.badge_color ? `
                                <span class="inline-flex items-center gap-1 text-white px-1 py-1 text-xs font-semibold rounded-full"
                                    style="background-color: ${creator.badge_color};"
                                    title="${creator.badge_label} (₹${creator.badge_amount})">
                                    <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 00-1.414 0L9 11.586 
                                            6.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l7-7a1 1 0 
                                            000-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </span>
                            ` : ''}
                        </span>
                    </a>
                `;

                searchResults.appendChild(li);
            });
        } catch (err) {
            console.error('Search error:', err);
        }
    });

    const clearSearchIcon = document.getElementById('clearSearchIcon');

    searchInput?.addEventListener('input', function () {
        if (this.value.length > 0) {
            clearSearchIcon.classList.remove('hidden');
        } else {
            clearSearchIcon.classList.add('hidden');
        }
    });

    clearSearchIcon?.addEventListener('click', () => {
        searchInput.value = '';
        searchInput.focus();
        searchResults.innerHTML = '';
        clearSearchIcon.classList.add('hidden');
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

    // Moments modal toggle
    const openMomentsBtns = [
        document.getElementById('openMoments'),
        document.getElementById('openMomentsMobile')
    ];
    const momentsModal = document.getElementById('momentsModal');
    const closeMomentsModal = document.getElementById('closeMomentsModal');

    openMomentsBtns.forEach(btn => {
        btn?.addEventListener('click', () => {
            momentsModal.classList.remove('hidden');
        });
    });

    closeMomentsModal?.addEventListener('click', () => {
        momentsModal.classList.add('hidden');
    });

    // Close moments modal on outside click
    document.addEventListener('DOMContentLoaded', () => {
        const momentsModal = document.getElementById('momentsModal');
        const openMomentsBtns = [
            document.getElementById('openMoments'),
            document.getElementById('openMomentsMobile')
        ];
        const closeMomentsModal = document.getElementById('closeMomentsModal');
        const modalContent = momentsModal.querySelector('.bg-white');

        // Show modal on button click
        openMomentsBtns.forEach(btn => {
            btn?.addEventListener('click', (e) => {
                e.preventDefault();
                momentsModal.classList.remove('hidden');
            });
        });

        // Close modal on close button click
        closeMomentsModal?.addEventListener('click', () => {
            momentsModal.classList.add('hidden');
        });

        // Close modal on outside click
        document.addEventListener('click', function (e) {
            if (
                !momentsModal.classList.contains('hidden') &&
                !modalContent.contains(e.target) &&
                !e.target.closest('#openMoments') &&
                !e.target.closest('#openMomentsMobile')
            ) {
                momentsModal.classList.add('hidden');
            }
        });
    });


</script>

<script>
     function fetchMoments() {
        fetch('/moments/fetch')
            .then(response => response.json())
            .then(data => {
                const container = document.querySelector('#momentsModal div > div:last-child');

                if (data.length > 0) {
                    container.innerHTML = data.map(m => {
                        const actor = m.actor;
                        const profileLink = `/${actor.username}`;
                        const photo = actor.profile_photo
                            ? `/storage/public/profile_photos/${actor.profile_photo}`
                            : '/default-avatar.jpg';

                        let mediaElement = '';
                        if (m.media_url) {
                            if (m.media_url.endsWith('.mp4')) {
                                mediaElement = `
                                    <video src="${m.media_url}" class="w-16 h-16 object-cover rounded" muted loop playsinline></video>
                                `;
                            } else {
                                mediaElement = `
                                    <img src="${m.media_url}" class="w-16 h-16 object-cover rounded" alt="media">
                                `;
                            }
                        }

                        // Conditionally show follow or unfollow button
                        let followButton = '';
                        if (m.type === 'follow') {
                            const buttonText = m.is_following_actor ? 'Unfollow' : 'Follow';
                            const buttonClass = m.is_following_actor
                                ? 'bg-white text-black border border-black hover:bg-gray-100'
                                : 'bg-green-500 text-white hover:bg-green-600';

                            followButton = `
                                <button id="follow-btn-${actor.id}" data-creator-id="${actor.id}"
                                    onclick="toggleFollow(${actor.id}, this)"
                                    class="ml-2 px-4 py-1 rounded-full transition duration-300 ${buttonClass}">
                                    ${buttonText}
                                </button>
                            `;
                        }

                        return `
                            <div class="flex items-center space-x-3 py-3 border-b">
                                <a href="${profileLink}">
                                    <img src="${photo}" class="w-9 h-9 rounded-full object-cover mt-1" alt="${actor.username}">
                                </a>
                                <div class="flex justify-between items-center w-full">
                                    <div class="flex-1 pr-2 text-sm">
                                        <div class="flex justify-between items-center">
                                            <p class="flex items-center flex-wrap">
                                                <a href="${profileLink}" class="font-semibold hover:underline mr-1">${actor.username}</a>
                                                ${m.type === 'donation' ? `<span class="text-green-600 font-semibold">${m.message}</span>` : m.message}
                                            </p>
                                            ${followButton}
                                        </div>
                                        <p class="text-xs text-gray-500 mt-0.5">${m.created_at}</p>
                                    </div>
                                    ${mediaElement ? `<a href="${m.link}" onclick="event.preventDefault();">${mediaElement}</a>` : ''}
                                </div>
                            </div>
                        `;
                    }).join('');
                } else {
                    container.innerHTML = '<p class="text-gray-600">You have no new moments.</p>';
                }
            });
    }
    setInterval(fetchMoments, 10000);
    document.addEventListener("DOMContentLoaded", fetchMoments);
    function toggleFollow(userId, btn) {
        fetch(`/creator/${userId}/toggle-follow`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({}) // Laravel expects a body in POST
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === 'followed') {
                btn.className = 'px-4 py-1 rounded-full transition duration-300 bg-white text-black border border-black hover:bg-gray-100';
                btn.textContent = 'Unfollow';
            } else if (result.status === 'unfollowed') {
                btn.className = 'px-4 py-1 rounded-full transition duration-300 bg-green-500 text-white hover:bg-green-600';
                btn.textContent = 'Follow';
            }
        })
        .catch(error => console.error('Error:', error));
    }
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
    #closeSearchPopup{
    font-size: 28px;
    }
    #closeMomentsModal{
    font-size: 28px;
    }
    #searchPopup {
    z-index: 110;
    }

    #momentsModal {
        z-index: 120;
    }

    #mobileSidebar {
        z-index: 130;
    }
 
</style>
