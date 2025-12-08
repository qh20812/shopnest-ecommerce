<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/login-page', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Check if user account is active
        if ($user && !$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return back()->withErrors([
                'identifier' => 'Your account has been deactivated. Please contact support for assistance.',
            ]);
        }
        
        // Check user role and redirect accordingly
        if ($user && $user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard', absolute: false))
                ->with('success', 'Welcome back, Admin!');
        }

        // Check if user has Seller role
        if($user && $user->role()->where('name->en', 'Seller')->exists()) {
            return redirect()->intended(route('seller.dashboard', absolute: false))
                ->with('success', 'Welcome back, Seller!');
        }

        // Check if user has Customer role
        if ($user && $user->role()->where('name->en', 'Customer')->exists()) {
            return redirect()->intended(route('home', absolute: false))
                ->with('success', 'Login successful!');
        }

        return redirect()->intended(route('home', absolute: false))
            ->with('success', 'Welcome back!');
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
