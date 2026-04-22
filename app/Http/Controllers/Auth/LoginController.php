<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Override method username untuk menggunakan custom field
     */
    public function username()
    {
        return 'login_identifier';
    }

    /**
     * Override method validateLogin
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ], [
            'identifier.required' => 'Email / NIP / NPSN harus diisi',
            'password.required' => 'Password harus diisi',
        ]);
    }

    /**
     * Override method credentials
     */
    protected function credentials(Request $request)
    {
        $identifier = $request->identifier;
        
        // Cek apakah identifier adalah NPSN (8 digit angka)
        if (preg_match('/^\d{8}$/', $identifier)) {
            // 8 digit angka -> NPSN (untuk operator)
            return [
                'npsn' => $identifier,
                'password' => $request->password,
            ];
        } else {
            // Selain itu -> cek sebagai email atau NIP
            // Kita akan coba cari user dengan email atau NIP
            $user = \App\Models\User::where('email', $identifier)
                    ->orWhere('nip', $identifier)
                    ->first();
            
            if ($user) {
                // Jika ditemukan, gunakan field yang sesuai
                if ($user->email == $identifier) {
                    return [
                        'email' => $identifier,
                        'password' => $request->password,
                    ];
                } else {
                    return [
                        'nip' => $identifier,
                        'password' => $request->password,
                    ];
                }
            }
            
            // Jika tidak ditemukan, kembalikan array kosong (login akan gagal)
            return [
                'email' => $identifier,
                'password' => $request->password,
            ];
        }
    }

    /**
     * Override attemptLogin untuk handle custom logic
     */
    protected function attemptLogin(Request $request)
    {
        $identifier = $request->identifier;
        $password = $request->password;
        
        // Coba login dengan NPSN (untuk operator)
        if (preg_match('/^\d{8}$/', $identifier)) {
            if (Auth::attempt(['npsn' => $identifier, 'password' => $password], $request->filled('remember'))) {
                $user = Auth::user();
                // Pastikan user dengan NPSN adalah operator
                if ($user->role === 'operator') {
                    // HAPUS FLAG SHOW_PASSWORD SETELAH LOGIN
                    $this->clearShowPasswordFlag($user);
                    return true;
                } else {
                    Auth::logout();
                    return false;
                }
            }
            return false;
        }
        
        // Coba login dengan email
        if (Auth::attempt(['email' => $identifier, 'password' => $password], $request->filled('remember'))) {
            $user = Auth::user();
            // Admin dan pimpinan boleh login dengan email
            if ($user->role === 'admin' || $user->role === 'pimpinan') {
                // HAPUS FLAG SHOW_PASSWORD SETELAH LOGIN
                $this->clearShowPasswordFlag($user);
                return true;
            } else {
                Auth::logout();
                return false;
            }
        }
        
        // Coba login dengan NIP
        if (Auth::attempt(['nip' => $identifier, 'password' => $password], $request->filled('remember'))) {
            $user = Auth::user();
            // Admin dan pimpinan boleh login dengan NIP
            if ($user->role === 'admin' || $user->role === 'pimpinan') {
                // HAPUS FLAG SHOW_PASSWORD SETELAH LOGIN
                $this->clearShowPasswordFlag($user);
                return true;
            } else {
                Auth::logout();
                return false;
            }
        }
        
        return false;
    }

    /**
     * Hapus flag show_password setelah user berhasil login
     */
    protected function clearShowPasswordFlag($user)
    {
        if ($user->show_password) {
            $user->show_password = false;
            $user->temp_password = null;
            $user->save();
        }
    }

    /**
     * Custom error message
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = ['identifier' => 'Email/NIP/NPSN atau password salah'];

        return redirect()->back()
            ->withInput($request->only('identifier', 'remember'))
            ->withErrors($errors);
    }
}