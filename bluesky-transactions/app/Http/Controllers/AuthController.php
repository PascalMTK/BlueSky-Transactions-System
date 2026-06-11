<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'Email is required.',
            'password.required' => 'Password is required.',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $user = Auth::user();

            if ($user->role !== 'admin' && $user->status !== 'active') {
                Auth::logout();
                return back()->with('error', 'Your account is pending activation by an administrator.');
            }

            $request->session()->regenerate();
            return $this->redirectByRole();
        }

        return back()->with('error', 'Invalid email or password.')->withInput($request->only('email'));
    }

    public function registerForm()
    {
        $countries = Country::where('is_active', true)->get();
        return view('auth.register', compact('countries'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'phone'         => 'required|string|max:20',
            'password'      => 'required|min:8|confirmed',
            'country_id'    => 'required|exists:countries,id',
            'address'       => 'nullable|string|max:500',
            'id_number'     => 'nullable|string|max:50',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ], [
            'name.required'       => 'Full name is required.',
            'email.unique'        => 'This email is already in use.',
            'phone.required'      => 'Phone number is required.',
            'password.min'        => 'Password must be at least 8 characters.',
            'password.confirmed'  => 'Passwords do not match.',
            'country_id.required' => 'Please select your country.',
            'profile_photo.image' => 'File must be an image.',
            'profile_photo.max'   => 'Photo must be under 3 MB.',
        ]);

        $country   = Country::findOrFail($request->country_id);
        $agentCode = 'BSK-' . $country->code . '-' . str_pad(User::count() + 1, 4, '0', STR_PAD_LEFT);

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('profile_photo')) {
            $photoPath = $request->file('profile_photo')->store('profiles', 'public');
        }

        User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'password'      => Hash::make($request->password),
            'role'          => 'agent',
            'country_id'    => $request->country_id,
            'agent_code'    => $agentCode,
            'status'        => 'pending',
            'address'       => $request->address,
            'id_number'     => $request->id_number,
            'profile_photo' => $photoPath,
        ]);

        return redirect()->route('login')
            ->with('success', 'Account created successfully! Awaiting administrator activation.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function redirectByRole()
    {
        return Auth::user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('agent.dashboard');
    }
}
