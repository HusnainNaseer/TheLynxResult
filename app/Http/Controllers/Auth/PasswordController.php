<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }

    public function teacherpassreset(Request $request){
        $request->validate([
           'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $password = Hash::make($request->password);
        $user = User::find($request->user_id);
        $user->password = $password;
        $user->save();
        return response()->json([
            'message' => 'Password Reset Successfully.',
        ]);
    }
}
