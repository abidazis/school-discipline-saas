<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'School Discipline') }} - {{ $title ?? 'Dashboard' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* ============================================
           CSS RESET & BASE
           ============================================ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #4f46e5;
            --primary-light: #eef2ff;
            --primary-dark: #4338ca;
            --success: #16a34a;
            --warning: #d97706;
            --danger: #ef4444;
            --info: #0891b2;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --sidebar-width: 260px;
            --header-height: 64px;
            --font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        html, body {
            min-height: 100vh;
            font-family: var(--font-family);
            font-size: 14px;
            color: var(--gray-700);
            background: var(--gray-100);
            line-height: 1.5;
        }

        a { text-decoration: none; color: inherit; }
        button { font-family: inherit; cursor: pointer; }

        /* ============================================
           LAYOUT STRUCTURE
           ============================================ */
        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* ============================================
           SIDEBAR
           ============================================ */
        .sidebar {
            width: var(--sidebar-width);
            min-width: var(--sidebar-width);
            background: var(--gray-800);
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        /* Sidebar Brand */
        .sidebar-brand {
            height: var(--header-height);
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 16px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-brand-icon {
            width: 36px;
            height: 36px;
            background: var(--primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
            flex-shrink: 0;
        }

        .sidebar-brand-text { display: flex; flex-direction: column; }

        .sidebar-brand-name {
            font-weight: 600;
            font-size: 14px;
            color: #fff;
            line-height: 1.2;
        }

        .sidebar-brand-desc {
            font-size: 11px;
            color: var(--gray-400);
        }

        /* Sidebar Navigation */
        .sidebar-nav {
            flex: 1;
            padding: 12px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }

        .sidebar-section { margin-bottom: 4px; }

        /* Menu Link (Single) */
        .menu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            font-size: 14px;
            font-weight: 500;
            color: var(--gray-400);
            border-radius: 8px;
            transition: all 0.15s;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .menu-link:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }

        .menu-link.active {
            background: var(--primary);
            color: #fff;
        }

        .menu-link i { font-size: 18px; width: 20px; text-align: center; }

        /* Section Toggle (Collapsible) */
        .section-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 10px 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--gray-500);
            background: none;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.15s;
        }

        .section-toggle:hover {
            background: rgba(255,255,255,0.05);
            color: var(--gray-300);
        }

        .section-toggle-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-toggle-left i { font-size: 16px; width: 18px; text-align: center; }

        .section-chevron {
            width: 16px;
            height: 16px;
            transition: transform 0.2s;
            color: var(--gray-500);
        }

        .section-toggle.open { color: var(--gray-300); }
        .section-toggle.open .section-chevron { transform: rotate(90deg); }

        /* Submenu */
        .submenu {
            padding-left: 20px;
            margin-top: 2px;
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.3s ease;
        }

        .submenu.open { max-height: 500px; }

        .submenu-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 500;
            color: var(--gray-400);
            border-radius: 6px;
            transition: all 0.15s;
        }

        .submenu-link:hover {
            background: rgba(255,255,255,0.08);
            color: #fff;
        }

        .submenu-link.active {
            background: rgba(79, 70, 229, 0.3);
            color: #fff;
        }

        .submenu-link i { font-size: 16px; width: 18px; text-align: center; }

        /* Sidebar Footer */
        .sidebar-footer {
            padding: 12px;
            border-top: 1px solid rgba(255,255,255,0.1);
            background: rgba(0,0,0,0.2);
        }

        .school-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
            margin-bottom: 8px;
        }

        .school-icon {
            width: 32px;
            height: 32px;
            background: var(--primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 14px;
            flex-shrink: 0;
        }

        .school-name {
            font-size: 12px;
            font-weight: 600;
            color: #fff;
            line-height: 1.3;
        }

        .school-role {
            font-size: 11px;
            color: var(--gray-400);
            text-transform: capitalize;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            flex-shrink: 0;
        }

        .user-name {
            flex: 1;
            font-size: 13px;
            font-weight: 500;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .logout-btn {
            padding: 6px;
            color: var(--gray-400);
            border-radius: 6px;
            background: none;
            border: none;
            cursor: pointer;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logout-btn:hover {
            color: var(--danger);
            background: rgba(239, 68, 68, 0.1);
        }

        /* ============================================
           MAIN CONTENT
           ============================================ */
        .main {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header */
        .header {
            height: var(--header-height);
            background: #fff;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--gray-800);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Header Buttons */
        .header-btn {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-500);
            border-radius: 8px;
            background: none;
            border: none;
            cursor: pointer;
            transition: all 0.15s;
            position: relative;
        }

        .header-btn:hover {
            background: var(--gray-100);
            color: var(--gray-700);
        }

        .header-btn i { font-size: 20px; }

        .notif-dot {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 8px;
            height: 8px;
            background: var(--danger);
            border-radius: 50%;
            border: 2px solid #fff;
        }

        /* Dropdown */
        .dropdown { position: relative; }

        .dropdown-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            color: var(--gray-600);
            border-radius: 8px;
            background: none;
            border: 1px solid var(--gray-200);
            cursor: pointer;
            transition: all 0.15s;
            font-size: 14px;
        }

        .dropdown-btn:hover {
            background: var(--gray-50);
            border-color: var(--gray-300);
        }

        .dropdown-btn .user-avatar {
            width: 28px;
            height: 28px;
            font-size: 12px;
        }

        .dropdown-btn span { font-weight: 500; }

        .dropdown-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            width: 220px;
            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12);
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s ease;
        }

        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-header {
            padding: 12px 16px;
            border-bottom: 1px solid var(--gray-100);
        }

        .dropdown-header-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--gray-800);
        }

        .dropdown-header-email {
            font-size: 12px;
            color: var(--gray-400);
            margin-top: 2px;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            font-size: 14px;
            color: var(--gray-600);
            transition: all 0.15s;
            border: none;
            background: none;
            width: 100%;
            cursor: pointer;
            text-align: left;
        }

        .dropdown-item:hover {
            background: var(--gray-50);
            color: var(--gray-800);
        }

        .dropdown-item.danger {
            color: var(--danger);
        }

        .dropdown-item.danger:hover {
            background: #fef2f2;
        }

        .dropdown-item i { font-size: 16px; width: 18px; color: var(--gray-400); }
        .dropdown-item.danger i { color: var(--danger); }

        .dropdown-divider {
            border-top: 1px solid var(--gray-100);
            margin: 4px 0;
        }

        /* Main Content Area */
        .main-area {
            flex: 1;
            padding: 24px;
        }

        /* ============================================
           MOBILE OVERLAY
           ============================================ */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main {
                margin-left: 0;
            }

            .header-btn-menu {
                display: flex !important;
            }
        }

        @media (max-width: 640px) {
            .header {
                padding: 0 16px;
            }

            .main-area {
                padding: 16px;
            }

            .header-title {
                font-size: 16px;
            }

            .dropdown-btn span {
                display: none;
            }
        }

        /* ============================================
           ALPINE.JS HELPERS
           ============================================ */
        [x-cloak] { display: none !important; }

        /* ============================================
           PAGE COMPONENTS - Dashboard & Cards
           ============================================ */

        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 16px;
            padding: 24px 28px;
            color: white;
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 24px;
        }

        .welcome-banner-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            flex-shrink: 0;
        }

        .welcome-banner-content { flex: 1; }

        .welcome-banner-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 6px;
            color: white;
        }

        .welcome-banner-text {
            font-size: 14px;
            opacity: 0.9;
            margin: 0;
            color: rgba(255,255,255,0.9);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: flex-start;
            gap: 16px;
            transition: all 0.2s;
        }

        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .stat-icon-primary { background: #eef2ff; color: #4f46e5; }
        .stat-icon-success { background: #dcfce7; color: #16a34a; }
        .stat-icon-warning { background: #fef3c7; color: #d97706; }
        .stat-icon-danger { background: #fee2e2; color: #dc2626; }
        .stat-icon-info { background: #cffafe; color: #0891b2; }

        .stat-content { flex: 1; }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            line-height: 1.1;
            color: var(--gray-800);
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 13px;
            color: var(--gray-500);
        }

        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }

        /* Dashboard Card */
        .dashboard-card {
            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            overflow: hidden;
        }

        .dashboard-card-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--gray-100);
        }

        .dashboard-card-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--gray-800);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dashboard-card-body { padding: 20px; }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .quick-action {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            padding: 20px 12px;
            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: 10px;
            text-decoration: none;
            color: var(--gray-700);
            transition: all 0.15s;
        }

        .quick-action:hover {
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.1);
            transform: translateY(-2px);
        }

        .quick-action-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .quick-action-label {
            font-size: 12px;
            font-weight: 500;
            text-align: center;
            color: var(--gray-600);
        }

        /* Account Info */
        .account-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .account-avatar {
            width: 56px;
            height: 56px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .account-details { flex: 1; }

        .account-name {
            font-size: 16px;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 2px;
        }

        .account-email {
            font-size: 13px;
            color: var(--gray-500);
            margin-bottom: 8px;
        }

        /* Info List */
        .info-list { display: flex; flex-direction: column; gap: 8px; }

        .info-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 0;
        }

        .info-row dt {
            font-size: 13px;
            color: var(--gray-500);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-row dt i { font-size: 16px; }

        .info-row dd {
            font-size: 13px;
            font-weight: 500;
            color: var(--gray-700);
            margin: 0;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* ============================================
           BUTTONS
           ============================================ */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 14px;
            font-weight: 500;
            padding: 10px 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.15s;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-sm {
            font-size: 13px;
            padding: 8px 12px;
        }

        .btn-lg {
            font-size: 16px;
            padding: 12px 24px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover { background: var(--primary-dark); }

        .btn-secondary {
            background: var(--gray-100);
            color: var(--gray-600);
        }

        .btn-secondary:hover { background: var(--gray-200); }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover { background: #dc2626; }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--gray-200);
            color: var(--gray-600);
        }

        .btn-outline:hover {
            background: var(--gray-50);
            border-color: var(--gray-300);
        }

        .btn-outline-primary {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
        }

        .btn-outline-secondary {
            background: transparent;
            border: 1px solid var(--gray-300);
            color: var(--gray-600);
        }

        .btn-outline-secondary:hover {
            background: var(--gray-50);
        }

        .btn-outline-danger {
            background: transparent;
            border: 1px solid var(--danger);
            color: var(--danger);
        }

        .btn-outline-danger:hover {
            background: var(--danger);
            color: white;
        }

        /* ============================================
           BADGES
           ============================================ */
        .badge {
            display: inline-flex;
            align-items: center;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 9999px;
            text-transform: capitalize;
        }

        .badge-primary { background: #eef2ff; color: #4f46e5; }
        .badge-success { background: #dcfce7; color: #16a34a; }
        .badge-warning { background: #fef3c7; color: #d97706; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
        .badge-info { background: #cffafe; color: #0891b2; }
        .badge-secondary { background: #f1f5f9; color: #64748b; }

        /* ============================================
           CARDS
           ============================================ */
        .card {
            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            overflow: hidden;
        }

        .card-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--gray-100);
            font-weight: 600;
            color: var(--gray-800);
        }

        .card-body { padding: 20px; }

        .card-footer {
            padding: 16px 20px;
            border-top: 1px solid var(--gray-100);
            background: var(--gray-50);
        }

        /* ============================================
           TABLE
           ============================================ */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .table thead th {
            background: var(--gray-50);
            border-bottom: 2px solid var(--gray-200);
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: var(--gray-500);
            padding: 12px 16px;
            white-space: nowrap;
            text-align: left;
        }

        .table tbody td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--gray-100);
            color: var(--gray-700);
            vertical-align: middle;
        }

        .table tbody tr:hover { background-color: var(--gray-50); }
        .table tbody tr:last-child td { border-bottom: none; }

        .table-actions {
            display: flex;
            gap: 6px;
        }

        .table .btn {
            padding: 6px 10px;
            font-size: 14px;
        }

        /* ============================================
           FORMS
           ============================================ */
        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--gray-700);
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            font-size: 14px;
            border: 1px solid var(--gray-300);
            border-radius: 8px;
            transition: all 0.15s;
            background: #fff;
            color: var(--gray-800);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-control::placeholder { color: var(--gray-400); }

        .form-select {
            width: 100%;
            padding: 10px 36px 10px 14px;
            font-size: 14px;
            border: 1px solid var(--gray-300);
            border-radius: 8px;
            transition: all 0.15s;
            background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2364748b' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E") no-repeat right 12px center;
            appearance: none;
            color: var(--gray-800);
            cursor: pointer;
        }

        .form-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .input-group {
            display: flex;
        }

        .input-group-text {
            background: var(--gray-50);
            border: 1px solid var(--gray-300);
            border-right: none;
            color: var(--gray-500);
            font-size: 14px;
            padding: 10px 14px;
            border-radius: 8px 0 0 8px;
            display: flex;
            align-items: center;
        }

        .input-group .form-control,
        .input-group .form-select {
            border-left: none;
            border-radius: 0 8px 8px 0;
        }

        /* ============================================
           PAGE HEADER
           ============================================ */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            gap: 16px;
            flex-wrap: wrap;
        }

        .page-title {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 22px;
            font-weight: 700;
            color: var(--gray-800);
            margin: 0;
        }

        .page-title-icon {
            width: 48px;
            height: 48px;
            background: var(--primary-light);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 22px;
        }

        /* ============================================
           EMPTY STATE
           ============================================ */
        .empty-state {
            padding: 48px 24px;
            text-align: center;
        }

        .empty-state-icon {
            width: 64px;
            height: 64px;
            background: var(--gray-100);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 24px;
            color: var(--gray-400);
        }

        .empty-state-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--gray-700);
        }

        .empty-state-text {
            font-size: 14px;
            color: var(--gray-500);
            margin-bottom: 20px;
        }

        /* ============================================
           ALERTS
           ============================================ */
        .alert {
            padding: 14px 16px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .alert i { font-size: 18px; margin-top: 1px; }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .alert-info {
            background: #cffafe;
            color: #155e75;
            border: 1px solid #a5f3fc;
        }

        /* ============================================
           PAGINATION
           ============================================ */
        .pagination {
            display: flex;
            align-items: center;
            gap: 4px;
            flex-wrap: wrap;
        }

        .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 12px;
            font-size: 14px;
            font-weight: 500;
            color: var(--gray-600);
            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.15s;
        }

        .page-link:hover {
            background: var(--gray-50);
            border-color: var(--gray-300);
            color: var(--gray-800);
        }

        .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .page-item.disabled .page-link {
            opacity: 0.5;
            pointer-events: none;
        }

        /* ============================================
           UTILITIES
           ============================================ */
        .text-primary { color: var(--primary) !important; }
        .text-success { color: var(--success) !important; }
        .text-warning { color: var(--warning) !important; }
        .text-danger { color: var(--danger) !important; }
        .text-muted { color: var(--gray-500) !important; }
        .text-decoration-none { text-decoration: none !important; }
        .fw-medium { font-weight: 500 !important; }
        .fw-semibold { font-weight: 600 !important; }
        .fw-bold { font-weight: 700 !important; }

        .my-4 { margin-top: 16px !important; margin-bottom: 16px !important; }
        .mb-0 { margin-bottom: 0 !important; }
        .mb-2 { margin-bottom: 8px !important; }
        .mb-3 { margin-bottom: 12px !important; }
        .mb-4 { margin-bottom: 16px !important; }
        .mt-2 { margin-top: 8px !important; }
        .me-1 { margin-right: 4px !important; }
        .ms-2 { margin-left: 8px !important; }

        .small { font-size: 12px !important; }

        /* ============================================
           RESPONSIVE - Page Components
           ============================================ */
        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .welcome-banner {
                flex-direction: column;
                text-align: center;
                padding: 20px;
            }

            .quick-actions {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 640px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .stat-card {
                flex-direction: column;
                text-align: center;
            }

            .stat-icon {
                margin: 0 auto;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .quick-actions {
                grid-template-columns: repeat(2, 1fr);
            }

            .action-buttons {
                flex-direction: column;
            }

            .action-buttons .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="layout" x-data="sidebarLayout()">
        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay" :class="{ 'show': sidebarOpen }" @click="sidebarOpen = false"></div>

        <!-- SIDEBAR -->
        <aside class="sidebar" :class="{ 'show': sidebarOpen }">
            <div class="sidebar-brand">
                <div class="sidebar-brand-icon"><i class="bi bi-shield-check"></i></div>
                <div class="sidebar-brand-text">
                    <div class="sidebar-brand-name">School Discipline</div>
                    <div class="sidebar-brand-desc">Management System</div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <!-- Dashboard -->
                <div class="sidebar-section">
                    <a href="{{ route('dashboard') }}" class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" @click="sidebarOpen = false">
                        <i class="bi bi-grid"></i>
                        <span>Dashboard</span>
                    </a>
                </div>

                <!-- Super Admin -->
                @if(auth()->user()->isSuperAdmin())
                <div class="sidebar-section">
                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="section-toggle" :class="{ 'open': open }">
                            <span class="section-toggle-left">
                                <i class="bi bi-shield-lock"></i>
                                <span>Super Admin</span>
                            </span>
                            <svg class="section-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        <ul class="submenu" :class="{ 'open': open }">
                            <li><a href="{{ route('schools.index') }}" class="submenu-link {{ request()->routeIs('schools.*') ? 'active' : '' }}" @click="sidebarOpen = false"><i class="bi bi-building"></i><span>Sekolah</span></a></li>
                            <li><a href="{{ route('users.index') }}" class="submenu-link {{ request()->routeIs('users.*') ? 'active' : '' }}" @click="sidebarOpen = false"><i class="bi bi-people"></i><span>Pengguna</span></a></li>
                        </ul>
                    </div>
                </div>
                @endif

                <!-- Akademik -->
                <div class="sidebar-section">
                    <div x-data="{ open: true }">
                        <button @click="open = !open" class="section-toggle" :class="{ 'open': open }">
                            <span class="section-toggle-left">
                                <i class="bi bi-book"></i>
                                <span>Akademik</span>
                            </span>
                            <svg class="section-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        <ul class="submenu" :class="{ 'open': open }">
                            <li><a href="{{ route('academic-years.index') }}" class="submenu-link {{ request()->routeIs('academic-years.*') ? 'active' : '' }}" @click="sidebarOpen = false"><i class="bi bi-calendar-range"></i><span>Tahun Ajaran</span></a></li>
                            <li><a href="{{ route('departments.index') }}" class="submenu-link {{ request()->routeIs('departments.*') ? 'active' : '' }}" @click="sidebarOpen = false"><i class="bi bi-diagram-3"></i><span>Program Keahlian</span></a></li>
                            <li><a href="{{ route('classes.index') }}" class="submenu-link {{ request()->routeIs('classes.*') ? 'active' : '' }}" @click="sidebarOpen = false"><i class="bi bi-mortarboard"></i><span>Kelas</span></a></li>
                        </ul>
                    </div>
                </div>

                <!-- Siswa -->
                <div class="sidebar-section">
                    <a href="{{ route('students.index') }}" class="menu-link {{ request()->routeIs('students.*') ? 'active' : '' }}" @click="sidebarOpen = false">
                        <i class="bi bi-person-badge"></i>
                        <span>Daftar Siswa</span>
                    </a>
                </div>

                <!-- Kedisiplinan -->
                <div class="sidebar-section">
                    <div x-data="{ open: true }">
                        <button @click="open = !open" class="section-toggle" :class="{ 'open': open }">
                            <span class="section-toggle-left">
                                <i class="bi bi-shield-exclamation"></i>
                                <span>Kedisiplinan</span>
                            </span>
                            <svg class="section-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        <ul class="submenu" :class="{ 'open': open }">
                            <li><a href="{{ route('violation-types.index') }}" class="submenu-link {{ request()->routeIs('violation-types.*') ? 'active' : '' }}" @click="sidebarOpen = false"><i class="bi bi-list-check"></i><span>Jenis Pelanggaran</span></a></li>
                            <li><a href="{{ route('violations.index') }}" class="submenu-link {{ request()->routeIs('violations.*') ? 'active' : '' }}" @click="sidebarOpen = false"><i class="bi bi-exclamation-triangle"></i><span>Pelanggaran</span></a></li>
                        </ul>
                    </div>
                </div>

                <!-- PKS -->
                <div class="sidebar-section">
                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="section-toggle" :class="{ 'open': open }">
                            <span class="section-toggle-left">
                                <i class="bi bi-shield-check"></i>
                                <span>PKS</span>
                            </span>
                            <svg class="section-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        <ul class="submenu" :class="{ 'open': open }">
                            <li><a href="{{ route('pks-members.index') }}" class="submenu-link {{ request()->routeIs('pks-members.*') ? 'active' : '' }}" @click="sidebarOpen = false"><i class="bi bi-people"></i><span>Anggota PKS</span></a></li>
                            <li><a href="{{ route('pks-shifts.index') }}" class="submenu-link {{ request()->routeIs('pks-shifts.*') ? 'active' : '' }}" @click="sidebarOpen = false"><i class="bi bi-clock"></i><span>Shift Piket</span></a></li>
                            <li><a href="{{ route('pks-duty-locations.index') }}" class="submenu-link {{ request()->routeIs('pks-duty-locations.*') ? 'active' : '' }}" @click="sidebarOpen = false"><i class="bi bi-geo-alt"></i><span>Lokasi Piket</span></a></li>
                            <li><a href="{{ route('pks-duty-schedules.index') }}" class="submenu-link {{ request()->routeIs('pks-duty-schedules.*') ? 'active' : '' }}" @click="sidebarOpen = false"><i class="bi bi-calendar-week"></i><span>Jadwal Piket</span></a></li>
                            <li><a href="{{ route('pks-duty-assignments.index') }}" class="submenu-link {{ request()->routeIs('pks-duty-assignments.*') ? 'active' : '' }}" @click="sidebarOpen = false"><i class="bi bi-clipboard-check"></i><span>Penugasan</span></a></li>
                            <li><a href="{{ route('pks-duty-attendances.index') }}" class="submenu-link {{ request()->routeIs('pks-duty-attendances.*') ? 'active' : '' }}" @click="sidebarOpen = false"><i class="bi bi-person-check"></i><span>Kehadiran</span></a></li>
                            <li><a href="{{ route('pks-field-activities.index') }}" class="submenu-link {{ request()->routeIs('pks-field-activities.*') ? 'active' : '' }}" @click="sidebarOpen = false"><i class="bi bi-map"></i><span>Aktivitas Lapangan</span></a></li>
                        </ul>
                    </div>
                </div>

                <!-- Laporan -->
                <div class="sidebar-section">
                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="section-toggle" :class="{ 'open': open }">
                            <span class="section-toggle-left">
                                <i class="bi bi-file-earmark-bar-graph"></i>
                                <span>Laporan</span>
                            </span>
                            <svg class="section-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        <ul class="submenu" :class="{ 'open': open }">
                            <li><a href="{{ route('reports.daily') }}" class="submenu-link {{ request()->routeIs('reports.daily*') ? 'active' : '' }}" @click="sidebarOpen = false"><i class="bi bi-calendar-day"></i><span>Laporan Harian</span></a></li>
                            <li><a href="{{ route('reports.monthly') }}" class="submenu-link {{ request()->routeIs('reports.monthly*') ? 'active' : '' }}" @click="sidebarOpen = false"><i class="bi bi-calendar-event"></i><span>Laporan Bulanan</span></a></li>
                        </ul>
                    </div>
                </div>

                <!-- Pengaturan -->
                <div class="sidebar-section">
                    <a href="{{ route('profile.edit') }}" class="menu-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" @click="sidebarOpen = false">
                        <i class="bi bi-sliders"></i>
                        <span>Pengaturan</span>
                    </a>
                </div>
            </nav>

            <!-- Footer -->
            <div class="sidebar-footer">
                @if(auth()->user()->school)
                <div class="school-info">
                    <div class="school-icon"><i class="bi bi-building"></i></div>
                    <div>
                        <div class="school-name">{{ auth()->user()->school->name }}</div>
                        <div class="school-role">{{ str_replace('_', ' ', auth()->user()->role) }}</div>
                    </div>
                </div>
                @endif
                <div class="user-info">
                    <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="logout-btn" title="Keluar"><i class="bi bi-box-arrow-right"></i></button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <div class="main">
            <header class="header">
                <div class="header-left">
                    <button class="header-btn header-btn-menu" @click="sidebarOpen = !sidebarOpen" style="display: none;">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="header-title">{{ $title ?? 'Dashboard' }}</h1>
                </div>
                <div class="header-right">
                    <button class="header-btn"><i class="bi bi-bell"></i><span class="notif-dot"></span></button>
                    <div class="dropdown" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" class="dropdown-btn">
                            <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                            <span>{{ auth()->user()->name }}</span>
                            <i class="bi bi-chevron-down" style="font-size: 12px; color: var(--gray-400);"></i>
                        </button>
                        <div class="dropdown-menu" :class="{ 'show': open }">
                            <div class="dropdown-header">
                                <div class="dropdown-header-name">{{ auth()->user()->name }}</div>
                                <div class="dropdown-header-email">{{ auth()->user()->email }}</div>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="dropdown-item"><i class="bi bi-person"></i>Profil Saya</a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                                @csrf
                                <button type="submit" class="dropdown-item danger"><i class="bi bi-box-arrow-right"></i>Keluar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>
            <main class="main-area">{{ $slot }}</main>
        </div>
    </div>

    <script>
        function sidebarLayout() {
            return {
                sidebarOpen: false,
                init() {
                    // Check screen size on load
                    this.checkScreenSize();
                    // Listen for resize
                    window.addEventListener('resize', () => this.checkScreenSize());
                },
                checkScreenSize() {
                    if (window.innerWidth <= 1024) {
                        this.sidebarOpen = false;
                    } else {
                        this.sidebarOpen = true;
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>
