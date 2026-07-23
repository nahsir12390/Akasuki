<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Mail\PasswordResetMail;

class ForgetpasswordController extends Controller
{
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $token = Str::random(65);

        DB::table('forget_password')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $link = url('/reset-password?token=' . $token . '&email=' . urlencode($request->email));

        Mail::to($request->email)->send(new PasswordResetMail($link));

        return back()->with('status', 'Password reset link sent to your Email!');
    }

    public function showResetPassword(Request $request)
    {
        return view('auth.reset-password', [
            'token' => $request->query('token'),
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        $record = DB::table('forget_password')
            ->where('email', $request->email)
            ->where('created_at', '>=', now()->subHour())
            ->orderByDesc('created_at')
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['token' => 'Invalid or expired token.']);
        }

        DB::table('users')
            ->where('email', $request->email)
            ->update([
                'password' => Hash::make($request->password),
            ]);

        DB::table('forget_password')
            ->where('email', $request->email)
            ->delete();

        return redirect('/login')->with('success', 'Your password has been reset.');
    }
}
