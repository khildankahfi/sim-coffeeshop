<x-app-layout>
    <x-slot name="title">Tambah Karyawan</x-slot>
    <x-slot name="subtitle">Buat akun karyawan baru</x-slot>

    <div class="card" style="max-width:560px">
        <div class="card-header">
            <h3>👤 Form Karyawan Baru</h3>
            <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('users.store') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="name">Nama Lengkap <span class="required">*</span></label>
                    <input id="name" type="text" name="name"
                           class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                           value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" autofocus required>
                    @error('name') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email <span class="required">*</span></label>
                    <input id="email" type="email" name="email"
                           class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                           value="{{ old('email') }}" placeholder="budi@coffeeshop.com" required>
                    @error('email') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="role">Role <span class="required">*</span></label>
                    <select id="role" name="role" class="form-control {{ $errors->has('role') ? 'is-invalid' : '' }}" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>👑 Admin — Akses penuh</option>
                        <option value="kasir" {{ old('role') === 'kasir' ? 'selected' : '' }}>💳 Kasir — Hanya kasir & transaksi</option>
                    </select>
                    @error('role') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                    <div class="form-group">
                        <label class="form-label" for="password">Password <span class="required">*</span></label>
                        <input id="password" type="password" name="password"
                               class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                               placeholder="Min. 8 karakter" required>
                        @error('password') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Konfirmasi Password <span class="required">*</span></label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                               class="form-control" placeholder="Ulangi password" required>
                    </div>
                </div>

                {{-- Info Role --}}
                <div class="alert alert-warning">
                    ⚠️ <strong>Penting:</strong> Role <strong>Admin</strong> dapat mengakses semua fitur termasuk laporan dan manajemen user. Role <strong>Kasir</strong> hanya bisa mengakses kasir dan riwayat transaksinya sendiri.
                </div>

                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary">💾 Simpan Karyawan</button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
