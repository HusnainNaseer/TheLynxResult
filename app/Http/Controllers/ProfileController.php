<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Validation\Rule;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request, $id)
    {
        // Get the user being edited
        $user = User::findOrFail($id);

        // Validate
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id), // important: ignore current user
            ],
            'branch_name' => ['nullable', 'string', 'max:255'],
            'branch' => ['integer', 'max:255'],
            'class' => ['integer', 'max:255'],
            'branch_email' => ['nullable', 'email', 'max:255'],
            'branch_phone' => ['nullable', 'string', 'max:20'],
            'branch_address' => ['nullable', 'string', 'max:500'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'], // optional
        ]);

        // Fill the data
        $branch = User::getBranchAttribute($validated['branch']); // get the branch data from the accessor
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->branch_name = $branch['name'] ?? $validated['branch_name'];
        $user->branch_id = $validated['branch'] ?? $user->branch_id;
        $user->class_id = $validated['class'] ?? $user->class_id;
        $user->branch_email = $validated['branch_email'] ?? $user->branch_email;
        $user->branch_phone = $validated['branch_phone'] ?? $user->branch_phone;
        $user->branch_address = $validated['branch_address'] ?? $user->branch_address;


        // Hash password if present
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully!',
        ]);
    }



    /**
     * Update the user's profile picture.
     */

    public function updateProfilePicture(Request $request, $id)
    {
        $request->validate([
            'profile_picture' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // ✅ Get the teacher being edited
        $user = User::findOrFail($id);

        // Delete old picture
        if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        // Store new picture
        $path = $request->file('profile_picture')->store('profile_pictures', 'public');

        $user->update([
            'profile_picture' => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile picture updated successfully!',
            'path' => asset('storage/' . $path),
        ]);
    }


    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully!'
        ]);
        // dd($request->user());
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Delete profile picture if exists
        if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Account deleted successfully!'
        ]);
    }
}
