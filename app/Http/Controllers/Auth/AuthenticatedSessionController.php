<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        $userRoles = $user->getRoleNames()->map(function ($role) {
            return strtolower($role);
        });

        // Block users with only 'user' role
        if ($userRoles->contains('user') && $userRoles->count() === 1) {
            Auth::logout();

            return back()->withErrors([
                'email' => 'You do not have access to login.',
            ])->onlyInput('email');
        }

        // Check if user has admin, teacher, or coordinator role
        if (!$userRoles->contains('admin') && !$userRoles->contains('teacher') && !$userRoles->contains('coordinator')) {
            Auth::logout();

            return back()->withErrors([
                'email' => 'You do not have permission to access this system.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
