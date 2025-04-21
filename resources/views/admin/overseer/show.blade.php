@extends('admin.layouts.app')

@section('title', 'Detail Overseer')

@section('content')
<div class="row">
    <!-- Profile Card -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                @if($overseer->photo)
                    <img src="{{ asset('storage/' . $overseer->photo) }}"
                         alt="Profile Photo"
                         class="rounded-circle img-thumbnail mb-3"
                         style="width: 150px; height: 150px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width: 150px; height: 150px;">
                        <i class="fas fa-user fa-4x text-white"></i>
                    </div>
                @endif

                <h5 class="mb-1">{{ $overseer->name }}</h5>
                <p class="text-muted mb-3">Overseer ID: {{ $overseer->id }}</p>

                <div class="d-grid gap-2">
                    <a href="{{ route('overseer.edit', $overseer->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit Profile
                    </a>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $overseer->id }})">
                        <i class="fas fa-trash me-2"></i>Hapus Overseer
                    </button>
                </div>
            </div>
        </div>

        <!-- Status Card -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Status</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <span>Status Akun</span>
                    <span class="badge bg-{{ $overseer->active ? 'success' : 'danger' }}">
                        {{ $overseer->active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span>Terakhir Login</span>
                    <span class="text-muted">{{ $overseer->last_login_at ?? 'Belum Pernah Login' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Card -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Informasi Detail</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted">Email</label>
                        <p class="mb-0">{{ $overseer->email }}</p>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted">Nomor Telepon</label>
                        <p class="mb-0">{{ $overseer->phone }}</p>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted">Region</label>
                        <p class="mb-0">{{ $overseer->region->name ?? 'N/A' }}</p>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-muted">Alamat</label>
                        <p class="mb-0">{{ $overseer->address ?? 'Tidak ada alamat' }}</p>
                    </div>

                    <hr>

                    <div class="col-12">
                        <h6 class="mb-3">Statistik Pengawasan</h6>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center">
                                    <h3 class="mb-1">{{ $overseer->total_supervisions ?? 0 }}</h3>
                                    <small class="text-muted">Total Pengawasan</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center">
                                    <h3 class="mb-1">{{ $overseer->active_supervisions ?? 0 }}</h3>
                                    <small class="text-muted">Pengawasan Aktif</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center">
                                    <h3 class="mb-1">{{ $overseer->completed_supervisions ?? 0 }}</h3>
                                    <small class="text-muted">Pengawasan Selesai</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Card -->
        <div class="card mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Aktivitas Terbaru</h6>
                <a href="#" class="btn btn-sm btn-link">Lihat Semua</a>
            </div>
            <div class="card-body">
                @if(count($recentActivities ?? []) > 0)
                    <div class="timeline">
                        @foreach($recentActivities as $activity)
                            <div class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">{{ $activity->title }}</h6>
                                    <p class="mb-0 text-muted">{{ $activity->description }}</p>
                                    <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-muted mb-0">Belum ada aktivitas</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus overseer ini?</p>
                <p class="text-danger"><small>Tindakan ini tidak dapat dibatalkan</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.timeline {
    position: relative;
    padding-left: 1.5rem;
}

.timeline-item {
    position: relative;
    padding-bottom: 1.5rem;
}

.timeline-marker {
    position: absolute;
    left: -1.5rem;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: var(--primary-color);
    border: 2px solid #fff;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -1.34rem;
    top: 12px;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item:last-child::before {
    display: none;
}
</style>
@endsection

@section('scripts')
<script>
function confirmDelete(id) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    form.action = `/admin/overseer/${id}`;
    modal.show();
}
</script>
@endsection
