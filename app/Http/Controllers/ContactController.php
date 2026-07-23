<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ContactFormMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $recipient = config('mail.contact_to.address');

        try {
            Mail::to($recipient, config('mail.contact_to.name'))->send(new ContactFormMail($validated));
        } catch (\Throwable $exception) {
            Log::error('Contact email failed: ' . $exception->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Your message could not be sent right now. Please try again soon.');
        }

        return back()->with('success', 'Thank you for your message. I will get back to you soon.');
    }
}
