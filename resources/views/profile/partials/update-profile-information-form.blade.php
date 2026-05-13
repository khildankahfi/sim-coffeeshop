<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}">
    @csrf
    @method('patch')

    <div class="form-group">
        <label class="form-label" for="name">Nama Lengkap</label>
        <input id="name" name="name" type="text"
               class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
               value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
        @error('name') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="email">Email</label>
        <input id="email" name="email" type="email"
               class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
               value="{{ old('email', $user->email) }}" required autocomplete="username">
        @error('email') <div class="form-error">{{ $message }}</div> @enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="alert alert-warning" style="margin-top:.75rem;">
                Email Anda belum terverifikasi.
                <button form="send-verification"
                        style="background:none;border:none;cursor:pointer;color:var(--amber-600);font-weight:700;font-family:'Poppins',sans-serif;font-size:.85rem;padding:0;text-decoration:underline;">
                    Kirim ulang email verifikasi
                </button>
            </div>
            @if (session('status') === 'verification-link-sent')
                <div class="alert alert-success" style="margin-top:.5rem;">
                    Link verifikasi baru telah dikirim ke email Anda.
                </div>
            @endif
        @endif
    </div>

    <div class="d-flex align-center gap-3" style="margin-top:1.5rem;">
        <button type="submit" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Simpan Perubahan
        </button>

        @if (session('status') === 'profile-updated')
            <span class="badge badge-success">✓ Tersimpan</span>
        @endif
    </div>
</form>