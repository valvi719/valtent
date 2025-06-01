window.addEventListener('load', function () {
    const modal = document.getElementById('contentModal');
    modal.style.display = 'none';
});

function number_format(number) {
    return new Intl.NumberFormat('en-IN').format(number);
}

function openModal(element) {
    const contentId = element.querySelector('input[name="content_id"]').value;
    const modal = document.getElementById('contentModal');
    modal.style.display = 'flex';

    fetch(`/modalcontent/${contentId}`)
        .then(response => response.json())
        .then(data => {
            const modalContent = document.getElementById('modalContent');
            const creatorUsernameElement = document.getElementById('creatorUsername');
            const creatorProfilePhotoElement = document.getElementById('creatorProfilePhoto');
            const moreMenu = document.getElementById('moreMenu');
            const moreOptionsButton = document.getElementById('moreOptions');
            console.log(data.badge_color);
            modalContent.innerHTML = '';
            creatorUsernameElement.innerHTML = `
                <span class="text-sm font-medium">${data.creator_username}</span>
                ${data.badge_color ? `
                    <span class="inline-flex items-center gap-1 text-white px-1 py-1 text-xs font-semibold rounded-full" style="background-color: ${data.badge_color.color};" title="${data.badge_color.label} (₹${number_format(data.badge_color.amount)})" >
                        <svg class="w14 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 00-1.414 0L9 11.586 6.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l7-7a1 1 0 000-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </span>
                ` : ''}
            `;
            creatorProfilePhotoElement.src = data.creator_profile_photo;

            // Reset and add fresh event listener
            if (moreMenu && moreOptionsButton) {
                moreOptionsButton.onclick = (e) => {
                    e.stopPropagation();
                    moreMenu.classList.toggle('hidden');
                };

                document.onclick = () => {
                    moreMenu.classList.add('hidden');
                };

                moreMenu.onclick = (e) => {
                    e.stopPropagation();
                };
            }

            let mediaHtml = '';

            if (data.type === 'Media') {
                if (data.value.includes('.mp4')) {
                    mediaHtml = `
                            <video class="w-full max-h-[300px] object-contain rounded-lg" controls>
                                <source src="${data.url}" type="video/mp4">
                            </video>`;
                } else {
                    mediaHtml = `
                                <img class="w-full max-h-[300px] object-contain rounded-lg" src="${data.url}" alt="${data.name}">
                            `;
                }
            } else {
                mediaHtml = `
                            <div class="w-full h-48 bg-gray-300 flex items-center justify-center rounded-lg">
                                <span class="text-white">NFT</span>
                            </div>
                        `;
            }

            modalContent.innerHTML = `
                    <div class="space-y-2 text-left">
                        ${mediaHtml}
                        <div class="flex items-center gap-2 mt-2">
                            <button class="modal-like-btn text-xl focus:outline-none" data-content-id="${data.id}">
                                <span class="like-text text-${data.likedContents.includes(data.id) ? 'green' : 'gray'}-500">
                                    ${data.likedContents.includes(data.id) ? '♥' : '♡'}
                                </span>
                            </button>
                            <span class="like-count text-sm text-gray-600">
                                ${data.like_count > 1 ? data.like_count + ' Likes' : data.like_count === 1 ? '1 Like' : ''}
                            </span>
                        </div>
                        <h3 class="text-base font-medium mt-1">${data.name}</h3>
                        <p class="text-gray-500 text-sm">${data.type}</p>
                    </div>
                `;


            document.getElementById('deleteContent').setAttribute('data-content-id', data.id);
        })
        .catch(error => {
            console.error('Error fetching content:', error);
        });
}

document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('click', function (e) {
        // Handle like/unlike button clicks
        if (e.target.closest('.modal-like-btn') || e.target.closest('.like-btn')) {
            
            const button = e.target.closest('.modal-like-btn') || e.target.closest('.like-btn');
            const contentId = button.getAttribute('data-content-id');
            const likeText = button.querySelector('.like-text');
            let likeCount;
            if (button.classList.contains('modal-like-btn')) {
                likeCount = document.querySelector('#modalContent .like-count');
            } else {
                likeCount = document.querySelector(`.like-count[data-content-id="${contentId}"]`);
            }


            fetch(`/content/${contentId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({}),
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw new Error(errorData.error || 'An unexpected error occurred.');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    const isLiked = data.message === 'liked';
                    const countText = data.like_count === 0
                        ? ''
                        : data.like_count === 1
                            ? '1 Like'
                            : `${data.like_count} Likes`;

                    // Update current button (modal or main)
                    if (likeText) {
                        likeText.textContent = isLiked ? '♥' : '♡';
                        likeText.className = `like-text text-${isLiked ? 'green' : 'gray'}-500`;
                    }

                    $('#modalContent .like-count').text(countText);
                    $(e.target.closest('.like-btn')).find('.like-count').text(countText);

                    

                    // Update corresponding other button (main or modal)
                    const otherSelector = button.classList.contains('modal-like-btn') ? '.like-btn' : '.modal-like-btn';
                    const otherButton = document.querySelector(`${otherSelector}[data-content-id="${contentId}"]`);
                    if (otherButton) {
                        const otherLikeText = otherButton.querySelector('.like-text');
                        const otherLikeCount = document.querySelector(`.like-count[data-content-id="${contentId}"]`);

                        if (otherLikeText) {
                            otherLikeText.textContent = isLiked ? '♥' : '♡';
                            otherLikeText.className = `like-text text-${isLiked ? 'green' : 'gray'}-500`;
                        }
                        if (otherLikeCount) {
                            otherLikeCount.textContent = countText;
                        }
                    }
                })
                .catch(error => {
                    alert(error.message);
                });
        }

        // Handle modal close button
        if (e.target.id === 'closeModal') {
            document.getElementById('contentModal').style.display = 'none';
        }

        // CLOSE MODALS on background click (contentModal and followModal)
        const contentModal = document.getElementById('contentModal');
        const followModal = document.getElementById('followModal');

        if (e.target === contentModal) {
            contentModal.style.display = 'none';
        }

        if (e.target === followModal) {
            followModal.style.display = 'none';
        }

        // Handle more options menu toggle
        if (e.target.id === 'moreOptions') {
            const menu = document.getElementById('moreMenu');
            if (menu) menu.classList.toggle('hidden');
        } else {
            const menu = document.getElementById('moreMenu');
            if (menu && !menu.contains(e.target)) {
                menu.classList.add('hidden');
            }
        }

        // Handle content deletion
        if (e.target.id === 'deleteContent') {
            const contentId = e.target.getAttribute('data-content-id');

            fetch(`/content/${contentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
            })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    });

});


