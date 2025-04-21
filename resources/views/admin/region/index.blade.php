@extends('admin.layouts.app')

@section('title', 'Manage Regions')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Manajemen Region</h5>
        <button type="button" class="btn btn-primary btn-sm" onclick="openCreateModal()">
            <i class="fas fa-plus me-2"></i>Tambah Region
        </button>
    </div>
    <div class="card-body">
        <!-- Search -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Cari region..." id="searchRegion">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Region Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Region</th>
                        <th>Kode</th>
                        <th>Jumlah Overseer</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($regions as $region)
                    <tr>
                        <td>{{ $region->id }}</td>
                        <td>{{ $region->name }}</td>
                        <td><code>{{ $region->code }}</code></td>
                        <td>{{ $region->overseers_count }}</td>
                        <td>
                            <span class="badge bg-{{ $region->active ? 'success' : 'danger' }}">
                                {{ $region->active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <button type="button"
                                        class="btn btn-sm btn-info"
                                        onclick="openEditModal({{ $region->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button"
                                        class="btn btn-sm btn-danger"
                                        onclick="confirmDelete({{ $region->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data region</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $regions->links() }}
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div class="modal fade" id="regionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Region</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="regionForm" method="POST">
                @csrf
                <div id="methodField"></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Region</label>
                        <input type="text"
                               class="form-control"
                               id="name"
                               name="name"
                               required>
                    </div>
                    <div class="mb-3">
                        <label for="code" class="form-label">Kode Region</label>
                        <input type="text"
                               class="form-control"
                               id="code"
                               name="code"
                               required>
                        <small class="text-muted">Contoh: JKT, BDG, SBY</small>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control"
                                  id="description"
                                  name="description"
                                  rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox"
                                   class="form-check-input"
                                   id="active"
                                   name="active"
                                   value="1"
                                   checked>
                            <label class="form-check-label" for="active">Region Aktif</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
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
                <p>Apakah Anda yakin ingin menghapus region ini?</p>
                <p class="text-danger"><small>Tindakan ini akan mempengaruhi data Overseer yang terkait</small></p>
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
const regionModal = new bootstrap.Modal(document.getElementById('regionModal'));

function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Region';
    document.getElementById('regionForm').reset();
    document.getElementById('regionForm').action = "{{ route('regions.store') }}";
    document.getElementById('methodField').innerHTML = '';
    regionModal.show();
}

function openEditModal(id) {
    document.getElementById('modalTitle').textContent = 'Edit Region';
    document.getElementById('methodField').innerHTML = '@method("PUT")';
    document.getElementById('regionForm').action = `/admin/regions/${id}`;

    // Fetch region data and populate form
    fetch(`/admin/regions/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('name').value = data.name;
            document.getElementById('code').value = data.code;
            document.getElementById('description').value = data.description;
            document.getElementById('active').checked = data.active;
            regionModal.show();
        });
}

function confirmDelete(id) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    form.action = `/admin/regions/${id}`;
    modal.show();
}

// Search Implementation
document.getElementById('searchRegion').addEventListener('keyup', function(e) {
    // Implement search functionality
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
