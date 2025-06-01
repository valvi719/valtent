@extends('layouts.app')

@section('title', 'Home')

@section('content')


<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-semibold text-center mb-6">Welcome to Your Feed</h1>

    @if($suggestedCreators->isNotEmpty())
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <h2 class="text-xl font-semibold mb-3">Suggested for You</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach($suggestedCreators as $creator)
                    @php
                        $badge = getDonationBadgeStyle($creator->id);
                    @endphp
                    <div class="flex flex-col items-center">
                        <a href="#" class="block relative w-16 h-16 rounded-full overflow-hidden mb-2">
                            <img src="{{ asset('storage/public/profile_photos/' . $creator->profile_photo) }}" alt="{{ $creator->name}}" class="w-full h-full object-cover">
                        </a>
                        <a href="/{{$creator->username}}" class="text-sm font-semibold text-center hover:underline">{{ $creator->username }}  
                            @if($badge)
                            <span class="inline-flex items-center gap-1 text-white px-1 py-1 mb-3 text-xs font-semibold rounded-full" style="background-color: {{ $badge['color'] }};"
                            title="{{ $badge['label'] }} (₹{{ number_format($badge['amount']) }})">
                                <svg class="w14 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 00-1.414 0L9 11.586 6.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l7-7a1 1 0 000-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </span>
                            @endif
                        </a>
                        @auth
                            @if(auth()->id() !== $creator->id)
                                <div class="mt-1">
                                    <button id="follow-btn-{{ $creator->id }}"
                                        data-creator-id="{{ $creator->id }}"
                                        class="px-4 py-1 rounded-full transition duration-300
                                            {{-- You'll need to determine the initial state in PHP --}}
                                            {{--  For simplicity, let's assume you have a $isFollowing array in your controller --}}
                                            {{ in_array($creator->id, $followingIds ?? []) 
                                                ? 'bg-white text-black border border-black hover:bg-gray-100'
                                                : 'bg-green-500 text-white hover:bg-green-600' }}">
                                        {{ in_array($creator->id, $followingIds ?? []) ? 'Unfollow' : 'Follow' }}
                                    </button>
                                </div>
                            @endif
                        @endauth
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($contents->isEmpty())
        <p class="text-center text-gray-600">No content available. Please follow some creators!</p>
    @else
        @foreach($contents as $content)
            <!-- Card container: Aspect ratio 1:1 -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6 w-full sm:w-11/12 md:w-10/12 lg:w-8/12 xl:w-6/12 mx-auto">
                <!-- Content Header: User's Profile and post time -->
                <div class="flex items-center p-4 border-b">
                    <!-- Profile Image -->
                    <img src="{{ asset('storage/public/profile_photos/' . $content->creator->profile_photo) }}" alt="User Avatar" class="w-12 h-12 rounded-full mr-3">
                    <div class="flex flex-col flex-grow">
                    @php
                        $badge = getDonationBadgeStyle($content->cre_id);
                    @endphp
                        @if(Auth::user()->username == $content->creator->username)
                            <a href="me/{{ $content->creator->username }}" class="font-semibold text-lg">{{ $content->creator->username }} 
                                @if($badge)
                                    <span class="inline-flex items-center gap-1 text-white px-1 py-1 mb-3 text-xs font-semibold rounded-full" style="background-color: {{ $badge['color'] }};"
                                    title="{{ $badge['label'] }} (₹{{ number_format($badge['amount']) }})">
                                        <svg class="w14 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 00-1.414 0L9 11.586 6.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l7-7a1 1 0 000-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                @endif
                            </a>
                        @else
                            <a href="{{ $content->creator->username }}" class="font-semibold text-lg">{{ $content->creator->username }} 
                                @if($badge)
                                    <span class="inline-flex items-center gap-1 text-white px-1 py-1 mb-3 text-xs font-semibold rounded-full" style="background-color: {{ $badge['color'] }};" title="{{ $badge['label'] }} (₹{{ number_format($badge['amount']) }})">
                                        <svg class="w14 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 00-1.414 0L9 11.586 6.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l7-7a1 1 0 000-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                @endif
                            </a>
                        @endif    
                        <p class="text-gray-500 text-sm">{{ $content->created_at->diffForHumans() }}</p>
                    </div>
                    <button data-open-donation-modal class="donate-btn px-4 py-1 rounded-full transition duration-300 bg-green-500 text-white hover:bg-green-600" 
                        data-content-id="{{ $content->id }}" 
                        data-recipient-id="{{ $content->cre_id }}" 
                        onclick="openDonationModal({{ $content->id }},{{ $content->cre_id }})">
                        Donate
                    </button>
                    <button data-open-donors-modal class="donate-btn px-4 py-1 rounded-full transition duration-300 bg-green-500 text-white hover:bg-green-600"
                        onclick="openDonatorsModal({{ $content->id }})">
                        Donors
                    </button>
                </div>
            
                <!-- Media Content (Image or Video) with Aspect Ratio 1:1 -->
                <div class="relative w-full" style="padding-top: 100%;"> <!-- 1:1 aspect ratio -->
                    @if($content->type == 'Media')
                        @if(str_contains($content->value, '.mp4'))
                            <video class="absolute top-0 left-0 w-full h-full object-cover" controls>
                                <source src="{{ asset('storage/' . $content->value) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @else
                            <img class="absolute top-0 left-0 w-full h-full object-cover" src="{{ asset('storage/' . $content->value) }}" alt="{{ $content->name }}">
                        @endif
                    @else
                        <div class="w-full h-full bg-gray-300 flex items-center justify-center">
                            <span class="text-white">NFT</span>
                        </div>
                    @endif
                </div>

                <!-- Content Footer: Like Button, Post Description -->
                <div class="p-4">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex space-x-4">
                            <!-- Like Button -->
                            <button class="like-btn text-xl focus:outline-none" data-content-id="{{ $content->id }}">
                                <span class="like-text text-{{ in_array($content->id, $likedContents) ? 'green' : 'gray' }}-500">
                                    {{ in_array($content->id, $likedContents) ? '♥' : '♡' }}
                                </span>
                                <span class="like-count">
                                    @if($content->likes()->count() > 1)
                                        {{ $content->likes()->count() }} Likes
                                    @elseif($content->likes()->count() == 1)
                                        1 Like
                                    @endif
                                </span>
                            </button>
                            @if (Auth::id() == $content->cre_id && $content->likes()->count() > 1)
                                <button class="extract-link" data-content-id="{{ $content->id }}">Extract</button>
                            @endif
                        </div>
                        
                    </div>

                    <!-- Post Description -->
                    <p class="text-gray-700 mt-2">
                        <span class="font-semibold">{{ $content->name }}</span> {{ $content->name }}
                    </p>
                </div>
            </div>
        @endforeach
    @endif
</div>
<!-- Donation Modal -->
<div id="donationModal"  class="donot-display-donation-modal fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-72 relative">
        <h5 class="text-lg font-bold mb-2">Donate to Creator</h5>
        <input type="number" id="donationAmount" class="form-control mt-2 w-full border rounded p-2" placeholder="Enter amount in ₹" min="1">
        <input type="hidden" id="donationContentId">
        <input type="hidden" id="recipient_id">
        <button class="btn btn-success mt-3 w-full bg-green-500 text-white py-2 rounded hover:bg-green-600" onclick="submitDonation()">Donate</button>
        <button class="btn btn-secondary mt-2 w-full bg-gray-300 text-black py-2 rounded hover:bg-gray-400" onclick="closeDonationModal()">Cancel</button>
    </div>
</div>
<!-- Donors Modal -->
<div id="donorsModal" class="donot-display-donors-modal" style="position:fixed; inset:0; background:rgba(0,0,0,0.6); justify-content:center; align-items:center; z-index:1050;">

    <div class="bg-white w-80 max-h-[90vh] rounded-lg shadow-lg overflow-hidden flex flex-col relative">
        <div class="p-4 border-b flex justify-between items-center">
            <h2 class="text-lg font-semibold">Top Donors</h2>
            <button onclick="closeDonorsModal()" class="text-3xl font-bold text-gray-600 hover:text-black">&times;</button>
        </div>
        <div class="px-4 py-2">
            <input type="text" id="donorSearch" placeholder="Search donors..." class="w-full p-2 border rounded">
        </div>
        <div id="donorsList" class="overflow-y-auto px-4 py-2 space-y-3 flex-1">
            <!-- Donor items inserted here -->
        </div>
    </div>
</div>
<script>
    // AJAX script to handle like/unlike
    document.querySelectorAll('.like-btn').forEach(button => {
        button.addEventListener('click', function() {
            let contentId = this.getAttribute('data-content-id');
            let likeText = this.querySelector('.like-text');
            let likeCount = this.querySelector('.like-count');

            // Send AJAX request to like/unlike the content
            fetch(`/content/${contentId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({}),
            })
            .then(response => response.json())
            .then(data => {
                if (data.message === 'liked') {
                    likeText.textContent = '♥';  // Change icon to liked
                    likeText.classList.replace('text-gray-500', 'text-green-500');  // Change color to green
                } else {
                    likeText.textContent = '♡';  // Change icon to unliked
                    likeText.classList.replace('text-green-500', 'text-gray-500');  // Change color to gray
                }
                 // Update the like count based on the response
                if (data.like_count === 0) {
                    likeCount.textContent = '';  // Hide the like count if it's 0
                } else if (data.like_count === 1) {
                    likeCount.textContent = '1 Like';  // Show "1 Like" when count is 1
                } else {
                    likeCount.textContent = `${data.like_count} Likes`;  // Show the count with "Likes" if greater than 1
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });

    // Extract button click handler
    document.querySelectorAll('.extract-link').forEach(button => {
        button.addEventListener('click', function() {
            let contentId = this.getAttribute('data-content-id');
            
            // Send AJAX request to the "extract" route
            fetch(`/content/${contentId}/extract`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({}),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Find the like-count element within the same card/container as the extract button
                    let likeCountElement = this.closest('.bg-white').querySelector('.like-count');
                    let likeText = this.closest('.bg-white').querySelector('.like-text');
                    if (likeCountElement) {
                        likeCountElement.textContent = '0'; // Set like count to 0
                        likeText.textContent = '♡';
                    }

                    // Optionally, hide the "Extract" button
                    this.style.display = 'none';  // Hide the extract button
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });

    //Toggle Follow Unfollow
    document.addEventListener('DOMContentLoaded', function () {
        // Get all follow buttons.  We'll use a class selector
        const buttons = document.querySelectorAll('[id^="follow-btn-"]');

        buttons.forEach(button => {
            button.addEventListener('click', function () {
                const creatorId = this.getAttribute('data-creator-id');
                const clickedButton = this; // Store the button reference

                fetch(`/creator/${creatorId}/toggle-follow`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'followed') {
                        clickedButton.className = 'px-4 py-1 rounded-full transition duration-300 bg-white text-black border border-black hover:bg-gray-100';
                        clickedButton.innerText = 'Unfollow';
                    } else if (data.status === 'unfollowed') {
                        clickedButton.className = 'px-4 py-1 rounded-full transition duration-300 bg-green-500 text-white hover:bg-green-600';
                        clickedButton.innerText = 'Follow';
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    });

    //Donation Process
    //Donation Process
        function openDonationModal(contentId, recipient_id) {
        document.getElementById('donationContentId').value = contentId;
        document.getElementById('recipient_id').value = recipient_id;
        const donationModal = document.getElementById('donationModal');
        donationModal.classList.remove('donot-display-donation-modal');
        donationModal.classList.add('display-donation-modal');
    }

    function closeDonationModal() {
        const donationModal = document.getElementById('donationModal');
        donationModal.classList.remove('display-donation-modal');
        donationModal.classList.add('donot-display-donation-modal');
        document.getElementById('donationAmount').value = '';
    }

    // Prevent decimal point in donation amount
        document.getElementById('donationAmount').addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, ''); // Only allow digits
    });

    function submitDonation() {
        const amount = document.getElementById('donationAmount').value;
        const contentId = document.getElementById('donationContentId').value;
        const recipient_id = document.getElementById('recipient_id').value;

        if (!amount || amount <= 0) {
            alert('Please enter a valid amount.');
            return;
        }

        fetch('/donate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                content_id: contentId,
                amount: amount,
                recipient_id:recipient_id,
            })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.error || 'Donation successful!');
            closeDonationModal();
        })
        .catch(error => {
            console.error('Donation error:', error);
            alert(data.error ||'Something went wrong.');
        });
    }

    //Close Donation Modal Outside Click
    document.addEventListener('click', function (e) {
        const modal = document.getElementById('donationModal');
        const modalContent = modal?.querySelector('.bg-white');

        // Only trigger close if modal is open AND click is outside content
        if (
            modal?.classList.contains('display-donation-modal') &&
            modalContent &&
            !modalContent.contains(e.target) &&
            !e.target.closest('[data-open-donation-modal]') // Prevent immediate close if clicking open button
        ) {
            closeDonationModal();
        }
    });

</script>
<!-- Donation/Donors  code -->
<script>
    let currentContentId = null;

    function openDonatorsModal(contentId) {
        const modal = document.getElementById('donorsModal');
        currentContentId = contentId;
        const donationModal = document.getElementById('donorsModal');
        donationModal.classList.remove('donot-display-donors-modal');
        donationModal.classList.add('display-donors-modal');
        fetchDonors(contentId, '');
    }
    
    function number_format(number) {
        return new Intl.NumberFormat('en-IN').format(number);
    }

    function fetchDonors(contentId, search = '') {
        fetch(`/content/${contentId}/donors/search?q=${encodeURIComponent(search)}`)
            .then(res => res.json())
            .then(data => {
                const donorsList = document.getElementById('donorsList');
                donorsList.innerHTML = '';

                if (data.donors.length === 0) {
                    donorsList.innerHTML = '<p class="text-center text-gray-500">No donors found.</p>';
                } else {
                    data.donors.forEach(donor => {
                        const route = donor.id === data.auth_id ? `/me/${donor.username}` : `/${donor.username}`;

                        // Create container div
                        const donorItem = document.createElement('div');
                        donorItem.classList.add('flex', 'items-center', 'justify-between', 'hover:bg-gray-100', 'p-2', 'rounded');

                        // Left: profile + amount
                        const leftSection = document.createElement('a');
                        leftSection.href = route;
                        leftSection.classList.add('flex', 'items-center', 'gap-3');
                        leftSection.innerHTML = `
                            <img src="/storage/public/profile_photos/${donor.profile_photo}" class="w-10 h-10 rounded-full object-cover">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium">${donor.username}</span>
                                    ${donor.badge_color && donor.badge_label ? `
                                        <span class="inline-flex items-center gap-1 text-white px-1 py-1 mb2 text-xs font-semibold rounded-full"
                                            style="background-color: ${donor.badge_color};"
                                            title="${donor.badge_label} (₹${number_format(donor.total_amount)})">
                                            <svg class="w14 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 00-1.414 0L9 11.586 6.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l7-7a1 1 0 000-1.414z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </span>` : ''}
                                </div>
                                <p class="text-sm text-gray-600">₹${parseFloat(donor.total_amount).toFixed(2)}</p>
                            </div>
                        `;

                        // Right: follow/unfollow or "You"
                        let rightSection;
                        if (donor.id === data.auth_id) {
                            rightSection = document.createElement('span');
                            rightSection.className = 'text-sm text-gray-500';
                            rightSection.textContent = 'You';
                        } else {
                            const followBtn = document.createElement('button');
                            followBtn.id = `follow-btn-${donor.id}`;
                            followBtn.dataset.creatorId = donor.id;
                            followBtn.className = `px-4 py-1 rounded-full transition duration-300 ${
                                donor.is_following
                                    ? 'bg-white text-black border border-black hover:bg-gray-100'
                                    : 'bg-green-500 text-white hover:bg-green-600'
                            }`;
                            followBtn.textContent = donor.is_following ? 'Unfollow' : 'Follow';

                            followBtn.addEventListener('click', function (e) {
                                e.preventDefault();
                                e.stopPropagation();

                                fetch(`/creator/${donor.id}/toggle-follow`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                    },
                                })
                                    .then(response => response.json())
                                    .then(result => {
                                        if (result.status === 'followed') {
                                            followBtn.className = 'px-4 py-1 rounded-full transition duration-300 bg-white text-black border border-black hover:bg-gray-100';
                                            followBtn.textContent = 'Unfollow';
                                        } else if (result.status === 'unfollowed') {
                                            followBtn.className = 'px-4 py-1 rounded-full transition duration-300 bg-green-500 text-white hover:bg-green-600';
                                            followBtn.textContent = 'Follow';
                                        }
                                    })
                                    .catch(error => console.error('Error:', error));

                            });

                            rightSection = followBtn;
                        }

                        donorItem.appendChild(leftSection);
                        donorItem.appendChild(rightSection);
                        donorsList.appendChild(donorItem);
                    });
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error loading donors.');
            });
    }

    function closeDonorsModal() {
        const donationModal = document.getElementById('donorsModal');
        donationModal.classList.remove('display-donors-modal');
        donationModal.classList.add('donot-display-donors-modal');
        document.getElementById('donorSearch').value = '';
    }

    document.getElementById('donorSearch').addEventListener('input', function () {
        if (currentContentId) {
            fetchDonors(currentContentId, this.value);
        }
    });

    // Outside click closes modal
    document.addEventListener('click', function (e) {
        const modal = document.getElementById('donorsModal');
        const modalContent = modal?.querySelector('.bg-white');

        // Only trigger close if modal is open AND click is outside content
        if (
            modal?.classList.contains('display-donors-modal') &&
            modalContent &&
            !modalContent.contains(e.target) &&
            !e.target.closest('[data-open-donors-modal]') // Prevent immediate close if clicking open button
        ) {
            closeDonorsModal();
        }
    });
</script>
<!-- end Donors -->
<style>
        /* CSS to style the link as a button */
        .extract-link {
            display: inline-block;        /* Make it behave like a button */
            padding: 10px 20px;           /* Add padding to make it button-like */
            color: white;                 /* White text color */
            background-color: green;      /* Green background */
            text-decoration: none;        /* Remove underline */
            border-radius: 5px;           /* Rounded corners */
            font-weight: bold;            /* Make the text bold */
            text-align: center;           /* Center the text inside the button */
            font-size: 14px;              /* Set font size */
            cursor: pointer;             /* Change cursor to pointer on hover */
            transition: background-color 0.3s ease, transform 0.2s; /* Smooth transition for hover effect */
        }

        /* Optional: Hover effect for the button */
        .extract-link:hover {
            background-color: darkgreen;  /* Darker green on hover */
            transform: scale(1.05);        /* Slightly enlarge the button on hover */
        }

        /* #donationModal {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        } */

        .display-donation-modal{
            display: flex;
        }

        .donot-display-donation-modal{
            display: none;
        }

        .display-donors-modal{
            display: flex;
        }

        .donot-display-donors-modal{
            display: none;
        }

        

</style>
@endsection
