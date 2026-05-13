<p style="font-size:.875rem; color:var(--coffee-500); line-height:1.6; margin-bottom:1.25rem;">
    Setelah akun dihapus, semua data akan terhapus secara permanen dan tidak dapat dikembalikan.
    Pastikan kamu sudah mengunduh data penting sebelum melanjutkan.
</p>

{{-- Trigger button --}}
<button type="button" class="btn btn-danger"
        onclick="document.getElementById('delete-account-modal').style.display='flex'">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
    </svg>
    Hapus Akun
</button>

{{-- Confirmation Modal --}}
<div id="delete-account-modal"
     style="display:none; position:fixed; inset:0; z-index:999;
            background:rgba(0,0,0,.5); backdrop-filter:blur(4px);
            align-items:center; justify-content:center; padding:1rem;">
    <div style="background:#fff; border-radius:1rem; padding:2rem; max-width:440px; width:100%;
                box-shadow:0 24px 60px rgba(0,0,0,.18); animation:slideUp .25s ease both;">
        <div style="display:flex; align-items:center; gap:.75rem; margin-bottom:1rem;">
            <div style="width:44px; height:44px; border-radius:12px; background:rgba(244,63,94,.1);
                        display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="22" height="22" fill="none" stroke="#f43f5e" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h3 style="font-size:1rem; font-weight:800; color:var(--coffee-950);">Hapus Akun?</h3>
                <p style="font-size:.8rem; color:var(--coffee-400); margin-top:2px;">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
        </div>

        <form method="post" action="{{ route('profile.destroy') }}">
            @csrf
            @method('delete')

            <div class="form-group">
                <label class="form-label" for="del-password">
                    Masukkan password untuk konfirmasi
                </label>
                <input id="del-password" name="password" type="password"
                       class="form-control {{ $errors->userDeletion->has('password') ? 'is-invalid' : '' }}"
                       placeholder="Password Anda" autofocus>
                @if($errors->userDeletion->has('password'))
                    <div class="form-error">{{ $errors->userDeletion->first('password') }}</div>
                @endif
            </div>

            <div class="d-flex gap-3" style="margin-top:1.5rem;">
                <button type="submit" class="btn btn-danger" style="flex:1;">
                    Ya, Hapus Akun
                </button>
                <button type="button" class="btn btn-secondary" style="flex:1;"
                        onclick="document.getElementById('delete-account-modal').style.display='none'">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Auto-open modal jika ada error validasi --}}
@if($errors->userDeletion->isNotEmpty())
<script>document.getElementById('delete-account-modal').style.display = 'flex';</script>
@endif

{{-- Close on backdrop click --}}
<script>
document.getElementById('delete-account-modal').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
</script>