<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Modul - SIADMIN FEB</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Lora:ital,wght@0,400;0,600;1,400&display=swap"
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
            /* ── Brand: Orange (FEB UNJ) ── */
            --brand: #c0510a;
            --brand-light: #e8681a;
            --brand-mid: #f07d34;
            --brand-50: #fff3eb;
            --brand-100: #fde2cc;

            /* ── Accent: Warm Amber (secondary modul) ── */
            --accent: #a8490b;
            --accent-light: #d45f18;
            --accent-50: #fff8f3;

            /* ── Neutral ── */
            --surface: #ffffff;
            --surface-2: #faf8f6;
            --border: rgba(0, 0, 0, 0.07);
            --border-hover: rgba(0, 0, 0, 0.14);
            --text-primary: #1a0f06;
            --text-secondary: #6b5040;
            --text-muted: #a8947f;

            --radius-lg: 20px;
            --radius-xl: 28px;
            --shadow-card: 0 1px 3px rgba(192, 81, 10, 0.06), 0 4px 16px rgba(192, 81, 10, 0.07);
            --shadow-hover: 0 8px 32px rgba(192, 81, 10, 0.16), 0 2px 8px rgba(192, 81, 10, 0.08);
        }

        html,
        body {
            height: 100%;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--surface-2);
            color: var(--text-primary);
        }

        /* Subtle warm-toned dot grid */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                radial-gradient(circle, rgba(192, 81, 10, 0.09) 1px, transparent 1px);
            background-size: 36px 36px;
            pointer-events: none;
            z-index: 0;
        }

        /* Warm glow top-center */
        body::after {
            content: '';
            position: fixed;
            top: -120px;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 400px;
            background: radial-gradient(ellipse, rgba(232, 104, 26, 0.10) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        .page-wrapper {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem;
        }

        .container {
            width: 100%;
            max-width: 820px;
        }

        /* ── Header ── */
        .header {
            text-align: center;
            margin-bottom: 3rem;
            animation: fadeUp 0.5s ease both;
        }

        .logo-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .logo-wrap img {
            width: 100px;
            height: 100px;
            object-fit: contain;
        }

        .header-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--brand);
            background: var(--brand-50);
            border: 1px solid var(--brand-100);
            padding: 5px 14px;
            border-radius: 100px;
            margin-bottom: 1rem;
        }

        .header-eyebrow::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--brand-mid);
        }

        .header h1 {
            font-size: clamp(1.6rem, 4vw, 2.1rem);
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1.2;
            letter-spacing: -0.02em;
        }

        .header p {
            margin-top: 0.5rem;
            font-size: 0.93rem;
            color: var(--text-secondary);
        }

        /* ── User Badge ── */
        .user-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--surface);
            border: 1px solid var(--brand-100);
            border-radius: 100px;
            padding: 6px 14px 6px 8px;
            margin-top: 1.25rem;
            font-size: 0.82rem;
            color: var(--text-secondary);
            box-shadow: 0 1px 4px rgba(192, 81, 10, 0.07);
        }

        .user-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-mid) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
        }

        /* ── Module Grid ── */
        .modules-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1.25rem;
            margin-bottom: 2.5rem;
        }

        /* ── Module Card ── */
        .module-card {
            flex: 1 1 300px;
            max-width: 380px;
            background: var(--surface);
            border-radius: var(--radius-xl);
            border: 1px solid var(--border);
            padding: 2rem 2rem 1.75rem;
            text-decoration: none;
            color: inherit;
            box-shadow: var(--shadow-card);
            transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.25s ease, border-color 0.2s ease;
            position: relative;
            overflow: hidden;
            animation: fadeUp 0.5s ease both;
        }

        .module-card:nth-child(2) {
            animation-delay: 0.08s;
        }

        .module-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .module-card--orange::before {
            background: radial-gradient(ellipse at top left, rgba(232, 104, 26, 0.07) 0%, transparent 65%);
        }

        .module-card--amber::before {
            background: radial-gradient(ellipse at top left, rgba(168, 73, 11, 0.07) 0%, transparent 65%);
        }

        .module-card:hover {
            transform: translateY(-4px) scale(1.01);
            box-shadow: var(--shadow-hover);
        }

        .module-card:hover::before {
            opacity: 1;
        }

        .module-card--orange:hover {
            border-color: rgba(232, 104, 26, 0.28);
        }

        .module-card--amber:hover {
            border-color: rgba(168, 73, 11, 0.28);
        }

        .module-card--blue::before {
            background: radial-gradient(ellipse at top left, rgba(59, 130, 246, 0.07) 0%, transparent 65%);
        }

        .module-card--blue:hover {
            border-color: rgba(59, 130, 246, 0.28);
        }

        /* Decorative serif number */
        .card-number {
            position: absolute;
            top: 1.5rem;
            right: 1.75rem;
            font-family: 'Lora', serif;
            font-size: 4.5rem;
            font-weight: 600;
            font-style: italic;
            line-height: 1;
            opacity: 0.045;
            user-select: none;
            color: var(--brand);
            transition: opacity 0.25s;
        }

        .module-card:hover .card-number {
            opacity: 0.08;
        }

        /* Icon */
        .card-icon-wrap {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .module-card:hover .card-icon-wrap {
            transform: scale(1.1) rotate(-4deg);
        }

        .card-icon-wrap--orange {
            background: var(--brand-50);
            color: var(--brand);
        }

        .card-icon-wrap--amber {
            background: #fff5ee;
            color: var(--accent);
        }

        .card-icon-wrap--blue {
            background: #eff6ff;
            color: #2563eb;
        }

        .card-icon-wrap svg {
            width: 28px;
            height: 28px;
            stroke-width: 1.75;
        }

        /* Tag */
        .card-tag {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 100px;
            margin-bottom: 0.65rem;
        }

        .card-tag--orange {
            background: var(--brand-50);
            color: var(--brand);
            border: 1px solid var(--brand-100);
        }

        .card-tag--amber {
            background: #fff5ee;
            color: var(--accent);
            border: 1px solid #fddec8;
        }

        .card-tag--blue {
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
        }

        /* Text */
        .card-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            letter-spacing: -0.01em;
        }

        .card-desc {
            font-size: 0.865rem;
            color: var(--text-secondary);
            line-height: 1.65;
        }

        /* Footer */
        .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1.75rem;
            padding-top: 1.25rem;
            border-top: 1px solid var(--border);
        }

        .card-cta {
            font-size: 0.82rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: gap 0.2s ease;
        }

        .module-card:hover .card-cta {
            gap: 10px;
        }

        .card-cta--orange {
            color: var(--brand);
        }

        .card-cta--amber {
            color: var(--accent);
        }

        .card-cta--blue {
            color: #2563eb;
        }

        .card-cta svg {
            width: 15px;
            height: 15px;
            stroke-width: 2.5;
        }

        .card-feature-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .card-feature {
            font-size: 10px;
            font-weight: 500;
            color: var(--text-muted);
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 3px 8px;
        }

        /* ── Logout ── */
        .logout-wrap {
            text-align: center;
            animation: fadeUp 0.5s 0.2s ease both;
        }

        .logout-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 0.83rem;
            font-weight: 500;
            color: var(--text-muted);
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px 16px;
            border-radius: 100px;
            transition: color 0.2s, background 0.2s;
            font-family: inherit;
        }

        .logout-btn:hover {
            color: var(--brand);
            background: var(--brand-50);
        }

        .logout-btn svg {
            width: 14px;
            height: 14px;
            stroke-width: 2;
        }

        /* ── Empty state ── */
        .no-modules {
            text-align: center;
            padding: 3rem 2rem;
            background: var(--surface);
            border-radius: var(--radius-xl);
            border: 1px solid var(--border);
        }

        /* ── Animations ── */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ── Focus ── */
        .module-card:focus-visible {
            outline: 2px solid var(--brand-mid);
            outline-offset: 4px;
        }

        .logout-btn:focus-visible {
            outline: 2px solid var(--brand-mid);
            outline-offset: 2px;
        }

        /* ── Responsive ── */
        @media (max-width: 520px) {
            .modules-grid {
                grid-template-columns: 1fr;
            }

            .card-footer {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }
        }
    </style>
