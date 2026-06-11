<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        $user      = auth()->user();
        $countries = Country::where('is_active', true)->get();
        return view('agent.profile', compact('user', 'countries'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'       => 'required|string|max:255',
            'phone'      => 'required|string|max:20',
            'country_id' => 'required|exists:countries,id,is_active,1',
            'address'    => 'nullable|string|max:500',
            'id_number'  => 'nullable|string|max:50',
        ], [
            'name.required'       => 'Full name is required.',
            'phone.required'      => 'Phone number is required.',
            'country_id.required' => 'Please select your country.',
        ]);

        $user->update([
            'name'       => $request->name,
            'phone'      => $request->phone,
            'country_id' => $request->country_id,
            'address'    => $request->address,
            'id_number'  => $request->id_number,
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
        ], [
            'photo.required' => 'Please select a photo.',
            'photo.image'    => 'File must be an image.',
            'photo.max'      => 'Photo must be under 3 MB.',
        ]);

        $user = auth()->user();

        try {
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $path = $request->file('photo')->store('profiles', 'public');

            if (! $path) {
                return back()->with('error', 'Photo upload is not available in this environment. Please configure cloud storage.');
            }

            $user->update(['profile_photo' => $path]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Photo upload failed. Please configure cloud storage for file uploads.');
        }

        return back()->with('success', 'Profile photo updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'current_password.required' => 'Current password is required.',
            'password.min'              => 'New password must be at least 8 characters.',
            'password.confirmed'        => 'Passwords do not match.',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.')->withErrors(['current_password' => 'Incorrect password.']);
        }

        $user->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Password changed successfully!');
    }
}
