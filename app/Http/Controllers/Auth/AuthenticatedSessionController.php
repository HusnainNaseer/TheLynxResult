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
    $userType = $request->input('user_type', 'admin');
    
    $request->authenticate();
    
    $user = Auth::user();
    
    // Get user's actual role (case-insensitive)
    $userRoles = $user->getRoleNames()->map(function($role) {
        return strtolower($role);
    });
    
    // Check if user has the selected role (case-insensitive)
    if (!$userRoles->contains(strtolower($userType))) {
        $userRole = $user->getRoleNames()->first() ?? 'unknown';
        
        Auth::logout();
        
        return back()->withErrors([
            'email' => 'Please provide ' . ucfirst($userType) . ' credentials. You are trying to login as ' . ucfirst($userRole) . '.',
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
