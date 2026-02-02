<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // Show profile page
    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    // Update profile information + avatar
    public function update(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'avatar'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $user = Auth::user();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'avatar_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/users'), $filename);

            $user->avatar = $filename;
        }

        // Update personal info
        $user->name    = $request->name;
        $user->email   = $request->email;
        $user->phone   = $request->phone;
        $user->address = $request->address;

        $user->save(); // Save all changes including avatar

        return back()->with('success', 'Profile updated successfully!');
    }

    // Update password (done inside the same profile page)
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'          => 'required',
            'new_password'              => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        // Validate current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password updated successfully!');
    }
}
