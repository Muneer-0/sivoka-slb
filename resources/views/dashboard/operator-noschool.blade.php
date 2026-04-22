@extends('layouts.dashboard')

@section('title', 'Dashboard Operator')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="table-container text-center py-5">
            <i class="bi bi-exclamation-triangle text-warning" style="font-size: 5rem;"></i>
            <h3 class="mt-3">Anda Belum Terdaftar sebagai Operator SLB</h3>
            <p class="text-muted">Silakan hubungi Administrator untuk menautkan akun Anda dengan data SLB.</p>
        </div>
    </div>
</div>
@endsection