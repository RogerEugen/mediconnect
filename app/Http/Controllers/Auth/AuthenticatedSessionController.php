<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $request->session()->regenerate();
        AuditLog::record('login', 'User logged in successfully');

        $role = strtolower(Auth::user()->role); // normalize role to lowercase

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($role === 'doctor') {
            return redirect()->route('doctor.dashboard');
        } elseif ($role === 'specialist') {
            return redirect()->route('specialist.dashboard');
        }

        Auth::logout();
        abort(403, 'Unauthorized role.');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        AuditLog::record('logout', 'User logged out');
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
