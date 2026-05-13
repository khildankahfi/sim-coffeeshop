<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Login' }} — SIM Coffeeshop</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-body">

<div class="auth-wrapper">

    {{-- ── PANEL KIRI: Branding ────────────────────────────────────────── --}}
    <div class="auth-brand-panel">
        {{-- Decorative rings --}}
        <div class="auth-ring auth-ring-1"></div>
        <div class="auth-ring auth-ring-2"></div>
        <div class="auth-ring auth-ring-3"></div>

        <div class="auth-brand-content">
            <div class="auth-logo">☕</div>
            <h1 class="auth-brand-name">SIM Coffee</h1>
            <p class="auth-brand-tagline">Premium Brew</p>
            <p class="auth-brand-desc">
                Sistem Informasi Manajemen Coffeeshop — kelola transaksi, produk, dan laporan dalam satu platform yang efisien.
            </p>

            {{-- Feature list --}}
            <div class="auth-features">
                @foreach(['Point of Sale (Kasir)', 'Laporan Pendapatan Real-time', 'Manajemen Menu & Stok', 'Multi-role (Admin & Kasir)'] as $feature)
                    <div class="auth-feature-item">
                        <div class="auth-feature-dot"></div>
                        {{ $feature }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── PANEL KANAN: Form ───────────────────────────────────────────── --}}
    <div class="auth-form-panel">
        <div class="auth-form-card">
            {{ $slot }}
        </div>
    </div>

</div>

</body>
</html>