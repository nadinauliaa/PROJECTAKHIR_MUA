<?php
// ============================================
// KOMPONEN UI BERSAMA — PROFESSIONAL DASHBOARD
// ============================================

function renderHead($title = 'Dashboard') {
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> — Brilliant Beauty Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* ============================================
           CSS VARIABLES — SOPHISTICATED PALETTE
           ============================================ */
        :root {
            --nav-bg: #1a1410;
            --nav-bg-secondary: #241d16;
            --nav-text: rgba(255,255,255,0.65);
            --nav-text-hover: rgba(255,255,255,0.95);
            --nav-text-active: #fff;
            --nav-border: rgba(196,149,106,0.12);
            --nav-accent: #c4956a;
            --nav-glow: rgba(196,149,106,0.25);

            --bg-body: #f6f3ef;
            --bg-surface: #ffffff;
            --bg-surface-hover: #fdfcfa;
            --bg-sunken: #f0ece6;
            --bg-muted: #f8f6f2;

            --text-heading: #1a1410;
            --text-body: #3d3428;
            --text-secondary: #7a6e62;
            --text-muted: #a89f94;
            --text-inverse: #ffffff;

            --accent-gold: #b8864e;
            --accent-gold-light: #d4a876;
            --accent-gold-pale: #f5e6d0;
            --accent-rose: #c47070;
            --accent-rose-pale: #fce8e8;
            --accent-emerald: #4a8b5c;
            --accent-emerald-pale: #e6f4eb;
            --accent-sky: #5a82a8;
            --accent-sky-pale: #e6eef6;
            --accent-amber: #c49a2e;
            --accent-amber-pale: #fdf5e0;
            --accent-violet: #7c6baa;
            --accent-violet-pale: #f0ecf8;

            --border-light: #e8e2da;
            --border-medium: #d5cdc2;
            --border-focus: var(--accent-gold);

            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            --radius-full: 9999px;

            --shadow-xs: 0 1px 3px rgba(26,20,16,0.04);
            --shadow-sm: 0 2px 8px rgba(26,20,16,0.06);
            --shadow-md: 0 4px 20px rgba(26,20,16,0.07);
            --shadow-lg: 0 8px 40px rgba(26,20,16,0.09);
            --shadow-xl: 0 16px 60px rgba(26,20,16,0.12);
            --shadow-glow: 0 0 30px rgba(184,134,78,0.15);
        }

        /* ============================================
           RESET & BASE
           ============================================ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { font-size: 14px; scroll-behavior: smooth; -webkit-font-smoothing: antialiased; }
        body {
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-body);
            color: var(--text-body);
            min-height: 100vh;
            line-height: 1.6;
            overflow-x: hidden;
        }
        a { color: var(--accent-gold); text-decoration: none; transition: color 0.2s; }
        a:hover { color: var(--accent-gold-light); }
        img { max-width: 100%; display: block; }
        ::selection { background: var(--accent-gold-pale); color: var(--text-heading); }

        /* ============================================
           TOP NAVIGATION BAR
           ============================================ */
        .topnav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            background: var(--nav-bg);
            border-bottom: 1px solid var(--nav-border);
        }
        .topnav-primary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            height: 62px;
        }
        .topnav-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .topnav-logo {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--accent-gold), var(--accent-gold-light));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(184,134,78,0.35);
        }
        .topnav-brand-text h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.15rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: 0.5px;
            line-height: 1.2;
        }
        .topnav-brand-text span {
            font-size: 0.65rem;
            color: var(--nav-text);
            letter-spacing: 2.5px;
            text-transform: uppercase;
            font-weight: 500;
        }

        /* Nav Links */
        .topnav-links {
            display: flex;
            align-items: center;
            gap: 2px;
            height: 100%;
        }
        .topnav-links a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            color: var(--nav-text);
            font-size: 0.82rem;
            font-weight: 500;
            transition: all 0.2s ease;
            position: relative;
            white-space: nowrap;
        }
        .topnav-links a:hover {
            color: var(--nav-text-hover);
            background: rgba(255,255,255,0.05);
        }
        .topnav-links a.active {
            color: var(--nav-text-active);
            background: rgba(196,149,106,0.12);
        }
        .topnav-links a.active::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 16px; right: 16px;
            height: 2px;
            background: var(--nav-accent);
            border-radius: 2px 2px 0 0;
        }
        .topnav-links a i { font-size: 0.9rem; width: 18px; text-align: center; }

        /* Nav Right */
        .topnav-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .topnav-search {
            position: relative;
        }
        .topnav-search input {
            width: 220px;
            padding: 8px 14px 8px 36px;
            border-radius: var(--radius-full);
            border: 1px solid var(--nav-border);
            background: var(--nav-bg-secondary);
            color: #fff;
            font-size: 0.8rem;
            font-family: inherit;
            transition: all 0.25s;
        }
        .topnav-search input::placeholder { color: rgba(255,255,255,0.3); }
        .topnav-search input:focus {
            outline: none;
            border-color: var(--nav-accent);
            background: rgba(255,255,255,0.06);
            width: 280px;
            box-shadow: 0 0 0 3px rgba(196,149,106,0.1);
        }
        .topnav-search i {
            position: absolute;
            left: 13px; top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.3);
            font-size: 0.8rem;
        }
        .topnav-notification {
            position: relative;
            width: 38px; height: 38px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--nav-text);
            cursor: pointer;
            transition: all 0.2s;
        }
        .topnav-notification:hover {
            background: rgba(255,255,255,0.06);
            color: var(--nav-text-hover);
        }
        .topnav-notification .badge-dot {
            position: absolute;
            top: 8px; right: 9px;
            width: 7px; height: 7px;
            background: var(--accent-rose);
            border-radius: 50%;
            border: 1.5px solid var(--nav-bg);
        }
        .topnav-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px 12px 5px 5px;
            border-radius: var(--radius-full);
            cursor: pointer;
            transition: background 0.2s;
        }
        .topnav-user:hover { background: rgba(255,255,255,0.06); }
        .topnav-avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-gold), #e0b98f);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 13px;
            font-weight: 600;
        }
        .topnav-user-info {
            line-height: 1.3;
        }
        .topnav-user-info .name {
            font-size: 0.8rem;
            font-weight: 600;
            color: #fff;
        }
        .topnav-user-info .role {
            font-size: 0.65rem;
            color: var(--nav-text);
        }

        /* Mobile hamburger */
        .topnav-mobile-btn {
            display: none;
            width: 38px; height: 38px;
            border-radius: var(--radius-sm);
            align-items: center;
            justify-content: center;
            color: var(--nav-text);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
        }

        /* Mobile nav overlay */
        .topnav-mobile-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 998;
        }
        .topnav-mobile-overlay.show { display: block; }

        /* ============================================
           MAIN LAYOUT
           ============================================ */
        .app-layout {
            margin-top: 62px;
            min-height: calc(100vh - 62px);
        }

        /* Sub-header / Page header */
        .page-header {
            background: var(--bg-surface);
            border-bottom: 1px solid var(--border-light);
            padding: 20px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .page-header-left {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-heading);
            line-height: 1.2;
        }
        .page-subtitle {
            font-size: 0.8rem;
            color: var(--text-muted);
        }
        .page-subtitle a { color: var(--text-muted); }
        .page-subtitle a:hover { color: var(--accent-gold); }
        .page-header-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        /* Content area */
        .page-content {
            padding: 24px 32px 48px;
        }

        /* ============================================
           SURFACE / CARDS
           ============================================ */
        .surface {
            background: var(--bg-surface);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            transition: box-shadow 0.25s;
        }
        .surface:hover { box-shadow: var(--shadow-md); }
        .surface-head {
            padding: 18px 22px;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .surface-head h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-heading);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .surface-head h2 i { color: var(--accent-gold); font-size: 0.95rem; }
        .surface-body { padding: 22px; }
        .surface-flush { padding: 0; }

        /* ============================================
           STAT CARDS — PREMIUM STYLE
           ============================================ */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }
        .stat-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-lg);
            padding: 22px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-xs);
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
        }
        .stat-card.sc-gold::before { background: linear-gradient(90deg, var(--accent-gold), var(--accent-gold-light)); }
        .stat-card.sc-emerald::before { background: linear-gradient(90deg, var(--accent-emerald), #6aaf7c); }
        .stat-card.sc-sky::before { background: linear-gradient(90deg, var(--accent-sky), #7aa0c2); }
        .stat-card.sc-amber::before { background: linear-gradient(90deg, var(--accent-amber), #dab44e); }
        .stat-card.sc-rose::before { background: linear-gradient(90deg, var(--accent-rose), #d89090); }
        .stat-card.sc-violet::before { background: linear-gradient(90deg, var(--accent-violet), #9a8cc8); }

        .stat-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 14px;
        }
        .stat-icon {
            width: 44px; height: 44px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }
        .sc-gold .stat-icon { background: var(--accent-gold-pale); color: var(--accent-gold); }
        .sc-emerald .stat-icon { background: var(--accent-emerald-pale); color: var(--accent-emerald); }
        .sc-sky .stat-icon { background: var(--accent-sky-pale); color: var(--accent-sky); }
        .sc-amber .stat-icon { background: var(--accent-amber-pale); color: var(--accent-amber); }
        .sc-rose .stat-icon { background: var(--accent-rose-pale); color: var(--accent-rose); }
        .sc-violet .stat-icon { background: var(--accent-violet-pale); color: var(--accent-violet); }

        .stat-trend {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            padding: 3px 8px;
            border-radius: var(--radius-full);
            font-size: 0.7rem;
            font-weight: 600;
        }
        .stat-trend.up { background: var(--accent-emerald-pale); color: var(--accent-emerald); }
        .stat-trend.down { background: var(--accent-rose-pale); color: var(--accent-rose); }
        .stat-trend.neutral { background: var(--bg-sunken); color: var(--text-muted); }

        .stat-value {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-heading);
            line-height: 1.1;
            margin-bottom: 4px;
        }
        .stat-label {
            font-size: 0.78rem;
            color: var(--text-muted);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        /* Mini bar chart in stat card */
        .stat-mini-chart {
            display: flex;
            align-items: flex-end;
            gap: 3px;
            height: 32px;
            margin-top: 12px;
        }
        .stat-mini-bar {
            flex: 1;
            border-radius: 3px 3px 0 0;
            transition: height 0.5s ease;
            min-height: 3px;
        }
        .sc-gold .stat-mini-bar { background: var(--accent-gold-pale); }
        .sc-emerald .stat-mini-bar { background: var(--accent-emerald-pale); }
        .sc-sky .stat-mini-bar { background: var(--accent-sky-pale); }
        .sc-amber .stat-mini-bar { background: var(--accent-amber-pale); }

        /* ============================================
           TABLE — PREMIUM
           ============================================ */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            padding: 11px 16px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            text-align: left;
            border-bottom: 1px solid var(--border-light);
            background: var(--bg-muted);
            white-space: nowrap;
        }
        tbody td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border-light);
            font-size: 0.85rem;
            vertical-align: middle;
            color: var(--text-body);
        }
        tbody tr { transition: background 0.15s; }
        tbody tr:hover { background: var(--bg-muted); }
        tbody tr:last-child td { border-bottom: none; }

        /* ============================================
           BUTTONS — PREMIUM
           ============================================ */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 10px 20px;
            border-radius: var(--radius-sm);
            font-size: 0.82rem;
            font-weight: 600;
            font-family: inherit;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
            line-height: 1.4;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--accent-gold), var(--accent-gold-light));
            color: #fff;
            box-shadow: 0 2px 8px rgba(184,134,78,0.25);
        }
        .btn-primary:hover {
            box-shadow: 0 4px 16px rgba(184,134,78,0.4);
            transform: translateY(-1px);
        }
        .btn-secondary {
            background: var(--bg-sunken);
            color: var(--text-body);
            border: 1px solid var(--border-light);
        }
        .btn-secondary:hover { background: var(--border-light); }
        .btn-success { background: var(--accent-emerald); color: #fff; }
        .btn-success:hover { background: #3f7a50; transform: translateY(-1px); }
        .btn-danger { background: var(--accent-rose); color: #fff; }
        .btn-danger:hover { background: #b06060; transform: translateY(-1px); }
        .btn-warning { background: var(--accent-amber); color: #fff; }
        .btn-warning:hover { background: #b08a28; transform: translateY(-1px); }
        .btn-ghost {
            background: transparent;
            color: var(--text-secondary);
            padding: 8px 12px;
        }
        .btn-ghost:hover { background: var(--bg-sunken); color: var(--text-heading); }
        .btn-sm { padding: 7px 14px; font-size: 0.75rem; border-radius: 6px; }
        .btn-xs { padding: 5px 10px; font-size: 0.7rem; border-radius: 5px; }
        .btn-icon { padding: 8px; width: 36px; height: 36px; }
        .btn-icon.btn-sm { width: 30px; height: 30px; padding: 0; }

        /* ============================================
           FORMS
           ============================================ */
        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }
        .form-label .req { color: var(--accent-rose); margin-left: 2px; }
        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-sm);
            font-size: 0.85rem;
            font-family: inherit;
            color: var(--text-heading);
            background: var(--bg-surface);
            transition: all 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: var(--accent-gold);
            box-shadow: 0 0 0 3px rgba(184,134,78,0.1);
        }
        .form-input::placeholder { color: var(--text-muted); }
        textarea.form-input { resize: vertical; min-height: 80px; }
        select.form-input { cursor: pointer; }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .form-hint { font-size: 0.72rem; color: var(--text-muted); margin-top: 4px; }

        /* ============================================
           BADGES / PILLS
           ============================================ */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: var(--radius-full);
            font-size: 0.72rem;
            font-weight: 600;
            white-space: nowrap;
        }
        .badge-gold { background: var(--accent-gold-pale); color: var(--accent-gold); }
        .badge-emerald { background: var(--accent-emerald-pale); color: var(--accent-emerald); }
        .badge-rose { background: var(--accent-rose-pale); color: var(--accent-rose); }
        .badge-sky { background: var(--accent-sky-pale); color: var(--accent-sky); }
        .badge-amber { background: var(--accent-amber-pale); color: var(--accent-amber); }
        .badge-violet { background: var(--accent-violet-pale); color: var(--accent-violet); }
        .badge-neutral { background: var(--bg-sunken); color: var(--text-muted); }
        .badge-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        /* ============================================
           INFO GRID
           ============================================ */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
        }
        .info-item {
            padding: 14px 16px;
            background: var(--bg-muted);
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-light);
        }
        .info-item .label {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 3px;
        }
        .info-item .value {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-heading);
        }

        /* ============================================
           TOAST NOTIFICATIONS
           ============================================ */
        .toast-stack {
            position: fixed;
            top: 76px; right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }
        .toast {
            pointer-events: auto;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            border-radius: var(--radius-md);
            background: var(--bg-surface);
            border: 1px solid var(--border-light);
            box-shadow: var(--shadow-lg);
            font-size: 0.84rem;
            font-weight: 500;
            color: var(--text-body);
            animation: toastSlide 0.35s cubic-bezier(0.21,1.02,0.73,1) forwards;
            max-width: 400px;
            position: relative;
            overflow: hidden;
        }
        .toast::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 4px;
        }
        .toast.t-success::before { background: var(--accent-emerald); }
        .toast.t-error::before { background: var(--accent-rose); }
        .toast.t-warning::before { background: var(--accent-amber); }
        .toast.t-info::before { background: var(--accent-sky); }
        .toast .t-icon {
            width: 32px; height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            flex-shrink: 0;
        }
        .toast.t-success .t-icon { background: var(--accent-emerald-pale); color: var(--accent-emerald); }
        .toast.t-error .t-icon { background: var(--accent-rose-pale); color: var(--accent-rose); }
        .toast.t-warning .t-icon { background: var(--accent-amber-pale); color: var(--accent-amber); }
        .toast.t-info .t-icon { background: var(--accent-sky-pale); color: var(--accent-sky); }
        .toast.removing { animation: toastOut 0.3s ease forwards; }
        @keyframes toastSlide { from { opacity:0; transform:translateX(40px) scale(0.96); } to { opacity:1; transform:translateX(0) scale(1); } }
        @keyframes toastOut { to { opacity:0; transform:translateX(40px) scale(0.96); } }

        /* ============================================
           MODAL
           ============================================ */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(26,20,16,0.5);
            backdrop-filter: blur(4px);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.show { display: flex; }
        .modal-panel {
            background: var(--bg-surface);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
            max-width: 440px;
            width: 92%;
            animation: modalIn 0.3s cubic-bezier(0.21,1.02,0.73,1);
        }
        .modal-panel-head {
            padding: 22px 24px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .modal-panel-head .modal-icon {
            width: 42px; height: 42px;
            border-radius: 50%;
            background: var(--accent-rose-pale);
            color: var(--accent-rose);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .modal-panel-head h3 { font-size: 1rem; font-weight: 700; color: var(--text-heading); }
        .modal-panel-body { padding: 14px 24px 22px; font-size: 0.88rem; color: var(--text-secondary); line-height: 1.6; }
        .modal-panel-foot {
            padding: 16px 24px;
            border-top: 1px solid var(--border-light);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        @keyframes modalIn { from { opacity:0; transform:scale(0.92) translateY(10px); } to { opacity:1; transform:scale(1) translateY(0); } }

        /* ============================================
           LIGHTBOX
           ============================================ */
        .lightbox {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.88);
            backdrop-filter: blur(8px);
            z-index: 3000;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .lightbox.show { display: flex; }
        .lightbox img {
            max-width: 88%;
            max-height: 88%;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-xl);
            animation: modalIn 0.3s ease;
        }
        .bukti-img {
            max-width: 180px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-light);
            cursor: pointer;
            transition: all 0.2s;
        }
        .bukti-img:hover { transform: scale(1.04); box-shadow: var(--shadow-md); }

        /* ============================================
           EMPTY STATE
           ============================================ */
        .empty-state {
            text-align: center;
            padding: 52px 24px;
            color: var(--text-muted);
        }
        .empty-state .empty-icon {
            width: 64px; height: 64px;
            margin: 0 auto 16px;
            border-radius: 50%;
            background: var(--bg-sunken);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--text-muted);
        }
        .empty-state p { font-size: 0.88rem; }

        /* ============================================
           SEARCH BAR (inline)
           ============================================ */
        .inline-search {
            position: relative;
        }
        .inline-search input {
            width: 260px;
            padding: 9px 14px 9px 36px;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-full);
            font-size: 0.82rem;
            font-family: inherit;
            background: var(--bg-surface);
            color: var(--text-heading);
            transition: all 0.25s;
        }
        .inline-search input:focus { outline: none; border-color: var(--accent-gold); box-shadow: 0 0 0 3px rgba(184,134,78,0.08); width: 320px; }
        .inline-search i { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.82rem; }

        /* ============================================
           CHART AREA (CSS-only bar chart)
           ============================================ */
        .css-chart {
            display: flex;
            align-items: flex-end;
            gap: 6px;
            height: 140px;
            padding: 0 4px;
        }
        .css-chart-bar-wrap {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            height: 100%;
            justify-content: flex-end;
        }
        .css-chart-bar {
            width: 100%;
            max-width: 36px;
            border-radius: 6px 6px 2px 2px;
            transition: height 0.6s cubic-bezier(0.21,1.02,0.73,1);
            position: relative;
            min-height: 4px;
        }
        .css-chart-bar:hover { opacity: 0.8; }
        .css-chart-bar .bar-tooltip {
            display: none;
            position: absolute;
            top: -28px; left: 50%;
            transform: translateX(-50%);
            background: var(--text-heading);
            color: #fff;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.65rem;
            font-weight: 600;
            white-space: nowrap;
        }
        .css-chart-bar:hover .bar-tooltip { display: block; }
        .css-chart-label {
            font-size: 0.65rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* ============================================
           PROGRESS RING (SVG)
           ============================================ */
        .progress-ring-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }
        .progress-ring-text {
            font-size: 0.7rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* ============================================
           ADDON ROW
           ============================================ */
        .addon-row {
            display: grid;
            grid-template-columns: 2fr 1fr auto;
            gap: 10px;
            align-items: end;
            margin-bottom: 10px;
            animation: toastSlide 0.25s ease;
        }

        /* ============================================
           JADWAL ITEM (edit mode)
           ============================================ */
        .jadwal-slot {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 20px;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            margin-bottom: 12px;
            background: var(--bg-surface);
            transition: all 0.2s;
        }
        .jadwal-slot:hover { box-shadow: var(--shadow-sm); border-color: var(--border-medium); }
        .jadwal-edit-form {
            display: none;
            margin: -4px 0 12px;
            padding: 20px;
            background: var(--accent-gold-pale);
            border: 2px dashed var(--accent-gold);
            border-radius: var(--radius-md);
            animation: toastSlide 0.25s ease;
        }

        /* ============================================
           TRANSAKSI CARD
           ============================================ */
        .tx-card {
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            padding: 18px;
            margin-bottom: 12px;
            background: var(--bg-surface);
            transition: all 0.2s;
        }
        .tx-card:hover { box-shadow: var(--shadow-sm); }

        /* ============================================
           FILTER SELECT (inline)
           ============================================ */
        .filter-select {
            padding: 9px 32px 9px 14px;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-full);
            font-size: 0.82rem;
            font-family: inherit;
            background: var(--bg-surface) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23a89f94' viewBox='0 0 16 16'%3E%3Cpath d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E") no-repeat right 12px center;
            color: var(--text-body);
            cursor: pointer;
            appearance: none;
            transition: all 0.2s;
        }
        .filter-select:focus { outline: none; border-color: var(--accent-gold); box-shadow: 0 0 0 3px rgba(184,134,78,0.08); }

        /* ============================================
           CALENDAR MINI (jadwal)
           ============================================ */
        .cal-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
        }
        .cal-head { text-align: center; font-size: 0.65rem; font-weight: 700; color: var(--text-muted); padding: 6px 0; text-transform: uppercase; }
        .cal-day {
            text-align: center;
            padding: 8px 2px;
            border-radius: var(--radius-sm);
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-body);
            cursor: pointer;
            transition: all 0.15s;
            border: 1px solid transparent;
        }
        .cal-day:hover { background: var(--bg-sunken); }
        .cal-day.cal-today { color: var(--accent-gold); font-weight: 700; border-color: var(--accent-gold); }
        .cal-day.cal-active { background: var(--accent-gold); color: #fff; font-weight: 700; box-shadow: 0 2px 8px rgba(184,134,78,0.3); }
        .cal-day.cal-empty { pointer-events: none; }

        .month-pills {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 5px;
        }
        .month-pill {
            padding: 7px 4px;
            border-radius: var(--radius-sm);
            text-align: center;
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--text-secondary);
            background: var(--bg-surface);
            border: 1px solid var(--border-light);
            cursor: pointer;
            transition: all 0.2s;
        }
        .month-pill:hover { background: var(--bg-sunken); border-color: var(--border-medium); }
        .month-pill.mp-active { background: var(--accent-gold); color: #fff; border-color: var(--accent-gold); box-shadow: 0 2px 8px rgba(184,134,78,0.25); }

        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 1024px) {
            .topnav-search { display: none; }
        }
        @media (max-width: 768px) {
            .topnav-links { 
                display: none;
                position: fixed;
                top: 62px; left: 0; right: 0; bottom: 0;
                background: var(--nav-bg);
                flex-direction: column;
                padding: 20px;
                gap: 4px;
                z-index: 999;
            }
            .topnav-links.open { display: flex; }
            .topnav-links a { padding: 14px 16px; font-size: 0.9rem; border-radius: var(--radius-sm); }
            .topnav-links a.active::after { display: none; }
            .topnav-mobile-btn { display: flex; }
            .page-header { padding: 16px 18px; flex-wrap: wrap; }
            .page-content { padding: 18px; }
            .form-row { grid-template-columns: 1fr; }
            .stats-row { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .addon-row { grid-template-columns: 1fr; }
            .topnav-user-info { display: none; }
        }
        @media (max-width: 480px) {
            .stats-row { grid-template-columns: 1fr; }
            .page-header-actions { width: 100%; }
            .page-header-actions .btn { flex: 1; }
        }
.topnav-user{
    position:relative;
}

.topnav-user-btn{
    display:flex;
    align-items:center;
    gap:12px;
    border:none;
    background:rgba(255,255,255,0.08);
    padding:7px 14px;
    border-radius:50px;
    cursor:pointer;
    transition:0.3s;
}

.topnav-user-btn:hover{
    background:rgba(255,255,255,0.15);
}

.topnav-avatar{
    width:42px;
    height:42px;
    border-radius:50%;
    background:linear-gradient(135deg,#d4af37,#f6d365);
    color:white;
    font-weight:bold;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:17px;
}

.topnav-user-info .name{
    font-size:14px;
    font-weight:600;
    color:white;
}

.topnav-user-info .role{
    font-size:12px;
    color:#ddd;
}

.dropdown-menu{
    position:absolute;
    top:65px;
    right:0;
    width:300px;
    background:white;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 15px 35px rgba(0,0,0,0.15);
    display:none;
    z-index:9999;
    animation:fadeDown .3s ease;
}

.dropdown-menu.show{
    display:block;
}

.dropdown-header{
    background:linear-gradient(135deg,#111,#333);
    padding:25px;
    text-align:center;
    color:white;
}

.avatar-large{
    width:75px;
    height:75px;
    border-radius:50%;
    background:linear-gradient(135deg,#d4af37,#f6d365);
    margin:auto;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:28px;
    font-weight:bold;
    margin-bottom:12px;
}

.dropdown-body{
    padding:18px;
}

.info-item{
    display:flex;
    justify-content:space-between;
    padding:10px 0;
    border-bottom:1px solid #eee;
    font-size:14px;
}

.info-item:last-child{
    border-bottom:none;
}

.dropdown-footer{
    padding:18px;
    border-top:1px solid #eee;
}

.menu-btn{
    display:block;
    padding:10px 12px;
    border-radius:10px;
    text-decoration:none;
    color:#333;
    margin-bottom:8px;
    background:#f8f8f8;
    transition:0.3s;
}

.menu-btn:hover{
    background:#ececec;
}

.logout-btn{
    display:block;
    padding:12px;
    background:#ff4d4d;
    color:white;
    text-align:center;
    border-radius:10px;
    text-decoration:none;
    font-weight:600;
}

.logout-btn:hover{
    background:#e60000;
}

@keyframes fadeDown{
    from{
        opacity:0;
        transform:translateY(-10px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}
    </style>
</head>
<body>
<?php
}

// ============================================
// SIDEBAR DIGANTI JADI NAV ATAS
// ============================================
function renderTopNav($active = 'dashboard') {
    $menu = [
        ['dashboard',      'index.php',          'fa-solid fa-grid-2',          'Dashboard'],
        ['booking-manage', 'booking_manage.php', 'fa-solid fa-clipboard-list', 'Kelola Booking'],
        ['booking-tambah', 'booking_tambah.php', 'fa-solid fa-calendar-plus',  'Booking Baru'],
        ['jadwal',         'jadwal.php',         'fa-solid fa-calendar-days',  'Kelola Jadwal'],
        ['portofolio',     'portofolio.php',     'fa-solid fa-image',           'Portofolio'],
        ['pricelist',      'pricelist.php',      'fa-solid fa-tags',           'Pricelist'],
    ];
?>
    <nav class="topnav">
        <div class="topnav-primary">
            <div class="topnav-brand">
                <button class="topnav-mobile-btn" onclick="toggleMobileNav()">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="topnav-logo"><i class="fa-solid fa-spa"></i></div>
                <div class="topnav-brand-text">
                    <h1>Brilliant Beauty</h1>
                    <span>Admin Panel</span>
                </div>
            </div>

            <div class="topnav-links" id="topnavLinks">
                <?php foreach ($menu as $m): ?>
                    <a href="<?= $m[1] ?>" class="<?= $active === $m[0] ? 'active' : '' ?>">
                        <i class="<?= $m[2] ?>"></i>
                        <span><?= $m[3] ?></span>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="topnav-right">
                <div class="topnav-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Cari booking, invoice...">
                </div>
                <div class="topnav-notification" title="Notifikasi">
                    <i class="fa-regular fa-bell"></i>
                    <div class="badge-dot"></div>
                </div>
                <div class="topnav-user">

    <button class="topnav-user-btn" onclick="toggleProfileCard()">
        <div class="topnav-avatar">A</div>

        <div class="topnav-user-info">
            <div class="name">Admin</div>
            <div class="role">Makeup Artist</div>
        </div>
    </button>

    <div class="dropdown-menu" id="profileCard">

        <div class="dropdown-header">
            <div class="avatar-large">A</div>
            <h3>Admin</h3>
            <p>Makeup Artist</p>
        </div>

        <div class="dropdown-body">
            <div class="info-item">
                <span>Email</span>
                <span>admin@gmail.com</span>
            </div>

            <div class="info-item">
                <span>Role</span>
                <span>Administrator</span>
            </div>

            <div class="info-item">
                <span>Status</span>
                <span style="color:green;">Online</span>
            </div>
        </div>

        <div class="dropdown-footer">
            <a href="profile.php" class="menu-btn">👤 Profile Saya</a>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>

    </div>
</div>
            </div>
        </div>
    </nav>
    <div class="topnav-mobile-overlay" id="mobileOverlay" onclick="toggleMobileNav()"></div>
<?php
}

function renderPageHeader($title, $subtitle = '', $actions = '') {
?>
    <div class="page-header">
        <div class="page-header-left">
            <h2 class="page-title"><?= $title ?></h2>
            <?php if ($subtitle): ?>
                <div class="page-subtitle"><?= $subtitle ?></div>
            <?php endif; ?>
        </div>
        <?php if ($actions): ?>
            <div class="page-header-actions"><?= $actions ?></div>
        <?php endif; ?>
    </div>
<?php
}

function renderFooter() {
?>
    <script>
        // Toast
        function showToast(msg, tipe = 'success') {
            const c = document.querySelector('.toast-stack') || (() => {
                const el = document.createElement('div');
                el.className = 'toast-stack';
                document.body.appendChild(el);
                return el;
            })();
            const icons = { success:'fa-circle-check', error:'fa-circle-xmark', warning:'fa-triangle-exclamation', info:'fa-circle-info' };
            const t = document.createElement('div');
            t.className = 'toast t-' + tipe;
            t.innerHTML = '<div class="t-icon"><i class="fa-solid ' + (icons[tipe]||icons.info) + '"></i></div><span>' + msg + '</span>';
            c.appendChild(t);
            setTimeout(() => { t.classList.add('removing'); setTimeout(() => t.remove(), 300); }, 3800);
        }

        <?php if (!empty($_SESSION['flash_msg'])): ?>
            showToast('<?= addslashes($_SESSION['flash_msg']) ?>', '<?= $_SESSION['flash_tipe'] ?? 'success' ?>');
            <?php unset($_SESSION['flash_msg'], $_SESSION['flash_tipe']); ?>
        <?php endif; ?>

        // Modal
        function konfirmasiHapus(url, label) {
            const o = document.getElementById('modalKonfirmasi');
            if (!o) return;
            document.getElementById('modalLabel').textContent = label || 'item ini';
            document.getElementById('btnKonfirmasiYa').onclick = () => window.location.href = url;
            o.classList.add('show');
        }
        function tutupModal() { document.getElementById('modalKonfirmasi').classList.remove('show'); }

        // Lightbox
        function bukaLightbox(src) {
            const lb = document.getElementById('lightbox');
            if (!lb) return;
            lb.querySelector('img').src = src;
            lb.classList.add('show');
        }
        function tutupLightbox() { document.getElementById('lightbox').classList.remove('show'); }

        // Mobile nav
        function toggleMobileNav() {
            document.getElementById('topnavLinks').classList.toggle('open');
            document.getElementById('mobileOverlay').classList.toggle('show');
        }

        // Tutup mobile nav saat klik link
        document.querySelectorAll('.topnav-links a').forEach(a => {
            a.addEventListener('click', () => {
                if (window.innerWidth <= 768) toggleMobileNav();
            });
        });

function toggleProfileCard(){
    document.getElementById("profileCard").classList.toggle("show");
}

window.onclick = function(e){
    if(!e.target.closest('.topnav-user')){
        document.getElementById("profileCard").classList.remove("show");
    }
}


    </script>

    <div class="modal-overlay" id="modalKonfirmasi" onclick="if(event.target===this)tutupModal()">
        <div class="modal-panel">
            <div class="modal-panel-head">
                <div class="modal-icon"><i class="fa-solid fa-trash-can"></i></div>
                <h3>Konfirmasi Hapus</h3>
            </div>
            <div class="modal-panel-body">Apakah kamu yakin ingin menghapus <strong id="modalLabel"></strong>? Tindakan ini tidak dapat dibatalkan.</div>
            <div class="modal-panel-foot">
                <button class="btn btn-secondary" onclick="tutupModal()">Batal</button>
                <button class="btn btn-danger" id="btnKonfirmasiYa"><i class="fa-solid fa-trash-can"></i> Hapus</button>
            </div>
        </div>
    </div>

    <div class="lightbox" id="lightbox" onclick="tutupLightbox()">
        <img src="" alt="Preview">
    </div>
</body>
</html>
<?php
}

// ============================================
// HELPER: Badge Status Booking (pakai class .badge baru)
// ============================================
function badgeStatus($status) {
    $map = [
        'menunggu'     => ['Menunggu',      'badge-amber'],
        'dikonfirmasi' => ['Dikonfirmasi',  'badge-emerald'],
        'dibatalkan'   => ['Dibatalkan',    'badge-rose'],
        'selesai'      => ['Selesai',       'badge-sky'],
    ];
    $d = $map[$status] ?? ['Unknown', 'badge-neutral'];
    return "<span class=\"badge {$d[1]}\"><span class=\"badge-dot\"></span>{$d[0]}</span>";
}

function badgeTransaksi($status) {
    $map = [
        'menunggu'     => ['Menunggu',     'badge-amber'],
        'dikonfirmasi' => ['Dikonfirmasi', 'badge-emerald'],
        'gagal'        => ['Gagal',        'badge-rose'],
    ];
    $d = $map[$status] ?? ['Unknown', 'badge-neutral'];
    return "<span class=\"badge {$d[1]}\"><span class=\"badge-dot\"></span>{$d[0]}</span>";
}
?>