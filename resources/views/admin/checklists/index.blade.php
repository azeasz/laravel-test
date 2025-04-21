@extends('admin.layouts.app')

@section('title', 'Manage Checklists')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Checklist</h5>
        <a href="{{ route('admin.checklists.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-2"></i>Tambah Checklist
        </a>
    </div>
    <div class="card-body">
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#fobi">FOBI</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#kupunesia">Kupunesia</a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- FOBI Tab -->
            <div class="tab-pane fade show active" id="fobi">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Lokasi</th>
                                <th>Observer</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($checklistsFobi as $checklist)
                            <tr>
                                <td>{{ $checklist->id }}</td>
                                <td>{{ $checklist->user->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="d-block">Lat: {{ $checklist->latitude }}</span>
                                    <span class="d-block">Long: {{ $checklist->longitude }}</span>
                                </td>
                                <td>{{ $checklist->observer }}</td>
                                <td>
                                    @if($checklist->completed)
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                                <td>{{ $checklist->tgl_pengamatan }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.checklists.edit', $checklist->id) }}"
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-sm btn-danger"
                                                onclick="confirmDelete({{ $checklist->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data checklist</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $checklistsFobi->links() }}
                </div>
            </div>

            <!-- Kupunesia Tab -->
            <div class="tab-pane fade" id="kupunesia">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Lokasi</th>
                                <th>Observer</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($checklistsKupunesia as $checklist)
                            <tr>
                                <td>{{ $checklist->id }}</td>
                                <td>{{ $checklist->user->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="d-block">Lat: {{ $checklist->latitude }}</span>
                                    <span class="d-block">Long: {{ $checklist->longitude }}</span>
                                </td>
                                <td>{{ $checklist->observer }}</td>
                                <td>
                                    @if($checklist->completed)
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                                <td>{{ $checklist->tgl_pengamatan }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.checklists.edit', $checklist->id) }}"
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-sm btn-danger"
                                                onclick="confirmDelete({{ $checklist->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data checklist</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $checklistsKupunesia->links() }}
                </div>
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
                <p>Apakah Anda yakin ingin menghapus checklist ini?</p>
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
function confirmDelete(id) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    form.action = `/admin/checklists/${id}`;
    modal.show();
}
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
