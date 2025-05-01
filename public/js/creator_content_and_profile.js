window.addEventListener('load', function () {
    const modal = document.getElementById('contentModal');
    modal.style.display = 'none';
});

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
    
                modalContent.innerHTML = '';
                creatorUsernameElement.textContent = data.creator_username;
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
            const likeCount = button.querySelector('.like-count');

    
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
                 
                // Update corresponding other button (main or modal)
                const otherSelector = button.classList.contains('modal-like-btn') ? '.like-btn' : '.modal-like-btn';
                const otherButton = document.querySelector(`${otherSelector}[data-content-id="${contentId}"]`);
                if (otherButton) {
                    const otherLikeText = otherButton.querySelector('.like-text');
                    const otherLikeCount = otherButton.querySelector('.like-count');
    
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

function openFollowModal(creatorId, type) {
    const modal = document.getElementById('followModal');
    modal.style.display = 'flex';

    const title = document.getElementById('followModalTitle');
    title.textContent = type.charAt(0).toUpperCase() + type.slice(1);

    fetch(`/creator/${creatorId}/${type}`) // new route
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('followModalContent');
            container.innerHTML = '';

            if (data.length === 0) {
                container.innerHTML = `<p class="text-center text-gray-500">No ${type} found.</p>`;
                return;
            }

            data.forEach(user => {
                const userHtml = `
                    <div class="flex items-center space-x-4 p-2 hover:bg-gray-100 rounded">
                        <img src="${window.baseUrl}/storage/public/profile_photos/${user.profile_photo}" class="w-10 h-10 rounded-full object-cover">
                        <div>
                            <div class="font-semibold">${user.username}</div>
                            <div class="text-gray-500 text-sm">${user.name}</div>
                        </div>
                    </div>
                `;
                container.innerHTML += userHtml;
            });
        })
        .catch(error => {
            console.error('Error fetching follow data:', error);
        });
}

function closeFollowModal() {
    document.getElementById('followModal').style.display = 'none';
}
