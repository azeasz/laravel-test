@extends('admin.layouts.app')

@section('title', 'Edit Overseer')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Edit Overseer</h5>
        <span class="badge bg-info">ID: {{ $overseer->id }}</span>
    </div>
    <div class="card-body">
        <form action="{{ route('overseer.update', $overseer->id) }}" method="POST" class="row g-3" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="col-md-6">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input type="text"
                       class="form-control @error('name') is-invalid @enderror"
                       name="name"
                       value="{{ old('name', $overseer->name) }}"
                       required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label">Email</label>
                <input type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       name="email"
                       value="{{ old('email', $overseer->email) }}"
                       required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Contact Information -->
            <div class="col-md-6">
                <label for="phone" class="form-label">Nomor Telepon</label>
                <input type="text"
                       class="form-control @error('phone') is-invalid @enderror"
                       name="phone"
                       value="{{ old('phone', $overseer->phone) }}"
                       required>
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="region_id" class="form-label">Region</label>
                <select name="region_id" class="form-select @error('region_id') is-invalid @enderror" required>
                    <option value="">Pilih Region</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}"
                                {{ old('region_id', $overseer->region_id) == $region->id ? 'selected' : '' }}>
                            {{ $region->name }}
                        </option>
                    @endforeach
                </select>
                @error('region_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password Fields (Optional) -->
            <div class="col-md-6">
                <label for="password" class="form-label">Password Baru (Kosongkan jika tidak diubah)</label>
                <div class="input-group">
                    <input type="password"
                           class="form-control @error('password') is-invalid @enderror"
                           name="password"
                           id="password">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                <div class="input-group">
                    <input type="password"
                           class="form-control"
                           name="password_confirmation"
                           id="password_confirmation">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="col-12">
                <label for="address" class="form-label">Alamat</label>
                <textarea class="form-control @error('address') is-invalid @enderror"
                          name="address"
                          rows="3">{{ old('address', $overseer->address) }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="photo" class="form-label">Foto Profil</label>
                @if($overseer->photo)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $overseer->photo) }}"
                             alt="Current photo"
                             class="img-thumbnail"
                             style="max-height: 100px;">
                    </div>
                @endif
                <input type="file"
                       class="form-control @error('photo') is-invalid @enderror"
                       name="photo"
                       accept="image/*">
                @error('photo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="status" class="form-label">Status</label>
                <div class="form-check form-switch">
                    <input type="checkbox"
                           class="form-check-input"
                           name="active"
                           id="active"
                           value="1"
                           {{ old('active', $overseer->active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="active">Aktif</label>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Overseer
                </button>
                <a href="{{ route('overseer.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    field.type = field.type === 'password' ? 'text' : 'password';
}
</script>
@endsection
