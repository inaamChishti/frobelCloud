<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Dashboard - Frobel</title>
     <link rel="icon" href="data:;base64,iVBORw0KGgo=">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700|Roboto:400,500,700" rel="stylesheet">
    <!-- Toastify CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Toastify JS -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --primary-blue: #2563EB;
            --primary-blue-dark: #1E3A8A;
            --primary-blue-light: #A3BFFA;
            --accent-blue: #3B82F6;
            --background-light: #F8FAFC;
            --card-bg: rgba(255, 255, 255, 0.98);
            --text-dark: #1F2A44;
            --text-light: #F9FAFB;
            --border-light: #D1D5DB;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            --glass-bg: rgba(255, 255, 255, 0.4);
            --glass-blur: blur(12px);
            --red-logout: #EF4444;
            --menu-border: #E5E7EB;
            --menu-hover: #F1F5F9;
            --toast-success: #67C0EA;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Roboto', sans-serif;
            background: var(--background-light);
            color: var(--text-dark);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .sidebar {
            height: 100vh;
            width: 280px;
            position: fixed;
            top: 0;
            left: 0;
            background: var(--card-bg);
            backdrop-filter: var(--glass-blur);
            border-right: 1px solid var(--border-light);
            padding: 24px 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            transition: width 0.3s ease, transform 0.3s ease;
            box-shadow: var(--shadow);
        }

        .sidebar .logo {
            text-align: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--border-light);
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .sidebar .logo img {
            width: 180px;
            height: 40px;
            border-radius: 8px;
            object-fit: contain;
        }

        .sidebar .close-btn {
            display: none;
            background: none;
            border: none;
            color: var(--text-dark);
            font-size: 20px;
            cursor: pointer;
            transition: color 0.3s ease;
            position: absolute;
            right: 20px;
            top: 30px;
        }

        .sidebar .close-btn:hover {
            color: var(--primary-blue);
        }

        .sidebar .nav-container {
            flex-grow: 1;
            padding: 20px 0;
        }

        .sidebar .nav-item {
            margin-bottom: 8px;
        }

        .sidebar .nav-link {
            padding: 12px 20px;
            color: var(--text-dark);
            text-decoration: none;
            display: flex;
            align-items: center;
            border-radius: 8px;
            margin: 0 12px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 14px;
            border: 1px solid var(--menu-border);
            background: rgba(255, 255, 255, 0.8);
        }

        .sidebar .nav-link i {
            margin-right: 12px;
            font-size: 16px;
            color: var(--primary-blue);
            transition: color 0.3s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active,
        .sidebar .nav-link.active-submenu {
            background: linear-gradient(90deg, var(--primary-blue) 0%, var(--accent-blue) 100%);
            color: var(--text-light);
            transform: translateX(4px);
            border-color: var(--primary-blue);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .sidebar .nav-link:hover i,
        .sidebar .nav-link.active i,
        .sidebar .nav-link.active-submenu i {
            color: var(--text-light);
        }

        .sidebar .submenu {
            display: none;
            padding-left: 36px;
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .sidebar .submenu.show {
            display: block;
            animation: slideIn 0.3s ease;
        }

        .submenu .nav-link {
            padding: 10px 14px;
            font-size: 13px;
            color: var(--text-dark);
            border-radius: 6px;
            border: 1px solid var(--menu-border);
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        .submenu .nav-link:hover,
        .submenu .nav-link.active {
            background: var(--primary-blue-light);
            color: var(--text-light);
            border-color: var(--primary-blue-light);
            transform: translateX(2px);
        }

        .sidebar .footer {
            padding: 16px 20px;
            text-align: center;
            color: var(--text-dark);
            font-size: 12px;
            font-weight: 500;
            border-top: 1px solid var(--border-light);
            position: sticky;
            bottom: 0;
            background: var(--card-bg);
            backdrop-filter: var(--glass-blur);
        }

        .header {
            background: linear-gradient(135deg, var(--primary-blue-dark) 0%, var(--primary-blue) 100%);
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: var(--shadow);
            backdrop-filter: var(--glass-blur);
            border-bottom: 1px solid var(--border-light);
        }

        .header .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--text-light);
            font-size: 24px;
            cursor: pointer;
            padding: 8px;
            transition: color 0.3s ease;
        }

        .header .menu-toggle:hover {
            color: var(--primary-blue-light);
        }

        .header .notifications {
            position: relative;
            margin-right: 20px;
            cursor: pointer;
        }

        .header .notifications .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--red-logout);
            color: var(--text-light);
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            animation: pulse 2s infinite;
        }

        .header .user-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header .user-section .dropdown .btn {
            background: var(--glass-bg);
            backdrop-filter: var(--glass-blur);
            border: 1px solid var(--border-light);
            color: var(--text-light);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 16px;
            border-radius: 40px;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .header .user-section .dropdown .btn i {
            font-size: 16px;
            color: var(--text-light);
        }

        .header .user-section .dropdown .btn:hover {
            background: var(--primary-blue-light);
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.2);
        }

        .header .user-section .dropdown-menu {
            background: var(--card-bg);
            border: 1px solid var(--border-light);
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 8px;
            min-width: 140px;
            margin-top: 8px;
            backdrop-filter: var(--glass-blur);
            z-index: 10000;
        }

        .header .user-section .dropdown-menu .dropdown-item {
            color: var(--text-dark);
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 13px;
            transition: background-color 0.3s ease;
        }

        .header .user-section .dropdown-menu .dropdown-item:hover {
            background: var(--primary-blue);
            color: var(--text-light);
        }

        .header .clock {
            color: var(--text-light);
            font-weight: 500;
            margin-right: 20px;
        }

        .content {
            margin-left: 280px;
            padding: 24px;
            min-height: 100vh;
            background: var(--background-light);
            display: flex;
            flex-direction: column;
            gap: 24px;
            transition: margin-left 0.3s ease;
        }

        .main-content {
            background: var(--card-bg);
            padding: 24px;
            border-radius: 12px;
            border: 1px solid var(--border-light);
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            backdrop-filter: var(--glass-blur);
        }

        .main-content:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(37, 0, 235, 0.15);
        }

        .main-content h1 {
            color: var(--primary-blue-dark);
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .main-content p {
            color: var(--text-dark);
            font-size: 15px;
            font-weight: 400;
            line-height: 1.5;
        }

        .notification-bar {
            position: fixed;
            top: 60px;
            right: 20px;
            width: 320px;
            background: var(--glass-bg);
            border: 1px solid var(--border-light);
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 20px;
            z-index: 1051;
            display: none;
            animation: slideIn 0.3s ease-in-out;
            backdrop-filter: var(--glass-blur);
        }

        .notification-bar.show {
            display: block;
        }

        .notification-item {
            padding: 18px;
            border: 1px solid var(--border-light);
            background: var(--glass-bg);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            color: var(--text-dark);
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
            position: relative;
        }

        .notification-item:last-child {
            margin-bottom: 0;
        }

        .notification-item i {
            margin-right: 14px;
            color: var(--primary-blue);
            font-size: 16px;
        }

        .notification-item .dismiss-btn {
            position: absolute;
            right: 12px;
            color: var(--text-dark);
            font-size: 14px;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }

        .notification-item .dismiss-btn:hover {
            opacity: 1;
            color: var(--primary-blue);
        }

        .notification-item:hover {
            background: var(--primary-blue-light);
            color: var(--text-light);
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .notification-item:hover i,
        .notification-item:hover .dismiss-btn {
            color: var(--text-light);
        }

        .rotate-180 {
            transform: rotate(180deg);
            transition: transform 0.3s ease;
        }

        .fa-chevron-down {
            font-size: 12px;
            margin-left: auto;
            color: var(--primary-blue);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        .badge.badge-danger {
            background: var(--red-logout);
            color: var(--text-light);
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            margin-left: 5px;
        }

        /* Enhanced Responsive Design */
        @media (max-width: 1200px) {
            .sidebar {
                width: 240px;
            }

            .content {
                margin-left: 240px;
            }

            .main-content h1 {
                font-size: 24px;
            }

            .main-content p {
                font-size: 14px;
            }
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 280px;
                transform: translateX(-100%);
                position: fixed;
                top: 0;
                left: 0;
                z-index: 1001;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .content {
                margin-left: 0;
            }

            .header .menu-toggle {
                display: block;
            }

            .sidebar .close-btn {
                display: block;
            }

            .notification-bar {
                width: 300px;
                right: 10px;
                top: 50px;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: 100vh;
                border-right: none;
            }

            .sidebar .logo img {
                width: 150px;
                height: 35px;
            }

            .sidebar .nav-link {
                font-size: 13px;
                padding: 10px 16px;
            }

            .sidebar .submenu .nav-link {
                font-size: 12px;
                padding: 8px 12px;
            }

            .content {
                padding: 16px;
            }

            .header {
                padding: 10px 16px;
                flex-wrap: wrap;
                gap: 10px;
            }

            .header .user-section {
                gap: 8px;
            }

            .header .user-section .dropdown .btn {
                padding: 6px 12px;
                font-size: 12px;
            }

            .header .clock {
                font-size: 12px;
                margin-right: 10px;
            }

            .main-content {
                padding: 16px;
                border-radius: 8px;
            }

            .main-content h1 {
                font-size: 20px;
            }

            .main-content p {
                font-size: 13px;
            }

            .notification-bar {
                width: 90%;
                max-width: 280px;
                right: 5%;
                top: 60px;
            }

            .notification-item {
                font-size: 13px;
                padding: 14px;
            }
        }

        @media (max-width: 576px) {
            .sidebar .nav-link {
                padding: 8px 12px;
                font-size: 12px;
            }

            .sidebar .submenu .nav-link {
                font-size: 11px;
                padding: 6px 10px;
            }

            .sidebar .logo img {
                width: 120px;
                height: 30px;
            }

            .header {
                padding: 8px 12px;
            }

            .header .user-section {
                flex-direction: row;
                gap: 6px;
            }

            .header .user-section .dropdown .btn {
                padding: 5px 10px;
                font-size: 11px;
            }

            .header .clock {
                font-size: 11px;
            }

            .main-content {
                padding: 12px;
            }

            .main-content h1 {
                font-size: 18px;
            }

            .main-content p {
                font-size: 12px;
            }

            .notification-bar {
                width: 95%;
                max-width: 260px;
                right: 2.5%;
                top: 50px;
            }

            .notification-item {
                font-size: 12px;
                padding: 12px;
            }

            .notification-item i {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    @php
        $user = auth()->user();
        $pagePermissions = [];

        if ($user && $user->permissions) {
            $pagePermissions = json_decode($user->permissions->page_name, true) ?? [];
        }
    @endphp
    @if(auth()->user()->role === 'super_admin')

    <div style="background: #1E3A8A; padding: 12px 24px; display: flex; align-items: center; justify-content: flex-end; position: sticky; top: 0; z-index: 999; border-bottom: 1px solid #D1D5DB; position: relative;">
    <!-- Centered Text -->
    <span style="color: #F9FAFB; font-size: 14px; font-weight: 500; position: absolute; left: 50%; transform: translateX(-50%);">
        You are logged in to {{ session('branch_name') ?? 'Branch' }}
    </span>


    <!-- Right Side Buttons -->
    <div style="display: flex; gap: 16px;">
        <a href="{{ route('clear-branch') }}" style="color: #F9FAFB; font-size: 14px; font-weight: 500; text-decoration: none; display: flex; align-items: center; transition: color 0.3s ease, transform 0.3s ease;" aria-label="Back to Super Admin Dashboard">
            <i class="fas fa-arrow-left" style="font-size: 12px; margin-right: 6px; color: #A3BFFA;"></i> Super Admin Dashboard
        </a>
        <form action="{{ route('clear-branch') }}" method="GET" style="display: inline;">
            @csrf
            <button type="submit" style="background: none; border: none; color: #F9FAFB; font-size: 14px; font-weight: 500; cursor: pointer; display: flex; align-items: center; transition: color 0.3s ease, transform 0.3s ease;" aria-label="Logout">
                <i class="fas fa-sign-out-alt" style="font-size: 12px; margin-right: 6px; color: #EF4444;"></i> Logout
            </button>
        </form>
    </div>
</div>
    @endif

    <div class="sidebar" role="navigation" aria-label="Main navigation">
        <div class="logo">
            <img src="{{ asset('FrobelEducationWhite (1).png') }}" alt="Frobel Logo" style="background: #2563EB;">
            <button class="close-btn" aria-label="Close menu">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="nav-container">
            @if (isset($pagePermissions['dashboard']) && $pagePermissions['dashboard'] === 'on')
                <div class="nav-item">
                    <a href="{{ url('branch-dashboard') }}"
                        class="nav-link {{ Request::path() === 'branch-dashboard' ? 'active' : '' }}"
                        aria-current="{{ Request::path() === 'branch-dashboard' ? 'page' : '' }}">
                        <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
                    </a>
                </div>
            @endif
@php
    $hasIagSubMenuAccess =
        (isset($pagePermissions['add_learner']) && $pagePermissions['add_learner'] === 'on') ||
        (isset($pagePermissions['view_learner']) && $pagePermissions['view_learner'] === 'on') ||
        (isset($pagePermissions['report']) && $pagePermissions['report'] === 'on') ||
        (isset($pagePermissions['alumni']) && $pagePermissions['alumni'] === 'on') || // Added alumni permission
        (isset($pagePermissions['iag_analysis']) && $pagePermissions['iag_analysis'] === 'on') ||
        (isset($pagePermissions['learner_requests']) && $pagePermissions['learner_requests'] === 'on');
    $unapprovedRequestsCount = App\Models\IAGLearnerRequest::where('branch_id', session('branch_id'))
        ->where('is_approved', 0)
        ->count();
@endphp
@if ($hasIagSubMenuAccess)
    <div class="nav-item">
        <a href="#"
            class="nav-link toggle-submenu {{ in_array(Request::path(), ['add-learner', 'view-learner', 'learner-report', 'alumni', 'learner-analysis', 'learner-requests']) ? 'active' : '' }}"
            aria-expanded="false">
            <i class="fas fa-handshake"></i><span>IAG Meeting Links</span>
            <i class="fas fa-chevron-down float-end"></i>
        </a>
        <div class="submenu">
            @if (isset($pagePermissions['add_learner']) && $pagePermissions['add_learner'] === 'on')
                <a href="{{ url('add-learner') }}"
                    class="nav-link {{ Request::path() === 'add-learner' ? 'active' : '' }}"
                    aria-current="{{ Request::path() === 'add-learner' ? 'page' : '' }}">
                    <i class="fas fa-user-plus"></i><span>Add Learner</span>
                </a>
            @endif
            @if (isset($pagePermissions['view_learner']) && $pagePermissions['view_learner'] === 'on')
                <a href="{{ url('view-learner') }}"
                    class="nav-link {{ Request::path() === 'view-learner' ? 'active' : '' }}"
                    aria-current="{{ Request::path() === 'view-learner' ? 'page' : '' }}">
                    <i class="fas fa-user-check"></i><span>View Learner</span>
                </a>
            @endif
            @if (isset($pagePermissions['report']) && $pagePermissions['report'] === 'on')
                <a href="{{ url('learner-report') }}"
                    class="nav-link {{ Request::path() === 'learner-report' ? 'active' : '' }}"
                    aria-current="{{ Request::path() === 'learner-report' ? 'page' : '' }}">
                    <i class="fas fa-file-alt"></i><span>Report</span>
                </a>
            @endif
            @if (isset($pagePermissions['alumni']) && $pagePermissions['alumni'] === 'on')
                <a href="{{ url('alumni') }}"
                    class="nav-link {{ Request::path() === 'alumni' ? 'active' : '' }}"
                    aria-current="{{ Request::path() === 'alumni' ? 'page' : '' }}">
                    <i class="fas fa-user-graduate"></i><span>Alumni</span>
                </a>
            @endif
            @if (isset($pagePermissions['iag_analysis']) && $pagePermissions['iag_analysis'] === 'on')
                <a href="{{ url('learner-analysis') }}"
                    class="nav-link {{ Request::path() === 'learner-analysis' ? 'active' : '' }}"
                    aria-current="{{ Request::path() === 'learner-analysis' ? 'page' : '' }}">
                    <i class="fas fa-file-signature"></i><span>IAG Analysis</span>
                </a>
            @endif
            @if (isset($pagePermissions['learner_requests']) && $pagePermissions['learner_requests'] === 'on')
                <a href="{{ url('learner-requests') }}"
                    class="nav-link {{ Request::path() === 'learner-requests' ? 'active' : '' }}"
                    aria-current="{{ Request::path() === 'learner-requests' ? 'page' : '' }}">
                    <i class="fas fa-user-clock"></i>
                    <span>Learner Requests</span>
                    @if ($unapprovedRequestsCount > 0)
                        <span class="request-count-badge"
                            style="color:#EF4444;">{{ $unapprovedRequestsCount }}</span>
                    @endif
                </a>
            @endif
        </div>
    </div>
@endif

            @php
                $hasMocksSubMenuAccess =
                    (isset($pagePermissions['add_mock_result']) && $pagePermissions['add_mock_result'] === 'on') ||
                    (isset($pagePermissions['view_mock_result']) && $pagePermissions['view_mock_result'] === 'on') ||
                    (isset($pagePermissions['mock_test_report']) && $pagePermissions['mock_test_report'] === 'on');
            @endphp
            @if ($hasMocksSubMenuAccess)
                <div class="nav-item">
                    <a href="#"
                        class="nav-link toggle-submenu {{ in_array(Request::path(), ['mocks/add-result', 'mocks/view-result', 'mock/test/report']) ? 'active' : '' }}"
                        aria-expanded="false">
                        <i class="fas fa-file-alt"></i><span>Mocks</span>
                        <i class="fas fa-chevron-down float-end"></i>
                    </a>
                    <div class="submenu">
                        @if (isset($pagePermissions['add_mock_result']) && $pagePermissions['add_mock_result'] === 'on')
                            <a href="{{ url('mocks/add-result') }}"
                                class="nav-link {{ Request::path() === 'mocks/add-result' ? 'active' : '' }}"
                                aria-current="{{ Request::path() === 'mocks/add-result' ? 'page' : '' }}">
                                <i class="fas fa-plus-circle"></i><span>Add Result</span>
                            </a>
                        @endif
                        @if (isset($pagePermissions['view_mock_result']) && $pagePermissions['view_mock_result'] === 'on')
                            <a href="{{ url('mocks/view-result') }}"
                                class="nav-link {{ Request::path() === 'mocks/view-result' ? 'active' : '' }}"
                                aria-current="{{ Request::path() === 'mocks/view-result' ? 'page' : '' }}">
                                <i class="fas fa-eye"></i><span>View Result</span>
                            </a>
                        @endif
                        @if (isset($pagePermissions['mock_test_report']) && $pagePermissions['mock_test_report'] === 'on')
                            <a href="{{ url('mock/test/report') }}"
                                class="nav-link {{ Request::path() === 'mock/test/report' ? 'active' : '' }}"
                                aria-current="{{ Request::path() === 'mock/test/report' ? 'page' : '' }}">
                                <i class="fas fa-chart-bar"></i><span>Mock Test Report</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            @php
                $hasUsersSubMenuAccess =
                    (isset($pagePermissions['Add_Users']) && $pagePermissions['Add_Users'] === 'on') ||
                    (isset($pagePermissions['add_roles']) && $pagePermissions['add_roles'] === 'on');
            @endphp
            @if ($hasUsersSubMenuAccess)
                <div class="nav-item">
                    <a href="#"
                        class="nav-link toggle-submenu {{ in_array(Request::path(), ['add-branch-users', 'add-branch-roles']) ? 'active' : '' }}"
                        aria-expanded="false">
                        <i class="fas fa-users"></i><span>Users</span>
                        <i class="fas fa-chevron-down float-end"></i>
                    </a>
                    <div class="submenu">
                        @if (isset($pagePermissions['Add_Users']) && $pagePermissions['Add_Users'] === 'on')
                            <a href="{{ url('add-branch-users') }}"
                                class="nav-link {{ Request::path() === 'add-branch-users' ? 'active' : '' }}"
                                aria-current="{{ Request::path() === 'add-branch-users' ? 'page' : '' }}">
                                <i class="fas fa-user-plus"></i><span>Add Users</span>
                            </a>
                        @endif
                        @if (isset($pagePermissions['add_roles']) && $pagePermissions['add_roles'] === 'on')
                            <a href="{{ url('add-branch-roles') }}"
                                class="nav-link {{ Request::path() === 'add-branch-roles' ? 'active' : '' }}"
                                aria-current="{{ Request::path() === 'add-branch-roles' ? 'page' : '' }}">
                                <i class="fas fa-user-tag"></i><span>Add Roles</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            @if (isset($pagePermissions['flag_candidate']) && $pagePermissions['flag_candidate'] === 'on')
                <div class="nav-item">
                    <a href="#"
                        class="nav-link toggle-submenu {{ Request::path() === 'flag-candidate' ? 'active' : '' }}"
                        aria-expanded="false">
                        <i class="fas fa-flag"></i><span>Flag Candidate</span>
                        <i class="fas fa-chevron-down float-end"></i>
                    </a>
                    <div class="submenu">
                        <a href="{{ url('flag-candidate') }}"
                            class="nav-link {{ Request::path() === 'flag-candidate' ? 'active' : '' }}"
                            aria-current="{{ Request::path() === 'flag-candidate' ? 'page' : '' }}">
                            <i class="fas fa-search"></i><span>Search & Flag</span>
                        </a>
                    </div>
                </div>
            @endif

            {{-- Family Block / Unblock --}}
            @if (isset($pagePermissions['family_block']) && $pagePermissions['family_block'] === 'on')
                <div class="nav-item">
                    <a href="{{ url('family-block') }}"
                        class="nav-link {{ Request::path() === 'family-block' ? 'active' : '' }}"
                        aria-current="{{ Request::path() === 'family-block' ? 'page' : '' }}">
                        <i class="fas fa-ban"></i><span>Family Block</span>
                    </a>
                </div>
            @endif

            @php
                $hasAdmissionSubMenuAccess =
                    (isset($pagePermissions['admission']) && $pagePermissions['admission'] === 'on') ||
                    (isset($pagePermissions['new_admission']) && $pagePermissions['new_admission'] === 'on') ||
                    (isset($pagePermissions['archived_admissions']) &&
                        $pagePermissions['archived_admissions'] === 'on') ||
                    (isset($pagePermissions['approved_admissions']) &&
                        $pagePermissions['approved_admissions'] === 'on');
            @endphp
            @if ($hasAdmissionSubMenuAccess)
                <div class="nav-item">
                    <a href="#"
                        class="nav-link toggle-submenu {{ in_array(Request::path(), ['create-admission', 'fetch-student-registration', 'approved-admissions']) ? 'active' : '' }}"
                        aria-expanded="false">
                        <i class="fa fa-edit"></i><span style="zoom:0.9;">Process Admission</span>
                        <i class="fas fa-chevron-down float-end"></i>
                    </a>
                    <div class="submenu">
                        @if (isset($pagePermissions['admission']) && $pagePermissions['admission'] === 'on')
                            <a href="{{ url('create-admission') }}"
                                class="nav-link {{ Request::path() === 'create-admission' ? 'active' : '' }}"
                                aria-current="{{ Request::path() === 'create-admission' ? 'page' : '' }}">
                                <i class="fas fa-file-medical"></i><span>Create Admission</span>
                            </a>
                        @endif
                        @if (isset($pagePermissions['new_admission']) && $pagePermissions['new_admission'] === 'on')
                            @php
                                $pendingRequests = \App\Models\StudentRequest::where('branch_id', session('branch_id'))
                                    ->where('is_approved', 0)
                                    ->count();
                            @endphp
                            <a href="{{ url('fetch-student-registration') }}"
                                class="nav-link {{ Request::path() === 'fetch-student-registration' ? 'active' : '' }}"
                                aria-current="{{ Request::path() === 'fetch-student-registration' ? 'page' : '' }}">
                                <i class="fas fa-file-medical-alt"></i><span>New Admission</span>
                                @if ($pendingRequests > 0)
                                    <span class="badge badge-danger">{{ $pendingRequests }}</span>
                                @endif
                            </a>
                        @endif
                        @if (isset($pagePermissions['approved_admissions']) && $pagePermissions['approved_admissions'] === 'on')
                            <a href="{{ url('approved-admissions') }}"
                                class="nav-link {{ Request::path() === 'approved-admissions' ? 'active' : '' }}"
                                aria-current="{{ Request::path() === 'approved-admissions' ? 'page' : '' }}">
                                <i class="fas fa-check-circle"></i><span>Approved</span>
                            </a>
                        @endif

                        @if (isset($pagePermissions['archived_admissions']) && $pagePermissions['archived_admissions'] === 'on')
                            @php
                                $archivedRequests = \App\Models\StudentRequest::where('branch_id', session('branch_id'))
                                    ->where('is_archive', 1)
                                    ->count();
                            @endphp
                            <a href="{{ url('archived-admissions') }}"
                                class="nav-link {{ Request::path() === 'archived-admissions' ? 'active' : '' }}"
                                aria-current="{{ Request::path() === 'archived-admissions' ? 'page' : '' }}">
                                <i class="fas fa-archive"></i><span>Archived</span>
                                @if ($archivedRequests > 0)
                                    <span class="badge badge-danger">{{ $archivedRequests }}</span>
                                @endif
                            </a>
                        @endif
                    </div>
                </div>
            @endif
            @php
                $hasAttendanceSubMenuAccess =
                    (isset($pagePermissions['attendance']) && $pagePermissions['attendance'] === 'on') ||
                    (isset($pagePermissions['view_attendance']) && $pagePermissions['view_attendance'] === 'on');
            @endphp
            @if ($hasAttendanceSubMenuAccess)
                <div class="nav-item">
                    <a href="#"
                        class="nav-link toggle-submenu {{ in_array(Request::path(), ['attendance', 'view-attendance']) ? 'active' : '' }}"
                        aria-expanded="false">
                        <i class="fas fa-calendar-check"></i><span>Attendance</span>
                        <i class="fas fa-chevron-down float-end"></i>
                    </a>
                    <div class="submenu">
                        @if (isset($pagePermissions['attendance']) && $pagePermissions['attendance'] === 'on')
                            <a href="{{ url('attendance') }}"
                                class="nav-link {{ Request::path() === 'attendance' ? 'active' : '' }}"
                                aria-current="{{ Request::path() === 'attendance' ? 'page' : '' }}">
                                <i class="fas fa-check-square"></i><span>Attendance</span>
                            </a>
                        @endif
                        @if (isset($pagePermissions['view_attendance']) && $pagePermissions['view_attendance'] === 'on')
                            <a href="{{ url('view-attendance') }}"
                                class="nav-link {{ Request::path() === 'view-attendance' ? 'active' : '' }}"
                                aria-current="{{ Request::path() === 'view-attendance' ? 'page' : '' }}">
                                <i class="fas fa-eye"></i><span>View Attendance</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            @if (isset($pagePermissions['add_subjects']) && $pagePermissions['add_subjects'] === 'on')
                <div class="nav-item">
                    <a href="#"
                        class="nav-link toggle-submenu {{ Request::path() === 'add-subject' ? 'active' : '' }}"
                        aria-expanded="false">
                        <i class="fas fa-book"></i><span>Subjects</span>
                        <i class="fas fa-chevron-down float-end"></i>
                    </a>
                    <div class="submenu">
                        <a href="{{ url('add-subject') }}"
                            class="nav-link {{ Request::path() === 'add-subject' ? 'active' : '' }}"
                            aria-current="{{ Request::path() === 'add-subject' ? 'page' : '' }}">
                            <i class="fas fa-plus-circle"></i><span>Add Subject</span>
                        </a>
                    </div>
                </div>
            @endif

       @php
    $hasTimetableSubMenuAccess =
        (isset($pagePermissions['manage_central_timetable']) &&
            $pagePermissions['manage_central_timetable'] === 'on') ||
        (isset($pagePermissions['term_break']) &&
            $pagePermissions['term_break'] === 'on');
@endphp

@if ($hasTimetableSubMenuAccess)
    <div class="nav-item">
        <a href="#"
            class="nav-link toggle-submenu
            {{ in_array(Request::path(), ['central-timetable', 'term-break', 'morning-session', 'afternoon-session']) ? 'active' : '' }}"
            aria-expanded="false">
            <i class="fas fa-calendar-alt"></i>
            <span>Central Timetable</span>
            <i class="fas fa-chevron-down float-end"></i>
        </a>

        <div class="submenu">
            {{-- Manage Central Timetable --}}
            @if (isset($pagePermissions['manage_central_timetable']) &&
                    $pagePermissions['manage_central_timetable'] === 'on')
                <a href="{{ url('central-timetable') }}"
                    class="nav-link {{ Request::path() === 'central-timetable' ? 'active' : '' }}"
                    aria-current="{{ Request::path() === 'central-timetable' ? 'page' : '' }}">
                    <i class="fas fa-table"></i>
                    <span>Manage Central Timetable</span>
                </a>
            @endif

            {{-- Term Break --}}
            @if (isset($pagePermissions['term_break']) &&
                    $pagePermissions['term_break'] === 'on')
                <a href="{{ url('term-break') }}"
                    class="nav-link {{ Request::path() === 'term-break' ? 'active' : '' }}"
                    aria-current="{{ Request::path() === 'term-break' ? 'page' : '' }}">
                    <i class="fas fa-pause-circle"></i>
                    <span>Term Break</span>
                </a>
            @endif
        </div>
    </div>
@endif





@php
    $hasSchedulerMenuAccess =
        (isset($pagePermissions['timetable_scheduler']) && $pagePermissions['timetable_scheduler'] === 'on') ||
        (isset($pagePermissions['term_break']) && $pagePermissions['term_break'] === 'on') ||
        (isset($pagePermissions['termbreak_scheduler']) && $pagePermissions['termbreak_scheduler'] === 'on') ||
        (isset($pagePermissions['teacher_availability']) && $pagePermissions['teacher_availability'] === 'on') ||
        (isset($pagePermissions['student_timetable']) && $pagePermissions['student_timetable'] === 'on') ||
        (isset($pagePermissions['staff_timetable']) && $pagePermissions['staff_timetable'] === 'on') ||
        (isset($pagePermissions['new_list_module']) && $pagePermissions['new_list_module'] === 'on');
@endphp

@if ($hasSchedulerMenuAccess)
    <div class="nav-item">
        <a href="#"
            class="nav-link toggle-submenu {{ in_array(Request::path(), [
                'timetable-scheduler',
                'termbreak-scheduler',
                'schedule',
                'generate-student-timetable',
                'generate-staff-timetable',
                'new-list-module'
            ]) ? 'active' : '' }}"
            aria-expanded="false">
            <i class="fas fa-calendar-week"></i><span>Scheduler</span>
            <i class="fas fa-chevron-down float-end"></i>
        </a>

        <div class="submenu">

            <!-- Timetable Scheduler -->
            @if (isset($pagePermissions['timetable_scheduler']) && $pagePermissions['timetable_scheduler'] === 'on')
                <a href="{{ url('timetable-scheduler') }}"
                    class="nav-link {{ Request::path() === 'timetable-scheduler' ? 'active' : '' }}">
                    <i class="fas fa-table"></i><span>Timetable Scheduler</span>
                </a>
            @endif

            <!-- Term Break Scheduler -->
            @if (isset($pagePermissions['termbreak_scheduler']) && $pagePermissions['termbreak_scheduler'] === 'on')
                <a href="{{ url('termbreak-scheduler') }}"
                    class="nav-link {{ Request::path() === 'termbreak-scheduler' ? 'active' : '' }}">
                    <i class="fas fa-calendar-minus"></i><span>TermBreak Scheduler</span>
                </a>
            @endif

            <!-- Teacher Availability -->
            @if (isset($pagePermissions['teacher_availability']) && $pagePermissions['teacher_availability'] === 'on')
                <a href="{{ url('schedule') }}"
                    class="nav-link {{ Request::path() === 'schedule' ? 'active' : '' }}">
                    <i class="fas fa-user-clock"></i><span>Teacher Availability</span>
                </a>
            @endif

            <!-- Student Timetable -->
            @if (isset($pagePermissions['student_timetable']) && $pagePermissions['student_timetable'] === 'on')
                <a href="{{ url('generate-student-timetable') }}"
                    class="nav-link {{ Request::path() === 'generate-student-timetable' ? 'active' : '' }}">
                    <i class="fas fa-user-graduate"></i><span>Student Timetable</span>
                </a>
            @endif

            <!-- Staff Timetable -->
            @if (isset($pagePermissions['staff_timetable']) && $pagePermissions['staff_timetable'] === 'on')
                <a href="{{ url('generate-staff-timetable') }}"
                    class="nav-link {{ Request::path() === 'generate-staff-timetable' ? 'active' : '' }}">
                    <i class="fas fa-users"></i><span>Staff Timetable</span>
                </a>
            @endif

            <!-- New List Module (NEW) -->
            {{-- @if (isset($pagePermissions['new_list_module']) && $pagePermissions['new_list_module'] === 'on')
                <a href="{{ url('new-list-module') }}"
                    class="nav-link {{ Request::path() === 'new-list-module' ? 'active' : '' }}">
                    <i class="fas fa-list"></i><span>New List Module</span>
                </a>
            @endif --}}

        </div>
    </div>
@endif




@if (isset($pagePermissions['grades_module']) && $pagePermissions['grades_module'] === 'on')
    <div class="nav-item">
        <a href="#"
            class="nav-link toggle-submenu {{ Request::path() === 'grades-module' ? 'active' : '' }}"
            aria-expanded="false">
            <i class="fas fa-clipboard-check"></i>
            <span>Grades</span>
            <i class="fas fa-chevron-down float-end"></i>
        </a>

        <div class="submenu">

            <!-- Only One Sub-menu: Grades Module -->
            <a href="{{ url('grades-module') }}"
                class="nav-link {{ Request::path() === 'grades-module' ? 'active' : '' }}"
                aria-current="{{ Request::path() === 'grades-module' ? 'page' : '' }}">
                <i class="fas fa-list-alt"></i>
                <span>Grades Module</span>
            </a>

        </div>
    </div>
@endif
            @php
                $hasTestsSubMenuAccess =
                    (isset($pagePermissions['add_tests_record']) && $pagePermissions['add_tests_record'] === 'on') ||
                    (isset($pagePermissions['add_tests_record_excel']) &&
                        $pagePermissions['add_tests_record_excel'] === 'on') ||
                    (isset($pagePermissions['view_tests_records']) &&
                        $pagePermissions['view_tests_records'] === 'on') ||
                    (isset($pagePermissions['teacher_comments']) && $pagePermissions['teacher_comments'] === 'on');
            @endphp
           @php
    $hasTestsSubMenuAccess =
        (isset($pagePermissions['add_tests_record']) && $pagePermissions['add_tests_record'] === 'on') ||
        (isset($pagePermissions['add_tests_record_excel']) && $pagePermissions['add_tests_record_excel'] === 'on') ||
        (isset($pagePermissions['view_tests_records']) && $pagePermissions['view_tests_records'] === 'on') ||
        (isset($pagePermissions['teacher_comments']) && $pagePermissions['teacher_comments'] === 'on') ||
        (isset($pagePermissions['test_submission_tracker']) && $pagePermissions['test_submission_tracker'] === 'on'); // Added test_submission_tracker permission
@endphp
@if ($hasTestsSubMenuAccess)
    <div class="nav-item">
        <a href="#"
            class="nav-link toggle-submenu {{ in_array(Request::path(), ['student-tests/add-record', 'student-tests/add-records-excel', 'student-tests/view-records', 'student-tests/teacher-comments', 'student-tests/test-submission-tracker']) ? 'active' : '' }}"
            aria-expanded="false">
            <i class="fas fa-clipboard-check"></i><span>Student Tests</span>
            <i class="fas fa-chevron-down float-end"></i>
        </a>
        <div class="submenu">
            @if (isset($pagePermissions['add_tests_record']) && $pagePermissions['add_tests_record'] === 'on')
                <a href="{{ url('student-tests/add-record') }}"
                    class="nav-link {{ Request::path() === 'student-tests/add-record' ? 'active' : '' }}"
                    aria-current="{{ Request::path() === 'student-tests/add-record' ? 'page' : '' }}">
                    <i class="fas fa-plus-circle"></i><span>Add Record</span>
                </a>
            @endif
            @if (isset($pagePermissions['add_tests_record_excel']) && $pagePermissions['add_tests_record_excel'] === 'on')
                <a href="{{ url('student-tests/add-records-excel') }}"
                    class="nav-link {{ Request::path() === 'student-tests/add-records-excel' ? 'active' : '' }}"
                    aria-current="{{ Request::path() === 'student-tests/add-records-excel' ? 'page' : '' }}">
                    <i class="fas fa-file-excel"></i><span>Add Records (excel)</span>
                </a>
            @endif
            @if (isset($pagePermissions['view_tests_records']) && $pagePermissions['view_tests_records'] === 'on')
                <a href="{{ url('student-tests/view-records') }}"
                    class="nav-link {{ Request::path() === 'student-tests/view-records' ? 'active' : '' }}"
                    aria-current="{{ Request::path() === 'student-tests/view-records' ? 'page' : '' }}">
                    <i class="fas fa-eye"></i><span>View Records</span>
                </a>
            @endif
            @if (isset($pagePermissions['teacher_comments']) && $pagePermissions['teacher_comments'] === 'on')
                <a href="{{ url('student-tests/teacher-comments') }}"
                    class="nav-link {{ Request::path() === 'student-tests/teacher-comments' ? 'active' : '' }}"
                    aria-current="{{ Request::path() === 'student-tests/teacher-comments' ? 'page' : '' }}">
                    <i class="fas fa-comment"></i><span>Teacher Comments</span>
                </a>
            @endif
            @if (isset($pagePermissions['test_submission_tracker']) && $pagePermissions['test_submission_tracker'] === 'on')
                <a href="{{ url('student-tests/test-submission-tracker') }}"
                    class="nav-link {{ Request::path() === 'student-tests/test-submission-tracker' ? 'active' : '' }}"
                    aria-current="{{ Request::path() === 'student-tests/test-submission-tracker' ? 'page' : '' }}">
                    <i class="fas fa-tasks"></i><span>Test Submission Tracker</span>
                </a>
            @endif
        </div>
    </div>
@endif

            @php
                $hasBooksSubMenuAccess =
                    (isset($pagePermissions['manage_books']) && $pagePermissions['manage_books'] === 'on') ||
                    (isset($pagePermissions['manage_sales']) && $pagePermissions['manage_sales'] === 'on') ||
                    (isset($pagePermissions['manage_purchases']) && $pagePermissions['manage_purchases'] === 'on') ||
                    (isset($pagePermissions['books_report']) && $pagePermissions['books_report'] === 'on');
            @endphp
            @if ($hasBooksSubMenuAccess)
                <div class="nav-item">
                    <a href="#"
                        class="nav-link toggle-submenu {{ in_array(Request::path(), ['books', 'sales/create', 'manage-purchases', 'books-report']) ? 'active' : '' }}"
                        aria-expanded="false">
                        <i class="fas fa-book-open"></i><span>Stock of Books</span>
                        <i class="fas fa-chevron-down float-end"></i>
                    </a>
                    <div class="submenu">
                        @if (isset($pagePermissions['manage_books']) && $pagePermissions['manage_books'] === 'on')
                            <a href="{{ url('books') }}"
                                class="nav-link {{ Request::path() === 'books' ? 'active' : '' }}"
                                aria-current="{{ Request::path() === 'books' ? 'page' : '' }}">
                                <i class="fas fa-book"></i><span>Manage Books</span>
                            </a>
                        @endif
                        @if (isset($pagePermissions['manage_sales']) && $pagePermissions['manage_sales'] === 'on')
                            <a href="{{ url('sales/create') }}"
                                class="nav-link {{ Request::path() === 'sales/create' ? 'active' : '' }}"
                                aria-current="{{ Request::path() === 'sales/create' ? 'page' : '' }}">
                                <i class="fas fa-money-bill"></i><span>Manage Sales</span>
                            </a>
                        @endif
                        @if (isset($pagePermissions['manage_purchases']) && $pagePermissions['manage_purchases'] === 'on')
                            <a href="{{ url('manage-purchases') }}"
                                class="nav-link {{ Request::path() === 'manage-purchases' ? 'active' : '' }}"
                                aria-current="{{ Request::path() === 'manage-purchases' ? 'page' : '' }}">
                                <i class="fas fa-shopping-cart"></i><span>Manage Purchases</span>
                            </a>
                        @endif
                        @if (isset($pagePermissions['books_report']) && $pagePermissions['books_report'] === 'on')
                            <a href="{{ url('books-report') }}"
                                class="nav-link {{ Request::path() === 'books-report' ? 'active' : '' }}"
                                aria-current="{{ Request::path() === 'books-report' ? 'page' : '' }}">
                                <i class="fas fa-chart-bar"></i><span>Books Report</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            @if (isset($pagePermissions['assign_books']) && $pagePermissions['assign_books'] === 'on')
                <div class="nav-item">
                    <a href="#"
                        class="nav-link toggle-submenu {{ Request::path() === 'assign-book' ? 'active' : '' }}"
                        aria-expanded="false">
                        <i class="fas fa-book-reader"></i><span>Assign Books</span>
                        <i class="fas fa-chevron-down float-end"></i>
                    </a>
                    <div class="submenu">
                        <a href="{{ url('assign-book') }}"
                            class="nav-link {{ Request::path() === 'assign-book' ? 'active' : '' }}"
                            aria-current="{{ Request::path() === 'assign-book' ? 'page' : '' }}">
                            <i class="fas fa-book"></i><span>Assign Book</span>
                        </a>
                    </div>
                </div>
            @endif

            @php
                $hasPaymentsSubMenuAccess =
                    (isset($pagePermissions['pay_student_fee']) && $pagePermissions['pay_student_fee'] === 'on') ||
                    (isset($pagePermissions['previous_payment']) && $pagePermissions['previous_payment'] === 'on') ||
                    (isset($pagePermissions['payment_export']) && $pagePermissions['payment_export'] === 'on') ||
                    (isset($pagePermissions['defaulter_list']) && $pagePermissions['defaulter_list'] === 'on') ||
                    (isset($pagePermissions['payment_logs']) && $pagePermissions['payment_logs'] === 'on');
            @endphp
            @if ($hasPaymentsSubMenuAccess)
                <div class="nav-item">
                    <a href="#"
                        class="nav-link toggle-submenu {{ in_array(Request::path(), ['pay-student-fee', 'previous-payments', 'payment-export', 'defaulter-list', 'payment-logs']) ? 'active' : '' }}"
                        aria-expanded="false">
                        <i class="fas fa-money-check-alt"></i><span>Payments</span>
                        <i class="fas fa-chevron-down float-end"></i>
                    </a>
                    <div class="submenu">
                        @if (isset($pagePermissions['pay_student_fee']) && $pagePermissions['pay_student_fee'] === 'on')
                            <a href="{{ url('pay-student-fee') }}"
                                class="nav-link {{ Request::path() === 'pay-student-fee' ? 'active' : '' }}"
                                aria-current="{{ Request::path() === 'pay-student-fee' ? 'page' : '' }}">
                                <i class="fas fa-credit-card"></i><span>Pay Student Fee</span>
                            </a>
                        @endif
                        @if (isset($pagePermissions['previous_payment']) && $pagePermissions['previous_payment'] === 'on')
                            <a href="{{ url('previous-payments') }}"
                                class="nav-link {{ Request::path() === 'previous-payments' ? 'active' : '' }}"
                                aria-current="{{ Request::path() === 'previous-payments' ? 'page' : '' }}">
                                <i class="fas fa-history"></i><span>Previous Payments</span>
                            </a>
                        @endif
                        @if (isset($pagePermissions['payment_export']) && $pagePermissions['payment_export'] === 'on')
                            <a href="{{ url('payment-export') }}"
                                class="nav-link {{ Request::path() === 'payment-export' ? 'active' : '' }}"
                                aria-current="{{ Request::path() === 'payment-export' ? 'page' : '' }}">
                                <i class="fas fa-file-export"></i><span>Payment Export</span>
                            </a>
                        @endif
                        @if (isset($pagePermissions['defaulter_list']) && $pagePermissions['defaulter_list'] === 'on')
                            <a href="{{ url('defaulter-list') }}"
                                class="nav-link {{ Request::path() === 'defaulter-list' ? 'active' : '' }}"
                                aria-current="{{ Request::path() === 'defaulter-list' ? 'page' : '' }}">
                                <i class="fas fa-exclamation-triangle"></i><span>Defaulter List</span>
                            </a>
                        @endif
                        @if (isset($pagePermissions['payment_logs']) && $pagePermissions['payment_logs'] === 'on')
                            <a href="{{ url('payment-logs') }}"
                                class="nav-link {{ Request::path() === 'payment-logs' ? 'active' : '' }}"
                                aria-current="{{ Request::path() === 'payment-logs' ? 'page' : '' }}">
                                <i class="fas fa-file-alt"></i><span>Payment Logs</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            @if (isset($pagePermissions['search_student']) && $pagePermissions['search_student'] === 'on')
                <div class="nav-item">
                    <a href="{{ url('searchStudent') }}"
                        class="nav-link {{ Request::path() === 'searchStudent' ? 'active' : '' }}"
                        aria-current="{{ Request::path() === 'searchStudent' ? 'page' : '' }}">
                        <i class="fas fa-search"></i><span>Search Student</span>
                    </a>
                </div>
            @endif

            @php
                $hasStaffSubMenuAccess =
                    (isset($pagePermissions['teacher_roster']) && $pagePermissions['teacher_roster'] === 'on');
            @endphp
            @if ($hasStaffSubMenuAccess)
                <div class="nav-item">
                    <a href="#"
                        class="nav-link toggle-submenu {{ in_array(Request::path(), ['teacher-roster', 'schedule']) ? 'active' : '' }}"
                        aria-expanded="false">
                        <i class="fas fa-chalkboard-teacher"></i><span>Staff Management</span>
                        <i class="fas fa-chevron-down float-end"></i>
                    </a>
                    <div class="submenu">
                        @if (isset($pagePermissions['teacher_roster']) && $pagePermissions['teacher_roster'] === 'on')
                            <a href="{{ url('teacher-roster') }}"
                                class="nav-link {{ Request::path() === 'teacher-roster' ? 'active' : '' }}"
                                aria-current="{{ Request::path() === 'teacher-roster' ? 'page' : '' }}">
                                <i class="fas fa-list-ul"></i><span>Teacher Roster</span>
                            </a>
                        @endif
                        {{-- @if (isset($pagePermissions['schedule']) && $pagePermissions['schedule'] === 'on')
                            <a href="{{ url('schedule') }}"
                                class="nav-link {{ Request::path() === 'schedule' ? 'active' : '' }}"
                                aria-current="{{ Request::path() === 'schedule' ? 'page' : '' }}">
                                <i class="fas fa-calendar-alt"></i><span>Schedule</span>
                            </a>
                        @endif --}}
                    </div>
                </div>
            @endif

            @if (isset($pagePermissions['logs']) && $pagePermissions['logs'] === 'on')
                <div class="nav-item">
                    <a href="{{ url('activity-log') }}"
                        class="nav-link {{ Request::path() === 'activity-log' ? 'active' : '' }}"
                        aria-current="{{ Request::path() === 'activity-log' ? 'page' : '' }}">
                        <i class="fas fa-history"></i><span>Logs</span>
                    </a>
                </div>
            @endif

            @if (isset($pagePermissions['diaries']) && $pagePermissions['diaries'] === 'on')
                <div class="nav-item">
                    <a href="{{ url('users/note') }}"
                        class="nav-link {{ Request::path() === 'users/note' ? 'active' : '' }}"
                        aria-current="{{ Request::path() === 'users/note' ? 'page' : '' }}">
                        <i class="fas fa-sticky-note"></i><span>Diaries</span>
                    </a>
                </div>
            @endif

           @php
    $hasReportsSubMenuAccess =
        (isset($pagePermissions['student_reports']) && $pagePermissions['student_reports'] === 'on') ||
        (isset($pagePermissions['teacher_reports']) && $pagePermissions['teacher_reports'] === 'on') ||
        (isset($pagePermissions['baseline_report']) && $pagePermissions['baseline_report'] === 'on') ||
        (isset($pagePermissions['progress_tracking_report']) && $pagePermissions['progress_tracking_report'] === 'on') ||
        (isset($pagePermissions['all_students_progress_report']) && $pagePermissions['all_students_progress_report'] === 'on') ||
        (isset($pagePermissions['lesson_tracking_report']) && $pagePermissions['lesson_tracking_report'] === 'on');
@endphp

@if ($hasReportsSubMenuAccess)
    <div class="nav-item">
        <a href="#"
            class="nav-link toggle-submenu {{ in_array(Request::path(), [
                'students-reports',
                'teacher-report',
                'baseline-report',
                'progress-tracking-report',
                'all-students-progress-report',
                'lesson-tracking-report'
            ]) ? 'active' : '' }}"
            aria-expanded="false">
            <i class="fas fa-chart-bar"></i><span>Reports</span>
            <i class="fas fa-chevron-down float-end"></i>
        </a>

        <div class="submenu">
            @if (isset($pagePermissions['student_reports']) && $pagePermissions['student_reports'] === 'on')
                <a href="{{ url('students-reports') }}"
                    class="nav-link {{ Request::path() === 'students-reports' ? 'active' : '' }}">
                    <i class="fas fa-user-graduate"></i><span>Students Reports</span>
                </a>
            @endif

            @if (isset($pagePermissions['teacher_reports']) && $pagePermissions['teacher_reports'] === 'on')
                <a href="{{ url('teacher-report') }}"
                    class="nav-link {{ Request::path() === 'teacher-report' ? 'active' : '' }}">
                    <i class="fas fa-chalkboard-teacher"></i><span>Teacher Report</span>
                </a>
            @endif

            @if (isset($pagePermissions['baseline_report']) && $pagePermissions['baseline_report'] === 'on')
                <a href="{{ url('baseline-report') }}"
                    class="nav-link {{ Request::path() === 'baseline-report' ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i><span>Starting Point / Baseline Report</span>
                </a>
            @endif

            @if (isset($pagePermissions['progress_tracking_report']) && $pagePermissions['progress_tracking_report'] === 'on')
                <a href="{{ url('progress-tracking-report') }}"
                    class="nav-link {{ Request::path() === 'progress-tracking-report' ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i><span>Progress Tracking Report</span>
                </a>
            @endif

            @if (isset($pagePermissions['all_students_progress_report']) &&
                    $pagePermissions['all_students_progress_report'] === 'on')
                <a href="{{ url('all-students-progress-report') }}"
                    class="nav-link {{ Request::path() === 'all-students-progress-report' ? 'active' : '' }}">
                    <i class="fas fa-users"></i><span>All Students Progress</span>
                </a>
            @endif

            {{-- ✅ NEW: Lesson Tracking Report --}}
            @if (isset($pagePermissions['lesson_tracking_report']) &&
                    $pagePermissions['lesson_tracking_report'] === 'on')
                <a href="{{ url('lesson-tracking-report') }}"
                    class="nav-link {{ Request::path() === 'lesson-tracking-report' ? 'active' : '' }}">
                    <i class="fas fa-book-open"></i><span>Lesson Tracking Report</span>
                </a>
            @endif
        </div>
    </div>
