<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Dashboard - Frobel</title>
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
            touch-action: manipulation;
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

        .header .welcome-text {
            color: var(--text-light);
            font-size: 18px;
            font-weight: 500;
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

        .header .logout-btn {
            background-color: var(--red-logout);
            color: var(--text-light);
            border: none;
            padding: 8px 16px;
            border-radius: 40px;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .header .logout-btn:hover {
            background-color: #B91C1C;
            transform: translateY(-1px);
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

            .header .welcome-text {
                font-size: 16px;
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

            .header .welcome-text {
                font-size: 14px;
                flex: 1;
            }

            .header .user-section {
                gap: 8px;
            }

            .header .user-section .dropdown .btn,
            .header .logout-btn {
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

            .header .user-section .dropdown .btn,
            .header .logout-btn {
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
    <div class="sidebar" role="navigation" aria-label="Main navigation">
        <div class="logo">
            <img src="{{ asset('FrobelEducationWhite (1).png') }}" alt="Frobel Logo" style="background: #002487;">
            <button class="close-btn" aria-label="Close menu">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="nav-container">
            @if (isset($pagePermissions['dashboard']) && $pagePermissions['dashboard'] === 'on')
                <div class="nav-item">
                    <a href="{{ url('home') }}" class="nav-link {{ Request::path() === 'home' ? 'active' : '' }}"
                        aria-current="{{ Request::path() === 'home' ? 'page' : '' }}">
                        <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
                    </a>
                </div>
            @endif

            @php
                $hasBranchControlSubMenuAccess =
                    (isset($pagePermissions['create_branch']) && $pagePermissions['create_branch'] === 'on') ||
                    (isset($pagePermissions['list_branch']) && $pagePermissions['list_branch'] === 'on');
            @endphp
            @if ($hasBranchControlSubMenuAccess)
                <div class="nav-item">
                    <a href="#"
                        class="nav-link toggle-submenu {{ in_array(Request::path(), ['create-branch', 'list-branch']) ? 'active' : '' }}"
                        aria-expanded="false">
                        <i class="fas fa-code-branch"></i><span>Branch Control</span>
                        <i class="fas fa-chevron-down float-end"></i>
                    </a>
                    <div class="submenu">
                        @if (isset($pagePermissions['create_branch']) && $pagePermissions['create_branch'] === 'on')
                            <a href="{{ url('create-branch') }}"
                                class="nav-link {{ Request::path() === 'create-branch' ? 'active' : '' }}"
                                aria-current="{{ Request::path() === 'create-branch' ? 'page' : '' }}">
                                <i class="fas fa-plus-circle"></i><span>Create Branch</span>
                            </a>
                        @endif
                        @if (isset($pagePermissions['list_branch']) && $pagePermissions['list_branch'] === 'on')
                            <a href="{{ url('list-branch') }}"
                                class="nav-link {{ Request::path() === 'list-branch' ? 'active' : '' }}"
                                aria-current="{{ Request::path() === 'list-branch' ? 'page' : '' }}">
                                <i class="fas fa-list-ul"></i><span>List Branches</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            @php
                $hasUsersSubMenuAccess = isset($pagePermissions['users']) && $pagePermissions['users'] === 'on';
            @endphp
            @if ($hasUsersSubMenuAccess)
                <div class="nav-item">
                    <a href="#"
                        class="nav-link toggle-submenu {{ Request::path() === 'manage-superadmin-users' ? 'active' : '' }}"
                        aria-expanded="false">
                        <i class="fas fa-users"></i><span>Users</span>
                        <i class="fas fa-chevron-down float-end"></i>
                    </a>
                    <div class="submenu">
                        @if (isset($pagePermissions['users']) && $pagePermissions['users'] === 'on')
                            <a href="{{ url('manage-superadmin-users') }}"
                                class="nav-link {{ Request::path() === 'manage-superadmin-users' ? 'active' : '' }}"
                                aria-current="{{ Request::path() === 'manage-superadmin-users' ? 'page' : '' }}">
                                <i class="fas fa-user-plus"></i><span>Add User</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            @if (isset($pagePermissions['roles']) && $pagePermissions['roles'] === 'on')
                <div class="nav-item">
                    <a href="#"
                        class="nav-link toggle-submenu {{ Request::path() === 'manage-roles' ? 'active' : '' }}"
                        aria-expanded="false">
                        <i class="fas fa-user-tag"></i><span>Roles</span>
                        <i class="fas fa-chevron-down float-end"></i>
                    </a>
                    <div class="submenu">
                        <a href="{{ url('manage-roles') }}"
                            class="nav-link {{ Request::path() === 'manage-roles' ? 'active' : '' }}"
                            aria-current="{{ Request::path() === 'manage-roles' ? 'page' : '' }}">
                            <i class="fas fa-cogs"></i><span>Manage Roles</span>
                        </a>
                    </div>
                </div>
            @endif

            @if (isset($pagePermissions['grant_permission']) && $pagePermissions['grant_permission'] === 'on')
                <div class="nav-item">
                    <a href="#"
                        class="nav-link toggle-submenu {{ Request::path() === 'grant-permission' ? 'active' : '' }}"
                        aria-expanded="false">
                        <i class="fas fa-key"></i><span>Permissions</span>
                        <i class="fas fa-chevron-down float-end"></i>
                    </a>
                    <div class="submenu">
                        <a href="{{ url('grant-permission') }}"
                            class="nav-link {{ Request::path() === 'grant-permission' ? 'active' : '' }}"
                            aria-current="{{ Request::path() === 'grant-permission' ? 'page' : '' }}">
                            <i class="fas fa-check-circle"></i><span>Grant Permission</span>
                        </a>
                    </div>
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
            <div class="welcome-text">Welcome, {{ auth()->user()->name ?? 'User' }}</div>
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
                    <ul class="dropdown-menu" aria-labelledby="userDropdown">
                        {{-- <li><a class="dropdown-item" href="{{ url('profile') }}">Profile</a></li> --}}
                        <li>
                            <form action="{{ url('logout') }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
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

        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });

        closeBtn.addEventListener('click', () => {
            sidebar.classList.remove('active');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
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
                console.log('Link Href:', href, 'Classes:', link.classList.toString());
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

        notificationBell.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationBar.classList.toggle('show');
        });

        document.addEventListener('click', function(e) {
            if (!notificationBell.contains(e.target) && !notificationBar.contains(e.target)) {
                notificationBar.classList.remove('show');
            }
        });

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
