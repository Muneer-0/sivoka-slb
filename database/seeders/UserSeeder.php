<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin (pakai NIP dan Email)
        DB::table('users')->updateOrInsert(
            ['nip' => '198001012005011001'],
            [
                'name' => 'Admin Bidang',
                'nip' => '198001012005011001',
                'email' => 'admin@sivoka.com',
                'npsn' => null,
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Pimpinan (pakai NIP dan Email)
        DB::table('users')->updateOrInsert(
            ['nip' => '197512311998022002'],
            [
                'name' => 'Kepala Bidang',
                'nip' => '197512311998022002',
                'email' => 'kabid@sivoka.com',
                'npsn' => null,
                'password' => Hash::make('password123'),
                'role' => 'pimpinan',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Operator (pakai NPSN)
        $operators = [
            [
                'name' => 'Operator SLB B Karya Murni', 
                'npsn' => '10207451',
                'school_id' => 1
            ],
            [
                'name' => 'Operator SLB N Pembina Medan', 
                'npsn' => '10207452',
                'school_id' => 2
            ],
            [
                'name' => 'Operator SLB C Dharma Wanita', 
                'npsn' => '10207453',
                'school_id' => 3
            ],
        ];

        foreach ($operators as $op) {
            DB::table('users')->updateOrInsert(
                ['npsn' => $op['npsn']],
                [
                    'name' => $op['name'],
                    'nip' => null,
                    'email' => null,
                    'npsn' => $op['npsn'],
                    'password' => Hash::make('password123'),
                    'role' => 'operator',
                    'school_id' => $op['school_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}