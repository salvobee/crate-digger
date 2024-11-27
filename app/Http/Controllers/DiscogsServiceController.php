<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

class DiscogsServiceController extends Controller
{
    public function create(Request $request)
    {
        return Socialite::driver('discogs')->redirect();
    }

    public function store()
    {
        $user = Auth::user();
        $oauth_user_meta = Socialite::driver('discogs')->user();
        $discogs_meta = [
            'id' => $oauth_user_meta->getId(),
            'nickname' => $oauth_user_meta->getNickname(),
            'token' => $oauth_user_meta->token,
            'tokenSecret' => $oauth_user_meta->tokenSecret,
        ];

        $user->services->each->delete();

        $user->services()->create([
            'type' => 'discogs',
            'meta' => $discogs_meta,
        ]);

        return redirect()->route('dashboard');
    }

    public function destroy(Request $request)
    {
        $request->user()->services->first()->delete();

        return Redirect::to(route('profile.edit'));
    }
}
