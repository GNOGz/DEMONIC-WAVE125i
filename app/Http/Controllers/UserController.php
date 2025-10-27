<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    public function updateProfilePhoto(Request $request)
    {
        $request->validate([
            'profile_img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Delete old photo if exists
        if ($user->profile_img) {
            File::delete(storage_path('app/private/profile_imgs/' . $user->profile_img));
        }

        $fileName = time() . '_' . $user->id . '_' . $request->file('profile_img')->getClientOriginalName();
        $filePath = $request->file('profile_img')->storeAs('profile_imgs', $fileName, 'local');

        $user->profile_img = $fileName; //Update the user record
        /** @var \App\Models\User $user **/
        $user->save();

        return redirect()->route('profile.edit')->with('status', 'profile-photo-updated');
    }

    public function showProfilePhoto($filename)
    {
        // Step 1: Get the currently authenticated user
    
        $user = auth()->user();

        // Step 2: Check if the user is trying to access their own photo
        if ($user->profile_img !== $filename) {
            abort(403); // Prevent access to others' photos
        }

        // Step 3: Construct the file path
        $path = storage_path('app/private/profile_imgs/' . $filename);

        // Step 4: Check if the file exists at the specified path
        if (!File::exists($path)) {
            abort(404);
        }

        // Step 5: Return the file response to the browser
        return response()->file($path);
    }
}