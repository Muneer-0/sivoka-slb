@extends('layouts.dashboard')

@section('title', 'Detail Kategori')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">
        <i class="bi bi-tags me-2 text-primary"></i>
        Detail Kategori
    </h3>
    <div>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
        @endif
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-xl-12 mb-4">
        <div class="table-container">
            <div class="text-center mb-4">
                <i class="{{ $category->icon }}" style="font-size: 4rem; color: #0d6efd;"></i>
                <h4 class="mt-3">{{ $category->name }}</h4>
                <p class="text-muted">Slug: {{ $category->slug }}</p>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <h5>Deskripsi</h5>
                    <p>{{ $category->description ?? 'Tidak ada deskripsi' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Program dalam Kategori ini -->
<div class="row">
    <div class="col-xl-12 mb-4">
        <div class="table-container">
            <h5 class="mb-3">
                <i class="bi bi-bar-chart me-2 text-primary"></i>
                Program Vokasi dalam Kategori "{{ $category->name }}"
            </h5>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Program</th>
                            <th>SLB</th>
                            <th>Kota/Kab</th>
                            <th>Jumlah Siswa</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($category->programs as $index => $program)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $program->program_name }}</td>
                            <td>{{ $program->school->name }}</td>
                            <td>{{ $program->school->city }}</td>
                            <td class="text-center">{{ $program->student_count }}</td>
                            <td>
                                <a href="{{ route('programs.show', $program) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada program dalam kategori ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection