@extends('admin.layouts.app')

@section('title', 'Create Checklist')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Tambah Checklist Baru</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.checklists.store') }}" method="POST" class="row g-3">
            @csrf

            <div class="col-md-6">
                <label for="user_id" class="form-label">User ID</label>
                <input type="number"
                       class="form-control @error('user_id') is-invalid @enderror"
                       name="user_id"
                       value="{{ old('user_id') }}"
                       required>
                @error('user_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="observer" class="form-label">Observer</label>
                <input type="text"
                       class="form-control @error('observer') is-invalid @enderror"
                       name="observer"
                       value="{{ old('observer') }}"
                       required>
                @error('observer')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="latitude" class="form-label">Latitude</label>
                <input type="text"
                       class="form-control @error('latitude') is-invalid @enderror"
                       name="latitude"
                       value="{{ old('latitude') }}"
                       required>
                @error('latitude')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="longitude" class="form-label">Longitude</label>
                <input type="text"
                       class="form-control @error('longitude') is-invalid @enderror"
                       name="longitude"
                       value="{{ old('longitude') }}"
                       required>
                @error('longitude')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="tgl_pengamatan" class="form-label">Tanggal Pengamatan</label>
                <input type="date"
                       class="form-control @error('tgl_pengamatan') is-invalid @enderror"
                       name="tgl_pengamatan"
                       value="{{ old('tgl_pengamatan') }}"
                       required>
                @error('tgl_pengamatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-3">
                <label for="start_time" class="form-label">Waktu Mulai</label>
                <input type="time"
                       class="form-control @error('start_time') is-invalid @enderror"
                       name="start_time"
                       value="{{ old('start_time') }}"
                       required>
                @error('start_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-3">
                <label for="end_time" class="form-label">Waktu Selesai</label>
                <input type="time"
                       class="form-control @error('end_time') is-invalid @enderror"
                       name="end_time"
                       value="{{ old('end_time') }}"
                       required>
                @error('end_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <label for="tujuan_pengamatan" class="form-label">Tujuan Pengamatan</label>
                <textarea class="form-control @error('tujuan_pengamatan') is-invalid @enderror"
                          name="tujuan_pengamatan"
                          rows="3">{{ old('tujuan_pengamatan') }}</textarea>
                @error('tujuan_pengamatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <label for="additional_note" class="form-label">Catatan Tambahan</label>
                <textarea class="form-control @error('additional_note') is-invalid @enderror"
                          name="additional_note"
                          rows="3">{{ old('additional_note') }}</textarea>
                @error('additional_note')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <div class="form-check">
                    <input type="checkbox"
                           class="form-check-input"
                           name="active"
                           id="active"
                           value="1"
                           {{ old('active') ? 'checked' : '' }}>
                    <label class="form-check-label" for="active">Active</label>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-check">
                    <input type="checkbox"
                           class="form-check-input"
                           name="completed"
                           id="completed"
                           value="1"
                           {{ old('completed') ? 'checked' : '' }}>
                    <label class="form-check-label" for="completed">Completed</label>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-check">
                    <input type="checkbox"
                           class="form-check-input"
                           name="can_edit"
                           id="can_edit"
                           value="1"
                           {{ old('can_edit') ? 'checked' : '' }}>
                    <label class="form-check-label" for="can_edit">Can Edit</label>
                </div>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Simpan Checklist
                </button>
                <a href="{{ route('admin.checklists.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
