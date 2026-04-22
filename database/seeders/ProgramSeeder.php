<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            // SLB B Karya Murni (school_id = 1)
            [
                'school_id' => 1,
                'category_id' => 1, // Tata Boga
                'program_name' => 'Membuat Kue Kering',
                'description' => 'Pembuatan nastar, kastengel, putri salju untuk kebutuhan pasar',
                'student_count' => 36,
                'teacher_count' => 3,
                'facilities' => '3 oven listrik, 5 loyang, 2 mixer, 1 lemari pendingin',
                'products' => 'Nastar, Kastengel, Putri Salju, Semprit',
                'achievements' => 'Juara 1 Lomba Tata Boga SLB Tingkat Provinsi 2025',
                'status' => 'active',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'school_id' => 1,
                'category_id' => 2, // Tata Busana
                'program_name' => 'Menjahit Dasar',
                'description' => 'Pembelajaran menjahit manual dan mesin untuk pemula',
                'student_count' => 70,
                'teacher_count' => 4,
                'facilities' => '10 mesin jahit manual, 5 mesin jahit listrik, 3 manekin',
                'products' => 'Taplak meja, sarung bantal, seragam sekolah',
                'achievements' => 'Produk dipasarkan di pameran UMKM Medan',
                'status' => 'active',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'school_id' => 1,
                'category_id' => 8, // Tata Rias Rambut
                'program_name' => 'Pangkas Rambut',
                'description' => 'Keterampilan potong rambut pria dan wanita',
                'student_count' => 18,
                'teacher_count' => 2,
                'facilities' => '5 kursi pangkas, 5 gunting profesional, 3 hair dryer',
                'products' => 'Jasa potong rambut',
                'achievements' => 'Magang di salon-salon Medan',
                'status' => 'active',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // SLB N Pembina Medan (school_id = 2)
            [
                'school_id' => 2,
                'category_id' => 3, // Kriya Kayu
                'program_name' => 'Kerajinan Kayu',
                'description' => 'Pembuatan souvenir dan perabot dari kayu',
                'student_count' => 25,
                'teacher_count' => 3,
                'facilities' => 'Workshop kayu, 5 set peralatan pertukangan, 2 mesin amplas',
                'products' => 'Gantungan kunci, kotak pensil, miniatur rumah',
                'achievements' => 'Produk dipasarkan di toko souvenir',
                'status' => 'active',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'school_id' => 2,
                'category_id' => 6, // Teknologi Informasi
                'program_name' => 'Desain Grafis',
                'description' => 'Pembelajaran desain grafis untuk pemula',
                'student_count' => 30,
                'teacher_count' => 2,
                'facilities' => '15 komputer, software desain, printer',
                'products' => 'Desain undangan, banner, logo',
                'achievements' => 'Juara 2 Lomba Desain Grafis SLB Nasional',
                'status' => 'active',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // SLB C Dharma Wanita (school_id = 3)
            [
                'school_id' => 3,
                'category_id' => 10, // Ecoprint
                'program_name' => 'Ecoprint',
                'description' => 'Pembuatan motif kain dengan bahan alami',
                'student_count' => 9,
                'teacher_count' => 2,
                'facilities' => 'Bahan alami, kain, alat pewarnaan',
                'products' => 'Kain ecoprint, scarf, baju ecoprint',
                'achievements' => 'Produk dipamerkan di event budaya',
                'status' => 'active',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'school_id' => 3,
                'category_id' => 9, // Merajut
                'program_name' => 'Merajut dan Makrame',
                'description' => 'Keterampilan merajut dan membuat makrame',
                'student_count' => 14,
                'teacher_count' => 2,
                'facilities' => 'Benang rajut, hakpen, alat makrame',
                'products' => 'Tas rajut, gantungan pot, aksesoris',
                'achievements' => 'Produk dijual online',
                'status' => 'active',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($programs as $program) {
            DB::table('programs')->insert($program);
        }

        $this->command->info('✅ Data program vokasi berhasil ditambahkan!');
    }
}