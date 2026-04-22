<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'npsn', 'name', 'address', 'village', 'district', 'city', 
        'province', 'phone', 'email', 'headmaster', 'status', 'accreditation'
    ];

    public function programs()
    {
        return $this->hasMany(Program::class);
    }

    public function operators()
    {
        return $this->hasMany(User::class)->where('role', 'operator');
    }
}