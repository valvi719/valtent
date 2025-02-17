<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use FFMpeg\FFMpeg;

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
}
