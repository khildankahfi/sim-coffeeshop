<x-guest-layout>
    <x-slot name="title">Login</x-slot>

    {{-- Header --}}
    <div style="margin-bottom:2rem; text-align:center;">
        <h2 style="font-size:1.5rem; font-weight:900; color:var(--coffee-950); letter-spacing:-.04em;">
            Selamat Datang
        </h2>
        <p style="font-size:.875rem; color:var(--coffee-500); margin-top:.35rem; font-weight:500;">
            Masuk ke dashboard SIM Coffeeshop
        </p>
    </div>

    {{-- Session Status --}}
    @if (session('status'))
        <div class="alert alert-success" style="margin-bottom:1.25rem;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <div style="position:relative;">
                <div style="position:absolute; left:.9rem; top:50%; transform:translateY(-50%); color:var(--coffee-400); pointer-events:none;">
                    <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                    </svg>
                </div>
                <input id="email" type="email" name="email"
                       class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                       style="padding-left:2.5rem;"
                       value="{{ old('email') }}"
                       placeholder="admin@coffeeshop.com"
                       required autofocus autocomplete="username">
            </div>
            @error('email') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        {{-- Password --}}
        <div class="form-group">
            <div class="d-flex justify-between align-center" style="margin-bottom:.5rem;">
                <label class="form-label" for="password" style="margin:0;">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       style="font-size:.78rem; color:var(--amber-500); font-weight:600; text-decoration:none;">
                        Lupa password?
                    </a>
                @endif
            </div>
            <div style="position:relative;">
                <div style="position:absolute; left:.9rem; top:50%; transform:translateY(-50%); color:var(--coffee-400); pointer-events:none;">
                    <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <input id="password" type="password" name="password"
                       class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                       style="padding-left:2.5rem;"
                       placeholder="••••••••"
                       required autocomplete="current-password">
            </div>
            @error('password') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        {{-- Remember Me --}}
        <div class="d-flex align-center gap-2" style="margin-bottom:1.5rem;">
            <input id="remember_me" type="checkbox" name="remember"
                   style="width:16px; height:16px; border-radius:4px; accent-color:var(--amber-500); cursor:pointer;">
            <label for="remember_me"
                   style="font-size:.82rem; color:var(--coffee-600); font-weight:500; cursor:pointer;">
                Ingat saya di perangkat ini
            </label>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn btn-primary btn-block btn-lg">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            Masuk ke Dashboard
        </button>
    </form>

    {{-- Footer note --}}
    <p style="text-align:center; font-size:.78rem; color:var(--coffee-400); margin-top:1.5rem; font-weight:500;">
        Hanya akun terdaftar yang dapat masuk.
    </p>

</x-guest-layout>