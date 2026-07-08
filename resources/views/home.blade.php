=@extends('layouts.app')

@section('content')

<style>
    /* Premium Modern Minimalist Dashboard Style Extensions */
    .tech-card {
        background: #ffffff !important;
        border: 1px solid #eaeaea !important;
        border-radius: 12px !important;
        padding: 24px;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
    }
    
    .tech-card:hover {
        border-color: #111111 !important;
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.04) !important;
    }

    /* Minimalist Neon Pulse Status Dot */
    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        position: absolute;
        top: 24px;
        right: 24px;
    }
    .dot-blue { background-color: #0070f3; box-shadow: 0 0 8px #0070f3; }
    .dot-green { background-color: #10b981; box-shadow: 0 0 8px #10b981; }
    .dot-amber { background-color: #f59e0b; box-shadow: 0 0 8px #f59e0b; }
    .dot-cyan { background-color: #06b6d4; box-shadow: 0 0 8px #06b6d4; }
    .dot-red { background-color: #ef4444; box-shadow: 0 0 8px #ef4444; }

    .tech-label {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #888888;
        margin-bottom: 8px;
        display: block;
    }

    .tech-count {
        font-size: 2.25rem;
        font-weight: 700;
        color: #000000;
        line-height: 1.1;
        margin-bottom: 16px;
    }

    .tech-action-arrow {
        font-size: 0.85rem;
        color: #111111;
        text-decoration: none;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: gap 0.15s ease;
    }

    .tech-card:hover .tech-action-arrow {
        gap: 8px;
    }

    /* Minimalist Attendance Summary Table Framework */
    .tech-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
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
    .tech-table tbody tr:hover td {
        background: #fafafa;
    }
</style>
<div class="container-fluid p-0">
    <!-- Header Section -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="fw-bold tracking-tight text-black mb-1" style="font-size: 1.75rem;">System Overview</h2>
            <p class="text-muted small mb-0">System performance indicators and records.</p>
        </div>
    </div>

    <!-- Metrics Grid -->
    <div class="row g-4 mb-5">
        <!-- Summary Cards (Students, Teachers, Courses, Present, Absent) -->
        @foreach([
            ['label' => 'Students Index', 'count' => $studentCount ?? 0, 'link' => '/students', 'color' => 'blue', 'text' => 'Database'],
            ['label' => 'Faculty Roster', 'count' => $teacherCount ?? 0, 'link' => '/teachers', 'color' => 'green', 'text' => 'Roster'],
            ['label' => 'Active Curriculum', 'count' => $courseCount ?? 0, 'link' => '/courses', 'color' => 'amber', 'text' => 'Courses'],
            ['label' => "Today's Present", 'count' => $todayPresentCount ?? 0, 'link' => '/attendance/student', 'color' => 'cyan', 'text' => 'Attendance'],
            ['label' => "Today's Absent", 'count' => $todayAbsentCount ?? 0, 'link' => '/attendance/student', 'color' => 'red', 'text' => 'Attendance']
        ] as $item)
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card tech-card h-100">
                    <span class="status-dot dot-{{ $item['color'] }}"></span>
                    <div class="card-body p-0 d-flex flex-column justify-content-between">
                        <div>
                            <span class="tech-label">{{ $item['label'] }}</span>
                            <h3 class="tech-count">{{ $item['count'] }}</h3>
                        </div>
                        <a href="{{ url($item['link']) }}" class="tech-action-arrow border-top pt-3 w-100">
                            <span>Open {{ $item['text'] }}</span> <i class="bi bi-arrow-right-short fs-5"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Real-Time Course-Wise & Faculty Attendance Matrix Section -->
    <div class="row g-4">
        
        <!-- Course Attendance Logs Matrix (Left Pane) -->
        <div class="col-12 col-xl-7">
            <div class="card tech-card h-100">
                <div class="card-body p-0">
                    <div class="mb-4">
                        <span class="tech-label">Real-time Curriculum Operational Log</span>
                        <h3 class="fw-bold tracking-tight text-black m-0" style="font-size: 1.3rem;">Course Attendance Logs (Today)</h3>
                    </div>

                    <div class="table-responsive">
                        <table class="tech-table">
                            <thead>
                                <tr>
                                    <th>Course Curriculum Title</th>
                                    <th style="width: 150px;">Present Students</th>
                                    <th style="width: 150px;">Absent Students</th>
                                    <th style="width: 160px; text-align: right;">Registry State</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($coursesAttendanceSummary as $summary)
                                    <tr>
                                        <td style="font-weight: 600;">{{ $summary['name'] }}</td>
                                        <td>
                                            <span class="badge bg-light text-primary border px-3 py-2" style="font-size: 0.85rem; font-weight: 600;">
                                                {{ $summary['present'] }} Present
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-danger border px-3 py-2" style="font-size: 0.85rem; font-weight: 600;">
                                                {{ $summary['absent'] }} Absent
                                            </span>
                                        </td>
                                        <td style="text-align: right;">
                                            @if($summary['is_tracked'])
                                                <span style="color: #10b981; font-weight: 500; font-size: 0.85rem;">● Synchronized</span>
                                            @else
                                                <span style="color: #888888; font-weight: 500; font-size: 0.85rem; font-style: italic;">Pending Entry</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted" style="font-style: italic;">
                                            No curriculum matrices compiled inside the system directory.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Integrated Teacher/Faculty Attendance Summary Matrix Box (Right Pane Header) -->
        <div class="col-12 col-xl-5">
            <div class="card tech-card h-100">
                <div class="card-body p-0 d-flex flex-column justify-content-between">
                    <div>
                        <div class="mb-4">
                            <span class="tech-label">Biometric Verification Directory</span>
                            <h3 class="fw-bold tracking-tight text-black m-0" style="font-size: 1.3rem;">Faculty Attendance (Today)</h3>
                        </div>

                        <!-- Simplified Status Counters List -->
                        <div class="d-flex flex-column gap-3 mb-4">
                            <div class="p-3 border rounded-3 d-flex justify-content-between align-items-center bg-white">
                                <span class="fw-semibold text-secondary" style="font-size: 0.9rem;">Total Faculty</span>
                                <span class="badge bg-dark px-3 py-2 fs-6 fw-bold">{{ $totalTeachers ?? 0 }}</span>
                            </div>
                            
                            <div class="p-3 border rounded-3 d-flex justify-content-between align-items-center bg-white" style="border-left: 4px solid #10b981 !important;">
                                <span class="fw-semibold text-secondary" style="font-size: 0.9rem;">Total Teachers Present</span>
                                <span class="badge text-white px-3 py-2 fs-6 fw-bold" style="background-color: #10b981 !important;">{{ $teachersCheckedIn ?? 0 }}</span>
                            </div>
                            <div class="p-3 border rounded-3 d-flex justify-content-between align-items-center bg-white" style="border-left: 4px solid #ef4444 !important;">
                                <span class="fw-semibold text-secondary" style="font-size: 0.9rem;">Total Teachers Absent</span>
                                <span class="badge text-white px-3 py-2 fs-6 fw-bold" style="background-color: #ef4444 !important;">{{ $teachersAbsent ?? 0 }}</span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ url('/attendance/teacher') }}" class="tech-action-arrow border-top pt-3 w-100 mt-auto">
                        <span>Open Biometric Log Panel</span> <i class="bi bi-arrow-right-short fs-5"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 🌟 NEW MASTER FINANCIAL OVERLOOK & OVERDUE FINE STATS PANEL CARD -->
        <div style="background: #ffffff; border: 1px solid #dee2e6; border-left: 4px solid #b91c1c; border-radius: 8px; padding: 22px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); margin-bottom: 25px; width: 100%; box-sizing: border-box;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f1f1; padding-bottom: 10px; margin-bottom: 15px;">
                <div>
                    <h4 style="margin: 0; font-weight: 800; color: #1e293b; font-size: 15px; letter-spacing: -0.2px;">⚠️ Real-Time Installments & Late Penalty Fines Shield</h4>
                    <p style="margin: 3px 0 0 0; color: #64748b; font-size: 12px;">Live system monitoring for installment compliance and Rs. 50/day overdue penalty fine collections.</p>
                </div>
                <a href="{{ url('/finance/defaulters') }}" style="background: #b91c1c; color: white; text-decoration: none; padding: 6px 14px; border-radius: 6px; font-weight: 700; font-size: 12px; transition: background 0.2s;">
                    Open Defaulter Roster →
                </a>
            </div>

            <!-- Inner Data Counter Points Metric Row Alignment -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div style="background: #f8fafc; padding: 12px; border-radius: 6px; border: 1px solid #edf2f7;">
                    <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Active Overdue Slots</span>
                    <div style="font-size: 22px; font-weight: 800; color: #b91c1c; margin-top: 4px;">
                        {{ DB::table('payment_installments')->where('status', '!=', 'Paid')->where('due_date', '<', date('Y-m-d'))->count() }} Milestones
                    </div>
                </div>
                <div style="background: #f8fafc; padding: 12px; border-radius: 6px; border: 1px solid #edf2f7;">
                    <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Accumulated Late Fines</span>
                    <div style="font-size: 22px; font-weight: 800; color: #d97706; margin-top: 4px;">
                        Rs. {{ number_format(DB::table('payment_installments')->where('status', '!=', 'Paid')->where('due_date', '<', date('Y-m-d'))->sum('fine_charged'), 2) }}
                    </div>
                </div>
                <div style="background: #f8fafc; padding: 12px; border-radius: 6px; border: 1px solid #edf2f7;">
                    <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Overdue Capital</span>
                    <div style="font-size: 22px; font-weight: 800; color: #0284c7; margin-top: 4px;">
                        Rs. {{ number_format(DB::table('payment_installments')->where('status', '!=', 'Paid')->where('due_date', '<', date('Y-m-d'))->sum('base_amount'), 2) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- ⚙️ SUNK PLATFORM STORAGE OPTIMIZATION WIDGET ENGINE -->
        @if(session('success'))
            <div style="margin-bottom: 20px; padding: 12px; font-size: 14px; border-radius: 6px; background-color: #e6f4ea; color: #137333; border: 1px solid #ceead6; font-weight: 700;">
                {{ session('success') }}
            </div>
        @endif

        <div style="background: #ffffff; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-top: 20px; width: 100%; box-sizing: border-box;">
            <h3 style="margin: 0 0 5px 0; font-weight: 800; color: #1e293b; font-size: 16px;">⚙️ Institutional Storage Management Utility</h3>
            <p style="margin: 0 0 15px 0; color: #64748b; font-size: 13px;">Wipes compiled page cache snapshots to maintain maximum server performance speeds.</p>
            
            <form action="{{ route('admin.optimize') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" style="background: #04AA6D; color: white; border: none; padding: 10px 18px; font-weight: 700; font-size: 13px; border-radius: 4px; cursor: pointer; box-shadow: 0 2px 4px rgba(4,170,109,0.2);">
                    ⚡ Flush Cache & Optimize Platform
                </button>
            </form>
        </div>

    </div>
</div>

@endsection
.