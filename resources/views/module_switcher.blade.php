<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Modul Layanan - SIADMIN FEB UNJ</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('logo-unj.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            /* FEB UNJ Signature Orange & Ambient Accents */
            --brand-primary: #ea580c;
            --brand-primary-light: #f97316;
            --brand-50: #fff7ed;
            --brand-100: #ffedd5;
            
            /* Slate Neutrals */
            --bg-base: #f8fafc;
            --surface-card: #ffffff;
            --surface-subtle: #f1f3f7;
            --border-light: rgba(226, 232, 240, 0.9);
            --border-hover: rgba(234, 88, 12, 0.35);
            
            /* Typography */
            --text-main: #0f172a;
            --text-secondary: #475569;
            --text-muted: #64748b;
            --text-faint: #94a3b8;
        }

        html,
        body {
            min-height: 100%;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-base);
            color: var(--text-main);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Dynamic Ambient Background ── */
        .ambient-bg {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        /* Subtle modern technical grid with radial vignette fade */
        .grid-pattern {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(148, 163, 184, 0.28) 1.25px, transparent 1.25px);
            background-size: 32px 32px;
            mask-image: radial-gradient(ellipse at 50% 40%, black 20%, transparent 80%);
            -webkit-mask-image: radial-gradient(ellipse at 50% 40%, black 20%, transparent 80%);
            opacity: 0.75;
        }

        /* Soft blurred ambient glow orbs */
        .glow-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            pointer-events: none;
            opacity: 0.6;
        }

        .orb-orange {
            top: -120px;
            left: 50%;
            transform: translateX(-50%);
            width: 720px;
            height: 480px;
            background: radial-gradient(ellipse, rgba(234, 88, 12, 0.15) 0%, rgba(249, 115, 22, 0.05) 50%, transparent 75%);
            animation: pulseGlow 10s ease-in-out infinite alternate;
        }

        .orb-teal {
            bottom: -80px;
            left: 10%;
            width: 480px;
            height: 380px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.09) 0%, transparent 70%);
            animation: floatLeft 14s ease-in-out infinite alternate;
        }

        .orb-purple {
            top: 35%;
            right: 5%;
            width: 420px;
            height: 360px;
            background: radial-gradient(circle, rgba(147, 51, 234, 0.07) 0%, transparent 70%);
            animation: floatRight 12s ease-in-out infinite alternate;
        }

        @keyframes pulseGlow {
            0% { transform: translateX(-50%) scale(0.95); opacity: 0.45; }
            100% { transform: translateX(-50%) scale(1.08); opacity: 0.75; }
        }

        @keyframes floatLeft {
            0% { transform: translate(0, 0); }
            100% { transform: translate(30px, -40px); }
        }

        @keyframes floatRight {
            0% { transform: translate(0, 0); }
            100% { transform: translate(-30px, 30px); }
        }

        /* ── Main Layout ── */
        .page-wrapper {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.5rem 1.25rem 2.5rem;
        }

        .main-container {
            width: 100%;
            max-width: 960px;
            margin: 0 auto;
        }

        /* ── Top Bar / Header Shell ── */
        .top-navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.85rem 1.5rem;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 100px;
            margin-bottom: 2.75rem;
            box-shadow: 0 4px 20px -4px rgba(15, 23, 42, 0.04);
            animation: fadeInDown 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .brand-cluster {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: inherit;
        }

        .logo-box {
            position: relative;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            padding: 4px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .logo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-meta {
            display: flex;
            flex-direction: column;
        }

        .brand-title {
            font-size: 0.92rem;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.01em;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .brand-pill {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            background: var(--brand-50);
            color: var(--brand-primary);
            border: 1px solid var(--brand-100);
            padding: 2px 7px;
            border-radius: 6px;
        }

        .brand-sub {
            font-size: 0.74rem;
            font-weight: 500;
            color: var(--text-muted);
        }

        .user-nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* User Profile Pill */
        .user-pill {
            display: flex;
            align-items: center;
            gap: 9px;
            background: #f8fafc;
            border: 1px solid rgba(226, 232, 240, 0.9);
            padding: 4px 12px 4px 5px;
            border-radius: 100px;
        }

        .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(234, 88, 12, 0.3);
            flex-shrink: 0;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .user-name {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--text-main);
            max-width: 140px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            font-size: 0.68rem;
            font-weight: 500;
            color: var(--text-muted);
        }

        /* Logout Button */
        .btn-logout {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--text-muted);
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.9);
            padding: 6px 14px;
            border-radius: 100px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .btn-logout:hover {
            color: #ef4444;
            background: #fef2f2;
            border-color: #fecaca;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.1);
        }

        .btn-logout svg {
            width: 14px;
            height: 14px;
            stroke-width: 2.2;
        }

        /* ── Hero Center Header ── */
        .hero-section {
            text-align: center;
            margin-bottom: 2.5rem;
            animation: fadeInDown 0.7s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--brand-primary);
            background: rgba(255, 247, 237, 0.9);
            border: 1px solid var(--brand-100);
            padding: 5px 14px;
            border-radius: 100px;
            margin-bottom: 0.9rem;
            box-shadow: 0 2px 6px rgba(234, 88, 12, 0.06);
        }

        .hero-badge .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: var(--brand-primary);
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.2);
            animation: pingDot 2.5s cubic-bezier(0, 0, 0.2, 1) infinite;
        }

        @keyframes pingDot {
            0% { box-shadow: 0 0 0 0 rgba(234, 88, 12, 0.4); }
            70% { box-shadow: 0 0 0 5px rgba(234, 88, 12, 0); }
            100% { box-shadow: 0 0 0 0 rgba(234, 88, 12, 0); }
        }

        .hero-title {
            font-size: clamp(1.75rem, 3.8vw, 2.35rem);
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.03em;
            line-height: 1.2;
            margin-bottom: 0.6rem;
        }

        .hero-desc {
            font-size: 0.94rem;
            color: var(--text-secondary);
            max-width: 580px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* ── Cards Grid ── */
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.75rem;
            margin-bottom: 3rem;
        }

        /* ── Card Specification (Matching Reference Image) ── */
        .module-card {
            display: flex;
            flex-direction: column;
            background: var(--surface-card);
            border-radius: 28px;
            border: 1px solid var(--border-light);
            padding: 14px;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 14px 30px -6px rgba(15, 23, 42, 0.06);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease, border-color 0.25s ease;
            position: relative;
            animation: cardEntrance 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
            outline: none;
        }

        .module-card:nth-child(1) { animation-delay: 0.08s; }
        .module-card:nth-child(2) { animation-delay: 0.16s; }
        .module-card:nth-child(3) { animation-delay: 0.24s; }
        .module-card:nth-child(4) { animation-delay: 0.32s; }

        .module-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 36px -8px rgba(15, 23, 42, 0.12), 0 4px 12px rgba(0, 0, 0, 0.03);
            border-color: var(--border-hover);
        }

        .module-card:focus-visible {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.18);
        }

        /* Top Visual Area (Isometric Illustration inside rounded container) */
        .card-visual {
            position: relative;
            width: 100%;
            height: 200px;
            background-color: var(--surface-subtle);
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-visual-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .module-card:hover .card-visual-img {
            transform: scale(1.045);
        }

        /* Dark Icon Badge at Top-Right of Visual (As in Reference) */
        .card-badge-icon {
            position: absolute;
            top: 14px;
            right: 14px;
            width: 36px;
            height: 36px;
            background-color: #0f172a;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.25);
            z-index: 2;
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s ease;
        }

        .module-card:hover .card-badge-icon {
            transform: scale(1.08);
            background-color: var(--brand-primary);
        }

        .card-badge-icon svg {
            width: 18px;
            height: 18px;
            stroke-width: 2.2;
        }

        /* Card Content Body */
        .card-content {
            padding: 18px 10px 10px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .card-title {
            font-size: 1.22rem;
            font-weight: 700;
            color: var(--text-main);
            letter-spacing: -0.02em;
            margin-bottom: 0.45rem;
            line-height: 1.3;
        }

        .card-desc {
            font-size: 0.865rem;
            color: var(--text-muted);
            line-height: 1.58;
            margin-bottom: 1.25rem;
            flex-grow: 1;
        }

        /* Card Footer / Action Bar (Pill button on left, Status info on right) */
        .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 14px;
            border-top: 1px solid rgba(241, 245, 249, 1);
            gap: 12px;
        }

        /* Action Button */
        .card-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 0.82rem;
            font-weight: 600;
            color: #1e293b;
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 1);
            padding: 8px 18px;
            border-radius: 12px;
            transition: all 0.25s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            white-space: nowrap;
        }

        .module-card:hover .card-action-btn {
            background: #0f172a;
            color: #ffffff;
            border-color: #0f172a;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.16);
        }

        .card-action-btn svg {
            width: 14px;
            height: 14px;
            stroke-width: 2.5;
            transition: transform 0.25s ease;
        }

        .module-card:hover .card-action-btn svg {
            transform: translateX(3px);
        }

        /* Status Info on Right */
        .card-status-info {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.78rem;
            font-weight: 500;
            color: var(--text-muted);
            white-space: nowrap;
        }

        .card-status-info svg {
            width: 15px;
            height: 15px;
            stroke-width: 2;
            color: var(--text-faint);
            transition: color 0.2s ease;
        }

        .module-card:hover .card-status-info svg {
            color: var(--brand-primary);
        }

        /* ── Empty State ── */
        .no-modules-box {
            text-align: center;
            padding: 3.5rem 2rem;
            background: #ffffff;
            border-radius: 28px;
            border: 1px solid var(--border-light);
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.04);
        }

        .no-modules-box p {
            color: var(--text-secondary);
            font-size: 0.92rem;
        }

        /* ── Footer ── */
        .page-footer {
            text-align: center;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(226, 232, 240, 0.6);
            color: var(--text-faint);
            font-size: 0.77rem;
            animation: fadeIn 1s ease both;
        }

        .page-footer a {
            color: var(--brand-primary);
            text-decoration: none;
            font-weight: 600;
        }

        .page-footer a:hover {
            text-decoration: underline;
        }

        /* ── Keyframe Animations ── */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes cardEntrance {
            from {
                opacity: 0;
                transform: translateY(22px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* ── Responsive Refinements ── */
        @media (max-width: 768px) {
            .top-navbar {
                flex-direction: column;
                gap: 12px;
                border-radius: 20px;
                padding: 1rem;
            }

            .user-nav-actions {
                width: 100%;
                justify-content: space-between;
            }

            .modules-grid {
                grid-template-columns: 1fr;
            }

            .card-visual {
                height: 180px;
            }

            .card-footer {
                flex-wrap: wrap;
            }
        }
    </style>
</head>

<body>
    {{-- Dynamic Ambient Lighting & Tech Grid Background --}}
    <div class="ambient-bg" aria-hidden="true">
        <div class="grid-pattern"></div>
        <div class="glow-orb orb-orange"></div>
        <div class="glow-orb orb-teal"></div>
        <div class="glow-orb orb-purple"></div>
    </div>

    <div class="page-wrapper">
        <div class="main-container">

            {{-- ── Top Navigation Bar with Logo UNJ & User Status ── --}}
            <header class="top-navbar">
                <a href="{{ route('switcher') }}" class="brand-cluster" title="Portal Layanan Terpadu FEB UNJ">
                    <div class="logo-box">
                        <img src="{{ asset('logo-unj.png') }}" alt="Logo Resmi UNJ">
                    </div>
                    <div class="brand-meta">
                        <div class="brand-title">
                            SIADMIN FEB
                            <span class="brand-pill">Portal</span>
                        </div>
                        <div class="brand-sub">Fakultas Ekonomi dan Bisnis • UNJ</div>
                    </div>
                </a>

                <div class="user-nav-actions">
                    <div class="user-pill" title="Akun Pengguna Terhubung">
                        <div class="user-avatar">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                        </div>
                        <div class="user-info">
                            <span class="user-name">{{ auth()->user()->name ?? 'Pengguna' }}</span>
                            <span class="user-role">
                                @if (auth()->user()->isAdmin())
                                    Administrator
                                @elseif (auth()->user()->isOperator())
                                    Operator Layanan
                                @else
                                    Staf Pengguna
                                @endif
                            </span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                        @csrf
                        <button type="submit" class="btn-logout" title="Keluar dari sesi login">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </header>

            {{-- ── Hero Heading Section ── --}}
            <section class="hero-section">
                <div class="hero-badge">
                    <span class="status-dot"></span>
                    Portal Layanan Terpadu FEB UNJ
                </div>
                <h1 class="hero-title">Pilih Modul Layanan</h1>
                <p class="hero-desc">
                    Akses cepat dan terintegrasi ke seluruh ekosistem administrasi persuratan, legalisir alumni, dan peminjaman fasilitas fakultas.
                </p>
            </section>

            {{-- ── Module Cards Section ── --}}
            @php
                $hasAny =
                    auth()->user()->can_access_letters ||
                    auth()->user()->can_access_legalizations ||
                    auth()->user()->can_access_room_reservations ||
                    auth()->user()->isAdmin();
            @endphp

            @if ($hasAny)
                <main class="modules-grid">

                    {{-- ── Card 1: Modul Persuratan & Arsip ── --}}
                    @if (auth()->user()->can_access_letters || auth()->user()->isAdmin())
                        <a href="{{ route('dashboard') }}" class="module-card" aria-label="Buka Modul Persuratan & Arsip">
                            <div class="card-visual">
                                <img src="{{ asset('images/modules/letters.jpg') }}" alt="Ilustrasi Modul Surat & Arsip" class="card-visual-img" loading="lazy">
                                <div class="card-badge-icon" title="Modul Persuratan">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                    </svg>
                                </div>
                            </div>

                            <div class="card-content">
                                <h2 class="card-title">Modul Persuratan &amp; Arsip</h2>
                                <p class="card-desc">
                                    Penomoran surat otomatis, klasifikasi agenda dinas, disposisi, dan pengelolaan arsip dokumen administrasi fakultas.
                                </p>

                                <div class="card-footer">
                                    <span class="card-action-btn">
                                        Buka Modul
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                        </svg>
                                    </span>
                                    <span class="card-status-info">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                        Penomoran &amp; Arsip
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endif

                    {{-- ── Card 2: Modul Layanan Legalisir ── --}}
                    @if (auth()->user()->can_access_legalizations || auth()->user()->isAdmin())
                        <a href="{{ route('legalizations.dashboard') }}" class="module-card" aria-label="Buka Modul Layanan Legalisir">
                            <div class="card-visual">
                                <img src="{{ asset('images/modules/legalization.jpg') }}" alt="Ilustrasi Modul Layanan Legalisir" class="card-visual-img" loading="lazy">
                                <div class="card-badge-icon" title="Modul Legalisir">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                                    </svg>
                                </div>
                            </div>

                            <div class="card-content">
                                <h2 class="card-title">Modul Layanan Legalisir</h2>
                                <p class="card-desc">
                                    Pelayanan legalisir ijazah dan transkrip alumni, pencetakan tanda terima resmi, serta monitoring status berkas legalisir.
                                </p>

                                <div class="card-footer">
                                    <span class="card-action-btn">
                                        Buka Modul
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                        </svg>
                                    </span>
                                    <span class="card-status-info">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                                        </svg>
                                        Berkas &amp; Tanda Terima
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endif

                    {{-- ── Card 3: Modul Peminjaman Ruang ── --}}
                    @if (auth()->user()->can_access_room_reservations || auth()->user()->isAdmin())
                        <a href="{{ route('room-reservations.dashboard') }}" class="module-card" aria-label="Buka Modul Peminjaman Ruang">
                            <div class="card-visual">
                                <img src="{{ asset('images/modules/rooms.jpg') }}" alt="Ilustrasi Modul Peminjaman Ruang" class="card-visual-img" loading="lazy">
                                <div class="card-badge-icon" title="Peminjaman Fasilitas Ruang">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.253M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                    </svg>
                                </div>
                            </div>

                            <div class="card-content">
                                <h2 class="card-title">Modul Peminjaman Ruang</h2>
                                <p class="card-desc">
                                    Reservasi ruang sidang, aula seminar, cek kalender ketersediaan jadwal waktu nyata, dan riwayat peminjaman.
                                </p>

                                <div class="card-footer">
                                    <span class="card-action-btn">
                                        Buka Modul
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                        </svg>
                                    </span>
                                    <span class="card-status-info">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                                        </svg>
                                        Jadwal &amp; Reservasi
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endif

                    {{-- ── Card 4: Modul Pengaturan Sistem ── --}}
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="module-card" aria-label="Buka Pengaturan Sistem">
                            <div class="card-visual">
                                <img src="{{ asset('images/modules/admin.jpg') }}" alt="Ilustrasi Pengaturan Sistem" class="card-visual-img" loading="lazy">
                                <div class="card-badge-icon" title="Pengaturan Administrator">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                                    </svg>
                                </div>
                            </div>

                            <div class="card-content">
                                <h2 class="card-title">Pengaturan Sistem</h2>
                                <p class="card-desc">
                                    Manajemen pengguna, konfigurasi hak akses modul, log aktivitas audit, serta pemeliharaan parameter sistem fakultas.
                                </p>

                                <div class="card-footer">
                                    <span class="card-action-btn">
                                        Buka Modul
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                        </svg>
                                    </span>
                                    <span class="card-status-info">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                        </svg>
                                        Kontrol Administrator
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endif

                </main>
            @else
                <div class="no-modules-box">
                    <p>Anda belum memiliki akses ke modul manapun. Silakan hubungi Administrator Sistem untuk verifikasi hak akses.</p>
                </div>
            @endif

            {{-- ── Footer ── --}}
            <footer class="page-footer">
                <p>&copy; {{ date('Y') }} Sistem Informasi Administrasi (SIADMIN) &bull; Fakultas Ekonomi dan Bisnis &bull; Universitas Negeri Jakarta</p>
            </footer>

        </div>
    </div>
</body>

</html>
