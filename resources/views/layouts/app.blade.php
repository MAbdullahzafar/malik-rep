<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token Validation Safeguard -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} – School Matrix System</title>

       <!-- Fonts Directory -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://bunny.net" rel="stylesheet">




    <!-- Styles Master Core Sync Layout Aligned With Left Sidebar Schemes -->
    <style>
        body, html {
            background-color: #f4f6f9;
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 0;
            color: #334155;
            height: 100vh;
            overflow: hidden;
        }
        
        /* Master layout container converted to clean horizontal row layout splitting */
        #app {
            display: flex;
            flex-direction: row;
            min-height: 100vh;
            width: 100vw;
        }

        /* 1. RIGID LEFT-HAND NAVIGATION SIDEBAR PANEL */
        .premium-sidebar {
            width: 260px;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            box-shadow: 4px 0 6px -1px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            height: 100vh;
            position: sticky;
            top: 0;
            z-index: 1000;
            flex-shrink: 0; /* Prevents menu column from getting crushed on small screens */
        }
        
        .sidebar-container {
            display: flex;
            flex-direction: column;
            height: 100%;
            padding: 24px 16px;
            box-sizing: border-box;
            overflow-y: auto; /* Independent inner sidebar scrolling */
        }

        .brand-logo {
            font-size: 20px;
            font-weight: 800;
            color: #1e293b;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: -0.5px;
            margin-bottom: 30px;
            padding-left: 8px;
        }
        .brand-logo span {
            color: #04AA6D; /* School Matrix green theme token */
        }
        /* Sidebar Vertical Link Stack Menu Layout */
        .nav-vertical-menu {
            display: flex;
            flex-direction: column;
            gap: 6px;
            list-style: none;
            margin: 0;
            padding: 0;
            flex-grow: 1;
        }

        .nav-link-item {
            color: #475569;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            padding: 12px 16px;
            border-radius: 6px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            box-sizing: border-box;
        }
        .nav-link-item:hover {
            background-color: #f1f5f9;
            color: #1e293b;
        }
        .nav-link-item.active-route {
            background-color: #e2e8f0;
            color: #1e293b;
        }

        /* SIDEBAR INTERACTIVE SLIDING ACCORDION SUBMENUS */
        .sidebar-item-dropdown {
            position: relative;
            width: 100%;
        }
        .sidebar-submenu-card {
            display: none;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 4px 0;
            margin-top: 4px;
            margin-bottom: 8px;
            list-style: none;
        }
        .sidebar-submenu-card a {
            color: #475569;
            padding: 10px 16px;
            text-decoration: none;
            display: block;
            font-size: 13px;
            font-weight: 700;
            transition: background 0.2s ease;
        }
        .sidebar-submenu-card a:hover {
            background-color: #f1f5f9;
            color: #1e293b;
            border-left: 3px solid #04AA6D;
        }
        /* Activates inner sliding links instantly upon mouse hover */
        .sidebar-item-dropdown:hover .sidebar-submenu-card {
            display: block;
        }

        /* 2. RIGHT-HAND MAIN WORKSPACE CANVAS FRAME */
        .workspace-right-frame {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
            min-width: 0; /* Safeguards responsive charts/tables from shifting widths */
            overflow-y: auto; /* Independent content viewport scrolling */
        }
        /* Minimal profile action top header block line */
        .top-profile-strip {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 40px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            flex-shrink: 0;
        }

        /* Dropdown profile badge selector elements */
        .user-profile-dropdown {
            position: relative;
            display: inline-block;
        }
        .dropdown-trigger-btn {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            color: #1e293b;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 700;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s ease;
        }
        .dropdown-trigger-btn:hover {
            background: #e2e8f0;
        }
        .dropdown-content-menu {
            display: none;
            position: absolute;
            right: 0;
            background-color: #ffffff;
            min-width: 200px;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 6px 0;
            z-index: 2000;
            margin-top: 8px;
        }
        .dropdown-content-menu a {
            color: #334155;
            padding: 10px 16px;
            text-decoration: none;
            display: block;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.2s ease;
        }
        .dropdown-content-menu a:hover {
            background-color: #f1f5f9;
            color: #1e293b;
        }
        
        /* Main View Injected Content Workspace Canvas */
        .main-workspace-body {
            padding: 40px;
            box-sizing: border-box;
            flex-grow: 1;
        }

        /* Flash message alerts banner notification framework */
        .custom-alert-banner {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 30px;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .login-btn {
            background-color: #3b82f6;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
            transition: opacity 0.2s ease;
            text-align: center;
            margin-top: auto;
        }
        .login-btn:hover {
            opacity: 0.9;
        }

        @media print {
            .no-print { display: none !important; }
            .workspace-right-frame { overflow: visible !important; height: auto !important; }
        }
    </style>
  

        <!-- Laravel Vite Asset Compiler Bundling Tags -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

</head>
<body>

    <div id="app">
        
        <!-- A. RIGID LEFT-HAND NAVIGATION SIDEBAR COLUMN PANEL -->
        <nav class="premium-sidebar no-print">
            <div class="sidebar-container">
                
                
                <a class="brand-logo" href="{{ Auth::check() ? url('/home') : url('/') }}">
                    🎓 School<span>Matrix</span>
                </a>
                <!-- Sidebar Clickable Actions List -->
                @auth
                    <ul class="nav-vertical-menu">
                        <li>
                            <a href="{{ url('/home') }}" class="nav-link-item {{ Request::is('home') || Request::is('dashboard') ? 'active-route':'' }}">
                                📊 Dashboard Overview
                            </a>
                        </li>
                        
                        <!-- STUDENTS VERTICAL MODULE SUBMENU ACCORDION -->
                        <li class="sidebar-item-dropdown">
                            <a href="#" class="nav-link-item {{ Request::is('students*') || Request::is('attendance/student*') || Request::is('grades*') ? 'active-route':'' }}">
                                <span>👥 Students Module</span> <span style="font-size: 9px;">►</span>
                            </a>
                            <div class="sidebar-submenu-card">
                                <a href="{{ url('/students') }}">📋 Students Directory</a>
                                <a href="{{ url('/attendance/student') }}">📅 Student Attendance Check</a>
                                <a href="{{ url('/grades') }}">📊 Academic Grades Ledger</a>
                            </div>
                        </li>

                        <!-- TEACHERS VERTICAL MODULE SUBMENU ACCORDION -->
                        <li class="sidebar-item-dropdown">
                            <a href="#" class="nav-link-item {{ Request::is('teachers*') || Request::is('attendance/teacher*') ? 'active-route':'' }}">
                                <span>👨‍🏫 Faculty Module</span> <span style="font-size: 9px;">►</span>
                            </a>
                            <div class="sidebar-submenu-card">
                                <a href="{{ url('/teachers') }}">👥 Faculty Directory</a>
                                <a href="{{ url('/attendance/teacher') }}">🎛️ Biometric Logs History</a>
                            </div>
                        </li>

                        <li><a href="{{ url('/courses') }}" class="nav-link-item {{ Request::is('courses*') ? 'active-route':'' }}">📚 Courses Catalog</a></li>
                        <li><a href="{{ url('/timetables') }}" class="nav-link-item {{ Request::is('timetables*') ? 'active-route':'' }}">📅 Class Timetables</a></li>
                        <li><a href="{{ url('/payments') }}" class="nav-link-item {{ Request::is('payments*') ? 'active-route':'' }}">💳 Fee Receipts Log</a></li>

                        <!-- 🌟 HR & SUPPORT NON-FACULTY STAFF MANAGEMENT SEGMENT -->
                        <li style="padding: 20px 16px 6px 16px; font-size: 11px; text-transform: uppercase; font-weight: 800; color: #94a3b8; letter-spacing: 0.6px; border-top: 1px solid #f1f5f9; margin-top: 10px;">
                            HR & Support Payroll
                        </li>

                        <!-- Non-Faculty Support Staff Link -->
                        <li>
                            <a href="{{ url('/staff') }}" class="nav-link-item {{ Request::is('staff*') ? 'active-route':'' }}">
                                🧹 Support Staff Roster
                            </a>
                        </li>

                        <!-- Master Institutional Salary payrolls Link -->
                        <li>
                            <a href="{{ url('/payrolls') }}" class="nav-link-item {{ Request::is('payrolls*') ? 'active-route':'' }}">
                                💼 Salaries Payroll Ledger
                            </a>
                        </li>
                    </ul>
                @endauth
                <!-- Guest System fallback authentication actions links -->
                @guest
                    @if (Route::has('login') && !Request::is('login'))
                        <a class="login-btn" href="{{ route('login') }}">Sign In to Portal</a>
                    @endif
                @endguest

            </div>
        </nav>
        
        <!-- B. RIGHT-HAND CORE LAYOUT FRAMEWORK CONTAINER WORKSPACE -->
        <div class="workspace-right-frame">
            
            <!-- Top account session username header strip ribbon -->
            <div class="top-profile-strip no-print">
                @auth
                    <div class="user-profile-dropdown">
                        <button type="button" class="dropdown-trigger-btn" onclick="toggleUserDropdownMenu()">
                            👤 {{ Auth::user()->name }} <span style="font-size: 10px; margin-left: 2px;">▼</span>
                        </button>
                        <div class="dropdown-content-menu" id="masterUserDropdown">
                            <!-- High Security Sub-Account Registrar Actions Option Module Link -->
                            <a href="{{ route('register') }}" style="color: #04AA6D; font-weight: 700;">
                                ➕ Register New User
                            </a>
                            
                            <!-- Standard Clear Logouts Session Triggers Endpoints -->
                            <a href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                               style="color: #dc3545; font-weight: 700;">
                                Sign Out / Exit Portal
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                @endauth
            </div>

            <!-- Main Interactive Component Workspace View Canvas Container -->
            <main class="main-workspace-body">
                @if(session('success'))
                    <div class="custom-alert-banner">
                        ✅ {{ session('success') }}
                    </div>
                @endif

                @yield('content')
            </main>

        </div>
    </div> <!-- CLOSES THE MASTER FLEXBOX #app HOLDER SHELL CONTAINER -->

    <!-- Dropdown context profile links toggler script -->
    <script>
        function toggleUserDropdownMenu() {
            let dropdown = document.getElementById('masterUserDropdown');
            if (dropdown) {
                dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';
            }
        }

        window.addEventListener('click', function(event) {
            if (!event.target.matches('.dropdown-trigger-btn')) {
                let openDropdown = document.getElementById('masterUserDropdown');
                if (openDropdown && openDropdown.style.display === 'block') {
                    openDropdown.style.display = 'none';
                }
            }
        });
    </script>

    <!-- 🌟 HIGH-SECURITY ADVANCED TAB-CLOSURE WATCHDOG ENGINE -->
    @auth
    <script>
        (function() {
            if (!sessionStorage.getItem('active_portal_session_state')) {
                let logoutData = new FormData();
                logoutData.append('_token', '{{ csrf_token() }}');
                navigator.sendBeacon("{{ route('logout') }}", logoutData);
                window.location.href = "{{ route('login') }}";
            }
            sessionStorage.setItem('active_portal_session_state', 'true');
        })();
    </script>
    @endauth
</body>
</html>
