<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'icon'];

    public function programs()
    {
        return $this->hasMany(Program::class, 'category_id');
    }
}