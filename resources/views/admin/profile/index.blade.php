@extends('admin.layouts.app')

@section('title', 'Profile')

@section('content')
<div class="row">
    <!-- Profile Information -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                @if(auth()->user()->photo)
                    <img src="{{ asset('storage/' . auth()->user()->photo) }}"
                         alt="Profile Photo"
                         class="rounded-circle img-thumbnail mb-3"
                         style="width: 150px; height: 150px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width: 150px; height: 150px;">
                        <i class="fas fa-user fa-4x text-white"></i>
                    </div>
                @endif

                <h5 class="mb-1">{{ auth()->user()->name }}</h5>
                <p class="text-muted mb-3">{{ auth()->user()->role }}</p>

                <button type="button" class="btn btn-primary" onclick="document.getElementById('photoInput').click()">
                    <i class="fas fa-camera me-2"></i>Ganti Foto
                </button>
                <form id="photoForm" action="{{ route('profile.update-photo') }}" method="POST" enctype="multipart/form-data" class="d-none">
                    @csrf
                    @method('PUT')
                    <input type="file" id="photoInput" name="photo" accept="image/*" onchange="this.form.submit()">
                </form>
            </div>
        </div>

        <!-- Account Status -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Status Akun</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Status</span>
                    <span class="badge bg-success">Active</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Member Sejak</span>
                    <span class="text-muted">{{ auth()->user()->created_at->format('d M Y') }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Login Terakhir</span>
                    <span class="text-muted">{{ auth()->user()->last_login_at?->diffForHumans() ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Edit Profile</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST" class="row g-3">
                    @csrf
                    @method('PUT')

                    <div class="col-md-6">
                        <label for="fname" class="form-label">Nama Depan</label>
                        <input type="text"
                               class="form-control @error('fname') is-invalid @enderror"
                               name="fname"
                               value="{{ old('fname', auth()->user()->fname) }}"
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
                               value="{{ old('lname', auth()->user()->lname) }}">
                        @error('lname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               name="email"
                               value="{{ old('email', auth()->user()->email) }}"
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label">Nomor Telepon</label>
                        <input type="text"
                               class="form-control @error('phone') is-invalid @enderror"
                               name="phone"
                               value="{{ old('phone', auth()->user()->phone) }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="address" class="form-label">Alamat</label>
                        <textarea class="form-control @error('address') is-invalid @enderror"
                                  name="address"
                                  rows="3">{{ old('address', auth()->user()->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Ganti Password</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.change-password') }}" method="POST" class="row g-3">
                    @csrf
                    @method('PUT')

                    <div class="col-md-12">
                        <label for="current_password" class="form-label">Password Saat Ini</label>
                        <input type="password"
                               class="form-control @error('current_password') is-invalid @enderror"
                               name="current_password"
                               required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="password" class="form-label">Password Baru</label>
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               name="password"
                               required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password"
                               class="form-control"
                               name="password_confirmation"
                               required>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key me-2"></i>Ganti Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Preview image before upload
document.getElementById('photoInput').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.querySelector('.img-thumbnail').src = e.target.result;
        }
        reader.readAsDataURL(e.target.files[0]);
    }
});
</script>
@endsection
