<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Conbank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFProbe;
use App\Models\ContentLike;
use App\Models\Creator;
use App\Models\Follower;
use App\Models\Following;
use App\Models\Donator;

class ContentController extends Controller
{
    public function create($id)
    {
        
        return view('content_create',compact('id'));
        
    }
    // video upload is only working with thumbnail moderation.make changes when the website is live.
    public function store(Request $request, $id)
    {
        $cre_id = Crypt::decrypt($id);

        $validator = Content::validate($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $value = null;
        $duration = null;
        $moderation_id = null;

        if ($request->type === 'Media' && $request->hasFile('value')) {
            $file = $request->file('value');
            $mimeType = $file->getMimeType();
            $fileExtension = strtolower($file->getClientOriginalExtension());

            $allowedMimeTypes = ['video/mp4', 'video/avi', 'video/mkv', 'video/webm', 'video/x-msvideo', 'video/quicktime'];
            $allowedExtensions = ['mp4', 'avi', 'mkv', 'webm', 'mov'];

            $isVideo = in_array($mimeType, $allowedMimeTypes) || in_array($fileExtension, $allowedExtensions);

            $filePath = $file->store('media', 'public');
            $fullPath = public_path('storage/' . $filePath);
            $publicUrl = asset('storage/' . $filePath);

            $api_user = env('SIGHTENGINE_USER');
            $api_secret = env('SIGHTENGINE_SECRET');

            if ($isVideo) {
                $ffmpeg = FFMpeg::create([
                    'ffmpeg.binaries'  => 'C:\\ffmpeg-7.1-essentials_build\\bin\\ffmpeg.exe',
                    'ffprobe.binaries' => 'C:\\ffmpeg-7.1-essentials_build\\bin\\ffprobe.exe',
                    'timeout'          => 3600,
                    'ffmpeg.threads'   => 12
                ]);

                $video = $ffmpeg->open($fullPath);
                $videoDuration = $video->getFormat()->get('duration');
                $duration = round($videoDuration);

                $callbackUrl = route('moderation.callback');

                $response = Http::asForm()->post('https://api.sightengine.com/1.0/video/check.json', [
                    'models' => 'nudity,wad',
                    'api_user' => $api_user,
                    'api_secret' => $api_secret,
                    'media_url' => $publicUrl,
                    'callback_url' => $callbackUrl
                ]);

                if ($response->successful() && isset($response['request']['id'])) {
                    $moderation_id = $response['request']['id'];
                } else {
                    // Fallback to image-based moderation using frame at 1s
                    $thumbnailName = Str::random(40) . '.jpg';
                    $thumbnailPath = storage_path('app/public/media/' . $thumbnailName);

                    try {
                        $video->frame(TimeCode::fromSeconds(1))->save($thumbnailPath);

                        $thumbResponse = Http::asMultipart()->attach(
                            'media', fopen($thumbnailPath, 'r'), $thumbnailName
                        )->post("https://api.sightengine.com/1.0/check.json", [
                            ['name' => 'models', 'contents' => 'nudity,wad'],
                            ['name' => 'api_user', 'contents' => $api_user],
                            ['name' => 'api_secret', 'contents' => $api_secret]
                        ]);

                        if (
                            $thumbResponse->failed() ||
                            ($thumbResponse['nudity']['raw'] ?? 0) > 0.5
                        ) {
                            Storage::disk('public')->delete($filePath);
                            Storage::delete('public/media/' . $thumbnailName);
                            return redirect()->back()->with('error', 'Nudity detected in video thumbnail. Upload blocked.');
                        }

                        Storage::delete('public/media/' . $thumbnailName); // cleanup
                    } catch (\Exception $e) {
                        Storage::disk('public')->delete($filePath);
                        return redirect()->back()->with('error', 'Video moderation failed. Please try again.');
                    }
                }
            } else {
                // Image moderation
                $response = Http::asMultipart()->attach(
                    'media', fopen($fullPath, 'r'), $file->getClientOriginalName()
                )->post("https://api.sightengine.com/1.0/check.json", [
                    ['name' => 'models', 'contents' => 'nudity,wad'],
                    ['name' => 'api_user', 'contents' => $api_user],
                    ['name' => 'api_secret', 'contents' => $api_secret]
                ]);

                if ($response->failed() || ($response['nudity']['raw'] ?? 0) > 0.5) {
                    Storage::disk('public')->delete($filePath);
                    return redirect()->back()->with('error', 'Nudity detected in image. Upload blocked.');
                }
            }

            $value = $filePath;
        }

        Content::create([
            'name' => $request->name,
            'type' => $request->type,
            'value' => $value,
            'cre_id' => $cre_id,
            'duration' => $duration,
            'moderation_id' => $moderation_id,
            'moderation_status' => $moderation_id ? 'pending' : 'approved'
        ]);

        return redirect()->route('content.create', ['id' => $id])
            ->with('success', 'Content created successfully! ' . ($moderation_id ? 'Awaiting moderation.' : ''));
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

        $badge = getDonationBadgeStyle();

        return view('creator_content', compact('creator','contents', 'creatorId', 'likedContents','badge'));
    }
    public function modalContent($content_id)
    {
        $content = Content::findOrFail($content_id);
        $likedContents = ContentLike::where('liked_by', Auth::id())->pluck('con_id')->toArray();
        $creator = $content->creator; // Access the creator relationship

        $badgeColor = getDonationBadgeStyle($creator->id);

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
            'badge_color' => $badgeColor,
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
            // $conbank = Conbank::where('cre_id', $creatorId)->first();

            // Block like/unlike if conbank is missing or balance is null
            // if (!$conbank || is_null($conbank->balance)) {
            //     return response()->json(['error' => 'Please add balance to your wallet.'], 403);
            // }

            // Check if the user has already liked the content
            $existingLike = ContentLike::where('con_id', $contentId)
                                    ->where('liked_by', $creatorId)
                                    ->first();

            if ($existingLike) {
                // Unlike
                $existingLike->delete();
                $message = 'unliked';
                // $conbank->balance += 1;
            } else {
                // Like
                ContentLike::create([
                    'con_id' => $contentId,
                    'liked_by' => $creatorId,
                    'name' => 'Like',
                ]);
                $message = 'liked';
                // $conbank->balance -= 1;
            }

            // $conbank->save();

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

        $badge = getDonationBadgeStyle($creator->id);

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
            'isFollowing' => $isFollowing,
            'badge' => $badge,
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

    //Donation 
    public function donate(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:creators,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $donatorId = auth()->id();

        // Fetch the conbank record for the user
        $conbank = Conbank::where('cre_id', $donatorId)->first();

        // Block donation if conbank is missing or balance is null
        if (!$conbank || is_null($conbank->balance)) {
            return response()->json(['error' => 'Please add balance to your wallet.'], 403);
        }
        $conbank->balance -= $request->amount;
        $conbank->save();

        // Save donation
        Donator::create([
            'donator_id' => $donatorId,
            'recipient_id' => $request->recipient_id,
            'content_id' => $request->content_id,
            'amount' => $request->amount,
        ]);

        // Add to recipient's balance
        $bank = Conbank::firstOrCreate(['cre_id' => $request->recipient_id]);
        $bank->balance += $request->amount;
        $bank->save();
        
        return response()->json(['success' => true]);
    }

    public function searchDonors(Request $request, $id)
    {
        $search = $request->input('q');
        $authId = auth()->id();
        $followingIds = Follower::where('follower', $authId)->pluck('cre_id')->toArray();

        $donors = \DB::table('donators')
            ->where('content_id', $id)
            ->join('creators', 'donators.donator_id', '=', 'creators.id')
            ->select('creators.id', 'creators.username', 'creators.name', 'creators.profile_photo', \DB::raw('SUM(donators.amount) as total_amount'))
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('creators.username', 'like', '%' . $search . '%')
                    ->orWhere('creators.name', 'like', '%' . $search . '%');
                });
            })
            ->groupBy('creators.id', 'creators.username', 'creators.name', 'creators.profile_photo')
            ->orderByDesc('total_amount')
            ->get()->map(function ($donor) use ($followingIds) {
                $donor->is_following = in_array($donor->id, $followingIds);
                $badge = getDonationBadgeStyle($donor->id); // Your helper function
                $donor->badge_color = $badge['color'] ?? null;
                $donor->badge_label = $badge['label'] ?? null;

                return $donor;
            });

        return response()->json([
            'donors' => $donors,
            'auth_id' => $authId,
        ]);
    }

    public function handle(Request $request)
    {
        $data = $request->all();
        $status = $data['summary']['action'] ?? null;
        $nudeScore = $data['nudity']['raw'] ?? 0;
        $uri = $data['media']['uri'] ?? null;

        if (!$uri) {
            \Log::warning('Moderation callback: Missing media URI.');
            return response()->json(['message' => 'Invalid callback'], 400);
        }

        // Match content using partial file path from URI
        $filename = basename(parse_url($uri, PHP_URL_PATH));
        $content = Content::where('value', 'like', "%$filename")->first();

        if (!$content) {
            \Log::warning('Moderation callback: Content not found for URI ' . $uri);
            return response()->json(['message' => 'Content not found'], 404);
        }

        $content->moderation_status = $nudeScore > 0.5 ? 'rejected' : 'approved';
        $content->save();

        return response()->json(['message' => 'Moderation processed'], 200);
    }

}
