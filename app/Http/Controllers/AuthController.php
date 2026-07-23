<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Attempt to authenticate user
        if (Auth::attempt([
            'email' => $validatedData['email'],
            'password' => $validatedData['password']
        ], $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // Update last_seen timestamp
            auth()->user()->update(['last_seen' => now()]);
            
            $user = auth()->user();
            $destination = $user->is_admin
                ? route('admin.dashboard')
                : route('user.profile', $user);

            return redirect()->to($destination)->with('success', 'Login successful! Welcome back!');
        }

        return redirect()->back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->except('password'));
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|min:2|max:200|unique:users,name',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        // Create the user with hashed password
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        // Log the user in after registration
        Auth::login($user);

        // Update last_seen timestamp
        $user->update(['last_seen' => now()]);

        return redirect()->route('user.profile', $user)->with('success', 'Registration successful! Welcome to Akatsuki Devs!');
    }

    public function logout(Request $request)
    {
        // Update last_seen timestamp before logout
        if (Auth::check()) {
            Auth::user()->update(['last_seen' => now()]);
        }

        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home')->with('success', 'Logged out successfully.');
    }

    // Optional: Add a method to handle authenticated user dashboard
    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('show.login');
        }

        // Update last_seen timestamp
        Auth::user()->update(['last_seen' => now()]);

        return view('dashboard', [
            'user' => Auth::user(),
            'posts' => Auth::user()->posts()->latest()->get(),
            'friendsCount' => Auth::user()->getFriends()->count(),
            'pendingRequests' => Auth::user()->pendingFriendRequests()->count(),
        ]);
    }

    // Optional: Add a method to update user profile
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'name' => 'required|string|min:2|max:200|unique:users,name,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'bio' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
        ]);

        $user->update($validatedData);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    // Optional: Add a method to change password
    public function changePassword(Request $request)
    {
        $validatedData = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'min:6', 'confirmed', 'different:current_password'],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($validatedData['password'])
        ]);

        return redirect()->back()->with('success', 'Password changed successfully!');
    }
}
