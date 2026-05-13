<form method="post" action="{{ route('password.update') }}">
    @csrf
    @method('put')

    <div class="form-group">
        <label class="form-label" for="update_password_current_password">Password Saat Ini</label>
        <input id="update_password_current_password" name="current_password" type="password"
               class="form-control {{ $errors->updatePassword->has('current_password') ? 'is-invalid' : '' }}"
               placeholder="Masukkan password saat ini" autocomplete="current-password">
        @if($errors->updatePassword->has('current_password'))
            <div class="form-error">{{ $errors->updatePassword->first('current_password') }}</div>
        @endif
    </div>

    <div class="form-group">
        <label class="form-label" for="update_password_password">Password Baru</label>
        <input id="update_password_password" name="password" type="password"
               class="form-control {{ $errors->updatePassword->has('password') ? 'is-invalid' : '' }}"
               placeholder="Min. 8 karakter" autocomplete="new-password">
        @if($errors->updatePassword->has('password'))
            <div class="form-error">{{ $errors->updatePassword->first('password') }}</div>
        @endif
    </div>

    <div class="form-group">
        <label class="form-label" for="update_password_password_confirmation">Konfirmasi Password Baru</label>
        <input id="update_password_password_confirmation" name="password_confirmation" type="password"
               class="form-control" placeholder="Ulangi password baru" autocomplete="new-password">
    </div>

    <div class="d-flex align-center gap-3" style="margin-top:1.5rem;">
        <button type="submit" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            Update Password
        </button>

        @if (session('status') === 'password-updated')
            <span class="badge badge-success">✓ Password diperbarui</span>
        @endif
    </div>
</form>