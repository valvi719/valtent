<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Moment;
use App\Models\Follower;
use Illuminate\Support\Facades\Auth;

class MomentController extends Controller
{
    public function fetch()
    {
        $cre_id = Auth::id();

        $moments = Moment::with('actor')
            ->where('cre_id', $cre_id)
            ->where('is_read', false)
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($moment) use ($cre_id) {
                $contentId = null;
                $mediaUrl = null;
                $mediaType = null;

                if (str_starts_with($moment->link, '/content/')) {
                    $contentId = basename($moment->link);
                    $content = \App\Models\Content::find($contentId);

                    if ($content) {
                        $mediaUrl = asset('storage/' . $content->value);
                        $extension = strtolower(pathinfo($content->value, PATHINFO_EXTENSION));

                        $imageExtensions = ['jpg', 'jpeg', 'png'];
                        $videoExtensions = ['mp4', 'mov', 'avi'];

                        if (in_array($extension, $imageExtensions)) {
                            $mediaType = 'image';
                        } elseif (in_array($extension, $videoExtensions)) {
                            $mediaType = 'video';
                        } else {
                            $mediaType = 'unknown';
                        }
                    }
                }

                $actor = $moment->actor;
                $isFollowing = Follower::where('cre_id', $actor->id)
                                ->where('follower', $cre_id)
                                ->exists();

                return [
                    'message' => $moment->message,
                    'link' => $moment->link,
                    'type' => $moment->type,
                    'created_at' => $moment->created_at->diffForHumans(),
                    'is_following_actor' => $isFollowing,
                    'actor' => [
                        'id' => $actor->id,
                        'username' => $actor->username,
                        'profile_photo' => $actor->profile_photo,
                    ],
                    'media_url' => $mediaUrl,
                    'media_type' => $mediaType,
                ];
            });

        return response()->json($moments);
    }

}
