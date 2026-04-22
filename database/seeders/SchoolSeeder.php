<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        $schools = [
            [
                'npsn' => '10207451',
                'name' => 'SLB B Karya Murni',
                'address' => 'Jl. Karya No. 45',
                'district' => 'Medan Barat',
                'city' => 'Medan',
                'phone' => '061-1234567',
                'headmaster' => 'Dra. Siti Aminah, M.Pd',
                'status' => 'swasta',
                'accreditation' => 'A',
            ],
            [
                'npsn' => '10207452',
                'name' => 'SLB N Pembina Medan',
                'address' => 'Jl. Pendidikan No. 10',
                'district' => 'Medan Petisah',
                'city' => 'Medan',
                'phone' => '061-7654321',
                'headmaster' => 'Drs. Ahmad Yani, M.Si',
                'status' => 'negeri',
                'accreditation' => 'A',
            ],
            [
                'npsn' => '10207453',
                'name' => 'SLB C Dharma Wanita',
                'address' => 'Jl. Deli No. 78',
                'district' => 'Medan Deli',
                'city' => 'Medan',
                'phone' => '061-2345678',
                'headmaster' => 'Hj. Nurhayati, S.Pd',
                'status' => 'swasta',
                'accreditation' => 'B',
            ],
            [
                'npsn' => '10207454',
                'name' => 'SLB N 1 Deli Serdang',
                'address' => 'Jl. Besar No. 23',
                'district' => 'Lubuk Pakam',
                'city' => 'Deli Serdang',
                'phone' => '061-3456789',
                'headmaster' => 'Drs. Maruli Pardede, M.Pd',
                'status' => 'negeri',
                'accreditation' => 'A',
            ],
            [
                'npsn' => '10207455',
                'name' => 'SLB YPAC Medan',
                'address' => 'Jl. Sei Deli No. 5',
                'district' => 'Medan Barat',
                'city' => 'Medan',
                'phone' => '061-4567890',
                'headmaster' => 'Rina Sari, S.Psi',
                'status' => 'swasta',
                'accreditation' => 'B',
            ],
        ];

        foreach ($schools as $school) {
            DB::table('schools')->insert([
                'npsn' => $school['npsn'],
                'name' => $school['name'],
                'address' => $school['address'],
                'district' => $school['district'],
                'city' => $school['city'],
                'province' => 'Sumatera Utara',
                'phone' => $school['phone'],
                'headmaster' => $school['headmaster'],
                'status' => $school['status'],
                'accreditation' => $school['accreditation'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}