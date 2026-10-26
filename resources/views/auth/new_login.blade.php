@php
    $brand = getSettingValue('software_name') ?: config('app.name', 'Cake Town');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign in | {{ $brand }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --wine: #a30f24;
            --wine-dark: #850a1b;
            --wine-soft: #fff0f1;
            --ink: #2b1714;
            --muted: #746966;
            --line: #ddd7d3;
            --paper: #fffdfa;
            --display: "Fraunces", Georgia, serif;
            --body: "Manrope", Arial, sans-serif;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html {
            min-height: 100%;
            background: #28150d;
        }

        body {
            width: 100%;
            height: 100vh;
            height: 100dvh;
            margin: 0;
            padding: 24px;
            display: grid;
            place-items: center;
            overflow: hidden;
            color: var(--ink);
            font-family: var(--body);
            background:
                linear-gradient(105deg, rgba(48, 25, 14, 0.78), rgba(126, 72, 37, 0.35)),
                url("https://images.unsplash.com/photo-1578985545062-69928b1d9587?auto=format&fit=crop&w=2200&q=82") center / cover fixed;
        }

        body::before {
            content: "";
            position: fixed;
            inset: -32px;
            z-index: -1;
            background: inherit;
            filter: blur(20px) saturate(0.9);
            transform: scale(1.04);
        }

        button,
        input,
        a {
            font: inherit;
        }

        .login-shell {
            width: min(1540px, 100%);
            height: min(944px, calc(100dvh - 48px));
            min-height: 0;
            display: grid;
            grid-template-columns: minmax(0, 58%) minmax(460px, 42%);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-radius: 16px;
            background: var(--paper);
            box-shadow: 0 28px 80px rgba(31, 13, 5, 0.34);
            animation: shell-in 620ms cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        .hero {
            position: relative;
            isolation: isolate;
            min-height: 0;
            display: flex;
            flex-direction: column;
            padding: clamp(40px, 5vw, 72px);
            color: white;
            background:
                linear-gradient(90deg, rgba(42, 17, 8, 0.82) 0%, rgba(49, 20, 9, 0.57) 48%, rgba(45, 21, 12, 0.28) 100%),
                linear-gradient(180deg, rgba(31, 12, 5, 0.22), rgba(31, 12, 5, 0.08) 45%, rgba(31, 12, 5, 0.45)),
                url("https://images.unsplash.com/photo-1578985545062-69928b1d9587?auto=format&fit=crop&w=1800&q=88") center / cover no-repeat;
        }

        .hero::after {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -1;
            background: radial-gradient(circle at 76% 12%, rgba(217, 157, 84, 0.17), transparent 36%);
            pointer-events: none;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            width: fit-content;
            color: white;
        }

        .brand-icon {
            width: 58px;
            height: 58px;
            flex: 0 0 auto;
            color: white;
        }

        .brand-wordmark {
            position: relative;
            font: 700 clamp(32px, 3vw, 43px)/1 var(--display);
            letter-spacing: -0.025em;
        }

        .brand-wordmark::after {
            content: "";
            position: absolute;
            right: 0;
            bottom: -8px;
            width: 48%;
            height: 3px;
            border-radius: 999px;
            background: #d31933;
        }

        .hero-copy {
            margin-top: clamp(48px, 7vh, 82px);
            max-width: 690px;
        }

        .eyebrow {
            margin: 0 0 14px;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 0.34em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.88);
        }

        .hero h1 {
            margin: 0;
            max-width: 660px;
            font: 700 clamp(58px, 5.4vw, 82px)/0.92 var(--display);
            letter-spacing: -0.045em;
            text-wrap: balance;
            text-shadow: 0 4px 28px rgba(30, 11, 4, 0.22);
        }

        .hero-description {
            margin: 22px 0 0;
            font-size: clamp(17px, 1.5vw, 21px);
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.82);
        }

        .feature-list {
            margin: auto 0 0;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            list-style: none;
        }

        .feature {
            min-height: 62px;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 13px 22px;
            border: 1px solid rgba(255, 255, 255, 0.34);
            border-radius: 15px;
            background: rgba(48, 25, 16, 0.52);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12), 0 12px 26px rgba(24, 9, 3, 0.2);
            backdrop-filter: blur(10px);
            font-size: 16px;
            font-weight: 600;
        }

        .feature svg {
            width: 27px;
            height: 27px;
            color: #f4dfc5;
        }

        .portal {
            position: relative;
            min-height: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            background:
                radial-gradient(circle at 78% 10%, rgba(163, 15, 36, 0.035), transparent 30%),
                var(--paper);
        }

        .portal-content {
            width: min(500px, calc(100% - 64px));
            margin: auto;
            padding: 10px 0 28px;
        }

        .portal-mark {
            width: 96px;
            height: 96px;
            display: grid;
            place-items: center;
            margin-bottom: 26px;
            border-radius: 16px;
            color: var(--wine);
            background: linear-gradient(145deg, #fff1f2, #fbe9e8);
        }

        .portal-mark svg {
            width: 58px;
            height: 58px;
        }

        .portal-kicker {
            margin: 0 0 10px;
            color: var(--wine);
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.31em;
            text-transform: uppercase;
        }

        .portal h2 {
            margin: 0;
            font: 700 clamp(46px, 4.2vw, 60px)/1 var(--display);
            letter-spacing: -0.04em;
            color: var(--ink);
        }

        .portal-subtitle {
            margin: 10px 0 38px;
            color: #705d59;
            font-size: clamp(17px, 1.6vw, 21px);
            line-height: 1.55;
        }

        .notice,
        .errors {
            margin-bottom: 20px;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 14px;
            line-height: 1.45;
        }

        .notice {
            color: #276243;
            border: 1px solid #c9e4d5;
            background: #effaf4;
        }

        .errors {
            color: #8c1727;
            border: 1px solid #f0c6cc;
            background: #fff2f3;
        }

        .errors p {
            margin: 0;
        }

        .errors p + p {
            margin-top: 4px;
        }

        .field {
            margin-bottom: 20px;
        }

        .field-label {
            display: block;
            margin-bottom: 8px;
            color: #24201f;
            font-size: 15px;
            font-weight: 600;
        }

        .control {
            height: 62px;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 0 17px;
            border: 1px solid var(--line);
            border-radius: 13px;
            background: #fff;
            transition: border-color 160ms ease, box-shadow 160ms ease, background-color 160ms ease;
        }

        .control:focus-within {
            border-color: var(--wine);
            box-shadow: 0 0 0 3px rgba(163, 15, 36, 0.1), 0 7px 18px rgba(91, 25, 24, 0.08);
        }

        .control.has-error {
            border-color: #c93345;
            background: #fffafa;
        }

        .control > svg {
            width: 22px;
            height: 22px;
            flex: 0 0 auto;
            color: #65605e;
        }

        .control input {
            min-width: 0;
            width: 100%;
            height: 100%;
            border: 0;
            outline: 0;
            background: transparent;
            color: var(--ink);
            font-size: 16px;
            font-weight: 500;
        }

        .control input::placeholder {
            color: #98908d;
            opacity: 1;
        }

        .password-toggle {
            width: 38px;
            height: 38px;
            flex: 0 0 auto;
            display: grid;
            place-items: center;
            padding: 0;
            border: 0;
            border-radius: 9px;
            color: #514d4b;
            background: transparent;
            cursor: pointer;
        }

        .password-toggle:hover,
        .password-toggle:focus-visible {
            color: var(--wine);
            background: var(--wine-soft);
            outline: none;
        }

        .password-toggle svg {
            width: 22px;
            height: 22px;
        }

        .password-toggle .eye-off,
        .password-toggle.is-visible .eye-on {
            display: none;
        }

        .password-toggle.is-visible .eye-off {
            display: block;
        }

        .field-error {
            margin: 7px 2px 0;
            color: #a11b2e;
            font-size: 13px;
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin: 2px 0 24px;
        }

        .remember {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #6f6966;
            font-size: 14px;
            cursor: pointer;
            user-select: none;
        }

        .remember input {
            width: 20px;
            height: 20px;
            margin: 0;
            accent-color: var(--wine);
        }

        .forgot {
            color: var(--wine);
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
        }

        .forgot:hover,
        .forgot:focus-visible {
            color: var(--wine-dark);
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .submit {
            width: 100%;
            height: 64px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 18px;
            border: 0;
            border-radius: 13px;
            color: white;
            background: linear-gradient(105deg, var(--wine), #b51229);
            box-shadow: 0 12px 24px rgba(163, 15, 36, 0.18);
            font-size: 17px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 160ms ease, box-shadow 160ms ease, background-color 160ms ease;
        }

        .submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 15px 30px rgba(163, 15, 36, 0.24);
        }

        .submit:active {
            transform: translateY(0);
        }

        .submit:focus-visible {
            outline: 3px solid rgba(163, 15, 36, 0.22);
            outline-offset: 3px;
        }

        .submit svg {
            width: 22px;
            height: 22px;
        }

        .submit .waiting {
            display: none;
            align-items: center;
            gap: 10px;
        }

        .submit.is-loading {
            pointer-events: none;
            opacity: 0.9;
        }

        .submit.is-loading .ready {
            display: none;
        }

        .submit.is-loading .waiting {
            display: inline-flex;
        }

        .spinner {
            width: 19px;
            height: 19px;
            border: 2px solid rgba(255, 255, 255, 0.36);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 700ms linear infinite;
        }

        .security-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            margin: 25px 0 0;
            color: #8b8582;
            font-size: 12px;
        }

        .security-note svg {
            width: 16px;
            height: 16px;
        }

        .portal-footer {
            width: min(500px, calc(100% - 64px));
            margin: 0 auto;
            padding: 18px 0 max(30px, env(safe-area-inset-bottom));
            flex: 0 0 auto;
            border-top: 1px solid #e4dfdb;
            color: #948d89;
            text-align: center;
            font-size: 12px;
            line-height: 1.4;
        }

        @keyframes shell-in {
            from { opacity: 0; transform: translateY(12px) scale(0.995); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 1180px) {
            .login-shell {
                grid-template-columns: minmax(0, 52%) minmax(440px, 48%);
            }

            .hero h1 {
                font-size: clamp(52px, 6vw, 68px);
            }

            .feature {
                padding-inline: 16px;
            }
        }

        /* Keep the full experience inside shorter desktop viewports. */
        @media (min-width: 901px) and (max-height: 900px) {
            body {
                padding: 16px;
            }

            .login-shell {
                height: calc(100dvh - 32px);
            }

            .hero {
                padding: clamp(28px, 4vw, 48px);
            }

            .hero-copy {
                margin-top: clamp(26px, 4vh, 42px);
            }

            .hero h1 {
                font-size: clamp(46px, 4.6vw, 66px);
            }

            .hero-description {
                margin-top: 14px;
                font-size: 16px;
                line-height: 1.45;
            }

            .feature-list {
                gap: 9px;
            }

            .feature {
                min-height: 54px;
                padding: 0 15px;
            }

            .portal-content {
                width: min(460px, calc(100% - 56px));
                padding: 0 0 14px;
            }

            .portal-mark {
                width: 66px;
                height: 66px;
                margin-bottom: 14px;
                border-radius: 14px;
            }

            .portal-mark svg {
                width: 42px;
                height: 42px;
            }

            .portal-kicker {
                margin-bottom: 6px;
                font-size: 12px;
            }

            .portal h2 {
                font-size: clamp(40px, 3.8vw, 50px);
            }

            .portal-subtitle {
                margin: 7px 0 20px;
                font-size: 16px;
            }

            .field {
                margin-bottom: 13px;
            }

            .field-label {
                margin-bottom: 5px;
                font-size: 14px;
            }

            .control {
                height: 52px;
            }

            .form-options {
                margin: 0 0 15px;
            }

            .submit {
                height: 54px;
            }

            .security-note {
                margin-top: 14px;
            }

            .portal-footer {
                width: min(460px, calc(100% - 56px));
                padding: 10px 0 26px;
            }
        }

        @media (min-width: 901px) and (max-height: 760px) {
            .portal-mark,
            .security-note {
                display: none;
            }

            .hero-copy {
                margin-top: 20px;
            }

            .hero-description {
                display: none;
            }

            .feature-list {
                display: none;
            }

            .portal-content {
                margin-block: auto;
            }

            .portal-footer {
                padding: 8px 0 22px;
            }
        }

        @media (min-width: 901px) and (max-height: 520px) {
            .portal-footer {
                display: none;
            }
        }

        @media (max-width: 900px) {
            body {
                padding: 0;
                display: block;
                background: var(--paper);
            }

            body::before {
                display: none;
            }

            .login-shell {
                width: 100%;
                height: 100dvh;
                min-height: 0;
                grid-template-rows: clamp(120px, 24dvh, 210px) minmax(0, 1fr);
                grid-template-columns: 1fr;
                border: 0;
                border-radius: 0;
                box-shadow: none;
                animation: none;
            }

            .hero {
                min-height: 0;
                padding: 24px 32px;
                background-position: center 57%;
            }

            .brand-icon {
                width: 48px;
                height: 48px;
            }

            .brand-wordmark {
                font-size: 34px;
            }

            .hero-copy {
                margin-top: auto;
            }

            .eyebrow,
            .hero-description {
                display: none;
            }

            .hero h1 {
                max-width: 580px;
                font-size: clamp(34px, 7vw, 52px);
                line-height: 0.98;
            }

            .hero h1 br {
                display: none;
            }

            .feature-list {
                display: none;
            }

            .portal {
                min-height: 0;
            }

            .portal-content {
                padding-top: 10px;
                padding-bottom: 14px;
            }

            .portal-mark {
                width: 58px;
                height: 58px;
                margin-bottom: 12px;
            }

            .portal-mark svg {
                width: 38px;
                height: 38px;
            }

            .portal-kicker {
                margin-bottom: 5px;
                font-size: 11px;
            }

            .portal h2 {
                font-size: clamp(36px, 8vw, 46px);
            }

            .portal-subtitle {
                margin: 5px 0 16px;
                font-size: 15px;
            }

            .field {
                margin-bottom: 11px;
            }

            .field-label {
                margin-bottom: 4px;
                font-size: 13px;
            }

            .control {
                height: 50px;
            }

            .form-options {
                margin: 0 0 13px;
            }

            .submit {
                height: 52px;
            }

            .security-note {
                margin-top: 12px;
            }

            .portal-footer {
                padding: 8px 0 18px;
            }
        }

        @media (max-width: 600px) {
            .login-shell {
                grid-template-rows: clamp(104px, 20dvh, 160px) minmax(0, 1fr);
            }

            .hero {
                min-height: 0;
                padding: 18px 22px;
                background-position: center 54%;
            }

            .brand {
                gap: 10px;
            }

            .brand-icon {
                width: 40px;
                height: 40px;
            }

            .brand-wordmark {
                font-size: 29px;
            }

            .brand-wordmark::after {
                bottom: -5px;
                height: 2px;
            }

            .hero-copy {
                margin-top: auto;
            }

            .hero h1 {
                max-width: 350px;
                font-size: clamp(29px, 8.5vw, 40px);
            }

            .portal {
                min-height: 0;
            }

            .portal-content,
            .portal-footer {
                width: min(100% - 40px, 500px);
            }

            .portal-content {
                margin-top: 0;
                padding: 7px 0 10px;
            }

            .portal-mark {
                width: 52px;
                height: 52px;
                margin-bottom: 9px;
                border-radius: 12px;
            }

            .portal-mark svg {
                width: 34px;
                height: 34px;
            }

            .portal-kicker {
                font-size: 12px;
            }

            .portal h2 {
                font-size: clamp(34px, 10vw, 43px);
            }

            .portal-subtitle {
                margin-bottom: 14px;
                font-size: 14px;
            }

            .control {
                height: 48px;
            }

            .submit {
                height: 50px;
            }

            .portal-footer {
                padding-bottom: 18px;
            }
        }

        /* Mobile landscape and very short screens prioritise the working form. */
        @media (max-width: 900px) and (max-height: 700px) {
            .login-shell {
                grid-template-rows: 72px minmax(0, 1fr);
            }

            .hero {
                padding: 10px 18px;
            }

            .brand-icon {
                width: 34px;
                height: 34px;
            }

            .brand-wordmark {
                font-size: 26px;
            }

            .hero-copy,
            .portal-mark,
            .security-note,
            .portal-footer {
                display: none;
            }

            .portal-content {
                margin-block: auto;
                padding-block: 8px;
            }

            .portal h2 {
                font-size: 34px;
            }

            .portal-subtitle {
                margin: 3px 0 10px;
            }

            .field {
                margin-bottom: 8px;
            }

            .control {
                height: 44px;
            }

            .form-options {
                margin-bottom: 9px;
            }

            .submit {
                height: 46px;
            }
        }

        @media (max-width: 900px) and (max-height: 520px) {
            .login-shell {
                grid-template-rows: 1fr;
            }

            .hero,
            .portal-kicker,
            .portal-subtitle {
                display: none;
            }
        }

        @media (max-width: 380px) {
            .form-options {
                align-items: flex-start;
                flex-direction: column;
                gap: 12px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>
<main class="login-shell">
    <section class="hero" aria-label="{{ $brand }} workspace">
        <div class="brand">
            <svg class="brand-icon" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                <path d="M16 30h32l-2 24H18l-2-24Z" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/>
                <path d="M19 38c5-3 9 3 14 0s9 3 14 0M18 47h28M23 30c1-7 5-11 10-11 6 0 10 4 11 11" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                <circle cx="33" cy="16" r="4" stroke="currentColor" stroke-width="3"/>
                <path d="M33 12c0-4 2-7 5-9" stroke="#d31933" stroke-width="3" stroke-linecap="round"/>
            </svg>
            <span class="brand-wordmark">{{ $brand }}</span>
        </div>

        <div class="hero-copy">
            <p class="eyebrow">{{ $brand }} Workspace</p>
            <h1>A sweeter way<br>to manage<br>your business.</h1>
            <p class="hero-description">Your outlets, inventory and daily sales. All in one place.</p>
        </div>

        <ul class="feature-list" aria-label="Workspace modules">
            <li class="feature">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M4 10v10h16V10M3 8l2-4h14l2 4M3 8c0 2 3 3 4.5 1.2C9 11 12 10 12 8c0 2 3 3 4.5 1.2C18 11 21 10 21 8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Outlets
            </li>
            <li class="feature">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="m4 7 8-4 8 4-8 4-8-4Zm0 0v10l8 4 8-4V7M12 11v10" stroke-linejoin="round"/>
                </svg>
                Inventory
            </li>
            <li class="feature">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M5 20V10h4v10H5Zm10 0V4h4v16h-4Z" stroke-linejoin="round"/>
                </svg>
                Daily sales
            </li>
        </ul>
    </section>

    <section class="portal">
        <div class="portal-content">
            <div class="portal-mark" aria-hidden="true">
                <svg viewBox="0 0 64 64" fill="none">
                    <path d="M16 30h32l-2 24H18l-2-24Z" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/>
                    <path d="M19 38c5-3 9 3 14 0s9 3 14 0M18 47h28M23 30c1-7 5-11 10-11 6 0 10 4 11 11" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                    <circle cx="33" cy="16" r="4" stroke="currentColor" stroke-width="3"/>
                    <path d="M33 12c0-4 2-7 5-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                </svg>
            </div>

            <p class="portal-kicker">Staff Portal</p>
            <h2>Welcome back</h2>
            <p class="portal-subtitle">Sign in to your {{ $brand }} workspace.</p>

            @if (session('status'))
                <div class="notice" role="status">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="errors" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form id="login-form" action="{{ route('login') }}" method="post">
                @csrf

                <div class="field">
                    <label class="field-label" for="email">Email address</label>
                    <div class="control @error('email') has-error @enderror">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <rect x="3.5" y="5" width="17" height="14" rx="2"/>
                            <path d="m5 7 7 6 7-6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email', $_COOKIE['email'] ?? '') }}"
                            placeholder="you@company.com"
                            required
                            autofocus
                            autocomplete="username"
                            @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                        >
                    </div>
                    @error('email')
                        <p class="field-error" id="email-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label class="field-label" for="password">Password</label>
                    <div class="control @error('password') has-error @enderror">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <rect x="5" y="10" width="14" height="11" rx="2"/>
                            <path d="M8 10V7a4 4 0 0 1 8 0v3" stroke-linecap="round"/>
                        </svg>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            value="{{ $_COOKIE['password'] ?? '' }}"
                            placeholder="Enter your password"
                            required
                            autocomplete="current-password"
                            @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                        >
                        <button class="password-toggle" id="password-toggle" type="button" aria-label="Show password" aria-controls="password" aria-pressed="false">
                            <svg class="eye-on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/>
                                <circle cx="12" cy="12" r="2.7"/>
                            </svg>
                            <svg class="eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="m3 3 18 18M10.6 6.1A10 10 0 0 1 12 6c6 0 9.5 6 9.5 6a15.5 15.5 0 0 1-2.2 2.8M6.5 7.3A15.4 15.4 0 0 0 2.5 12s3.5 6 9.5 6c1.2 0 2.3-.2 3.3-.6" stroke-linecap="round"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="field-error" id="password-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-options">
                    <label class="remember" for="remember">
                        <input
                            id="remember"
                            type="checkbox"
                            name="remember"
                            @checked(old('remember', isset($_COOKIE['email'])))
                        >
                        <span>Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="forgot" href="{{ route('password.request') }}">Forgot password?</a>
                    @endif
                </div>

                <button class="submit" id="login-submit" type="submit">
                    <span class="ready">Sign in</span>
                    <svg class="ready" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M5 12h14m-5-5 5 5-5 5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="waiting" aria-hidden="true">
                        <span class="spinner"></span>
                        Signing in…
                    </span>
                </button>
            </form>

            <p class="security-note">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <rect x="6" y="10" width="12" height="10" rx="2"/>
                    <path d="M9 10V7a3 3 0 0 1 6 0v3" stroke-linecap="round"/>
                </svg>
                Secure access for authorized staff only.
            </p>
        </div>

        <footer class="portal-footer">© {{ now()->year }} {{ $brand }}. All rights reserved.</footer>
    </section>
</main>

<script>
    (function () {
        var form = document.getElementById('login-form');
        var submit = document.getElementById('login-submit');
        var password = document.getElementById('password');
        var passwordToggle = document.getElementById('password-toggle');
        var submitting = false;

        if (password && passwordToggle) {
            passwordToggle.addEventListener('click', function () {
                var visible = password.type === 'text';
                password.type = visible ? 'password' : 'text';
                passwordToggle.classList.toggle('is-visible', !visible);
                passwordToggle.setAttribute('aria-label', visible ? 'Show password' : 'Hide password');
                passwordToggle.setAttribute('aria-pressed', visible ? 'false' : 'true');
                password.focus();
            });
        }

        if (form && submit) {
            form.addEventListener('submit', function (event) {
                if (submitting) {
                    event.preventDefault();
                    return;
                }

                submitting = true;
                submit.classList.add('is-loading');
                submit.setAttribute('aria-busy', 'true');
            });
        }
    })();
</script>
</body>
</html>
