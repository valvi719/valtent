<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InstagramController extends Controller
{
    private $client_id;
    private $client_secret;
    private $redirect_uri;

    public function __construct()
    {
        $this->client_id = env('INSTAGRAM_CLIENT_ID');
        $this->client_secret = env('INSTAGRAM_CLIENT_SECRET');
        $this->redirect_uri = env('INSTAGRAM_REDIRECT_URI');
    }

    public function connectInstagramIndex()
    {
        return view('Instagram.connect_instagram'); // Create a Blade view called 'payment-form.blade.php'
    }

    public function redirectToFacebook()
    {
        $query = http_build_query([
            'client_id' => env('INSTAGRAM_CLIENT_ID'),
            'redirect_uri' => env('INSTAGRAM_REDIRECT_URI'),
            'response_type' => 'code',
            'scope' => 'pages_show_list,pages_read_engagement,instagram_basic',
            'auth_type' => 'rerequest'
        ]);
    
        return redirect('https://www.facebook.com/v18.0/dialog/oauth?' . $query);
    }

    public function handleCallback(Request $request)
    {
        
        // Step 1: Get user access token
        $response = Http::asForm()->post('https://graph.facebook.com/v18.0/oauth/access_token', [
            'client_id' => env('INSTAGRAM_CLIENT_ID'),
            'client_secret' => env('INSTAGRAM_CLIENT_SECRET'),
            'redirect_uri' => env('INSTAGRAM_REDIRECT_URI'),
            'code' => $request->code,
        ]);
        

        $data = $response->json();
        $accessToken = $data['access_token'];
        
        // Step 2: Get Facebook user ID
        $me = Http::get("https://graph.facebook.com/v18.0/me?fields=id,name", [
            'access_token' => $accessToken,
        ])->json();
        // dd($me);
        // page id 1= 660708007116271
        // Step 3: Get Pages the user has access to
        $pages = Http::get("https://graph.facebook.com/v18.0/{$me['id']}/accounts", [
            'access_token' => $accessToken,
        ])->json();
        
        if (empty($pages['data'])) {
            return response()->json(['error' => 'No Facebook pages found.']);
        }

        $pageId = $pages['data'][0]['id'];
        $pageAccessToken = $pages['data'][0]['access_token'];

        // Step 4: Get Instagram Business account linked to the Page
        $igAccount = Http::get("https://graph.facebook.com/v18.0/{$pageId}?fields=instagram_business_account", [
            'access_token' => $accessToken,
        ])->json();
        // dd($igAccount);

        $instagramId = $igAccount['instagram_business_account']['id'] ?? null;

        if (!$instagramId) {
            return response()->json(['error' => 'No connected Instagram Creator account found.']);
        }
        
        // Step 5: Get Media
        $Insta_business_account_id = env("Insta_business_account_id"); 
        $media = Http::get("https://graph.facebook.com/v18.0/{$instagramId}/media", [
            'fields' => 'id,caption,media_type,media_url,thumbnail_url,timestamp',
            'access_token' => $accessToken,
        ])->json();

        return view('Instagram.media', ['media' => $media]);
    }

    public function privacypolicy()
    {
        return view('Instagram.privacy_policy'); // Create a Blade view called 'payment-form.blade.php'
    }

}
