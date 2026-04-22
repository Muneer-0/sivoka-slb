<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\School;

class SchoolCoordinatesSeeder extends Seeder
{
    /**
     * Data koordinat untuk kota-kota di Sumatera Utara
     */
    private $coordinates = [
        'Medan' => ['lat' => 3.5952, 'lng' => 98.6722],
        'Deli Serdang' => ['lat' => 3.5854, 'lng' => 98.6722],
        'Karo' => ['lat' => 3.1177, 'lng' => 98.4967],
        'Simalungun' => ['lat' => 2.8981, 'lng' => 99.1419],
        'Tapanuli' => ['lat' => 2.0189, 'lng' => 99.5136],
        'Tapanuli Selatan' => ['lat' => 1.4856, 'lng' => 99.2333],
        'Tapanuli Tengah' => ['lat' => 1.8833, 'lng' => 98.6667],
        'Tapanuli Utara' => ['lat' => 2.0167, 'lng' => 99.0667],
        'Asahan' => ['lat' => 2.9965, 'lng' => 99.7245],
        'Labuhanbatu' => ['lat' => 2.2865, 'lng' => 99.8767],
        'Labuhanbatu Selatan' => ['lat' => 1.9833, 'lng' => 100.0833],
        'Labuhanbatu Utara' => ['lat' => 2.3333, 'lng' => 99.6333],
        'Langkat' => ['lat' => 3.7857, 'lng' => 98.2397],
        'Binjai' => ['lat' => 3.6133, 'lng' => 98.4854],
        'Tebing Tinggi' => ['lat' => 3.3285, 'lng' => 99.1625],
        'Pematangsiantar' => ['lat' => 2.9595, 'lng' => 99.0671],
        'Tanjungbalai' => ['lat' => 2.9667, 'lng' => 99.8000],
        'Sibolga' => ['lat' => 1.7400, 'lng' => 98.7811],
        'Padang Sidempuan' => ['lat' => 1.3738, 'lng' => 99.2667],
        'Gunungsitoli' => ['lat' => 1.2889, 'lng' => 97.6144],
        'Nias' => ['lat' => 1.1036, 'lng' => 97.5667],
        'Nias Selatan' => ['lat' => 0.7833, 'lng' => 97.7833],
        'Nias Utara' => ['lat' => 1.3333, 'lng' => 97.3167],
        'Nias Barat' => ['lat' => 1.0667, 'lng' => 97.5333],
        'Humbang Hasundutan' => ['lat' => 2.2667, 'lng' => 98.5000],
        'Pakpak Bharat' => ['lat' => 2.5667, 'lng' => 98.2833],
        'Samosir' => ['lat' => 2.5833, 'lng' => 98.8167],
        'Serdang Bedagai' => ['lat' => 3.3667, 'lng' => 99.0333],
        'Batu Bara' => ['lat' => 3.1667, 'lng' => 99.5333],
        'Padang Lawas' => ['lat' => 1.1167, 'lng' => 99.8167],
        'Padang Lawas Utara' => ['lat' => 1.7500, 'lng' => 99.6833],
    ];

    public function run(): void
    {
        $schools = School::all();
        $updated = 0;
        
        foreach ($schools as $school) {
            // Cari koordinat berdasarkan kota
            $coord = $this->coordinates[$school->city] ?? ['lat' => 3.5952, 'lng' => 98.6722];
            
            // Update hanya jika koordinat belum diisi
            if (is_null($school->latitude) || is_null($school->longitude)) {
                $school->latitude = $coord['lat'];
                $school->longitude = $coord['lng'];
                $school->save();
                $updated++;
            }
        }
        
        $this->command->info("✅ Berhasil mengupdate koordinat untuk {$updated} sekolah");
        $this->command->warn("📍 Total sekolah: " . count($schools));
    }
}