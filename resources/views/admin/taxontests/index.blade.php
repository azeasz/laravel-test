@extends('admin.layouts.app')

@section('title', 'Manage Taxa')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Data Taxa</h5>
        <div>
            <button class="btn btn-success btn-sm me-2" onclick="openImportModal()">
                <i class="fas fa-file-import me-2"></i>Import Excel
            </button>
            <a href="{{ route('taxontests.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-2"></i>Tambah Taxa
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Search and Filter -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Cari taxa..." id="searchInput">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-6">
                <select class="form-select" id="filterSelect">
                    <option value="">Semua Kategori</option>
                    <option value="family">Family</option>
                    <option value="genus">Genus</option>
                    <option value="species">Species</option>
                </select>
            </div>
        </div>

        <!-- Taxa Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Family</th>
                        <th>Genus</th>
                        <th>Species</th>
                        <th>Common Name</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($taxa as $taxon)
                    <tr>
                        <td>{{ $taxon->id }}</td>
                        <td>{{ $taxon->family }}</td>
                        <td>{{ $taxon->genus }}</td>
                        <td><em>{{ $taxon->species }}</em></td>
                        <td>{{ $taxon->common_name }}</td>
                        <td>
                            <span class="badge bg-{{ $taxon->status == 'active' ? 'success' : 'warning' }}">
                                {{ $taxon->status }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('taxontests.edit', $taxon->id) }}"
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-sm btn-danger"
                                        onclick="confirmDelete({{ $taxon->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data taxa</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $taxa->links() }}
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Data Taxa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('taxontests.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Pilih File Excel</label>
                        <input type="file" class="form-control" name="file" accept=".xlsx,.xls,.csv" required>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Format file yang didukung: .xlsx, .xls, .csv
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Import
                    </button>
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
                <p>Apakah Anda yakin ingin menghapus taxa ini?</p>
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
function openImportModal() {
    const modal = new bootstrap.Modal(document.getElementById('importModal'));
    modal.show();
}

function confirmDelete(id) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    form.action = `/admin/taxontests/${id}`;
    modal.show();
}

// Search and Filter Functionality
document.getElementById('searchInput').addEventListener('keyup', function(e) {
    // Implement search functionality
});

document.getElementById('filterSelect').addEventListener('change', function(e) {
    // Implement filter functionality
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
