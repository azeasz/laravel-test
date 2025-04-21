@extends('admin.layouts.app')

@section('title', 'Edit Checklist')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Edit Checklist</h5>
        <button type="button" class="btn btn-info btn-sm" onclick="openHelperModal()">
            <i class="fas fa-question-circle me-2"></i>Bantuan
        </button>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.checklists.update', $checklist->id) }}" method="POST" class="row g-3">
            @csrf
            @method('PUT')

            <div class="col-md-6">
                <label for="user_id" class="form-label">User ID</label>
                <input type="number"
                       class="form-control @error('user_id') is-invalid @enderror"
                       name="user_id"
                       value="{{ old('user_id', $checklist->user_id) }}"
                       readonly>
                @error('user_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="observer" class="form-label">Observer</label>
                <input type="text"
                       class="form-control @error('observer') is-invalid @enderror"
                       name="observer"
                       value="{{ old('observer', $checklist->observer) }}"
                       required>
                @error('observer')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Location Fields -->
            <div class="col-md-6">
                <label for="latitude" class="form-label">Latitude</label>
                <div class="input-group">
                    <input type="text"
                           class="form-control @error('latitude') is-invalid @enderror"
                           name="latitude"
                           value="{{ old('latitude', $checklist->latitude) }}"
                           required>
                    <button class="btn btn-outline-secondary" type="button" onclick="getCurrentLocation()">
                        <i class="fas fa-map-marker-alt"></i>
                    </button>
                </div>
                @error('latitude')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="longitude" class="form-label">Longitude</label>
                <input type="text"
                       class="form-control @error('longitude') is-invalid @enderror"
                       name="longitude"
                       value="{{ old('longitude', $checklist->longitude) }}"
                       required>
                @error('longitude')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Date and Time Fields -->
            <div class="col-md-4">
                <label for="tgl_pengamatan" class="form-label">Tanggal Pengamatan</label>
                <input type="date"
                       class="form-control @error('tgl_pengamatan') is-invalid @enderror"
                       name="tgl_pengamatan"
                       value="{{ old('tgl_pengamatan', $checklist->tgl_pengamatan) }}"
                       required>
                @error('tgl_pengamatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label for="start_time" class="form-label">Waktu Mulai</label>
                <input type="time"
                       class="form-control @error('start_time') is-invalid @enderror"
                       name="start_time"
                       value="{{ old('start_time', $checklist->start_time) }}"
                       required>
                @error('start_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label for="end_time" class="form-label">Waktu Selesai</label>
                <input type="time"
                       class="form-control @error('end_time') is-invalid @enderror"
                       name="end_time"
                       value="{{ old('end_time', $checklist->end_time) }}"
                       required>
                @error('end_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Text Areas -->
            <div class="col-12">
                <label for="tujuan_pengamatan" class="form-label">Tujuan Pengamatan</label>
                <textarea class="form-control @error('tujuan_pengamatan') is-invalid @enderror"
                          name="tujuan_pengamatan"
                          rows="3">{{ old('tujuan_pengamatan', $checklist->tujuan_pengamatan) }}</textarea>
                @error('tujuan_pengamatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <label for="additional_note" class="form-label">Catatan Tambahan</label>
                <textarea class="form-control @error('additional_note') is-invalid @enderror"
                          name="additional_note"
                          rows="3">{{ old('additional_note', $checklist->additional_note) }}</textarea>
                @error('additional_note')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Checkboxes -->
            <div class="col-md-4">
                <div class="form-check">
                    <input type="checkbox"
                           class="form-check-input"
                           name="active"
                           id="active"
                           value="1"
                           {{ old('active', $checklist->active) ? 'checked' : '' }}>
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
                           {{ old('completed', $checklist->completed) ? 'checked' : '' }}>
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
                           {{ old('can_edit', $checklist->can_edit) ? 'checked' : '' }}>
                    <label class="form-check-label" for="can_edit">Can Edit</label>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Checklist
                </button>
                <a href="{{ route('admin.checklists.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Helper Modal -->
<div class="modal fade" id="helperModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bantuan Pengisian Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Untuk mengubah waktu:</p>
                <ol>
                    <li>Hapus terlebih dahulu waktu mulai dan selesai yang ada</li>
                    <li>Setelah itu, Anda dapat mengatur ulang waktu mulai dan selesai</li>
                    <li>Pastikan waktu selesai lebih besar dari waktu mulai</li>
                </ol>
                <p class="text-muted">Note: Fitur pengeditan waktu masih dalam tahap pengembangan</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openHelperModal() {
    const modal = new bootstrap.Modal(document.getElementById('helperModal'));
    modal.show();
}

function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.querySelector('input[name="latitude"]').value = position.coords.latitude;
            document.querySelector('input[name="longitude"]').value = position.coords.longitude;
        });
    }
}
</script>
@endsection
