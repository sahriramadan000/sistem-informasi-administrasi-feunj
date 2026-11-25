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
            max-width: 1120px;
            margin: 0 auto;
        }

        /* ── Top Bar / Header Shell ── */
        .top-navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1.35rem;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 100px;
            margin-bottom: 1.75rem;
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
            margin-bottom: 1.5rem;
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
            padding: 4px 12px;
            border-radius: 100px;
            margin-bottom: 0.65rem;
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

        /* ── Quick Access Bar (Terakhir Dibuka - Tanpa Card Putih) ── */
        .quick-access-bar {
            display: none;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 1.15rem;
            padding: 2px 0;
        }

        .quick-access-bar.has-items {
            display: flex;
        }

        .quick-access-label {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-muted);
            margin-right: 2px;
        }

        .quick-access-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 100px;
            font-size: 0.76rem;
            font-weight: 600;
            color: var(--text-secondary);
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(226, 232, 240, 0.9);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .quick-access-pill:hover {
            border-color: var(--border-hover);
            color: var(--brand-primary);
            background: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 3px 10px -2px rgba(234, 88, 12, 0.14);
        }

        /* ── Search, Category Filter & View Mode Toolbar (Menyatu dengan Canvas) ── */
        .module-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 1.35rem;
            padding-bottom: 0.9rem;
            border-bottom: 1px solid rgba(226, 232, 240, 0.7);
        }

        .category-tabs {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
        }

        .category-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 13px;
            border-radius: 100px;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--text-secondary);
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(226, 232, 240, 0.85);
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .category-pill:hover {
            background: #ffffff;
            color: var(--text-main);
            border-color: rgba(203, 213, 225, 0.95);
        }

        .category-pill.active {
            background: #0f172a;
            color: #ffffff;
            border-color: #0f172a;
            box-shadow: 0 3px 10px rgba(15, 23, 42, 0.16);
        }

        .category-count {
            font-size: 10px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 100px;
            background: rgba(148, 163, 184, 0.18);
            color: inherit;
        }

        .category-pill.active .category-count {
            background: rgba(255, 255, 255, 0.2);
        }

        .toolbar-right {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1 1 auto;
            justify-content: flex-end;
            min-width: 240px;
        }

        .search-input-wrap {
            position: relative;
            flex: 1 1 220px;
            max-width: 280px;
        }

        .search-input-wrap svg.search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 15px;
            height: 15px;
            color: var(--text-faint);
            pointer-events: none;
        }

        .module-search-input {
            width: 100%;
            padding: 7px 34px 7px 34px;
            font-size: 0.8rem;
            font-weight: 500;
            font-family: inherit;
            color: var(--text-main);
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 100px;
            outline: none;
            transition: all 0.2s ease;
        }

        .module-search-input:focus {
            background: #ffffff;
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.12);
        }

        .search-kbd {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 10px;
            font-weight: 700;
            color: var(--text-faint);
            background: #f8fafc;
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 5px;
            padding: 1px 5px;
            pointer-events: none;
        }

        .view-toggle {
            display: inline-flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 100px;
            padding: 3px;
            gap: 2px;
            border: 1px solid rgba(226, 232, 240, 0.9);
        }

        .view-toggle-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 28px;
            border: none;
            border-radius: 100px;
            background: transparent;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.18s ease;
        }

        .view-toggle-btn.active {
            background: #0f172a;
            color: #ffffff;
            box-shadow: 0 1px 4px rgba(15, 23, 42, 0.15);
        }

        .view-toggle-btn svg {
            width: 15px;
            height: 15px;
            stroke-width: 2;
        }

        /* ── Cards Grid (Maksimal 3 Kolom di Desktop) ── */
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1.25rem;
            margin-bottom: 2.5rem;
        }

        .modules-grid.is-compact {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.9rem;
        }

        @media (max-width: 1024px) {
            .modules-grid,
            .modules-grid.is-compact {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        /* ── Card Specification (Compact Apple Style) ── */
        .module-card {
            display: flex;
            flex-direction: column;
            background: var(--surface-card);
            border-radius: 22px;
            border: 1px solid var(--border-light);
            padding: 12px;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 10px 24px -6px rgba(15, 23, 42, 0.05);
            transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.28s ease, border-color 0.2s ease;
            position: relative;
            animation: cardEntrance 0.55s cubic-bezier(0.16, 1, 0.3, 1) both;
            outline: none;
        }

        .module-card.is-hidden {
            display: none !important;
        }

        .module-card:nth-child(1) { animation-delay: 0.04s; }
        .module-card:nth-child(2) { animation-delay: 0.08s; }
        .module-card:nth-child(3) { animation-delay: 0.12s; }
        .module-card:nth-child(4) { animation-delay: 0.16s; }
        .module-card:nth-child(5) { animation-delay: 0.20s; }
        .module-card:nth-child(6) { animation-delay: 0.24s; }

        .module-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 14px 30px -8px rgba(15, 23, 42, 0.11), 0 4px 10px rgba(0, 0, 0, 0.03);
            border-color: var(--border-hover);
        }

        .module-card:focus-visible {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.18);
        }

        /* Top Visual Area (Compact Illustration) */
        .card-visual {
            position: relative;
            width: 100%;
            height: 148px;
            background-color: var(--surface-subtle);
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
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

        /* Dark Icon Badge at Top-Right of Visual */
        .card-badge-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 34px;
            height: 34px;
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
            width: 17px;
            height: 17px;
            stroke-width: 2.2;
        }

        /* Card Content Body */
        .card-content {
            padding: 14px 8px 6px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .card-title {
            font-size: 1.06rem;
            font-weight: 700;
            color: var(--text-main);
            letter-spacing: -0.02em;
            margin-bottom: 0.35rem;
            line-height: 1.3;
        }

        .card-desc {
            font-size: 0.82rem;
            color: var(--text-muted);
            line-height: 1.52;
            margin-bottom: 1rem;
            flex-grow: 1;
        }

        /* Card Footer / Action Bar */
        .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 11px;
            border-top: 1px solid rgba(241, 245, 249, 1);
            gap: 10px;
        }

        /* Compact List Mode Overrides */
        .modules-grid.is-compact .module-card {
            flex-direction: row;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            border-radius: 18px;
        }

        .modules-grid.is-compact .card-visual {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            border: 1px solid rgba(234, 88, 12, 0.15);
        }

        .modules-grid.is-compact .card-visual-img {
            display: none;
        }

        .modules-grid.is-compact .card-badge-icon {
            position: static;
            width: 34px;
            height: 34px;
            box-shadow: none;
        }

        .modules-grid.is-compact .card-content {
            padding: 0;
            min-width: 0;
        }

        .modules-grid.is-compact .card-title {
            font-size: 0.96rem;
            margin-bottom: 0.15rem;
        }

        .modules-grid.is-compact .card-desc {
            font-size: 0.78rem;
            margin-bottom: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .modules-grid.is-compact .card-footer {
            display: none;
        }

        .compact-arrow {
            display: none;
            color: var(--text-faint);
            flex-shrink: 0;
            transition: transform 0.2s ease, color 0.2s ease;
        }

        .modules-grid.is-compact .compact-arrow {
            display: inline-flex;
        }

        .modules-grid.is-compact .module-card:hover .compact-arrow {
            color: var(--brand-primary);
            transform: translateX(3px);
        }

        /* Action Button */
        .card-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.78rem;
            font-weight: 600;
            color: #1e293b;
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 1);
            padding: 7px 14px;
            border-radius: 11px;
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

        .empty-search-box {
            display: none;
            text-align: center;
            padding: 3rem 1.5rem;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 24px;
            border: 1px dashed rgba(203, 213, 225, 0.9);
            margin-bottom: 2.5rem;
        }

        .empty-search-box.show {
            display: block;
        }

        .empty-search-btn {
            margin-top: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #ffffff;
            background: #0f172a;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-family: inherit;
            transition: background 0.2s ease;
        }

        .empty-search-btn:hover {
            background: var(--brand-primary);
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

            .modules-grid,
            .modules-grid.is-compact {
                grid-template-columns: 1fr;
            }

            .toolbar-right {
                width: 100%;
            }

            .search-input-wrap {
                max-width: none;
            }

            .search-kbd {
                display: none;
            }

            .card-visual {
                height: 160px;
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
                $hasAny = true; // Modul Tools Administrasi tersedia untuk seluruh pengguna aktif
            @endphp

            @if ($hasAny)
                {{-- ── Quick Access Bar (Terakhir Dibuka) ── --}}
                <div id="quickAccessBar" class="quick-access-bar" aria-label="Modul terakhir dibuka">
                    <span class="quick-access-label">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Terakhir Dibuka:
                    </span>
                    <div id="quickAccessItems" style="display:flex; flex-wrap:wrap; gap:6px;"></div>
                </div>

                {{-- ── Toolbar: Kategori, Pencarian & Mode Tampilan ── --}}
                <div class="module-toolbar">
                    <div class="category-tabs" role="tablist" aria-label="Filter kategori modul">
                        <button type="button" class="category-pill active" data-filter="all">
                            Semua <span class="category-count" data-count-for="all">0</span>
                        </button>
                        <button type="button" class="category-pill" data-filter="persuratan">
                            Persuratan &amp; Arsip <span class="category-count" data-count-for="persuratan">0</span>
                        </button>
                        <button type="button" class="category-pill" data-filter="layanan">
                            Layanan &amp; Alumni <span class="category-count" data-count-for="layanan">0</span>
                        </button>
                        <button type="button" class="category-pill" data-filter="fasilitas">
                            Fasilitas <span class="category-count" data-count-for="fasilitas">0</span>
                        </button>
                        <button type="button" class="category-pill" data-filter="utilitas">
                            Utilitas <span class="category-count" data-count-for="utilitas">0</span>
                        </button>
                        <button type="button" class="category-pill" data-filter="sistem">
                            Sistem <span class="category-count" data-count-for="sistem">0</span>
                        </button>
                    </div>

                    <div class="toolbar-right">
                        <div class="search-input-wrap">
                            <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                            <input type="text" id="moduleSearchInput" class="module-search-input" placeholder="Cari modul layanan..." autocomplete="off">
                            <span class="search-kbd">/</span>
                        </div>

                        <div class="view-toggle" role="group" aria-label="Mode tampilan modul">
                            <button type="button" id="viewGridBtn" class="view-toggle-btn active" title="Tampilan Kartu (Grid)" aria-label="Tampilan Kartu">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                                </svg>
                            </button>
                            <button type="button" id="viewCompactBtn" class="view-toggle-btn" title="Tampilan Ringkas (Compact)" aria-label="Tampilan Ringkas">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <main id="modulesGrid" class="modules-grid">

                    {{-- ── Card 1: Modul Persuratan & Arsip ── --}}
                    @if (auth()->user()->can_access_letters || auth()->user()->isAdmin())
                        <a href="{{ route('dashboard') }}" class="module-card" data-module-id="letters" data-category="persuratan" data-keywords="surat penomoran agenda disposisi arsip dinas" aria-label="Buka Modul Persuratan & Arsip">
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
                            <span class="compact-arrow" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </span>
                        </a>
                    @endif

                    {{-- ── Card 2: Modul Arsip Digital (Read-Only Portal) ── --}}
                    @if (auth()->user()->can_access_archives || auth()->user()->isAdmin())
                        <a href="{{ route('archives.index') }}" class="module-card" data-module-id="archives" data-category="persuratan" data-keywords="arsip digital google drive dokumen pdf pratinjau" aria-label="Buka Modul Arsip Digital">
                            <div class="card-visual">
                                <img src="{{ asset('images/modules/archives.jpg') . '?v=' . (file_exists(public_path('images/modules/archives.jpg')) ? filemtime(public_path('images/modules/archives.jpg')) : time()) }}" alt="Ilustrasi Modul Arsip Digital" class="card-visual-img" loading="lazy">
                                <div class="card-badge-icon" title="Modul Arsip Digital">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                    </svg>
                                </div>
                            </div>

                            <div class="card-content">
                                <h2 class="card-title">Modul Arsip Digital</h2>
                                <p class="card-desc">
                                    Pusat penelusuran dokumen surat terarsip di Google Drive, penjelajahan berkas tervalidasi, dan pratinjau surat resmi fakultas.
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
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                                        </svg>
                                        E-Arsip &amp; Pratinjau
                                    </span>
                                </div>
                            </div>
                            <span class="compact-arrow" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </span>
                        </a>
                    @endif

                    {{-- ── Card 2: Modul Layanan Legalisir ── --}}
                    @if (auth()->user()->can_access_legalizations || auth()->user()->isAdmin())
                        <a href="{{ route('legalizations.dashboard') }}" class="module-card" data-module-id="legalizations" data-category="layanan" data-keywords="legalisir ijazah transkrip alumni rekon keuangan tanda terima" aria-label="Buka Modul Layanan Legalisir">
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
                            <span class="compact-arrow" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </span>
                        </a>
                    @endif

                    {{-- ── Card 3: Modul Peminjaman Ruang ── --}}
                    @if (auth()->user()->can_access_room_reservations || auth()->user()->isAdmin())
                        <a href="{{ route('room-reservations.dashboard') }}" class="module-card" data-module-id="room-reservations" data-category="fasilitas" data-keywords="ruang sidang aula seminar peminjaman reservasi jadwal fasilitas" aria-label="Buka Modul Peminjaman Ruang">
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
                            <span class="compact-arrow" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </span>
                        </a>
                    @endif

                    {{-- ── Card: Modul Tools Administrasi ── --}}
                    <a href="{{ route('tools.index') }}" class="module-card" data-module-id="tools" data-category="utilitas" data-keywords="tools pdf jpg png merge split compress watermark qr code barcode custom logo template terbilang helper utilitas" aria-label="Buka Modul Tools Administrasi">
                        <div class="card-visual">
                            <img src="{{ asset('images/modules/tools.jpg') . '?v=' . (file_exists(public_path('images/modules/tools.jpg')) ? filemtime(public_path('images/modules/tools.jpg')) : time()) }}" alt="Ilustrasi Modul Tools Administrasi" class="card-visual-img" loading="lazy">
                            <div class="card-badge-icon" title="Tools Administrasi">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
                                </svg>
                            </div>
                        </div>

                        <div class="card-content">
                            <h2 class="card-title">Tools Administrasi</h2>
                            <p class="card-desc">
                                QR Code &amp; Barcode custom logo beserta template desain, konverter PDF ke JPG/PNG, penggabung/pemisah/kompres PDF, watermark, &amp; helper admin.
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
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                                    </svg>
                                    PDF, QR &amp; Helper
                                </span>
                            </div>
                        </div>
                        <span class="compact-arrow" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </span>
                    </a>

                    {{-- ── Card 4: Modul Pengaturan Sistem ── --}}
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="module-card" data-module-id="admin" data-category="sistem" data-keywords="pengaturan sistem admin user pengguna hak akses audit log" aria-label="Buka Pengaturan Sistem">
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
                            <span class="compact-arrow" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </span>
                        </a>
                    @endif

                </main>

                <div id="emptySearchBox" class="empty-search-box">
                    <p style="font-weight:700; color:var(--text-main); font-size:0.95rem;">Modul tidak ditemukan</p>
                    <p style="color:var(--text-muted); font-size:0.83rem; margin-top:4px;">Coba kata kunci lain atau ubah filter kategori.</p>
                    <button type="button" id="resetFilterBtn" class="empty-search-btn">Reset Filter</button>
                </div>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const grid = document.getElementById('modulesGrid');
            if (!grid) return;

            const cards = Array.from(grid.querySelectorAll('.module-card'));
            const searchInput = document.getElementById('moduleSearchInput');
            const categoryTabs = Array.from(document.querySelectorAll('.category-pill'));
            const emptyBox = document.getElementById('emptySearchBox');
            const resetBtn = document.getElementById('resetFilterBtn');
            const viewGridBtn = document.getElementById('viewGridBtn');
            const viewCompactBtn = document.getElementById('viewCompactBtn');
            const quickBar = document.getElementById('quickAccessBar');
            const quickItems = document.getElementById('quickAccessItems');

            let activeCategory = 'all';

            // 1. Hitung jumlah modul per kategori & sembunyikan tab yang kosong
            const counts = { all: cards.length };
            cards.forEach((card) => {
                const cat = card.dataset.category || 'other';
                counts[cat] = (counts[cat] || 0) + 1;
            });

            categoryTabs.forEach((tab) => {
                const filter = tab.dataset.filter;
                const countEl = tab.querySelector('[data-count-for]');
                const count = counts[filter] || 0;
                if (countEl) countEl.textContent = count;
                if (filter !== 'all' && count === 0) {
                    tab.style.display = 'none';
                }
            });

            // 2. Fungsi filter kategori + pencarian real-time
            function applyFilters() {
                const query = (searchInput?.value || '').trim().toLowerCase();
                let visibleCount = 0;

                cards.forEach((card) => {
                    const cat = card.dataset.category || '';
                    const title = (card.querySelector('.card-title')?.textContent || '').toLowerCase();
                    const desc = (card.querySelector('.card-desc')?.textContent || '').toLowerCase();
                    const keywords = (card.dataset.keywords || '').toLowerCase();

                    const matchesCat = activeCategory === 'all' || cat === activeCategory;
                    const matchesQuery = !query || title.includes(query) || desc.includes(query) || keywords.includes(query);

                    if (matchesCat && matchesQuery) {
                        card.classList.remove('is-hidden');
                        visibleCount++;
                    } else {
                        card.classList.add('is-hidden');
                    }
                });

                if (emptyBox) {
                    emptyBox.classList.toggle('show', visibleCount === 0);
                }
            }

            categoryTabs.forEach((tab) => {
                tab.addEventListener('click', function () {
                    categoryTabs.forEach((t) => t.classList.remove('active'));
                    this.classList.add('active');
                    activeCategory = this.dataset.filter || 'all';
                    applyFilters();
                });
            });

            if (searchInput) {
                searchInput.addEventListener('input', applyFilters);
            }

            if (resetBtn) {
                resetBtn.addEventListener('click', function () {
                    if (searchInput) searchInput.value = '';
                    activeCategory = 'all';
                    categoryTabs.forEach((t) => t.classList.toggle('active', t.dataset.filter === 'all'));
                    applyFilters();
                });
            }

            // Keyboard shortcut '/' untuk fokus pencarian, 'Escape' untuk reset input
            window.addEventListener('keydown', function (e) {
                if (e.key === '/' && document.activeElement !== searchInput && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
                    e.preventDefault();
                    searchInput?.focus();
                } else if (e.key === 'Escape' && document.activeElement === searchInput) {
                    searchInput.value = '';
                    searchInput.blur();
                    applyFilters();
                }
            });

            // 3. Toggle Mode Tampilan (Grid vs Ringkas)
            const VIEW_KEY = 'siadmin_module_view_mode';
            function setViewMode(mode) {
                const isCompact = mode === 'compact';
                grid.classList.toggle('is-compact', isCompact);
                viewGridBtn?.classList.toggle('active', !isCompact);
                viewCompactBtn?.classList.toggle('active', isCompact);
                try {
                    localStorage.setItem(VIEW_KEY, mode);
                } catch (err) {}
            }

            try {
                const savedView = localStorage.getItem(VIEW_KEY);
                if (savedView === 'compact') setViewMode('compact');
            } catch (err) {}

            viewGridBtn?.addEventListener('click', () => setViewMode('grid'));
            viewCompactBtn?.addEventListener('click', () => setViewMode('compact'));

            // 4. Quick Access: Simpan & tampilkan 3 modul terakhir dibuka (hanya yang masih punya akses)
            const RECENT_KEY = 'siadmin_recent_modules';
            const cardMap = new Map();
            cards.forEach((card) => {
                const id = card.dataset.moduleId;
                if (id) {
                    cardMap.set(id, {
                        id,
                        href: card.getAttribute('href'),
                        title: (card.querySelector('.card-title')?.textContent || '').trim(),
                    });

                    card.addEventListener('click', function () {
                        try {
                            const existing = JSON.parse(localStorage.getItem(RECENT_KEY) || '[]');
                            const updated = [id, ...existing.filter((item) => item !== id)].slice(0, 3);
                            localStorage.setItem(RECENT_KEY, JSON.stringify(updated));
                        } catch (err) {}
                    });
                }
            });

            try {
                const recentIds = JSON.parse(localStorage.getItem(RECENT_KEY) || '[]');
                const validRecent = recentIds.map((id) => cardMap.get(id)).filter(Boolean);
                if (validRecent.length > 0 && quickBar && quickItems) {
                    quickItems.innerHTML = '';
                    validRecent.forEach((mod) => {
                        const pill = document.createElement('a');
                        pill.href = mod.href;
                        pill.className = 'quick-access-pill';
                        pill.textContent = mod.title;
                        quickItems.appendChild(pill);
                    });
                    quickBar.classList.add('has-items');
                }
            } catch (err) {}
        });
    </script>
</body>

</html>
