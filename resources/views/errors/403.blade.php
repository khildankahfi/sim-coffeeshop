<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 — Akses Ditolak | SIM Coffeeshop</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&family=DM+Mono:wght@500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Poppins', system-ui, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: #f4f1ec;
            background-image:
                radial-gradient(ellipse 80% 50% at 50% 0%, rgba(244,63,94,.07) 0%, transparent 60%),
                radial-gradient(ellipse 60% 60% at 80% 100%, rgba(139,79,30,.05) 0%, transparent 60%);
            -webkit-font-smoothing: antialiased;
        }
        @keyframes up {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .wrap {
            text-align: center;
            max-width: 460px;
            width: 100%;
            animation: up .4s cubic-bezier(.4,0,.2,1) both;
        }
        .emoji  { font-size: 4rem; line-height: 1; margin-bottom: .75rem; display: block; }
        .code   {
            font-size: 6rem; font-weight: 900; line-height: 1; letter-spacing: -.06em;
            color: #e3bb8a; margin-bottom: .2rem;
        }
        .title  { font-size: 1.4rem; font-weight: 800; color: #1a0d00; margin: .3rem 0 .65rem; }
        .desc   { font-size: .9rem; color: #8b4f1e; line-height: 1.7; font-weight: 500; margin-bottom: 1.5rem; }
        .chip {
            display: inline-flex; align-items: center; gap: .5rem;
            background: rgba(244,63,94,.08); border: 1px solid rgba(244,63,94,.18);
            padding: .45rem 1rem; border-radius: 10px; margin-bottom: 1.75rem;
            font-size: .78rem; font-weight: 700; color: #f43f5e;
        }
        .btns { display: flex; gap: .75rem; justify-content: center; flex-wrap: wrap; margin-bottom: 2.25rem; }
        .btn {
            display: inline-flex; align-items: center; gap: .45rem;
            padding: .7rem 1.5rem; border-radius: 10px; font-size: .875rem;
            font-weight: 700; text-decoration: none; font-family: 'Poppins', sans-serif;
            cursor: pointer; border: 1.5px solid transparent;
            transition: transform .2s, box-shadow .2s, filter .2s; line-height: 1;
        }
        .btn:hover { transform: translateY(-2px); }
        .btn-p {
            background: linear-gradient(135deg,#f59e0b,#d97706); color: #fff;
            box-shadow: 0 6px 18px rgba(245,158,11,.3); border-color: transparent;
        }
        .btn-p:hover { filter: brightness(1.06); box-shadow: 0 10px 24px rgba(245,158,11,.38); }
        .btn-s {
            background: #fff; color: #4a2008; border-color: #e3bb8a;
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
        }
        .btn-s:hover { background: #fdf6ec; border-color: #cc9055; }
        .brand { font-size: .72rem; color: #cc9055; font-weight: 600; }
    </style>
</head>
<body>
<div class="wrap">

    <span class="emoji">🚫</span>
    <div class="code">403</div>
    <h1 class="title">Akses Ditolak</h1>
    <p class="desc">
        {{ $exception->getMessage() ?: 'Kamu tidak memiliki izin untuk mengakses halaman ini.' }}
    </p>

    @auth
        <div class="chip">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            Login sebagai <strong>{{ auth()->user()->name }}</strong> &bull; {{ ucfirst(auth()->user()->role) }}
        </div>
    @endauth

    <div class="btns">
        @auth
            <a href="{{ route('dashboard') }}" class="btn btn-p">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Ke Dashboard
            </a>
        @else
            <a href="{{ route('login') }}" class="btn btn-p">Login</a>
        @endauth
        <a href="javascript:history.back()" class="btn btn-s">← Kembali</a>
    </div>

    <div class="brand">☕ SIM Coffeeshop</div>
</div>
</body>
</html>