<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'nip', 'email', 'npsn', 'password', 'role', 'school_id',
        'show_password',     // TAMBAHKAN
        'temp_password'      // TAMBAHKAN
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'show_password' => 'boolean',  // TAMBAHKAN
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPimpinan()
    {
        return $this->role === 'pimpinan';
    }

    public function isOperator()
    {
        return $this->role === 'operator';
    }

    public function createdPrograms()
    {
        return $this->hasMany(Program::class, 'created_by');
    }

    /**
     * Cari user berdasarkan identifier (NIP untuk admin/pimpinan, NPSN untuk operator)
     */
    public function findForLogin($identifier)
    {
        // Cek dulu apakah ini NPSN (8 digit angka)
        if (preg_match('/^\d{8}$/', $identifier)) {
            return $this->where('npsn', $identifier)->first();
        }
        
        // Jika bukan NPSN, cek sebagai NIP
        return $this->where('nip', $identifier)->first();
    }
}