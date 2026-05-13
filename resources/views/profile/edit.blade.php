<x-app-layout>
    <x-slot name="title">Profil Saya</x-slot>
    <x-slot name="subtitle">Kelola informasi akun Anda</x-slot>

    <div style="max-width:680px; display:flex; flex-direction:column; gap:1.5rem;">

        {{-- Update Info Profil --}}
        <div class="card">
            <div class="card-header">
                <h3>Informasi Profil</h3>
            </div>
            <div class="card-body">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        {{-- Update Password --}}
        <div class="card">
            <div class="card-header">
                <h3>Ubah Password</h3>
            </div>
            <div class="card-body">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        {{-- Hapus Akun --}}
        <div class="card" style="border-color:rgba(244,63,94,.15);">
            <div class="card-header" style="background:rgba(244,63,94,.02);">
                <h3 style="color:var(--danger);">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;">
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
</x-app-layout>