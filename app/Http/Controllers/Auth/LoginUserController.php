<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route as RouteFacade;
use Inertia\Inertia;
use Inertia\Response;

class LoginUserController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/login');
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
                'email' => 'Tài khoản của bạn đã bị vô hiệu hóa. Vui lòng liên hệ hỗ trợ.',
            ]);
        }

        // Redirect based on role if no intended URL present
        $fallback = route('home');
        if ($user->isSeller() && RouteFacade::has('seller.dashboard')) {
            $fallback = route('seller.dashboard');
        } elseif ($user->isAdmin() && RouteFacade::has('admin.dashboard')) {
            $fallback = route('admin.dashboard');
        }

        return redirect()->intended($fallback)->with('status', 'Đăng nhập thành công!');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'Đăng xuất thành công!');
    }
}
