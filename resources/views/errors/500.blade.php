<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 — Server Error | Caffeine</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
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
            background-image: radial-gradient(ellipse 80% 50% at 50% 0%, rgba(99,102,241,.07) 0%, transparent 60%);
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
        .code   { font-size: 6rem; font-weight: 900; line-height: 1; letter-spacing: -.06em; color: #e3bb8a; margin-bottom: .2rem; }
        .title  { font-size: 1.4rem; font-weight: 800; color: #1a0d00; margin: .3rem 0 .65rem; }
        .desc   { font-size: .9rem; color: #8b4f1e; line-height: 1.7; font-weight: 500; margin-bottom: 1.75rem; }

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
            box-shadow: 0 6px 18px rgba(245,158,11,.3);
        }
        .btn-p:hover { filter: brightness(1.06); }
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

    <span class="emoji">⚙️</span>
    <div class="code">500</div>
    <h1 class="title">Terjadi Kesalahan Server</h1>
    <p class="desc">
        Mesin kopi sedang rewel.<br>
        Coba lagi dalam beberapa saat — tim teknis sudah diberitahu.
    </p>

    <div class="btns">
        <a href="javascript:location.reload()" class="btn btn-p">
            🔄 Coba Lagi
        </a>
        @auth
            <a href="{{ route('dashboard') }}" class="btn btn-s">🏠 Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="btn btn-s">Login</a>
        @endauth
    </div>

    <div class="brand">☕ Caffeine</div>
</div>
</body>
</html>