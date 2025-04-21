@extends('admin.layouts.app')

@section('title', 'Add New Taxa')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Tambah Taxa Baru</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('taxontests.store') }}" method="POST" class="row g-3">
            @csrf

            <div class="col-md-6">
                <label for="family" class="form-label">Family</label>
                <input type="text"
                       class="form-control @error('family') is-invalid @enderror"
                       name="family"
                       value="{{ old('family') }}"
                       required>
                @error('family')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="genus" class="form-label">Genus</label>
                <input type="text"
                       class="form-control @error('genus') is-invalid @enderror"
                       name="genus"
                       value="{{ old('genus') }}"
                       required>
                @error('genus')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="species" class="form-label">Species</label>
                <input type="text"
                       class="form-control @error('species') is-invalid @enderror"
                       name="species"
                       value="{{ old('species') }}"
                       required>
                @error('species')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="common_name" class="form-label">Common Name</label>
                <input type="text"
                       class="form-control @error('common_name') is-invalid @enderror"
                       name="common_name"
                       value="{{ old('common_name') }}">
                @error('common_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="status" class="form-label">Status</label>
                <select name="status" class="form-select @error('status') is-invalid @enderror">
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror"
                          name="description"
                          rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Simpan Taxa
                </button>
                <a href="{{ route('taxontests.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
