<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Conbank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use FFMpeg\FFMpeg;
use App\Models\ContentLike;
use App\Models\Creator;
use App\Models\Follower;
use App\Models\Following;

class ContentController extends Controller
{
    public function create($id)
    {
    
        return view('content_create',compact('id'));
        
    }
    public function store(Request $request, $id)
    {
        // Decrypt the creator ID
        $cre_id = Crypt::decrypt($id);

        // Validate the request data
        $validator = Content::validate($request->all());
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Handle file upload if Media type is selected
        $value = null;
        $duration = null;  // Initialize duration variable
        
        // Check if file is provided
        if ($request->type === 'Media' && $request->hasFile('value')) {
            $file = $request->file('value');
            
            // Get MIME type and extension of the uploaded file
            $mimeType = $file->getMimeType();
            $fileExtension = $file->getClientOriginalExtension();
            
            // Define allowed video MIME types and file extensions
            $allowedMimeTypes = ['video/mp4', 'video/avi', 'video/mkv', 'video/webm', 'video/x-msvideo', 'video/quicktime'];
            $allowedExtensions = ['mp4', 'avi', 'mkv', 'webm', 'mov'];

            // Check if the file is a video based on MIME type or extension
            $isVideo = in_array($mimeType, $allowedMimeTypes) || in_array(strtolower($fileExtension), $allowedExtensions);

            if ($isVideo) {
                // Store video file in the "public/media" directory
                $value = $file->store('media', 'public'); 

                // Use FFMpeg to get the video duration
                $video = FFMpeg::create([
                    'ffmpeg.binaries'  => 'C:\\ffmpeg-7.1-essentials_build\\bin\\ffmpeg.exe',   // Full path to ffmpeg binary
                    'ffprobe.binaries' => 'C:\\ffmpeg-7.1-essentials_build\\bin\\ffprobe.exe',  // Full path to ffprobe binary
                    'timeout'           => 3600,                             // Timeout for processes
                    'ffmpeg.threads'    => 12                                // Number of threads to use
                ]);

                $videoFile = public_path('storage/' . $value); // Get the full path to the video
                $videoDuration = $video->open($videoFile)->getFormat()->get('duration');  // Get the video duration

                $duration = round($videoDuration); // Round it to the nearest whole number (in seconds)
            } else {
                // If the file is not a video, treat it as a non-video file (like an image)
                $value = $file->store('media', 'public');  // Store non-video media in the same directory
            }
        }

        // Create the Content and save to the database
        Content::create([
            'name' => $request->name,
            'type' => $request->type,
            'value' => $value, // Store the file path
            'cre_id'=> $cre_id,
            'duration' => $duration,  // Store the video duration if it's a video, null otherwise
        ]);

        return redirect()->route('content.create',['id' => $id])->with('success', 'Content created successfully!');
    }
    public function index($username)
    {
        
        // Fetch content associated with the creator (creator id is decrypted)
        $creatorId = Auth::user()->id;
        $creator = Creator::where('username', $username)->firstOrFail();
        $contents = Content::with('creator')
        ->where('cre_id', $creatorId)
        ->latest()
        ->get();

        $likedContents = ContentLike::where('liked_by', Auth::id())->pluck('con_id')->toArray();

        return view('creator_content', compact('creator','contents', 'creatorId', 'likedContents'));
    }
    public function modalContent($content_id)
    {
        $content = Content::findOrFail($content_id);
        $likedContents = ContentLike::where('liked_by', Auth::id())->pluck('con_id')->toArray();
        $creator = $content->creator; // Access the creator relationship
        // Prepare response data (could include other data as needed)
        return response()->json([
            'id' => $content->id,
            'name' => $content->name,
            'value' => $content->value,
            'type' => $content->type,
            'url' => asset('storage/' . $content->value),  // Assuming media file is stored in the storage folder
            'likedContents' => $likedContents,
            'like_count' => $content->likes()->count(),
            'creator_username' => $creator->username, // Pass creator username
            'creator_profile_photo' => asset('storage/public/profile_photos/' . $creator->profile_photo), // Pass profile photo URL
        ]);
        
    }