</head>

<body>
    <div class="page-wrapper">
        <div class="container">

            {{-- ── Header ── --}}
            <div class="header">
                <div class="logo-wrap">
                    <img src="{{ asset('logo-unj.png') }}" alt="Logo UNJ">
                </div>
                <h1>Portal Layanan Terpadu</h1>
                <p>Akses cepat dan terpusat ke seluruh layanan Sistem Informasi Administrasi (SIADMIN) Fakultas Ekonomi
                    UNJ.</p>

                {{-- User badge --}}
                <div class="user-badge">
                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                    </div>
                    <span>{{ auth()->user()->name ?? 'Pengguna' }}</span>
                </div>
            </div>

            {{-- ── Module Cards ── --}}
            @php
                $hasAny =
                    auth()->user()->can_access_letters ||
                    auth()->user()->can_access_legalizations ||
                    auth()->user()->isAdmin();
            @endphp

            @if ($hasAny)
                <div class="modules-grid">

                    {{-- Modul Surat --}}
                    @if (auth()->user()->can_access_letters)
                        <a href="{{ route('dashboard') }}" class="module-card module-card--orange">
                            <span class="card-number" aria-hidden="true">01</span>

                            <div class="card-icon-wrap card-icon-wrap--orange" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                            </div>

                            <span class="card-tag card-tag--orange">Persuratan</span>
                            <h2 class="card-title">Modul Surat &amp; Arsip</h2>
                            <p class="card-desc">Penomoran surat, klasifikasi, jenis surat, dan pengelolaan arsip
                                dokumen administrasi fakultas.</p>

                            <div class="card-footer">
                                <div class="card-feature-list" aria-label="Fitur tersedia">
                                    <span class="card-feature">Penomoran</span>
                                    <span class="card-feature">Klasifikasi</span>
                                    <span class="card-feature">Arsip</span>
                                </div>
                                <span class="card-cta card-cta--orange" aria-hidden="true">
                                    Buka Modul
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                    </svg>
                                </span>
                            </div>
                        </a>
                    @endif

                    {{-- Modul Legalisir --}}
                    @if (auth()->user()->can_access_legalizations)
                        <a href="{{ route('legalizations.dashboard') }}" class="module-card module-card--amber">
                            <span class="card-number" aria-hidden="true">02</span>

                            <div class="card-icon-wrap card-icon-wrap--amber" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                                </svg>
                            </div>

                            <span class="card-tag card-tag--amber">Legalisir</span>
                            <h2 class="card-title">Modul Layanan Legalisir</h2>
                            <p class="card-desc">Layanan legalisir alumni, pembuatan tanda terima, dan pelaporan
                                penerimaan berkas legalisir.</p>

                            <div class="card-footer">
                                <div class="card-feature-list" aria-label="Fitur tersedia">
                                    <span class="card-feature">Alumni</span>
                                    <span class="card-feature">Tanda Terima</span>
                                    <span class="card-feature">Laporan</span>
                                </div>
                                <span class="card-cta card-cta--amber" aria-hidden="true">
                                    Buka Modul
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                    </svg>
                                </span>
                            </div>
                        </a>
                    @endif

                    {{-- Modul Pengaturan Sistem --}}
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="module-card module-card--amber">
                            <span class="card-number" aria-hidden="true">03</span>

                            <div class="card-icon-wrap card-icon-wrap--amber" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>

                            <span class="card-tag card-tag--amber">Sistem</span>
                            <h2 class="card-title">Pengaturan Sistem</h2>
                            <p class="card-desc">Manajemen pengguna, pengaturan akses, dan pemantauan aktivitas.</p>

                            <div class="card-footer">
                                <div class="card-feature-list" aria-label="Fitur tersedia">
                                    <span class="card-feature">Pengguna</span>
                                    <span class="card-feature">Logs</span>
                                </div>
                                <span class="card-cta card-cta--amber" aria-hidden="true">
                                    Buka modul
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                    </svg>
                                </span>
                            </div>
                        </a>
                    @endif

                </div>
            @else
                <div class="no-modules">
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">Anda belum memiliki akses ke modul
                        apapun. Hubungi administrator sistem.</p>
                </div>
            @endif

            {{-- ── Logout ── --}}
            <div class="logout-wrap">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                        Keluar dari Sistem
                    </button>
                </form>
            </div>

        </div>
    </div>
</body>

</html>
