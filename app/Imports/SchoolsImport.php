<?php

namespace App\Imports;

use App\Models\School;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SchoolsImport implements 
    ToModel, 
    WithHeadingRow, 
    WithValidation, 
    SkipsOnFailure,
    WithBatchInserts,
    WithChunkReading
{
    protected $successCount = 0;
    protected $updatedCount = 0;
    protected $userCreatedCount = 0; // TAMBAHKAN
    protected $failures = [];

    /**
     * Memproses setiap baris dari Excel
     */
    public function model(array $row)
    {
        // PERBAIKAN 1: Cek apakah NPSN sudah ada
        $existingSchool = School::where('npsn', $row['npsn'])->first();
        
        // Tentukan status (Negeri/Swasta)
        $status = strtolower(trim($row['status'])) == 'negeri' ? 'negeri' : 'swasta';
        
        // Tentukan akreditasi (jika kosong, set null)
        $accreditation = !empty($row['accreditation']) ? $row['accreditation'] : null;
        
        // Dapatkan koordinat default berdasarkan kota
        $coordinates = $this->getDefaultCoordinates($row['city'] ?? 'medan');
        
        // Data sekolah yang akan disimpan
        $schoolData = [
            'npsn'          => $row['npsn'],
            'name'          => $row['name'],
            'address'       => $row['address'] ?? null,
            'village'       => $row['village'] ?? null,
            'district'      => $row['district'] ?? null,
            'city'          => $row['city'] ?? null,
            'province'      => $row['province'] ?? 'Sumatera Utara',
            'phone'         => $row['phone'] ?? null,
            'email'         => $row['email'] ?? null,
            'headmaster'    => $row['headmaster'] ?? null,
            'status'        => $status,
            'accreditation' => $accreditation,
            'latitude'      => $coordinates['lat'],
            'longitude'     => $coordinates['lng'],
        ];
        
        // PERBAIKAN 2: Update jika ada, Insert jika belum ada
        if ($existingSchool) {
            // Update data yang sudah ada
            $existingSchool->update($schoolData);
            $this->updatedCount++;
            $school = $existingSchool;
        } else {
            // Insert data baru (biarkan auto-increment)
            $school = School::create($schoolData);
            $this->successCount++;
        }
        
        // PERBAIKAN 3: Update atau buat user operator
        $this->createOrUpdateOperatorUser($school, $row);
        
        return null; // Karena sudah dihandle oleh update/create di atas
    }

    /**
     * Membuat atau mengupdate user operator (DIPERBAIKI - TAMBAHKAN EMAIL)
     */
    private function createOrUpdateOperatorUser($school, $row = [])
    {
        try {
            // Buat email untuk operator (jika tidak ada di Excel)
            $operatorEmail = !empty($row['email']) 
                ? $row['email'] 
                : 'operator_' . $school->npsn . '@sivoka.com';
            
            // Cek apakah user dengan NPSN ini sudah ada
            $existingUser = User::where('npsn', $school->npsn)->first();
            
            if ($existingUser) {
                // Update user yang sudah ada
                $existingUser->update([
                    'name' => 'Operator ' . $school->name,
                    'school_id' => $school->id,
                    'role' => 'operator',
                    'email' => $operatorEmail,
                ]);
                $this->userCreatedCount++;
                return $existingUser;
            }
            
            // Cek apakah user dengan school_id ini sudah ada
            $userBySchool = User::where('school_id', $school->id)->first();
            
            if ($userBySchool) {
                // Update user yang sudah ada
                $userBySchool->update([
                    'name' => 'Operator ' . $school->name,
                    'npsn' => $school->npsn,
                    'role' => 'operator',
                    'email' => $operatorEmail,
                ]);
                $this->userCreatedCount++;
                return $userBySchool;
            }
            
            // Buat user operator baru
            User::create([
                'name' => 'Operator ' . $school->name,
                'email' => $operatorEmail,
                'npsn' => $school->npsn,
                'password' => Hash::make('password123'),
                'role' => 'operator',
                'school_id' => $school->id,
            ]);
            $this->userCreatedCount++;
            
        } catch (\Exception $e) {
            // Log error jika gagal
            \Log::error('Gagal membuat user operator untuk NPSN ' . $school->npsn . ': ' . $e->getMessage());
        }
    }

    /**
     * Aturan validasi
     */
    public function rules(): array
    {
        return [
            'npsn'      => 'required|digits:8',
            'name'      => 'required|max:255',
            'address'   => 'nullable|max:500',
            'district'  => 'nullable|max:100',
            'city'      => 'nullable|max:100',
            'status'    => 'nullable|in:Negeri,Swasta,negeri,swasta',
            'email'     => 'nullable|email', // Validasi email jika diisi
        ];
    }

    /**
     * Pesan validasi
     */
    public function customValidationMessages()
    {
        return [
            'npsn.required' => 'NPSN wajib diisi',
            'npsn.digits' => 'NPSN harus 8 digit angka',
            'name.required' => 'Nama sekolah wajib diisi',
            'email.email' => 'Format email tidak valid',
        ];
    }

    /**
     * Mapping header Excel ke field database
     */
    public function headingRow(): int
    {
        return 1;
    }

    /**
     * Batch insert untuk performa
     */
    public function batchSize(): int
    {
        return 50;
    }

    /**
     * Chunk reading untuk hemat memori
     */
    public function chunkSize(): int
    {
        return 50;
    }

    /**
     * Handle failures
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->failures[] = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ];
        }
    }

    /**
     * Mendapatkan jumlah sukses (insert baru)
     */
    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    /**
     * Mendapatkan jumlah update
     */
    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }

    /**
     * Mendapatkan jumlah user operator yang dibuat/diupdate
     */
    public function getUserCreatedCount(): int
    {
        return $this->userCreatedCount;
    }

    /**
     * Mendapatkan total data yang diproses
     */
    public function getTotalProcessed(): int
    {
        return $this->successCount + $this->updatedCount;
    }

    /**
     * Mendapatkan daftar failure
     */
    public function getFailures(): array
    {
        return $this->failures;
    }

    /**
     * Koordinat default per kota
     */
    private function getDefaultCoordinates($city)
    {
        $city = strtolower(trim($city));
        
        $coordinates = [
            'medan' => ['lat' => 3.5952, 'lng' => 98.6722],
            'pematangsiantar' => ['lat' => 2.9595, 'lng' => 99.0671],
            'binjai' => ['lat' => 3.6133, 'lng' => 98.4854],
            'tebing tinggi' => ['lat' => 3.3285, 'lng' => 99.1625],
            'tanjungbalai' => ['lat' => 2.9667, 'lng' => 99.8000],
            'sibolga' => ['lat' => 1.7400, 'lng' => 98.7811],
            'padangsidimpuan' => ['lat' => 1.3738, 'lng' => 99.2667],
            'gunungsitoli' => ['lat' => 1.2889, 'lng' => 97.6144],
            'deli serdang' => ['lat' => 3.5854, 'lng' => 98.6722],
            'langkat' => ['lat' => 3.7857, 'lng' => 98.2397],
            'karo' => ['lat' => 3.1177, 'lng' => 98.4967],
            'simalungun' => ['lat' => 2.8981, 'lng' => 99.1419],
            'asahan' => ['lat' => 2.9965, 'lng' => 99.7245],
            'labuhanbatu' => ['lat' => 2.2865, 'lng' => 99.8767],
            'tapanuli utara' => ['lat' => 2.0167, 'lng' => 99.0667],
            'tapanuli tengah' => ['lat' => 1.8833, 'lng' => 98.6667],
            'tapanuli selatan' => ['lat' => 1.4856, 'lng' => 99.2333],
            'mandailing natal' => ['lat' => 0.7833, 'lng' => 99.6167],
            'nias' => ['lat' => 1.1036, 'lng' => 97.5667],
            'nias barat' => ['lat' => 1.0667, 'lng' => 97.5333],
            'nias selatan' => ['lat' => 0.7833, 'lng' => 97.7833],
            'nias utara' => ['lat' => 1.3333, 'lng' => 97.3167],
            'humbang hasundutan' => ['lat' => 2.2667, 'lng' => 98.5000],
            'pakpak bharat' => ['lat' => 2.5667, 'lng' => 98.2833],
            'samosir' => ['lat' => 2.5833, 'lng' => 98.8167],
            'serdang bedagai' => ['lat' => 3.3667, 'lng' => 99.0333],
            'batu bara' => ['lat' => 3.1667, 'lng' => 99.5333],
            'padang lawas' => ['lat' => 1.1167, 'lng' => 99.8167],
            'padang lawas utara' => ['lat' => 1.7500, 'lng' => 99.6833],
            'labuhanbatu selatan' => ['lat' => 1.9833, 'lng' => 100.0833],
            'labuhanbatu utara' => ['lat' => 2.3333, 'lng' => 99.6333],
            'toba' => ['lat' => 2.3333, 'lng' => 99.1667],
            'dairi' => ['lat' => 2.8667, 'lng' => 98.2333],
        ];
        
        return $coordinates[$city] ?? ['lat' => 3.5952, 'lng' => 98.6722];
    }
}