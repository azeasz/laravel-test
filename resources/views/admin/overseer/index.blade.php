@extends('admin.layouts.app')

@section('title', 'Manage Overseer')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Manajemen Overseer</h5>
        <a href="{{ route('overseer.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-user-plus me-2"></i>Tambah Overseer
        </a>
    </div>
    <div class="card-body">
        <!-- Search and Filter -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Cari overseer..." id="searchOverseer">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="filterRegion">
                    <option value="">Semua Region</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}">{{ $region->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="filterStatus">
                    <option value="">Semua Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>

        <!-- Overseer Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Region</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($overseers as $overseer)
                    <tr>
                        <td>{{ $overseer->id }}</td>
                        <td>{{ $overseer->name }}</td>
                        <td>{{ $overseer->region->name ?? 'N/A' }}</td>
                        <td>{{ $overseer->email }}</td>
                        <td>{{ $overseer->phone }}</td>
                        <td>
                            <span class="badge bg-{{ $overseer->active ? 'success' : 'danger' }}">
                                {{ $overseer->active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('overseer.show', $overseer->id) }}"
                                   class="btn btn-sm btn-info"
                                   data-bs-toggle="tooltip"
                                   title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('overseer.edit', $overseer->id) }}"
                                   class="btn btn-sm btn-warning"
                                   data-bs-toggle="tooltip"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-sm btn-danger"
                                        onclick="confirmDelete({{ $overseer->id }})"
                                        data-bs-toggle="tooltip"
                                        title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data overseer</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $overseers->links() }}
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

@section('scripts')
<script>
// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});

function confirmDelete(id) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    form.action = `/admin/overseer/${id}`;
    modal.show();
}

// Search and Filter Implementation
document.getElementById('searchOverseer').addEventListener('keyup', function(e) {
    // Implement search functionality
});

document.getElementById('filterRegion').addEventListener('change', function(e) {
    // Implement region filter
});

document.getElementById('filterStatus').addEventListener('change', function(e) {
    // Implement status filter
});
</script>
@endsection

@section('styles')
<style>
.table td {
    vertical-align: middle;
}
.badge {
    font-size: 0.8rem;
    padding: 0.4em 0.8em;
}
.btn-group .btn {
    padding: 0.25rem 0.5rem;
}
</style>
@endsection
