<?php

namespace App\Imports;

use App\Models\User;
use App\Models\School;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\Hash;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;
    
    private $successCount = 0;
    
    public function model(array $row)
    {
        // Cek apakah email sudah ada
        $existingUser = User::where('email', $row['email'])->first();
        
        if ($existingUser) {
            $this->successCount++;
            return null; // Skip jika sudah ada
        }
        
        $userData = [
            'name' => $row['name'],
            'email' => $row['email'],
            'role' => strtolower($row['role']),
            'password' => Hash::make($row['password'] ?? 'password123'),
        ];
        
        // Jika role operator, cari school_id berdasarkan NPSN
        if ($userData['role'] == 'operator' && !empty($row['npsn'])) {
            $school = School::where('npsn', $row['npsn'])->first();
            if ($school) {
                $userData['school_id'] = $school->id;
            }
        }
        
        $this->successCount++;
        return new User($userData);
    }
    
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,pimpinan,operator',
            'npsn' => 'required_if:role,operator|nullable|exists:schools,npsn',
            'password' => 'nullable|min:6',
        ];
    }
    
    public function customValidationMessages()
    {
        return [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'role.required' => 'Role wajib diisi',
            'role.in' => 'Role harus admin, pimpinan, atau operator',
            'npsn.required_if' => 'NPSN wajib diisi untuk role operator',
            'npsn.exists' => 'NPSN tidak ditemukan di database SLB',
            'password.min' => 'Password minimal 6 karakter',
        ];
    }
    
    public function getSuccessCount()
    {
        return $this->successCount;
    }
}