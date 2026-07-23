<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    // Redirect to Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Handle Google callback
    public function handleGoogleCallback()
    {
        try {
            $socialUser = Socialite::driver('google')->user();
            
            return $this->handleSocialUser($socialUser, 'google');
        } catch (\Exception $e) {
            return redirect()->route('show.login')->with('error', 'Google authentication failed. Please try again.');
        }
    }

    // Redirect to GitHub
    public function redirectToGithub()
    {
        return Socialite::driver('github')->redirect();
    }

    // Handle GitHub callback
    public function handleGithubCallback()
    {
        try {
            $socialUser = Socialite::driver('github')->user();
            
            return $this->handleSocialUser($socialUser, 'github');
        } catch (\Exception $e) {
            return redirect()->route('show.login')->with('error', 'GitHub authentication failed. Please try again.');
        }
    }

    // Common method to handle social users
    private function handleSocialUser($socialUser, $provider)
    {
        // Check if user already exists by email
        $user = User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            // Create new user
            $user = User::create([
                'name' => $this->generateName($socialUser, $provider),
                'email' => $socialUser->getEmail(),
                'password' => Hash::make(Str::random(24)), // Random password for social users
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
                'email_verified_at' => now(), // Social emails are typically verified
            ]);
        } else {
            // Update existing user with provider info
            $user->update([
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
            ]);
        }

        // Log the user in
        Auth::login($user, true);

        // Update last_seen timestamp
        $user->update(['last_seen' => now()]);

        return redirect()->route('show.post')->with('success', 'Successfully logged in with ' . ucfirst($provider) . '!');
    }

    // Generate appropriate name based on provider
    private function generateName($socialUser, $provider)
    {
        if ($provider === 'github') {
            return $socialUser->getNickname() ?? $socialUser->getName() ?? 'GitHub User';
        }
        
        return $socialUser->getName() ?? 'Social User';
    }
}
