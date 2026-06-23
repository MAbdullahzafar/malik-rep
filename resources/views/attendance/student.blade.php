
@extends('layouts.app')

@section('content')

<style>
    /* Premium Modern Minimalist Layout Extensions */
    .tech-card {
        background: #ffffff !important;
        border: 1px solid #eaeaea !important;
        border-radius: 12px !important;
        padding: 32px;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
    }
    
    .tech-label {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #888888;
        margin-bottom: 6px;
        display: block;
    }

    .btn-tech-dark {
        background: #111111;
        color: #ffffff;
        border: 1px solid #111111;
        padding: 10px 22px;
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: 6px;
        transition: all 0.15s ease;
        cursor: pointer;
    }
    .btn-tech-dark:hover {
        background: #000000;
        color: #ffffff;
    }

    .tech-input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #eaeaea;
        border-radius: 6px;
        background: #ffffff;
        font-size: 0.9rem;
        color: #111111;
        transition: border-color 0.15s ease;
    }
    .tech-input:focus {
        border-color: #111111;
        outline: none;
    }

    /* Attendance Grid Elements */
    .tech-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
        margin-top: 15px;
    }
    .tech-table thead tr th {
        color: #888888;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 16px;
        border-bottom: 1px solid #eaeaea;
    }
    .tech-table tbody tr {
        background: #ffffff;
    }
    .tech-table tbody tr td {
        padding: 16px;
        border-top: 1px solid #eaeaea;
        border-bottom: 1px solid #eaeaea;
        color: #111111;
        font-size: 0.9rem;
        vertical-align: middle;
    }
    .tech-table tbody tr td:first-child {
        border-left: 1px solid #eaeaea;
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
    }
    .tech-table tbody tr td:last-child {
        border-right: 1px solid #eaeaea;
        border-top-right-radius: 8px;
        border-bottom-right-radius: 8px;
    }

    /* Minimalist Attendance Status Radio Custom Toggles */
    .status-pill-group {
        display: flex;
        gap: 4px;
    }
    .status-pill-item {
        position: relative;
    }
    .status-pill-item input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0; height: 0;
    }
    .status-label {
        display: inline-block;
        padding: 6px 14px;
        font-size: 0.8rem;
        font-weight: 600;
        border-radius: 6px;
        border: 1px solid #eaeaea;
        color: #666666;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .status-pill-item input[type="radio"]:checked + .status-label.p-lbl { background: #fff5f5; border-color: #0070f3; color: #0070f3; background: transparent; }
    .status-pill-item input[type="radio"]:checked + .status-label.a-lbl { background: #fff5f5; border-color: #dc3545; color: #dc3545; }
    .status-pill-item input[type="radio"]:checked + .status-label.l-lbl { background: #fff9db; border-color: #f59e0b; color: #f59e0b; }
    .status-pill-item input[type="radio"]:checked + .status-label.e-lbl { background: #f4f4f5; border-color: #6c757d; color: #6c757d; }
</style>

<div class="container-fluid p-0">
    <!-- Header Configuration Matrix Panel -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="fw-bold tracking-tight text-black mb-1" style="font-size: 1.75rem;">Student Attendance Matrix</h2>
            <p class="text-muted small mb-0">Select curriculum tracking indexes to log or adjust daily session entries.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-dark mb-4" style="background: #111111; color: #ffffff; border: none; border-radius: 8px; padding: 14px 20px; font-size: 0.9rem;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Selection Configuration Control Deck -->
    <div class="card tech-card mb-4">
        <div class="card-body p-0">
            <form method="GET" action="{{ route('attendance.student.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-5">
                        <label class="tech-label">Active Curriculum Course</label>
                        <select name="course_id" class="tech-input" required>
                            <option value="">Select registry reference...</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ $selectedCourseId == $course->id ? 'selected' : '' }}>
                                    {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="tech-label">Session Tracking Date</label>
                        <input type="date" name="date" class="tech-input" value="{{ $selectedDate }}" max="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-12 col-md-3">
                        <button type="submit" class="btn btn-tech-dark w-100" style="padding: 11px 22px;">
                            Load Attendance Register
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Entry Directory Matrix Sheet -->
    @if($selectedCourseId)
        <div class="card tech-card">
            <div class="card-body p-0">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <span class="tech-label">Register Status Indicator</span>
                        <h4 class="fw-bold tracking-tight text-black m-0" style="font-size: 1.15rem;">
                            @if($existingSheet)
                                <span style="color: #f59e0b;">● Editing Existing Records</span>
                            @else
                                <span style="color: #10b981;">● New Attendance Session</span>
                            @endif
                        </h4>
                    </div>
                </div>

                <form method="POST" action="{{ route('attendance.student.store') }}">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $selectedCourseId }}">
                    <input type="hidden" name="attendance_date" value="{{ $selectedDate }}">

                    <div class="table-responsive">
                        <table class="tech-table">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th>Student Name</th>
                                    <th style="width: 320px;">Presence Status</th>
                                    <th>Log Remarks / Exceptions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $entry)
                                    @php
                                        // Handle data whether it is an existing record model or a raw student record model
                                        $student = $existingSheet ? $entry->student : $entry;
                                        $currentStatus = $existingSheet ? $entry->status : 'Present';
                                        $currentRemarks = $existingSheet ? $entry->remarks : '';
                                    @endphp

                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td style="font-weight: 600;">{{ $student->name }}</td>
                                        <td>
                                            <div class="status-pill-group">
                                                <!-- Present Toggle -->
                                                <div class="status-pill-item">
                                                    <input type="radio" 
                                                           name="attendance[{{ $student->id }}][status]" 
                                                           id="p-{{ $student->id }}" 
                                                           value="Present" 
                                                           {{ $currentStatus == 'Present' ? 'checked' : '' }}>
                                                    <label for="p-{{ $student->id }}" class="status-label p-lbl">Present</label>
                                                </div>
                                                
                                                <!-- Absent Toggle -->
                                                <div class="status-pill-item">
                                                    <input type="radio" 
                                                           name="attendance[{{ $student->id }}][status]" 
                                                           id="a-{{ $student->id }}" 
                                                           value="Absent" 
                                                           {{ $currentStatus == 'Absent' ? 'checked' : '' }}>
                                                    <label for="a-{{ $student->id }}" class="status-label a-lbl">Absent</label>
                                                </div>

                                                <!-- Late Toggle -->
                                                <div class="status-pill-item">
                                                    <input type="radio" 
                                                           name="attendance[{{ $student->id }}][status]" 
                                                           id="l-{{ $student->id }}" 
                                                           value="Late" 
                                                           {{ $currentStatus == 'Late' ? 'checked' : '' }}>
                                                    <label for="l-{{ $student->id }}" class="status-label l-lbl">Late</label>
                                                </div>

                                                <!-- Excused Toggle -->
                                                <div class="status-pill-item">
                                                    <input type="radio" 
                                                           name="attendance[{{ $student->id }}][status]" 
                                                           id="e-{{ $student->id }}" 
                                                           value="Excused" 
                                                           {{ $currentStatus == 'Excused' ? 'checked' : '' }}>
                                                    <label for="e-{{ $student->id }}" class="status-label e-lbl">Excused</label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" 
                                                   name="attendance[{{ $student->id }}][remarks]" 
                                                   value="{{ $currentRemarks }}" 
                                                   class="tech-input" 
                                                   placeholder="Optional details (e.g., Sick leave, Late bus)..." 
                                                   style="padding: 6px 12px; font-size: 0.85rem;">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted" style="font-style: italic;">
                                            No student records found matching this course context.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($students->count() > 0)
                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-tech-dark px-5">
                                Commit Attendance Registry
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    @endif
</div>

@endsection
