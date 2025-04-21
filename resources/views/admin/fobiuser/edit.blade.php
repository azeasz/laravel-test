@extends('admin.layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Edit User</h5>
        <span class="badge bg-info">ID: {{ $user->id }}</span>
    </div>
    <div class="card-body">
        <form action="{{ route('fobiuser.update', $user->id) }}" method="POST" class="row g-3">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="col-md-6">
                <label for="uname" class="form-label">Username</label>
                <input type="text"
                       class="form-control @error('uname') is-invalid @enderror"
                       name="uname"
                       value="{{ old('uname', $user->uname) }}"
                       required>
                @error('uname')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label">Email</label>
                <input type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       name="email"
                       value="{{ old('email', $user->email) }}"
                       required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Name Fields -->
            <div class="col-md-6">
                <label for="fname" class="form-label">Nama Depan</label>
                <input type="text"
                       class="form-control @error('fname') is-invalid @enderror"
                       name="fname"
                       value="{{ old('fname', $user->fname) }}"
                       required>
                @error('fname')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="lname" class="form-label">Nama Belakang</label>
                <input type="text"
                       class="form-control @error('lname') is-invalid @enderror"
                       name="lname"
                       value="{{ old('lname', $user->lname) }}">
                @error('lname')
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

            <!-- Additional Fields -->
            <div class="col-md-6">
                <label for="level" class="form-label">Level</label>
                <select name="level" class="form-select @error('level') is-invalid @enderror" required>
                    <option value="">Pilih Level</option>
                    <option value="1" {{ old('level', $user->level) == '1' ? 'selected' : '' }}>Level 1</option>
                    <option value="2" {{ old('level', $user->level) == '2' ? 'selected' : '' }}>Level 2</option>
                    <option value="3" {{ old('level', $user->level) == '3' ? 'selected' : '' }}>Level 3</option>
                    <option value="4" {{ old('level', $user->level) == '4' ? 'selected' : '' }}>Level 4</option>
                </select>
                @error('level')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="phone" class="form-label">Nomor Telepon</label>
                <input type="text"
                       class="form-control @error('phone') is-invalid @enderror"
                       name="phone"
                       value="{{ old('phone', $user->phone) }}">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <label for="address" class="form-label">Alamat</label>
                <textarea class="form-control @error('address') is-invalid @enderror"
                          name="address"
                          rows="3">{{ old('address', $user->address) }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Status -->
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input type="checkbox"
                           class="form-check-input"
                           name="active"
                           id="active"
                           value="1"
                           {{ old('active', $user->active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="active">User Aktif</label>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update User
                </button>
                <a href="{{ route('fobiuser.index') }}" class="btn btn-secondary">
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