    public function destroy($id)
    {
        $content = Content::findOrFail($id);

        // Check if current user is the owner
        if ($content->cre_id != auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Delete the media file from storage
        if ($content->value && Storage::disk('public')->exists($content->value)) {
            Storage::disk('public')->delete($content->value);
        }

        // Delete the content
        $content->delete();

        return response()->json(['message' => 'Content deleted successfully']);
    }

    public function showall()
    {
        // Get the ID of the currently logged-in user
        $currentUserId = Auth::id();
        
        // Get the IDs of creators the current user is already following
        $followingIds = Follower::where('follower', $currentUserId)->pluck('cre_id')->toArray();

        // Get content only from followed creators
        $contents = Content::with('creator')->whereIn('cre_id', $followingIds)->latest()->get();

        $likedContents = ContentLike::where('liked_by', Auth::id())->pluck('con_id')->toArray();

        // Get suggested creators (randomly select a few who the user isn't following)
        $suggestedCreators = Creator::where('id', '!=', $currentUserId)
            ->whereNotIn('id', $followingIds)
            ->inRandomOrder()
            ->limit(5) // Adjust the number of suggestions as needed
            ->get(['id', 'username', 'name', 'profile_photo']);

        return view('home_contents', compact('contents', 'likedContents', 'suggestedCreators'));
    }

    public function toggleLike($contentId)
    {
        try {
            $content = Content::findOrFail($contentId);
            $creatorId = Auth::id(); // Get the logged-in user's ID

            if ($creatorId == null) {
                return redirect()->route('login'); 
            }

            // Fetch the conbank record for the user
            $conbank = Conbank::where('cre_id', $creatorId)->first();

            // Block like/unlike if conbank is missing or balance is null
            if (!$conbank || is_null($conbank->balance)) {
                return response()->json(['error' => 'Please add balance to your wallet.'], 403);
            }

            // Check if the user has already liked the content
            $existingLike = ContentLike::where('con_id', $contentId)
                                    ->where('liked_by', $creatorId)
                                    ->first();

            if ($existingLike) {
                // Unlike
                $existingLike->delete();
                $message = 'unliked';
                $conbank->balance += 1;
            } else {
                // Like
                ContentLike::create([
                    'con_id' => $contentId,
                    'liked_by' => $creatorId,
                    'name' => 'Like',
                ]);
                $message = 'liked';
                $conbank->balance -= 1;
            }

            $conbank->save();

            // Return the response
            return response()->json([
                'message' => $message,
                'like_count' => $content->likes()->count(),
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
    }


    public function extract($contentId)
    {
        $authUserId = Auth::id();
        $creId = $authUserId; 
        $likeCount = DB::table('content_like')->where('con_id', $contentId)->count();
        DB::table('conbank')->where('cre_id', $creId)->increment('balance', $likeCount);
        DB::table('content_like')->where('con_id', $contentId)->delete();
        
        return response()->json(['success' => true]);
    }

    public function showProfile($username)
    {
        $creator = Creator::where('username', $username)->firstOrFail();

        $contents = $creator->contents()->with('likes')->latest()->get();

        $likedContents = [];

        if (auth()->check()) {
            // Get all content IDs that the authenticated user has liked
            $likedContents = ContentLike::where('liked_by', auth()->id())
                ->pluck('con_id')
                ->toArray();
        }

        $isFollowing = false;
        if (auth()->check()) {
            $isFollowing = $creator->followers()->where('follower', auth()->id())->exists();
        }
        
        return view('profile', [
            'creator' => $creator,
            'contents' => $contents,
            'likedContents' => $likedContents,
            'isFollowing' => $isFollowing
        ]);
    }

    public function toggleFollow(Creator $creator)
    {
        $currentUserId = auth()->id();
        $targetCreatorId = $creator->id;

        // Check if current user is already following the target
        $isFollowing = Follower::where('cre_id', $targetCreatorId)
                        ->where('follower', $currentUserId)
                        ->exists();

        if ($isFollowing) {
            // Unfollow - Remove both follower and following records
            Follower::where('cre_id', $targetCreatorId)
                ->where('follower', $currentUserId)
                ->delete();

            Following::where('cre_id', $currentUserId)
                ->where('whom', $targetCreatorId)
                ->delete();

            return response()->json(['status' => 'unfollowed']);
        } else {
            // Follow - Add both follower and following records
            Follower::create([
                'cre_id' => $targetCreatorId,
                'follower' => $currentUserId,
            ]);

            Following::create([
                'cre_id' => $currentUserId,
                'whom' => $targetCreatorId,
            ]);

            return response()->json(['status' => 'followed']);
        }

    }
}
