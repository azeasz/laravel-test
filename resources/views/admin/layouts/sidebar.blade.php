<aside class="sidebar">
    <div class="sidebar-header">
        <h4 class="mb-0">FOBI Admin</h4>
    </div>

    <div class="sidebar-menu">
        <a href="{{ route('admin.dashboard') }}" class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('admin.checklists.index') }}" class="menu-item {{ request()->routeIs('admin.checklists.*') ? 'active' : '' }}">
            <i class="fas fa-tasks"></i>
            <span>Checklist</span>
        </a>

        <a href="{{ route('taxontests.index') }}" class="menu-item {{ request()->routeIs('taxontests.*') ? 'active' : '' }}">
            <i class="fas fa-leaf"></i>
            <span>Taxa</span>
        </a>

        <a href="{{ route('fobiuser.index') }}" class="menu-item {{ request()->routeIs('fobiuser.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Fobi-User</span>
        </a>

        <a href="{{ route('overseer.index') }}" class="menu-item {{ request()->routeIs('overseer.*') ? 'active' : '' }}">
            <i class="fas fa-user-shield"></i>
            <span>Overseer</span>
        </a>

        <div class="menu-section">
            <span class="menu-section-text">Settings</span>
        </div>

        <a href="#" class="menu-item">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>

        <a href="#" class="menu-item">
            <i class="fas fa-newspaper"></i>
            <span>Berita</span>
        </a>

        <a href="#" class="menu-item">
            <i class="fas fa-info-circle"></i>
            <span>Tentang Kami</span>
        </a>

        <a href="#" class="menu-item">
            <i class="fas fa-envelope"></i>
            <span>Kontak</span>
        </a>
    </div>
</aside>
