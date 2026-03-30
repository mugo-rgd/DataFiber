<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormSubmitted;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'company' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'category' => 'required|string',
            'message' => 'required|string|max:2000',
            'newsletter' => 'nullable|boolean'
        ]);

        // Save to database if needed
        // $contact = ContactMessage::create($request->all());

        // Send email notification
        Mail::to(config('mail.admin_address'))
            ->send(new ContactFormSubmitted($request->all()));

        return back()->with('success', 'Thank you! Your message has been sent successfully.');
    }
}
