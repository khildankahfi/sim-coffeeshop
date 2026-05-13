<x-app-layout>
    <x-slot name="title">Profil Saya</x-slot>
    <x-slot name="subtitle">Kelola informasi akun Anda</x-slot>

    {{-- ── WRAPPER TENGAH ───────────────────────────────────────────────── --}}
    <div style="max-width:660px; margin:0 auto;">

        {{-- Profile Header --}}
        <div style="text-align:center; margin-bottom:2rem;">
            <div style="width:80px; height:80px; border-radius:22px;
                        margin:0 auto .85rem;
                        background:linear-gradient(135deg,#f59e0b,#d97706);
                        display:flex; align-items:center; justify-content:center;
                        font-size:2rem; font-weight:900; color:#fff;
                        box-shadow:0 8px 24px rgba(245,158,11,.35),
                                   inset 0 1px 0 rgba(255,255,255,.25);">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div style="font-size:1.3rem; font-weight:900; color:var(--coffee-950);
                        letter-spacing:-.03em; line-height:1.2;">
                {{ auth()->user()->name }}
            </div>
            <div style="margin-top:.5rem; display:flex; align-items:center;
                        justify-content:center; gap:.5rem; flex-wrap:wrap;">
                <span class="badge {{ auth()->user()->isAdmin() ? 'badge-admin' : 'badge-kasir' }}">
                    {{ ucfirst(auth()->user()->role) }}
                </span>
                <span style="font-size:.82rem; color:var(--coffee-400); font-weight:500;">
                    {{ auth()->user()->email }}
                </span>
            </div>
        </div>

        {{-- Cards --}}
        <div style="display:flex; flex-direction:column; gap:1.5rem;">

            <div class="card">
                <div class="card-header"><h3>Informasi Profil</h3></div>
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3>Ubah Password</h3></div>
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="card" style="border-color:rgba(244,63,94,.15);">
                <div class="card-header" style="background:rgba(244,63,94,.02);">
                    <h3 style="color:var(--danger);">
                        <svg width="16" height="16" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24" style="flex-shrink:0;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Hapus Akun
                    </h3>
                </div>
                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>

</x-app-layout>