@endif


            @if (isset($pagePermissions['active_inactive_students']) && $pagePermissions['active_inactive_students'] === 'on')
                <div class="nav-item">
                    <a href="{{ url('active-inactive-students') }}"
                        class="nav-link {{ Request::path() === 'active-inactive-students' ? 'active' : '' }}"
                        aria-current="{{ Request::path() === 'active-inactive-students' ? 'page' : '' }}">
                        <i class="fas fa-user-graduate"></i><span>Active/Inactive Students</span>
                    </a>
                </div>
            @endif
          @php
    $hasCrashCourseAccess =
        (isset($pagePermissions['crash_course_package_management']) &&
            $pagePermissions['crash_course_package_management'] === 'on') ||
        (isset($pagePermissions['crash_course_registration']) &&
            $pagePermissions['crash_course_registration'] === 'on') ||
        (isset($pagePermissions['crash_course_attendance']) &&
            $pagePermissions['crash_course_attendance'] === 'on') ||
        (isset($pagePermissions['crash_course_view_records']) &&
            $pagePermissions['crash_course_view_records'] === 'on');
@endphp
@if ($hasCrashCourseAccess)
    <div class="nav-item has-submenu">
        <a href="#" class="nav-link toggle-submenu {{ in_array(Request::path(), ['crash-course', 'crash-course/packages', 'crash-course/registration', 'crash-course/attendance', 'crash-course/records']) ? 'active' : '' }}">
            <i class="fas fa-bolt"></i>
            <span>Crash Course</span>
            <i class="fas fa-angle-down submenu-arrow"></i>
        </a>

        <div class="submenu">

            @if (!empty($pagePermissions['crash_course_package_management']) &&
                $pagePermissions['crash_course_package_management'] === 'on')
                <a href="{{ url('crash-course/packages') }}" class="nav-link {{ Request::path() === 'crash-course/packages' ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    <span>Package Management</span>
                </a>
            @endif

            @if (!empty($pagePermissions['crash_course_registration']) &&
                $pagePermissions['crash_course_registration'] === 'on')
                <a href="{{ url('crash-course/registration') }}" class="nav-link {{ Request::path() === 'crash-course/registration' ? 'active' : '' }}">
                    <i class="fas fa-user-plus"></i>
                    <span>Registration & Payment</span>
                </a>
            @endif

            @if (!empty($pagePermissions['crash_course_attendance']) &&
                $pagePermissions['crash_course_attendance'] === 'on')
                <a href="{{ url('crash-course/attendance') }}" class="nav-link {{ Request::path() === 'crash-course/attendance' ? 'active' : '' }}">
                    <i class="fas fa-calendar-check"></i>
                    <span>Attendance</span>
                </a>
            @endif

            @if (!empty($pagePermissions['crash_course_view_records']) &&
                $pagePermissions['crash_course_view_records'] === 'on')
                <a href="{{ url('crash-course/records') }}" class="nav-link {{ Request::path() === 'crash-course/records' ? 'active' : '' }}">
                    <i class="fas fa-folder-open"></i>
                    <span>View Records</span>
                </a>
            @endif

        </div>
    </div>
