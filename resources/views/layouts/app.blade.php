<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — Caffeine</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
<div class="app-wrapper">

    {{-- ── SIDEBAR ──────────────────────────────────────────────────────── --}}
    <aside class="sidebar">

        {{-- Brand --}}
        <div class="sidebar-brand">
            <div class="brand-icon">☕</div>
            <div>
                <div class="brand-text">Caffeine</div>
            </div>
        </div>

        {{-- Navigasi --}}
        <nav class="sidebar-nav">
            <div class="nav-section-label">Main Menu</div>

            <a href="{{ route('dashboard') }}"
               class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('orders.create') }}"
               class="nav-item {{ request()->routeIs('orders.create') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Kasir / POS
            </a>

            <a href="{{ route('orders.index') }}"
               class="nav-item {{ request()->routeIs('orders.index', 'orders.show') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Riwayat
            </a>

            @if(auth()->check() && auth()->user()->isAdmin())
            <div class="nav-section-label">Management</div>

            <a href="{{ route('products.index') }}"
               class="nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Menu / Produk
            </a>

            <a href="{{ route('reports.index') }}"
               class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Laporan
            </a>
            @endif
        </nav>

        {{-- User Info & Logout --}}
        <div class="sidebar-user">
            <div class="avatar">
                {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : '?' }}
            </div>
            <div style="flex:1; min-width:0;">
                <div class="user-name truncate">
                    {{ auth()->check() ? auth()->user()->name : 'Guest' }}
                </div>
                <div class="user-role">
                    {{ auth()->check() ? auth()->user()->role : '' }}
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin:0; flex-shrink:0;">
                @csrf
                <button type="submit"
                        title="Logout"
                        class="logout-btn"
                        style="background:none; border:none; cursor:pointer; color:rgba(255,255,255,.35);
                               padding:.4rem; display:flex; align-items:center; border-radius:8px;
                               transition:var(--transition);"
                        onmouseover="this.style.color='#fff'; this.style.background='rgba(255,255,255,.08)'"
                        onmouseout="this.style.color='rgba(255,255,255,.35)'; this.style.background='none'">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>

    </aside>

    {{-- ── MAIN CONTENT ──────────────────────────────────────────────────── --}}
    <div class="main-content">

        {{-- Mobile Overlay (klik untuk tutup sidebar) --}}
        <div id="sidebar-overlay"
             onclick="closeSidebar()"
             style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5);
                    backdrop-filter:blur(2px); z-index:150;"></div>

        {{-- Topbar --}}
        <header class="topbar">
            <div class="d-flex align-center gap-3">
                {{-- Hamburger — hanya muncul di mobile --}}
                <button id="hamburger-btn"
                        onclick="toggleSidebar()"
                        class="hamburger-btn"
                        aria-label="Buka menu">
                    <span></span><span></span><span></span>
                </button>

                <div class="topbar-left">
                    <div class="topbar-title">{{ $title ?? 'Dashboard' }}</div>
                    @isset($subtitle)
                        <div class="topbar-subtitle">{{ $subtitle }}</div>
                    @endisset
                </div>
            </div>

            <div class="d-flex align-center gap-3">
                {{-- Slot untuk tombol aksi per halaman (opsional) --}}
                @isset($actions)
                    {{ $actions }}
                @endisset

                <a href="{{ route('profile.edit') }}" class="btn btn-secondary btn-sm">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="hide-mobile">Profil</span>
                </a>
            </div>
        </header>

        {{-- Page Content (flash + slot digabung dalam satu page-content) --}}
        <main class="page-content">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0; margin-top:1px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0; margin-top:1px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0; margin-top:1px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    {{ session('warning') }}
                </div>
            @endif

            {{-- Konten halaman --}}
            {{ $slot }}

        </main>
    </div>

</div>

@stack('scripts')

<script>
/* ─── SIDEBAR MOBILE TOGGLE ─────────────────────────────────────────── */
function toggleSidebar() {
    const sidebar  = document.querySelector('.sidebar');
    const overlay  = document.getElementById('sidebar-overlay');
    const btn      = document.getElementById('hamburger-btn');
    const isOpen   = sidebar.classList.contains('open');

    if (isOpen) {
        closeSidebar();
    } else {
        sidebar.classList.add('open');
        overlay.style.display = 'block';
        btn.classList.add('active');
        document.body.style.overflow = 'hidden'; // cegah scroll di belakang
    }
}

function closeSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const btn     = document.getElementById('hamburger-btn');
    sidebar.classList.remove('open');
    overlay.style.display = 'none';
    btn.classList.remove('active');
    document.body.style.overflow = '';
}

// Tutup sidebar otomatis saat nav item diklik (mobile UX)
document.querySelectorAll('.nav-item').forEach(item => {
    item.addEventListener('click', () => {
        if (window.innerWidth <= 768) closeSidebar();
    });
});

// Tutup sidebar saat resize ke desktop
window.addEventListener('resize', () => {
    if (window.innerWidth > 768) closeSidebar();
});
</script>
</body>
</html>