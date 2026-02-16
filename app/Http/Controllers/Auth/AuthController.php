<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Get authenticated user
            $user = Auth::user();
            
            // Validasi: User non-super-admin harus punya hotel_id
            if (!$user->isSuperAdmin() && !$user->hotel_id) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Akun Anda belum terdaftar ke hotel manapun. Hubungi administrator.',
                ]);
            }

            // Validasi: Hotel harus aktif
            if ($user->hotel_id) {
                $hotel = $user->hotel;
                if (!$hotel || $hotel->status !== 'active') {
                    Auth::logout();
                    throw ValidationException::withMessages([
                        'email' => 'Hotel Anda sedang tidak aktif. Hubungi administrator.',
                    ]);
                }
            }

            // Redirect based on role
            return $this->redirectUserByRole($user);
        }

        throw ValidationException::withMessages([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ]);
    }

    /**
     * Redirect user based on their role
     */

    protected function redirectUserByRole($user)
    {
        switch ($user->role) {
            case 'super_admin':
                return redirect()->route('super-admin.dashboard');
                    
            case 'admin':
                return redirect()->route('admin.dashboard');
                    
            case 'receptionist':
                return redirect()->route('receptionist.dashboard');
                    
            case 'customer':
            default:
                return redirect()->route('dashboard');
        }
    }

    // protected function redirectUserByRole($user)
    // {
    //     switch ($user->role) {
    //         case 'super_admin':
    //             return redirect()->intended(route('super-admin.dashboard'));
                
    //         case 'admin':
    //             return redirect()->intended(route('admin.dashboard'));
                
    //         case 'receptionist':
    //             return redirect()->intended(route('receptionist.dashboard'));
                
    //         case 'customer':
    //         default:
    //             return redirect()->intended(route('dashboard'));
    //     }
    // }

    /**
     * Show registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'hotel_id' => ['nullable', 'exists:hotels,id'], // Optional untuk public registration
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer', // Default role untuk public registration
            'hotel_id' => $validated['hotel_id'] ?? null, // Customer bisa tanpa hotel
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil! Selamat datang.');
    }

    /**
     * Show forgot password form.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => 'Link reset password telah dikirim ke email Anda.'])
            : back()->withErrors(['email' => 'Email tidak ditemukan.']);
    }

    /**
     * Show reset password form.
     */
    public function showResetPasswordForm(string $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Handle password reset.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Password berhasil direset.')
            : back()->withErrors(['email' => [__($status)]]);
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
