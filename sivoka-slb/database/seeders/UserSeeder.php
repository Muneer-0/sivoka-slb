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
        $schoolIds = DB::table('schools')->pluck('id')->toArray();

        // Admin
        DB::table('users')->insert([
            'name' => 'Admin Bidang',
            'email' => 'admin@sivoka.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Pimpinan
        DB::table('users')->insert([
            'name' => 'Kepala Bidang',
            'email' => 'kabid@sivoka.com',
            'password' => Hash::make('password123'),
            'role' => 'pimpinan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Operators
        $operators = [
            ['name' => 'Operator SLB B Karya Murni', 'email' => 'op.karyamurni@sivoka.com', 'school_id' => 1],
            ['name' => 'Operator SLB N Pembina Medan', 'email' => 'op.pembina@sivoka.com', 'school_id' => 2],
            ['name' => 'Operator SLB C Dharma Wanita', 'email' => 'op.dharmawanita@sivoka.com', 'school_id' => 3],
        ];

        foreach ($operators as $op) {
            DB::table('users')->insert([
                'name' => $op['name'],
                'email' => $op['email'],
                'password' => Hash::make('password123'),
                'role' => 'operator',
                'school_id' => $op['school_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}