<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'school_id'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

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
}