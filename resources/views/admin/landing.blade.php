@extends('admin.layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card welcome-card">
            <div class="card-body text-center py-5">
                <img src="{{ asset('images/fobi-logo.png') }}" alt="FOBI Logo" class="mb-4" style="max-width: 200px;">
                <h2 class="mb-4">Selamat Datang di Admin Panel FOBI</h2>
                <p class="lead text-muted">
                    Kelola data dan informasi FOBI dengan mudah dan efisien
                </p>
                <div class="mt-4">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Buka Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.welcome-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
</style>
@endsection
