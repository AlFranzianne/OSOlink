<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Rinvex\Country\CountryLoader;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
        {
            $countries = countries(); // Returns array of country arrays

            return view('profile.edit', [
                'user' => $request->user(),
                'countries' => $countries,
            ]);
        }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return redirect()->route('profile.edit')->with('update_success', 'Profile updated successfully!');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();

        $path = $request->file('profile_picture')->store('profile_pictures', 'public');

        if ($user->profile_picture) {
            \Storage::disk('public')->delete($user->profile_picture);
        }

        $user->profile_picture = $path;
        $user->save();

        return back()->with('upload_success', 'Profile picture updated!');
    }

    public function remove(Request $request)
    {
        $user = auth()->user();
        if ($user->profile_picture) {
            \Storage::disk('public')->delete($user->profile_picture);
            $user->profile_picture = null;
            $user->save();
        }
        return back()->with('remove_success', 'Profile picture removed.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
