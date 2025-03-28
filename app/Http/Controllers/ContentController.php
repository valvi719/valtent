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
    public function index($id)
    {
        // Fetch content associated with the creator (creator id is decrypted)
        $creatorId = Crypt::decrypt($id);
        $contents = Content::where('cre_id', $creatorId)->get(); // Fetch content related to the creator

        return view('creator_content', compact('contents', 'creatorId'));
    }
    public function modalContent($content_id)
    {
        $content = Content::findOrFail($content_id);

        // Prepare response data (could include other data as needed)
        return response()->json([
            'id' => $content->id,
            'name' => $content->name,
            'value' => $content->value,
            'type' => $content->type,
            'url' => asset('storage/' . $content->value),  // Assuming media file is stored in the storage folder
        ]);
        
    }
    public function showall()
    {  
        // dd('rr');
        $contents = Content::with('creator')->latest()->get();
        $likedContents = ContentLike::where('liked_by', Auth::id())->pluck('con_id')->toArray();
        // dd($contents);
        return view('home_contents', compact('contents','likedContents'));
    }

    public function toggleLike($contentId)
    {
        
        try {
            
            $content = Content::findOrFail($contentId);
            $conbank = Conbank::where('cre_id', auth()->user()->id)->first();
            $creatorId = Auth::id(); // Get the logged-in user's ID
            if($creatorId==null)
            {
                return redirect()->route('login'); 
            }
            // dd($creatorId);
            // Check if the user has already liked the content
            $existingLike = ContentLike::where('con_id', $contentId)
                                    ->where('liked_by', $creatorId)
                                    ->first();
                                   
            if ($existingLike) {
                // If like exists, delete the like (unlike)
                $existingLike->delete();
                $message = 'unliked';
                $conbank->balance +=  1;
            } else {
                // dd('rr');    
                // Otherwise, create a new like
                ContentLike::create([
                    'con_id' => $contentId,
                    'liked_by' => $creatorId,
                    'name' => 'Like',
                ]);
                $message = 'liked';
                
                $conbank->balance -=  1;
            }
            $conbank->save();
            // Return the response (we can also return the updated like count here)
            return response()->json([
                'message' => $message,
                'like_count' => $content->likes()->count(), // Return the total like count
            ]);
        } catch (\Exception $e) {
            // If an error occurs, return an error message
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
}
