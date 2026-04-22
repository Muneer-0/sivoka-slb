<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProgramCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Tata Boga', 'icon' => 'bi bi-cup-hot'],
            ['name' => 'Tata Busana', 'icon' => 'bi bi-scissors'],
            ['name' => 'Kriya Kayu', 'icon' => 'bi bi-hammer'],
            ['name' => 'Kriya Logam', 'icon' => 'bi bi-tools'],
            ['name' => 'Kriya Tekstil', 'icon' => 'bi bi-brush'],
            ['name' => 'Teknologi Informasi', 'icon' => 'bi bi-laptop'],
            ['name' => 'Kecantikan', 'icon' => 'bi bi-star'],
            ['name' => 'Tata Rias Rambut', 'icon' => 'bi bi-scissors'],
            ['name' => 'Merajut', 'icon' => 'bi bi-tshirt'],
            ['name' => 'Ecoprint', 'icon' => 'bi bi-leaf'],
            ['name' => 'Pertanian', 'icon' => 'bi bi-flower1'],
            ['name' => 'Peternakan', 'icon' => 'bi bi-tree'],
            ['name' => 'Budidaya Tanaman Hias', 'icon' => 'bi bi-flower2'],
            ['name' => 'Pembuatan Sabun', 'icon' => 'bi bi-droplet'],
            ['name' => 'Recycling', 'icon' => 'bi bi-recycle'],
        ];

        foreach ($categories as $category) {
            DB::table('program_categories')->insert([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'icon' => $category['icon'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}