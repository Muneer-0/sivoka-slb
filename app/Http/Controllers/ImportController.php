<?php

namespace App\Http\Controllers;

use App\Imports\SchoolsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ImportController extends Controller
{
    public function showImportForm()
    {
        return view('schools.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        try {
            $file = $request->file('file');
            
            $import = new SchoolsImport();
            Excel::import($import, $file);
            
            $successCount = $import->getSuccessCount();
            $updatedCount = $import->getUpdatedCount();
            $userCreatedCount = $import->getUserCreatedCount(); // TAMBAHKAN
            $totalProcessed = $import->getTotalProcessed();
            $failures = $import->getFailures();
            
            $message = "🎉 Proses import selesai! ";
            $message .= "Sekolah baru: {$successCount}, ";
            $message .= "Sekolah diperbarui: {$updatedCount}, ";
            $message .= "User operator: {$userCreatedCount}, ";
            $message .= "Total diproses: {$totalProcessed}.";
            
            if (count($failures) > 0) {
                $errorMessages = [];
                foreach ($failures as $failure) {
                    $errorMessages[] = "Baris {$failure['row']}: " . implode(', ', $failure['errors']);
                }
                
                return redirect()->back()
                    ->with('warning', $message . " Terdapat " . count($failures) . " baris gagal.")
                    ->with('errors_detail', $errorMessages);
            }
            
            return redirect()->route('schools.index')
                ->with('success', $message);
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'npsn', 'name', 'address', 'village', 'district', 'city', 
            'province', 'phone', 'email', 'headmaster', 'status', 'accreditation'
        ];
        
        // TAMBAHKAN CONTOH DATA AGAR USER MUDAH MEMAHAMI FORMAT
        return response()->streamDownload(function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            
            // Contoh data (baris 2)
            fputcsv($file, [
                '10207451',
                'SLB B Karya Murni',
                'Jl. Karya No. 45',
                'Pulo Brayan',
                'Medan Barat',
                'Medan',
                'Sumatera Utara',
                '061-1234567',
                'slbbkaryamurni@sch.id',
                'Dra. Siti Aminah, M.Pd',
                'swasta',
                'A'
            ]);
            
            // Contoh data (baris 3)
            fputcsv($file, [
                '10207452',
                'SLB N Pembina Medan',
                'Jl. Pendidikan No. 10',
                'Petisah Tengah',
                'Medan Petisah',
                'Medan',
                'Sumatera Utara',
                '061-7654321',
                'slbnpembinamedan@sch.id',
                'Drs. Ahmad Yani, M.Si',
                'negeri',
                'A'
            ]);
            
            fclose($file);
        }, 'template_import_slb.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}