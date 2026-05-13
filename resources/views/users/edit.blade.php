<x-app-layout>
    <x-slot name="title">Edit Karyawan</x-slot>
    <x-slot name="subtitle">Ubah data akun: {{ $user->name }}</x-slot>

    <div class="card" style="max-width:560px">
        <div class="card-header">
            <h3>✏️ Edit Karyawan</h3>
            <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('users.update', $user) }}">
                @csrf @method('PATCH')

                <div class="form-group">
                    <label class="form-label" for="name">Nama Lengkap <span class="required">*</span></label>
                    <input id="name" type="text" name="name"
                           class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                           value="{{ old('name', $user->name) }}" required>
                    @error('name') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email <span class="required">*</span></label>
                    <input id="email" type="email" name="email"
                           class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                           value="{{ old('email', $user->email) }}" required>
                    @error('email') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="role">Role <span class="required">*</span></label>
                    <select id="role" name="role" class="form-control"
                            {{ $user->id === auth()->id() ? 'disabled' : '' }} required>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>👑 Admin</option>
                        <option value="kasir" {{ old('role', $user->role) === 'kasir' ? 'selected' : '' }}>💳 Kasir</option>
                    </select>
                    @if($user->id === auth()->id())
                        <input type="hidden" name="role" value="{{ $user->role }}">
                        <div class="form-hint">⚠️ Anda tidak dapat mengubah role akun sendiri.</div>
                    @endif
                </div>

                <div style="border:1px dashed var(--coffee-200);border-radius:.6rem;padding:1rem;margin-bottom:1.25rem">
                    <div class="form-label" style="margin-bottom:.75rem">🔒 Ubah Password (opsional)</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                        <div class="form-group" style="margin:0">
                            <label class="form-label" for="password" style="font-weight:400">Password Baru</label>
                            <input id="password" type="password" name="password"
                                   class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                   placeholder="Kosongkan jika tidak diubah">
                            @error('password') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group" style="margin:0">
                            <label class="form-label" for="password_confirmation" style="font-weight:400">Konfirmasi</label>
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                   class="form-control" placeholder="Ulangi password baru">
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary">💾 Update Data</button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
