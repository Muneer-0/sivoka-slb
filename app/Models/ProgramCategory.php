<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProgramCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'slug', 
        'description', 
        'icon', 
        'is_global', 
        'school_id',
        'created_by'
    ];

    protected $casts = [
        'is_global' => 'boolean',
    ];

    // Relasi ke sekolah (untuk kategori lokal)
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    // Relasi ke user pembuat
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke program
    public function programs()
    {
        return $this->hasMany(Program::class, 'category_id');
    }

    // Scope untuk kategori global
    public function scopeGlobal($query)
    {
        return $query->where('is_global', true);
    }

    // Scope untuk kategori lokal (milik sekolah tertentu)
    public function scopeLocal($query, $schoolId = null)
    {
        if ($schoolId) {
            return $query->where('is_global', false)->where('school_id', $schoolId);
        }
        return $query->where('is_global', false);
    }

    // Scope untuk kategori yang bisa diakses user (global + lokal sekolahnya)
    public function scopeAccessibleBy($query, $userId)
    {
        $user = User::find($userId);
        if ($user->isAdmin()) {
            return $query;
        }
        
        $schoolId = $user->school_id;
        return $query->where(function($q) use ($schoolId) {
            $q->where('is_global', true)
              ->orWhere(function($q2) use ($schoolId) {
                  $q2->where('is_global', false)->where('school_id', $schoolId);
              });
        });
    }

    // Boot method untuk generate slug otomatis
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });
        
        static::updating(function ($category) {
            $category->slug = Str::slug($category->name);
        });
    }
}