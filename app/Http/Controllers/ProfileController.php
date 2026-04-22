<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Ganti password via AJAX (Modal)
     */
    public function changePassword(Request $request)
    {
        // Log request untuk debug
        Log::info('Change password attempt', [
            'user_id' => Auth::id(),
            'data' => $request->all()
        ]);
        
        // Validasi
        $validator = validator($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Password lama wajib diisi',
            'new_password.required' => 'Password baru wajib diisi',
            'new_password.min' => 'Password baru minimal 6 karakter',
            'new_password.confirmed' => 'Konfirmasi password baru tidak sesuai',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        // Cek password lama
        if (!Hash::check($request->current_password, $user->password)) {
            Log::warning('Password lama salah', ['user_id' => $user->id]);
            return response()->json([
                'success' => false,
                'message' => 'Password lama yang Anda masukkan salah.'
            ], 422);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->show_password = false;
        $user->temp_password = null;
        $user->save();

        Log::info('Password berhasil diubah', ['user_id' => $user->id, 'user_name' => $user->name]);

        return response()->json([
            'success' => true,
            'message' => '✅ Password berhasil diubah!'
        ]);
    }
}