function openFollowersModal(creatorId) {
    openFollowModal(creatorId, 'followers');
}

function openFollowingModal(creatorId) {
    openFollowModal(creatorId, 'following');
}

let currentFollowType = '';
let currentCreatorId = '';

function openFollowModal(creatorId, type) {
    const modal = document.getElementById('followModal');
    modal.style.display = 'flex';

    const title = document.getElementById('followModalTitle');
    title.textContent = type.charAt(0).toUpperCase() + type.slice(1);

    const modalContent = document.getElementById('followModalContent');
    modalContent.innerHTML = `
        <div class="p-3">
            <div class="relative">
                <input type="text" id="followSearchInput" onkeyup="searchFollowersFollowing()" class="w-full pl-3 pr-10 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300" placeholder="Search ${type}">
                <button id="clearFollowSearch" onclick="clearFollowSearch()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 cursor-pointer text-2xl hidden">&times;</button>
            </div>
            <div id="followSearchResult" class="max-h-72 overflow-y-auto mt-2">
                </div>
            <div id="initialFollowList" class="max-h-72 overflow-y-auto mt-2">
                </div>
        </div>
    `;

    currentFollowType = type;
    currentCreatorId = creatorId;
    fetchFollowList(creatorId, type);
}

function closeFollowModal() {
    document.getElementById('followModal').style.display = 'none';
    document.getElementById('followSearchInput').value = ''; // Clear search input
    document.getElementById('followSearchResult').innerHTML = ''; // Clear search results
    document.getElementById('initialFollowList').innerHTML = ''; // Clear initial list
}

function fetchFollowList(creatorId, type, searchTerm = '') {
    let url = `/creator/${creatorId}/${type}`;
    if (searchTerm) {
        url += `?search=${searchTerm}`;
    }

    const initialFollowList = document.getElementById('initialFollowList');
    const followSearchResult = document.getElementById('followSearchResult');
    const clearSearchButton = document.getElementById('clearFollowSearch');

    if (!searchTerm) {
        followSearchResult.innerHTML = '';
        initialFollowList.innerHTML = '<p class="text-center text-gray-500">Loading...</p>';
        clearSearchButton.classList.add('hidden');
    } else {
        initialFollowList.innerHTML = '';
        followSearchResult.innerHTML = '<p class="text-center text-gray-500">Searching...</p>';
        clearSearchButton.classList.remove('hidden');
    }


    fetch(url)
        .then(response => response.json())
        .then(data => {
            const container = searchTerm ? followSearchResult : initialFollowList;
            container.innerHTML = '';

            const creatorlist = type === 'followers' ? data.followers : data.following;
        
            if (!creatorlist || creatorlist.length === 0) {
                container.innerHTML = `<p class="text-center text-gray-500">No ${type} found${searchTerm ? ' matching your search' : ''}.</p>`;
                return;
            }
        
            // Render followers/following
            creatorlist.forEach(user => {
                const profileLink = `${window.baseUrl}/${user.username}`;
                container.innerHTML += `
                    <a href="${profileLink}" class="flex items-center space-x-3 hover:bg-gray-100 p-2 rounded">
                        <img src="${window.baseUrl}/storage/public/profile_photos/${user.profile_photo}" alt="${user.username}" class="w-8 h-8 rounded-full object-cover">
                        <span class="text-sm font-medium">${user.username}</span>
                    </a>
                `;
            });
        
            // Render suggested at the end
            if (!searchTerm && data.suggested && data.suggested.length > 0) {
                container.innerHTML += `<hr class="my-2 border-gray-300"><p class="text-sm font-semibold text-gray-600 mb-1">Suggested</p>`;
                data.suggested.forEach(user => {
                    const profileLink = `${window.baseUrl}/${user.username}`;
                    container.innerHTML += `
                        <a href="${profileLink}" class="flex items-center space-x-3 hover:bg-gray-100 p-2 rounded">
                            <img src="${window.baseUrl}/storage/public/profile_photos/${user.profile_photo}" alt="${user.username}" class="w-8 h-8 rounded-full object-cover">
                            <span class="text-sm font-medium">${user.username}</span>
                        </a>
                    `;
                });
            }
        })
        .catch(error => {
            console.error('Error fetching follow data:', error);
            const container = searchTerm ? followSearchResult : initialFollowList;
            container.innerHTML = '<p class="text-center text-red-500">Error loading data.</p>';
        });
}

function searchFollowersFollowing() {
    const searchTerm = document.getElementById('followSearchInput').value.trim();
    fetchFollowList(currentCreatorId, currentFollowType, searchTerm);
}

function clearFollowSearch() {
    document.getElementById('followSearchInput').value = '';
    fetchFollowList(currentCreatorId, currentFollowType);
}   