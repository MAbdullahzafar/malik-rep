<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;

class mailcontroller extends Controller
{
    /**
     * Send email out using background queues to eliminate web latency freeze lags.
     */
    public function sendEmail(Request $request)
    {
        $to = $request->input('to');
        $msg = $request->input('message'); 
        $subject = $request->input('subject');

        // FIXED: Replaced ->send() with ->queue() to process emails in the background 
        // without forcing your web browser to freeze up while waiting for Gmail.
        Mail::to($to)->queue(new WelcomeEmail($msg, $subject));

        return view('students.index')->with('flash_message', 'Email added to delivery queue successfully!');
    }
}
