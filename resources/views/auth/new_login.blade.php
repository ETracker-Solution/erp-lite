<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in | {{ getSettingValue('software_name') ?: config('app.name', 'Cake Town') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #1c1410;
            --paper: #f7f1e8;
            --paper-soft: rgba(247, 241, 232, 0.92);
            --accent: #2f6b4f;
            --accent-deep: #245540;
            --line: rgba(28, 20, 16, 0.14);
            --muted: rgba(28, 20, 16, 0.68);
            --danger: #9b2c2c;
            --radius: 14px;
            --font-display: "Fraunces", Georgia, serif;
            --font-body: "Manrope", sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            font-family: var(--font-body);
            color: var(--ink);
            background: #120e0b;
            overflow-x: hidden;
        }

        .stage {
            position: relative;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 2rem 1.25rem;
        }

        .backdrop {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(120deg, rgba(18, 14, 11, 0.78) 0%, rgba(18, 14, 11, 0.42) 48%, rgba(18, 14, 11, 0.7) 100%),
                url("https://images.unsplash.com/photo-1578985545062-69928b1d9587?auto=format&fit=crop&w=1920&q=80") center / cover no-repeat;
            transform: scale(1.04);
            animation: drift 18s ease-in-out infinite alternate;
        }

        .backdrop::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 18% 22%, rgba(47, 107, 79, 0.28), transparent 42%),
                radial-gradient(circle at 82% 78%, rgba(247, 241, 232, 0.12), transparent 36%);
            pointer-events: none;
        }

        .shell {
            position: relative;
            z-index: 1;
            width: min(100%, 440px);
            animation: rise 700ms cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        .brand {
            text-align: center;
            margin-bottom: 1.5rem;
            color: var(--paper);
        }

        .brand-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            animation: fade 900ms ease both 120ms;
        }

        .brand-mark img {
            max-width: 120px;
            max-height: 56px;
            object-fit: contain;
            filter: drop-shadow(0 8px 18px rgba(0, 0, 0, 0.28));
        }

        .brand h1 {
            font-family: var(--font-display);
            font-size: clamp(2.1rem, 5vw, 2.7rem);
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1.05;
            animation: fade 900ms ease both 180ms;
        }

        .brand p {
            margin-top: 0.65rem;
            font-size: 0.98rem;
            color: rgba(247, 241, 232, 0.82);
            animation: fade 900ms ease both 260ms;
        }

        .panel {
            background: var(--paper-soft);
            border: 1px solid rgba(247, 241, 232, 0.35);
            border-radius: var(--radius);
            padding: 1.5rem;
            backdrop-filter: blur(10px);
            animation: fade 900ms ease both 340ms;
        }

        .field {
            margin-bottom: 1rem;
        }

        .field label {
            display: block;
            margin-bottom: 0.4rem;
            font-size: 0.82rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .field input[type="email"],
        .field input[type="text"],
        .field input[type="password"] {
            width: 100%;
            height: 48px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #fffdf9;
            padding: 0 0.95rem;
            font: 500 1rem/1.2 var(--font-body);
            color: var(--ink);
            transition: border-color 180ms ease, background-color 180ms ease;
        }

        .field input:focus {
            outline: none;
            border-color: var(--accent);
            background: #fff;
        }

        .row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin: 0.35rem 0 1.25rem;
        }

        .remember {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.92rem;
            color: var(--muted);
            cursor: pointer;
        }

        .remember input {
            width: 16px;
            height: 16px;
            accent-color: var(--accent);
        }

        .submit {
            width: 100%;
            height: 50px;
            border: 0;
            border-radius: 10px;
            background: var(--accent);
            color: #f7f1e8;
            font: 700 1rem/1 var(--font-body);
            letter-spacing: 0.01em;
            cursor: pointer;
            transition: background-color 180ms ease, transform 180ms ease;
        }

        .submit:hover {
            background: var(--accent-deep);
        }

        .submit:active {
            transform: translateY(1px);
        }

        .errors {
            margin-bottom: 1rem;
            padding: 0.75rem 0.9rem;
            border-radius: 10px;
            background: rgba(155, 44, 44, 0.08);
            border: 1px solid rgba(155, 44, 44, 0.18);
            color: var(--danger);
            font-size: 0.9rem;
        }

        .errors p + p {
            margin-top: 0.25rem;
        }

        .foot {
            margin-top: 1rem;
            text-align: center;
            font-size: 0.82rem;
            color: rgba(247, 241, 232, 0.7);
        }

        @keyframes drift {
            from { transform: scale(1.04) translate3d(0, 0, 0); }
            to { transform: scale(1.08) translate3d(-1.2%, -0.8%, 0); }
        }

        @keyframes rise {
            from { opacity: 0; transform: translateY(18px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fade {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 520px) {
            .stage {
                padding: 1.25rem 1rem;
                align-items: end;
            }

            .panel {
                padding: 1.2rem;
            }

            .brand h1 {
                font-size: 2rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .backdrop,
            .shell,
            .brand-mark,
            .brand h1,
            .brand p,
            .panel {
                animation: none;
            }
        }

        .submit {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
        }

        .submit.is-loading {
            pointer-events: none;
            cursor: wait;
            opacity: 0.92;
        }

        .submit .btn-label,
        .submit .btn-wait {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .submit .btn-wait {
            display: none;
        }

        .submit.is-loading .btn-label {
            display: none;
        }

        .submit.is-loading .btn-wait {
            display: inline-flex;
        }

        .spinner {
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(247, 241, 232, 0.35);
            border-top-color: #f7f1e8;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        .page-loader {
            position: fixed;
            inset: 0;
            z-index: 50;
            display: none;
            place-items: center;
            background: rgba(18, 14, 11, 0.55);
            backdrop-filter: blur(4px);
        }

        .page-loader.is-on {
            display: grid;
            animation: fade 220ms ease both;
        }

        .page-loader__card {
            display: grid;
            justify-items: center;
            gap: 0.85rem;
            padding: 1.35rem 1.5rem;
            border-radius: 14px;
            background: rgba(247, 241, 232, 0.94);
            border: 1px solid rgba(247, 241, 232, 0.4);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.28);
            color: var(--ink);
            min-width: 180px;
        }

        .page-loader__card .spinner {
            width: 1.6rem;
            height: 1.6rem;
            border-color: rgba(47, 107, 79, 0.25);
            border-top-color: var(--accent);
        }

        .page-loader__card p {
            font-size: 0.92rem;
            font-weight: 600;
            letter-spacing: 0.01em;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (prefers-reduced-motion: reduce) {
            .spinner {
                animation: none;
                border-top-color: var(--accent);
            }
        }
    </style>
</head>
<body>
@php
    $brand = getSettingValue('software_name') ?: config('app.name', 'Cake Town');
    $logo = getSettingValue('company_logo');
@endphp
<div class="stage">
    <div class="backdrop" aria-hidden="true"></div>

    <div class="shell">
        <header class="brand">
            @if($logo)
                <div class="brand-mark">
                    <img src="{{ asset('upload/' . $logo) }}" alt="{{ $brand }}">
                </div>
            @endif
            <h1>{{ $brand }}</h1>
            <p>Sign in to manage outlets, stock, and daily sales.</p>
        </header>

        <form class="panel" id="login-form" action="{{ route('login') }}" method="post">
            @csrf

            @if ($errors->any())
                <div class="errors" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="field">
                <label for="email">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email', $_COOKIE['email'] ?? '') }}"
                    placeholder="you@company.com"
                    required
                    autofocus
                    autocomplete="username"
                >
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    value="{{ $_COOKIE['password'] ?? '' }}"
                    placeholder="Enter your password"
                    required
                    autocomplete="current-password"
                >
            </div>

            <div class="row">
                <label class="remember">
                    <input
                        type="checkbox"
                        name="remember"
                        @checked(old('remember', isset($_COOKIE['email'])))
                    >
                    Remember me
                </label>
            </div>

            <button type="submit" class="submit" id="login-submit">
                <span class="btn-label">Sign in</span>
                <span class="btn-wait" aria-hidden="true">
                    <span class="spinner"></span>
                    Signing in…
                </span>
            </button>
        </form>

        <p class="foot">Secure access for authorized staff only</p>
    </div>
</div>

<div class="page-loader" id="login-loader" aria-live="polite" aria-busy="false" hidden>
    <div class="page-loader__card">
        <span class="spinner" aria-hidden="true"></span>
        <p>Signing you in…</p>
    </div>
</div>
<script>
    (function () {
        var form = document.getElementById('login-form');
        if (!form) return;

        var btn = document.getElementById('login-submit');
        var loader = document.getElementById('login-loader');
        var submitting = false;

        form.addEventListener('submit', function (e) {
            if (submitting) {
                e.preventDefault();
                return;
            }
            submitting = true;
            if (btn) {
                btn.classList.add('is-loading');
                btn.setAttribute('aria-busy', 'true');
            }
            if (loader) {
                loader.hidden = false;
                loader.classList.add('is-on');
                loader.setAttribute('aria-busy', 'true');
            }
        });
    })();
</script>
</body>

</html>
