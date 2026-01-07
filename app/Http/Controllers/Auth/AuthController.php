<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }
        
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Log activity
            \App\Models\ActivityLog::createLog(
                'login',
                'User',
                auth()->id(),
                auth()->user()->name . ' berhasil login'
            );

            return $this->redirectToDashboard();
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput($request->only('email'));
    }

    /**
     * Show register form.
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }
        
        return view('auth.register');
    }

    /**
     * Handle register request.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Create user with client role by default
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'client',
            'phone' => $request->phone,
        ]);

        // Create client profile if company info provided
        if ($request->filled('company_name')) {
            \App\Models\Client::create([
                'user_id' => $user->id,
                'company_name' => $request->company_name,
                'company_address' => $request->company_address,
                'contact_person' => $request->name,
                'contact_phone' => $request->phone,
            ]);
        }

        // Log activity
        \App\Models\ActivityLog::createLog(
            'register',
            'User',
            $user->id,
            $user->name . ' mendaftar sebagai client baru'
        );

        // Auto login after register
        Auth::login($user);

        return redirect()->route('client.dashboard')
            ->with('success', 'Registrasi berhasil! Selamat datang di sistem kami.');
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        // Log activity before logout
        \App\Models\ActivityLog::createLog(
            'logout',
            'User',
            auth()->id(),
            auth()->user()->name . ' logout'
        );

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah logout.');
    }

    /**
     * Redirect to appropriate dashboard based on role.
     */
    protected function redirectToDashboard()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isClient()) {
            return redirect()->route('client.dashboard');
        } else {
            return redirect()->route('employee.dashboard');
        }
    }

    /**
     * Show forgot password form.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle forgot password request - send reset link.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Email tidak ditemukan dalam sistem.',
        ]);

        // Create a token
        $token = Str::random(64);

        // Store the token in the database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Get the user
        $user = User::where('email', $request->email)->first();

        // Send reset link via notification
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $request->email]);
        
        try {
            $user->notify(new \App\Notifications\ResetPasswordNotification($resetUrl, $token));
            
            return back()->with('success', 'Link reset password telah dikirim ke email Anda!');
        } catch (\Exception $e) {
            // If email fails, still show success (for security reasons)
            // But log the error for debugging
            \Log::error('Failed to send reset email: ' . $e->getMessage());
            
            return back()->with('success', 'Link reset password telah dikirim ke email Anda!');
        }
    }

    /**
     * Show reset password form.
     */
    public function showResetPasswordForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Handle reset password request.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Email tidak ditemukan dalam sistem.',
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Check if token exists and is valid (not expired - 60 minutes)
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset) {
            return back()->withErrors(['email' => 'Token reset password tidak valid.']);
        }

        // Check if token matches
        if (!Hash::check($request->token, $passwordReset->token)) {
            return back()->withErrors(['email' => 'Token reset password tidak valid.']);
        }

        // Check if token is expired (60 minutes)
        if (now()->diffInMinutes($passwordReset->created_at) > 60) {
            return back()->withErrors(['email' => 'Token reset password telah kadaluarsa. Silakan minta token baru.']);
        }

        // Update the user's password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the token
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        // Log activity
        \App\Models\ActivityLog::createLog(
            'password_reset',
            'User',
            $user->id,
            $user->name . ' berhasil reset password'
        );

        return redirect()->route('login')
            ->with('success', 'Password berhasil direset! Silakan login dengan password baru Anda.');
    }
}