@endif




            @php
                $hasWhatsAppSubMenuAccess =
                    (isset($pagePermissions['send_whatsapp_msg']) && $pagePermissions['send_whatsapp_msg'] === 'on') ||
                    (isset($pagePermissions['receive_whatsapp_msg']) &&
                        $pagePermissions['receive_whatsapp_msg'] === 'on');
            @endphp
            @if ($hasWhatsAppSubMenuAccess)
                <div class="nav-item">
                    <a href="#"
                        class="nav-link toggle-submenu {{ in_array(Request::path(), ['whatsapp', 'whatsapp/receive']) ? 'active' : '' }}"
                        aria-expanded="false">
                        <i class="fab fa-whatsapp"></i><span>WhatsApp Messages</span>
                        <i class="fas fa-chevron-down float-end"></i>
                    </a>
                    <div class="submenu">
                        @if (isset($pagePermissions['send_whatsapp_msg']) && $pagePermissions['send_whatsapp_msg'] === 'on')
                            <a href="{{ url('whatsapp') }}"
                                class="nav-link {{ Request::path() === 'whatsapp' ? 'active' : '' }}"
                                aria-current="{{ Request::path() === 'whatsapp' ? 'page' : '' }}">
                                <i class="fas fa-paper-plane"></i><span>Send Messages</span>
                            </a>
                        @endif
                        {{-- @if (isset($pagePermissions['receive_whatsapp_msg']) && $pagePermissions['receive_whatsapp_msg'] === 'on')
                            <a href="{{ url('whatsapp/receive') }}"
                                class="nav-link {{ Request::path() === 'whatsapp/receive' ? 'active' : '' }}"
                                aria-current="{{ Request::path() === 'whatsapp/receive' ? 'page' : '' }}">
                                <i class="fas fa-inbox"></i><span>Receive Messages</span>
                            </a>
                        @endif --}}
                    </div>
                </div>
            @endif

            @if (isset($pagePermissions['manage_permissions']) && $pagePermissions['manage_permissions'] === 'on')
                <div class="nav-item">
                    <a href="{{ url('manage-permissions') }}"
                        class="nav-link {{ Request::path() === 'manage-permissions' ? 'active' : '' }}"
                        aria-current="{{ Request::path() === 'manage-permissions' ? 'page' : '' }}">
                        <i class="fas fa-shield-alt"></i><span>Manage Permissions</span>
                    </a>
                </div>
            @endif
        </div>
        <div class="footer">
            © <span id="copy-year"></span> Frobel
        </div>
    </div>

    <div class="content">
        <div class="header">
            <button class="menu-toggle" aria-label="Open menu">
                <i class="fas fa-bars"></i>
            </button>
            <div class="notifications" id="notificationBell" role="button" aria-label="Notifications">
                {{-- <i class="fas fa-bell"></i>
                <span class="badge">3</span>
                <div class="notification-bar" id="notificationBar" aria-live="polite">
                    <div class="notification-item">
                        <i class="fas fa-info-circle"></i>
                        <span>New exam results published.</span>
                        <i class="fas fa-times dismiss-btn" aria-label="Dismiss notification"></i>
                    </div>
                    <div class="notification-item">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Payment due for Branch A.</span>
                        <i class="fas fa-times dismiss-btn" aria-label="Dismiss notification"></i>
                    </div>
                    <div class="notification-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Branch B report generated.</span>
                        <i class="fas fa-times dismiss-btn" aria-label="Dismiss notification"></i>
                    </div>
                </div> --}}
            </div>
            <div class="user-section">
                <div class="clock" id="clock" aria-live="assertive">07:57:00 PM PKT</div>
                <div class="dropdown">
                    <button class="btn dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fas fa-user"></i> {{ auth()->user()->name ?? 'User' }}
                    </button>
                     @if(auth()->user()->role === 'super_admin')
                     <ul class="dropdown-menu" aria-labelledby="userDropdown">
                        {{-- <li><a class="dropdown-item" href="{{ url('profile') }}">Profile</a></li> --}}
                        <li>


                                <button type="" class="dropdown-item">Login restricted</button>

                        </li>
                    </ul>
                    @else
                     <ul class="dropdown-menu" aria-labelledby="userDropdown">
                        {{-- <li><a class="dropdown-item" href="{{ url('profile') }}">Profile</a></li> --}}
                        <li>
                            <form action="{{ url('logout') }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>

                     @endif

                </div>
            </div>
        </div>
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update clock
        function updateClock() {
            const now = new Date();
            const options = {
                timeZone: 'Europe/London', // UK time zone (automatically adjusts for BST)
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            };
            const timeString = now.toLocaleTimeString('en-GB', options) +
                (now.getTimezoneOffset() === 0 ? ' GMT' : ' '); // Shows GMT or BST based on daylight saving
            document.getElementById('clock').textContent = timeString;
        }

        setInterval(updateClock, 1000);
        updateClock();

        // Set copyright year
        document.getElementById('copy-year').textContent = new Date().getFullYear();

        // Sidebar toggle
        const menuToggle = document.querySelector('.menu-toggle');
        const sidebar = document.querySelector('.sidebar');
        const closeBtn = document.querySelector('.sidebar .close-btn');

        if (menuToggle && sidebar) {
            menuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('active');
            });
        }

        if (closeBtn && sidebar) {
            closeBtn.addEventListener('click', () => {
                sidebar.classList.remove('active');
            });
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (!sidebar || !menuToggle) return;
            if (window.innerWidth <= 992 && !sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        });

        // Highlight active menu/submenu item and expand submenu if active
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname.replace(/\/$/, '');
            const navLinks = document.querySelectorAll('.sidebar .nav-link');

            navLinks.forEach(link => {
                const href = link.getAttribute('href')?.replace(/\/$/, '');
            });

            const submenus = document.querySelectorAll('.sidebar .submenu');
            submenus.forEach(submenu => {
                const hasActiveSubmenuItem = submenu.querySelector('.nav-link.active');
                if (hasActiveSubmenuItem) {
                    submenu.classList.add('show');
                    const parentToggle = submenu.previousElementSibling;
                    parentToggle.classList.add('active-submenu');
                    parentToggle.setAttribute('aria-expanded', 'true');
                    const chevron = parentToggle.querySelector('.fa-chevron-down');
                    if (chevron) {
                        chevron.classList.add('rotate-180');
                    }
                }
            });
        });

        // Toggle submenu on click
        document.querySelectorAll('.toggle-submenu').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const submenu = this.nextElementSibling;
                const isExpanded = submenu.classList.contains('show');
                submenu.classList.toggle('show');
                this.setAttribute('aria-expanded', !isExpanded);
                const chevron = this.querySelector('.fa-chevron-down');
                chevron.classList.toggle('rotate-180');
            });
        });

        // Notification bar toggle
        const notificationBell = document.getElementById('notificationBell');
        const notificationBar = document.getElementById('notificationBar');

        if (notificationBell && notificationBar) {
            notificationBell.addEventListener('click', function(e) {
                e.stopPropagation();
                notificationBar.classList.toggle('show');
            });

            document.addEventListener('click', function(e) {
                if (!notificationBell.contains(e.target) && !notificationBar.contains(e.target)) {
                    notificationBar.classList.remove('show');
                }
            });
        }

        // Dismiss notifications
        document.querySelectorAll('.notification-item .dismiss-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const notification = this.parentElement;
                notification.style.opacity = '0';
                setTimeout(() => {
                    notification.remove();
                    const badge = document.querySelector('.notifications .badge');
                    const currentCount = parseInt(badge.textContent);
                    badge.textContent = Math.max(0, currentCount - 1);
                }, 300);
            });
        });
    </script>
</body>

</html>
