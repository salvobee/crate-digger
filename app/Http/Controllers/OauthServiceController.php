<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\SocialiteManager;

class OauthServiceController extends Controller
{
    public function create(string $provider, Request $request)
    {
        if ($provider !== 'discogs')
            abort(404);

        return Socialite::driver($provider)->redirect();
    }

    public function store(string $provider)
    {
        if ($provider !== 'discogs')
            abort(404);

        $oauth_user_meta = Socialite::driver('discogs')->user();
        $service = Service::create([
            'user_id' => Auth::user()->id,
            'meta' => $oauth_user_meta
        ]);
        dd($service);
    }
}
