@extends('admin.layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="row">
    <!-- Navigation Tabs -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-pills" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#general" type="button">
                            <i class="fas fa-cog me-2"></i>Umum
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#notification" type="button">
                            <i class="fas fa-bell me-2"></i>Notifikasi
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#security" type="button">
                            <i class="fas fa-shield-alt me-2"></i>Keamanan
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#backup" type="button">
                            <i class="fas fa-database me-2"></i>Backup
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Settings Content -->
    <div class="col-12">
        <div class="tab-content">
            <!-- General Settings -->
            <div class="tab-pane fade show active" id="general">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Pengaturan Umum</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.update-general') }}" method="POST" class="row g-3">
                            @csrf
                            @method('PUT')

                            <div class="col-md-6">
                                <label class="form-label">Nama Aplikasi</label>
                                <input type="text"
                                       class="form-control"
                                       name="app_name"
                                       value="{{ $settings->app_name ?? config('app.name') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Zona Waktu Default</label>
                                <select class="form-select" name="timezone">
                                    @foreach($timezones as $tz)
                                        <option value="{{ $tz }}"
                                                {{ ($settings->timezone ?? config('app.timezone')) == $tz ? 'selected' : '' }}>
                                            {{ $tz }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Format Tanggal</label>
                                <select class="form-select" name="date_format">
                                    <option value="d/m/Y" {{ ($settings->date_format ?? 'd/m/Y') == 'd/m/Y' ? 'selected' : '' }}>
                                        DD/MM/YYYY (31/12/2023)
                                    </option>
                                    <option value="Y-m-d" {{ ($settings->date_format ?? 'd/m/Y') == 'Y-m-d' ? 'selected' : '' }}>
                                        YYYY-MM-DD (2023-12-31)
                                    </option>
                                    <option value="d M Y" {{ ($settings->date_format ?? 'd/m/Y') == 'd M Y' ? 'selected' : '' }}>
                                        DD Mon YYYY (31 Dec 2023)
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Bahasa Default</label>
                                <select class="form-select" name="locale">
                                    <option value="id" {{ ($settings->locale ?? 'id') == 'id' ? 'selected' : '' }}>
                                        Bahasa Indonesia
                                    </option>
                                    <option value="en" {{ ($settings->locale ?? 'id') == 'en' ? 'selected' : '' }}>
                                        English
                                    </option>
                                </select>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan Pengaturan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Notification Settings -->
            <div class="tab-pane fade" id="notification">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Pengaturan Notifikasi</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.update-notification') }}" method="POST" class="row g-3">
                            @csrf
                            @method('PUT')

                            <div class="col-12">
                                <h6 class="mb-3">Email Notifications</h6>
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox"
                                           class="form-check-input"
                                           name="notify_new_user"
                                           id="notify_new_user"
                                           {{ ($settings->notify_new_user ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="notify_new_user">
                                        Notifikasi User Baru
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox"
                                           class="form-check-input"
                                           name="notify_checklist_complete"
                                           id="notify_checklist_complete"
                                           {{ ($settings->notify_checklist_complete ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="notify_checklist_complete">
                                        Notifikasi Checklist Selesai
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email Admin</label>
                                <input type="email"
                                       class="form-control"
                                       name="admin_email"
                                       value="{{ $settings->admin_email ?? '' }}"
                                       placeholder="admin@example.com">
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan Pengaturan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="tab-pane fade" id="security">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Pengaturan Keamanan</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.update-security') }}" method="POST" class="row g-3">
                            @csrf
                            @method('PUT')

                            <div class="col-md-6">
                                <label class="form-label">Masa Berlaku Session (menit)</label>
                                <input type="number"
                                       class="form-control"
                                       name="session_lifetime"
                                       value="{{ $settings->session_lifetime ?? 120 }}"
                                       min="1">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Max Login Attempts</label>
                                <input type="number"
                                       class="form-control"
                                       name="max_login_attempts"
                                       value="{{ $settings->max_login_attempts ?? 5 }}"
                                       min="1">
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox"
                                           class="form-check-input"
                                           name="force_ssl"
                                           id="force_ssl"
                                           {{ ($settings->force_ssl ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="force_ssl">
                                        Paksa HTTPS
                                    </label>
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan Pengaturan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Backup Settings -->
            <div class="tab-pane fade" id="backup">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Pengaturan Backup</h6>
                        <button type="button" class="btn btn-primary btn-sm" onclick="startBackup()">
                            <i class="fas fa-download me-2"></i>Backup Sekarang
                        </button>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.update-backup') }}" method="POST" class="row g-3">
                            @csrf
                            @method('PUT')

                            <div class="col-md-6">
                                <label class="form-label">Backup Otomatis</label>
                                <select class="form-select" name="auto_backup">
                                    <option value="daily" {{ ($settings->auto_backup ?? 'weekly') == 'daily' ? 'selected' : '' }}>
                                        Harian
                                    </option>
                                    <option value="weekly" {{ ($settings->auto_backup ?? 'weekly') == 'weekly' ? 'selected' : '' }}>
                                        Mingguan
                                    </option>
                                    <option value="monthly" {{ ($settings->auto_backup ?? 'weekly') == 'monthly' ? 'selected' : '' }}>
                                        Bulanan
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Simpan Backup Selama (hari)</label>
                                <input type="number"
                                       class="form-control"
                                       name="backup_retention"
                                       value="{{ $settings->backup_retention ?? 30 }}"
                                       min="1">
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan Pengaturan
                                </button>
                            </div>
                        </form>

                        <!-- Recent Backups -->
                        <div class="mt-4">
                            <h6>Backup Terakhir</h6>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Ukuran</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($backups as $backup)
                                        <tr>
                                            <td>{{ $backup->created_at->format('d M Y H:i') }}</td>
                                            <td>{{ $backup->size }}</td>
                                            <td>
                                                <span class="badge bg-success">Success</span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('settings.download-backup', $backup->id) }}"
                                                       class="btn btn-sm btn-info">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <button type="button"
                                                            class="btn btn-sm btn-danger"
                                                            onclick="deleteBackup({{ $backup->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada data backup</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function startBackup() {
    if (confirm('Apakah Anda yakin ingin memulai backup sekarang?')) {
        // Implement backup logic
    }
}

function deleteBackup(id) {
    if (confirm('Apakah Anda yakin ingin menghapus backup ini?')) {
        // Implement delete backup logic
    }
}

// Initialize Bootstrap tabs
var triggerTabList = [].slice.call(document.querySelectorAll('#settingsTabs button'))
triggerTabList.forEach(function (triggerEl) {
    var tabTrigger = new bootstrap.Tab(triggerEl)
    triggerEl.addEventListener('click', function (event) {
        event.preventDefault()
        tabTrigger.show()
    })
})
</script>
@endsection

@section('styles')
<style>
.nav-pills .nav-link {
    color: var(--bs-gray-700);
    padding: 0.5rem 1rem;
}

.nav-pills .nav-link.active {
    background-color: var(--primary-color);
}

.tab-content {
    margin-top: 1rem;
}

.form-switch .form-check-input {
    width: 3em;
}

.table td {
    vertical-align: middle;
}
</style>
@endsection
