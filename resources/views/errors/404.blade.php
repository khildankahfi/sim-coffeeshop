<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Halaman Tidak Ditemukan | SIM Coffeeshop</title>
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
                radial-gradient(ellipse 80% 50% at 50% 0%, rgba(245,158,11,.08) 0%, transparent 60%),
                radial-gradient(ellipse 60% 60% at 20% 100%, rgba(139,79,30,.05) 0%, transparent 60%);
            -webkit-font-smoothing: antialiased;
        }
        @keyframes up {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .wrap {
            text-align: center;
            max-width: 480px;
            width: 100%;
            animation: up .4s cubic-bezier(.4,0,.2,1) both;
        }
        .emoji  { font-size: 4rem; line-height: 1; margin-bottom: .75rem; display: block; filter: drop-shadow(0 4px 16px rgba(245,158,11,.25)); }
        .code   {
            font-size: 6rem; font-weight: 900; line-height: 1; letter-spacing: -.06em;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 60%, #b06c30 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text; margin-bottom: .2rem;
        }
        .title  { font-size: 1.4rem; font-weight: 800; color: #1a0d00; margin: .3rem 0 .65rem; }
        .desc   { font-size: .9rem; color: #8b4f1e; line-height: 1.7; font-weight: 500; margin-bottom: 1.5rem; }

        /* URL chip */
        .url-chip {
            display: inline-flex; align-items: center; gap: .5rem;
            background: #fff; border: 1.5px solid #e3bb8a;
            padding: .45rem 1rem; border-radius: 10px; margin-bottom: 1.75rem;
            max-width: 100%; overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,.05);
        }
        .url-chip span {
            font-size: .78rem; font-weight: 600; color: #8b4f1e;
            font-family: 'DM Mono', monospace;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        /* Buttons */
        .btns { display: flex; gap: .75rem; justify-content: center; flex-wrap: wrap; margin-bottom: 1.75rem; }
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
            box-shadow: 0 6px 18px rgba(245,158,11,.3);
        }
        .btn-p:hover { filter: brightness(1.06); box-shadow: 0 10px 24px rgba(245,158,11,.38); }
        .btn-s {
            background: #fff; color: #4a2008; border-color: #e3bb8a;
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
        }
        .btn-s:hover { background: #fdf6ec; border-color: #cc9055; }

        /* Suggestion box */
        .suggestion-box {
            background: #fff; border-radius: 12px;
            border: 1px solid #f3dfc0;
            padding: 1.1rem 1.25rem; margin-bottom: 2.25rem;
            box-shadow: 0 2px 10px rgba(0,0,0,.05);
            text-align: left;
        }
        .suggestion-label {
            font-size: .68rem; font-weight: 800; color: #cc9055;
            text-transform: uppercase; letter-spacing: .1em; margin-bottom: .65rem;
            text-align: center;
        }
        .suggestion-link {
            display: flex; align-items: center; gap: .6rem;
            padding: .5rem .6rem; border-radius: 8px;
            text-decoration: none; font-size: .83rem; font-weight: 600;
            color: #4a2008; transition: background .15s, color .15s;
        }
        .suggestion-link:hover { background: #fdf6ec; color: #d97706; }

        .brand { font-size: .72rem; color: #cc9055; font-weight: 600; }
    </style>
</head>
<body>
<div class="wrap">

    <span class="emoji">☕</span>
    <div class="code">404</div>
    <h1 class="title">Halaman Tidak Ditemukan</h1>
    <p class="desc">
        Sepertinya menu yang kamu cari sudah habis atau tidak pernah ada.<br>
        Mungkin URL-nya salah ketik?
    </p>

    {{-- URL yang dicoba --}}
    <div class="url-chip">
        <svg width="13" height="13" fill="none" stroke="#cc9055" viewBox="0 0 24 24" style="flex-shrink:0;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
        </svg>
        <span>{{ request()->url() }}</span>
    </div>

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

    {{-- Saran halaman (hanya untuk user yang sudah login) --}}
    @auth
    <div class="suggestion-box">
        <div class="suggestion-label">Mungkin yang kamu cari:</div>
        <a href="{{ route('dashboard') }}"     class="suggestion-link">🏠 Dashboard</a>
        <a href="{{ route('orders.create') }}" class="suggestion-link">🛒 Kasir / POS</a>
        <a href="{{ route('orders.index') }}"  class="suggestion-link">📋 Riwayat Transaksi</a>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('products.index') }}" class="suggestion-link">☕ Menu / Produk</a>
            <a href="{{ route('reports.index') }}"  class="suggestion-link">📊 Laporan</a>
        @endif
    </div>
    @endauth

    <div class="brand">☕ SIM Coffeeshop</div>
</div>
</body>
</html>