<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;

class SignupController extends Controller
{
    public function index()
    {
        return view ('signup');
    }

    public function signup(Request $request)
    {
        // Validate the request data
        $request->validate([
            'full_name' => 'required|string|max:255',  
            'email' => 'required|email|unique:users',  
            'number' => 'required',  
            'address' => 'required|string|max:255',
            'dob' => 'required|date',  
            'password' => 'required|confirmed',
        ]);

        // Create the user with status as 'inactive'
        $user = User::create([
            'full_name' => $request->input('full_name'),
            'email' => $request->input('email'),
            'number' => $request->input('number'),
            'address' => $request->input('address'),
            'dob' => $request->input('dob'),
            'userRole' => 'patient',
            'password' => Hash::make($request->input('password')),
            'status' => 'inactive',
        ]);

        // If the user was created successfully
        if ($user) {
            // TODO: Email verification is currently disabled
            // Users can use any email format (no verification required)
            // Accounts will be manually approved by the assistant
            // 
            // To re-enable email verification in the future:
            // 1. Uncomment the line below: event(new Registered($user));
            // 2. Configure SMTP settings in .env file
            // 3. Update success message to mention email verification
            // event(new Registered($user));

            return redirect()->route('signin')->with('success', 'You are registered successfully! Your account is pending approval. Please wait for the assistant to approve your account.');
        }

        // If user creation failed
        return redirect()->route('signup')->with('error', 'Failed to create user.');
    }

    
}
