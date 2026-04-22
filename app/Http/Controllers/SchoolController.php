<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use App\Models\Program;
use App\Models\ProgramCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SchoolController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', School::class);
        
        $query = School::withCount('programs');
        
        // Filter
        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('npsn', 'like', '%' . $request->search . '%');
            });
        }
        
        $schools = $query->orderBy('city')->orderBy('name')->paginate(15);
        
        $cities = School::distinct('city')->orderBy('city')->pluck('city');
        $statuses = ['negeri' => 'Negeri', 'swasta' => 'Swasta'];
        
        return view('schools.index', compact('schools', 'cities', 'statuses'));
    }

    public function create()
    {
        $this->authorize('create', School::class);
        
        return view('schools.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', School::class);
        
        $validated = $request->validate([
            'npsn' => 'required|unique:schools|size:8',
            'name' => 'required|max:255',
            'address' => 'required',
            'district' => 'required',
            'city' => 'required',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'headmaster' => 'nullable',
            'status' => 'required|in:negeri,swasta',
            'accreditation' => 'nullable',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        // Jika latitude/longitude tidak diisi, gunakan default berdasarkan kota
        if (empty($validated['latitude']) || empty($validated['longitude'])) {
            $defaultCoord = $this->getDefaultCoordinates($validated['city']);
            $validated['latitude'] = $defaultCoord['lat'];
            $validated['longitude'] = $defaultCoord['lng'];
        }

        // Simpan data sekolah
        $school = School::create($validated);

        // ===== OTOMATIS BUAT USER OPERATOR =====
        $this->createOperatorUser($school);

        return redirect()->route('schools.index')
            ->with('success', 'Data SLB berhasil ditambahkan. User operator otomatis dibuat dengan NPSN: ' . $school->npsn);
    }

    public function show(School $school)
    {
        $this->authorize('view', $school);
        
        $school->load(['programs.category']);
        
        return view('schools.show', compact('school'));
    }

    public function edit(School $school)
    {
        $this->authorize('update', $school);
        
        return view('schools.edit', compact('school'));
    }

    public function update(Request $request, School $school)
    {
        $this->authorize('update', $school);
        
        $validated = $request->validate([
            'npsn' => 'required|unique:schools,npsn,' . $school->id . '|size:8',
            'name' => 'required|max:255',
            'address' => 'required',
            'district' => 'required',
            'city' => 'required',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'headmaster' => 'nullable',
            'status' => 'required|in:negeri,swasta',
            'accreditation' => 'nullable',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $school->update($validated);

        return redirect()->route('schools.index')
            ->with('success', 'Data SLB berhasil diperbarui.');
    }

    public function destroy(School $school)
    {
        $this->authorize('delete', $school);
        
        // Hapus juga user operator terkait
        User::where('school_id', $school->id)->where('role', 'operator')->delete();
        
        $school->delete();

        return redirect()->route('schools.index')
            ->with('success', 'Data SLB dan user operator terkait berhasil dihapus.');
    }

    public function mySchool()
    {
        $user = Auth::user();
        
        if (!$user->school) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda belum terdaftar sebagai operator SLB.');
        }
        
        return redirect()->route('schools.show', $user->school);
    }

    /**
     * Menampilkan peta dengan semua SLB
     */
public function leafletMap()
{
    $this->authorize('viewAny', School::class);
    
    $schools = School::withCount('programs')
                ->withSum('programs', 'student_count')
                ->get()
                ->map(function($school) {
                    return [
                        'id' => $school->id,
                        'npsn' => $school->npsn,
                        'name' => $school->name,
                        'address' => $school->address,
                        'city' => $school->city,
                        'district' => $school->district,
                        'latitude' => $school->latitude ?? 3.5952,
                        'longitude' => $school->longitude ?? 98.6722,
                        'accreditation' => $school->accreditation,
                        'status' => $school->status,
                        'programs_count' => (int) $school->programs_count, // CAST ke integer
                        'total_students' => (int) ($school->programs_sum_student_count ?? 0), 
                    ];
                });
    
    return view('schools.peta-slb', compact('schools'));
}

    /**
     * Membuat user operator otomatis berdasarkan data sekolah
     */
    private function createOperatorUser($school)
    {
        // Cek apakah user dengan NPSN ini sudah ada
        $existingUser = User::where('npsn', $school->npsn)->first();
        
        if ($existingUser) {
            // Jika sudah ada, update saja
            $existingUser->update([
                'name' => 'Operator ' . $school->name,
                'school_id' => $school->id,
            ]);
            return $existingUser;
        }

        // Buat user operator baru
        $operator = User::create([
            'name' => 'Operator ' . $school->name,
            'npsn' => $school->npsn,
            'password' => Hash::make('password123'), // Default password
            'role' => 'operator',
            'school_id' => $school->id,
            'email' => null, // Email opsional
        ]);

        return $operator;
    }

    /**
     * Mendapatkan koordinat default berdasarkan kota
     */
    private function getDefaultCoordinates($city)
    {
        $coordinates = [
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
        
        return $coordinates[$city] ?? ['lat' => 3.5952, 'lng' => 98.6722]; // default Medan
    }

    // Untuk backward compatibility
    private function getDefaultLatitude($city)
    {
        return $this->getDefaultCoordinates($city)['lat'];
    }
    
    private function getDefaultLongitude($city)
    {
        return $this->getDefaultCoordinates($city)['lng'];
    }
}