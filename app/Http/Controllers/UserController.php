<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;

class UserController extends Controller
{
    public function index(Request $request)  // TAMBAHKAN $request DI SINI
    {
        $this->authorize('viewAny', User::class);
        
        $query = User::with('school');
        
        // ===== FILTER =====
        // Filter berdasarkan role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        // Filter berdasarkan sekolah
        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }
        
        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('npsn', 'like', "%{$search}%");
            });
        }
        
        $users = $query->orderBy('name')->paginate(15);
        
        // ===== KIRIMKAN DATA SEKOLAH UNTUK FILTER =====
        $schools = School::orderBy('name')->get();
        
        return view('users.index', compact('users', 'schools'));
    }

    public function create()
    {
        $this->authorize('create', User::class);
        
        $schools = School::orderBy('name')->get();
        return view('users.create', compact('schools'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,pimpinan,operator',
            'school_id' => 'nullable|required_if:role,operator|exists:schools,id'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'school_id' => $validated['school_id'] ?? null,
            'show_password' => false,
            'temp_password' => null,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        
        $schools = School::orderBy('name')->get();
        return view('users.edit', compact('user', 'schools'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,pimpinan,operator',
            'school_id' => 'nullable|required_if:role,operator|exists:schools,id'
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8|confirmed']);
            $validated['password'] = Hash::make($request->password);
            $validated['show_password'] = false;
            $validated['temp_password'] = null;
        }

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    // ==================== RESET PASSWORD ====================

    public function resetPassword(User $user)
    {
        $newPassword = Str::random(8);
        
        $user->temp_password = $newPassword;
        $user->show_password = true;
        $user->password = Hash::make($newPassword);
        $user->save();
        
        return redirect()->back()->with('reset_password', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'password' => $newPassword
        ]);
    }

    // ==================== METHOD IMPORT USER ====================

    public function showImportForm()
    {
        $this->authorize('create', User::class);
        
        return view('users.import');
    }

    public function import(Request $request)
    {
        $this->authorize('create', User::class);
        
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);
        
        try {
            $import = new UsersImport();
            Excel::import($import, $request->file('file'));
            
            $successCount = $import->getSuccessCount();
            $failures = $import->getFailures();
            
            if (count($failures) > 0) {
                $errorMessages = [];
                foreach ($failures as $failure) {
                    $errorMessages[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
                }
                
                return redirect()->route('users.index')
                    ->with('warning', "Berhasil import {$successCount} user, tetapi ada " . count($failures) . " baris gagal.")
                    ->with('errors_detail', $errorMessages);
            }
            
            return redirect()->route('users.index')
                ->with('success', "Berhasil mengimport {$successCount} user!");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = ['name', 'email', 'role', 'npsn', 'password'];
        
        return response()->streamDownload(function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            
            fputcsv($file, ['Admin Sistem', 'admin@sivoka.com', 'admin', '', 'password123']);
            fputcsv($file, ['Kepala Bidang', 'kabid@sivoka.com', 'pimpinan', '', 'password123']);
            fputcsv($file, ['Operator SLB B Karya Murni', 'operator@karyamurni.sch.id', 'operator', '10207451', 'password123']);
            fputcsv($file, ['Operator SLB N Pembina Medan', 'operator@pembina.sch.id', 'operator', '10207452', 'password123']);
            
            fclose($file);
        }, 'template_import_user.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}