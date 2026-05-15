<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use App\Models\Session;
use App\Services\ErpAuthService;
use App\Services\StudentSyncService;

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
    public function store(
        LoginRequest $request,
        ErpAuthService $erpAuthService,
        StudentSyncService $studentSyncService
    ): RedirectResponse
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

        if ($userRoles->contains('admin') || $userRoles->contains('coordinator')) {
            try {
                $erpLogin = $erpAuthService->login($user);
            } catch (\Throwable $e) {
                Log::error('ERP login failed for local user ' . $user->id . ': ' . $e->getMessage());
                $erpLogin = [
                    'ok' => false,
                    'message' => 'ERP admin token login failed. Please try again or check the ERP connection.',
                ];
            }

            if (!$erpLogin['ok']) {
                Auth::logout();

                return back()->withErrors([
                    'email' => $erpLogin['message'],
                ])->onlyInput('email');
            }
        }

        $request->session()->regenerate();

        $activeSession = Session::active()->first();

        if ($activeSession && Schema::hasColumn('users', 'session_id')) {
            $sessionData = [
                'session_id' => $activeSession->id,
            ];

            if (Schema::hasColumn('users', 'erp_session_id')) {
                $sessionData['erp_session_id'] = $activeSession->erp_session_id ?: (string) $activeSession->id;
            }

            $user->forceFill($sessionData)->save();
        }

        if ($userRoles->contains('admin')) {
            try {
                // $studentSyncService->syncForActiveSession();
            } catch (\Throwable $e) {
                Log::error('Student sync on admin login failed for user ' . $user->id . ': ' . $e->getMessage());
            }
        }

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
