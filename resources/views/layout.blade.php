<!DOCTYPE html>
<html lang="en">
    
<head>

<link rel="stylesheet" href="{{ asset('build/assets/app-070655a4.css') }}">
<script src="{{ asset('build/assets/app-dc868d63.js') }}" defer></script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Student Management System</title>

    <style>
        body { background-color: #f8f9fa; font-family: sans-serif; }
        .sidebar {
            margin: 0; padding: 0; width: 200px;
            background-color: #f1f1f1; position: fixed;
            height: 100%; overflow: auto; z-index: 100;
        }
        .sidebar a {
            display: block; color: black;
            padding: 16px; text-decoration: none;
        }
        .sidebar a.active { background-color: #04AA6D; color: white; }
        .sidebar a:hover:not(.active) { background-color: #555; color: white; }
        .main-wrapper { margin-left: 200px; padding: 25px; }
        
        /* HOVER DROP-DOWN SYSTEM FOR STUDENT MODULE */
        .student-dropdown {
            position: relative;
            display: block;
        }
        .dropdown-container {
            display: none;
            background-color: #e4e4e4;
            padding-left: 15px;
        }
        .dropdown-container a {
            padding: 12px 16px;
            font-size: 14px;
        }
        .student-dropdown:hover .dropdown-container {
            display: block;
        }

        @media screen and (max-width: 700px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .sidebar a { float: left; }
            .main-wrapper { margin-left: 0; }
            .student-dropdown { float: left; }
            .student-dropdown:hover .dropdown-container { position: absolute; left: 0; top: 100%; width: 200px; }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 p-3 shadow-sm">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1 text-white">Student Management Project</span>
        </div>
    </nav>

    <div class="sidebar shadow-sm">
        <a class="{{ Request::is('home') || Request::is('/') ? 'active' : '' }}" href="{{ url('/home') }}">Home</a>
        
        <!-- NESTED HOVER DROP-DOWN SUB-PANEL SYSTEM -->
        <div class="student-dropdown">
            <a class="{{ Request::is('students*') || Request::is('grades*') || Request::is('timetables*') ? 'active' : '' }}" href="{{ url('/students') }}">Student ▾</a>
            <div class="dropdown-container">
                <a class="{{ Request::is('students') ? 'active' : '' }}" href="{{ url('/students') }}">All Students</a>
                <a class="{{ Request::is('grades*') ? 'active' : '' }}" href="{{ url('/grades') }}">Grades & Marks</a>
                <a class="{{ Request::is('timetables*') ? 'active' : '' }}" href="{{ url('/timetables') }}">Class Timetable</a>
            </div>
        </div>
        
        <a class="{{ Request::is('teachers*') ? 'active' : '' }}" href="{{ url('/teachers') }}">Teacher</a>
        <a class="{{ Request::is('courses*') ? 'active' : '' }}" href="{{ url('/courses') }}">Course</a>
        
        <!-- INTEGRATED: Distinct active highlighting rule assigned for student attendance tracking -->
        <a class="{{ Request::is('attendance/student*') ? 'active' : '' }}" href="{{ url('/attendance/student') }}">Students Attendance</a>
        
        <!-- INTEGRATED: Distinct active highlighting rule assigned for teacher biometric logs -->
        <a class="{{ Request::is('attendance/teacher*') ? 'active' : '' }}" href="{{ url('/attendance/teacher') }}">Teachers Attendance</a>
        
        <a class="{{ Request::is('payments*') ? 'active' : '' }}" href="{{ url('/payments') }}">Payment</a>
    </div>

    <div class="main-wrapper">
        @yield('content')
    </div>

    <!-- INSTANT BACK RECOVERY: Restores the page immediately from browser memory without freezing on back-clicks -->
    <script>
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>

</body>
</html>
