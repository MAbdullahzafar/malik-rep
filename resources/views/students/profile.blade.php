@extends('layouts.app')
@section('content')

<style>
    /* Premium Modern Minimalist Dashboard Style Extensions */
    .custom-card { 
        background: #ffffff; 
        border: 1px solid #dee2e6; 
        border-radius: 8px; 
        box-shadow: 0 4px 6px rgba(0,0,0,0.05); 
        margin: 15px 0; 
        padding: 20px; 
    }
    .custom-header { 
        border-bottom: 2px solid #f1f1f1; 
        padding-bottom: 12px; 
        margin-bottom: 20px; 
    }
    .stats-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); 
        gap: 15px; 
        margin-bottom: 25px; 
    }
    .stats-card { 
        background: #ffffff; 
        border: 1px solid #dee2e6; 
        border-radius: 6px; 
        padding: 15px; 
        box-shadow: 0 2px 4px rgba(0,0,0,0.02); 
    }
    .stats-value { 
        font-size: 24px; 
        font-weight: 700; 
        margin-top: 5px; 
    }
    .meta-list { 
        list-style: none; 
        padding: 0; 
        margin: 0; 
    }
    .meta-list li { 
        padding: 8px 0; 
        border-bottom: 1px solid #f8f9fa; 
        font-size: 14px; 
    }

    /* 🌟 COMPACT AUTOMATED TAB VIEWPORT TOGGLE LAYOUT SCHEMES */
    .profile-tab-content {
        display: none !important;
    }
    .profile-tab-content.active {
        display: block !important;
    }
    .profile-subnav-btn {
        padding: 10px 20px;
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-weight: 700;
        font-size: 13.5px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .profile-subnav-btn:hover {
        background: #f1f5f9;
        color: #1e293b;
    }
    .profile-subnav-btn.active {
        background: #04AA6D !important;
        color: #ffffff !important;
        border-color: #04AA6D !important;
        box-shadow: 0 4px 6px rgba(4,170,109,0.15);
    }
    
    @media print {
        .no-print, .sidebar, .navbar, .profile-subnav-btn, [style*="display: flex"] { display: none !important; }
        .main-wrapper { margin-left: 0 !important; padding: 0 !important; }
        .custom-card { border: none !important; box-shadow: none !important; }
        .profile-tab-content { display: block !important; }
    }
</style>

<div class="container-fluid p-0">
    <!-- Top Print & Action Options Navigation Sub-Stripe Bar -->
    <div class="no-print" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <a href="{{ url('/students') }}" class="btn btn-secondary" style="text-decoration: none; padding: 8px 16px; background: #555; color: white; border-radius: 4px; font-weight: 600;">← Back to Students</a>
        <button onclick="window.print()" class="btn btn-success" style="padding: 8px 16px; background: #04AA6D; color: white; border: none; border-radius: 4px; font-weight: 600; cursor: pointer;">🖨️ Print / Export PDF</button>
    </div>

    <!-- 🌟 MASTER CORE ANALYTICAL SUMMARY CARD OVERSIGHTS -->
    <div class="stats-grid">
        <div class="stats-card" style="border-left: 4px solid #17a2b8;">
            <div style="font-weight: 600; color: #666; font-size: 13px;">TOTAL COURSE FEES</div>
            <div class="stats-value" style="color: #17a2b8;">Rs. {{ number_format($totalCourseFees, 2) }}</div>
        </div>
        <div class="stats-card" style="border-left: 4px solid #04AA6D;">
            <div style="font-weight: 600; color: #666; font-size: 13px;">TOTAL REMITTED (PAID)</div>
            <div class="stats-value" style="color: #04AA6D;">Rs. {{ number_format($totalPaidToDate, 2) }}</div>
        </div>
        <div class="stats-card" style="border-left: 4px solid #dc3545;">
            <div style="font-weight: 600; color: #666; font-size: 13px;">OUTSTANDING BALANCE</div>
            <div class="stats-value" style="color: #dc3545;">Rs. {{ number_format($outstandingBalance, 2) }}</div>
        </div>
        <div class="stats-card" style="border-left: 4px solid #ffc107;">
            <div style="font-weight: 600; color: #666; font-size: 13px;">COMPLETE CUMULATIVE ATTENDANCE</div>
            <div class="stats-value" style="color: #ffc107;">{{ $attendancePercentage }}%</div>
            <small style="color: #777;">Accumulated summary of all operational school calendar months</small>
        </div>
    </div>
    <!-- 🌟 TOP VIEWPORT OVERVIEW SUB-NAV MENU TABS MATRIX -->
    <div class="no-print" style="background: #ffffff; border: 1px solid #dee2e6; border-radius: 8px; padding: 6px; display: flex; gap: 10px; margin-bottom: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <button type="button" class="profile-subnav-btn active" onclick="openProfileTabPane(event, 'personal-tab')">
            👤 Personal Details
        </button>
        <button type="button" class="profile-subnav-btn" onclick="openProfileTabPane(event, 'academic-tab')">
            📝 Academic Ledger
        </button>
        <button type="button" class="profile-subnav-btn" onclick="openProfileTabPane(event, 'payments-tab')">
            💳 Payments & Fees
        </button>
        <button type="button" class="profile-subnav-btn" onclick="openProfileTabPane(event, 'attendance-tab')">
            📅 Attendance Logs
        </button>
        <button type="button" class="profile-subnav-btn" onclick="openProfileTabPane(event, 'timetable-tab')">
            🕒 Class Timetable
        </button>
    </div>

    <!-- MAIN VIEWPORTS CONTENT HOLDER ACCORDION GRID CONTAINER -->
    <div class="tab-viewports-wrapper">

        <!-- ========================================================================= -->
        <!-- 🌟 TAB 1: PERSONAL DETAILS CORE VIEWPORT CONTAINER                        -->
        <!-- ========================================================================= -->
        <div id="personal-tab" class="profile-tab-content active">
            
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 25px;">
                <!-- Student Core Biological Profiling Summary Column -->
                <div class="custom-card" style="margin:0;">
                    <div class="custom-header"><h3 style="margin:0; font-weight:700; color:#333;">Personal Matrix</h3></div>
                    <div style="text-align: center; margin-bottom: 15px;">
                        @if(!empty($student->photo))
                            <img src="{{ asset($student->photo) }}" style="width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 1px solid #ddd;">
                        @else
                            <div style="width: 90px; height: 90px; border-radius: 50%; background: #f1f1f1; display: inline-flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 700; color: #777;">
                                {{ strtoupper(substr($student->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <ul class="meta-list">
                        <li><strong>SYSTEM ID KEY:</strong> <br><span style="color:#777;">ID #{{ $student->id }}</span></li>
                        <li><strong>FULL NAME:</strong> <br><span style="color:#777;">{{ $student->name }}</span></li>
                        <li><strong>RESIDENTIAL ADDRESS:</strong> <br><span style="color:#777;">{{ $student->address }}</span></li>
                        <li><strong>MOBILE CONTACT:</strong> <br><span style="color:#777;">{{ $student->contact ?? $student->mobile }}</span></li>
                    </ul>
                </div>

                <!-- Active Class Admission Selection Tracking Matrix Column -->
                <div class="custom-card" style="margin:0;">
                    <div class="custom-header"><h3 style="margin:0; font-weight:700; color:#333;">Active Course Enrollments</h3></div>
                    <table style="width:100%; border-collapse: collapse; font-size: 14px;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 1px solid #dee2e6; text-align: left;">
                                <th style="padding: 10px;">Enrollment No</th>
                                <th style="padding: 10px;">Course Name</th>
                                <th style="padding: 10px; text-align: center;">Registration Date</th>
                                <th style="padding: 10px; text-align: right;">Total Course Fee</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($enrollments as $enroll)
                            <tr style="border-bottom: 1px solid #f1f1f1;">
                                <td style="padding: 12px; font-weight: 700; color:#555;">E-00{{ $student->id }}</td>
                                <td style="padding: 12px;">{{ $enroll->course_name }} ({{ $enroll->duration }})</td>
                                <td style="padding: 12px; text-align: center; color:#666;">{{ $enroll->registration_date }}</td>
                                <td style="padding: 12px; text-align: right; font-weight: 700;">Rs. {{ number_format($enroll->total_course_fee, 2) }}</td>
                            </tr>
                            @exclude
                            @empty
                            <tr><td colspan="4" style="padding: 20px; text-align: center; color:#999; font-style: italic;">No active enrollment tracks registered.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div> <!-- END PERSONAL TAB PANEL CONTAINER -->
        <!-- ========================================================================= -->
        <!-- 🌟 TAB 2: ACADEMIC LEDGER CORE VIEWPORT CONTAINER                         -->
        <!-- ========================================================================= -->
        <div id="academic-tab" class="profile-tab-content">
            
            <!-- ACADEMIC PERFORMANCE EVALUATIONS SUMMARY SHEET -->
            <div class="custom-card" style="margin-top: 0;">
                <div class="custom-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="margin:0; font-weight:700; color:#333;">Academic Performance Evaluation Ledger</h3>
                    <span style="background-color: #04AA6D; color: white; padding: 4px 12px; border-radius: 4px; font-weight: 700; font-size: 14px;">
                        Cumulative Score: {{ $overallPercentage }}%
                    </span>
                </div>
                
                <!-- DAILY WORK TESTS LOGS -->
                <div style="margin-bottom: 30px; margin-top: 15px;">
                    <h4 style="color: #04AA6D; font-weight: 700; margin-bottom: 12px; font-size: 15px;">✔ Daily Class Work Progress Tests (50 Marks Baseline)</h4>
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px; border: 1px solid #dee2e6;">
                        <thead>
                            <tr style="background: #f8f9fa; text-align: left;">
                                <th style="padding: 10px; border: 1px solid #dee2e6;">Evaluation Parameters</th>
                                <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center; width: 150px;">Score Obtained</th>
                                <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center; width: 150px;">Total Scale Marks</th>
                                <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center; width: 100px;">Grade</th>
                                <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center; width: 120px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dailyTests as $dt)
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 10px; border: 1px solid #dee2e6;">Daily Class Quiz ({{ \Carbon\Carbon::parse($dt->evaluation_date)->format('d-M-Y') }})</td>
                                <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; font-weight: 700;">{{ number_format($dt->marks_obtained, 0) }}</td>
                                <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; color: #666;">{{ number_format($dt->total_marks, 0) }}</td>
                                <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; font-weight: 800; color: #04AA6D;">{{ $dt->grade_letter }}</td>
                                <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">
                                    <span style="background-color: {{ $dt->status == 'Pass' ? '#e6f4ea':'#fce8e6' }}; color: {{ $dt->status == 'Pass' ? '#137333':'#c5221f' }}; padding: 3px 8px; border-radius: 4px; font-weight: 700; font-size: 11px;">{{ strtoupper($dt->status) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" style="padding: 15px; text-align: center; color: #999; font-style: italic;">No daily progress test records logged inside system registers.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- TERM EXAMINATIONS BREAKDOWN -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <h4 style="color: #17a2b8; font-weight: 700; margin-bottom: 12px; font-size: 15px;">Mid-Term Evaluations (3-Month Threshold)</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 14px; border: 1px solid #dee2e6;">
                            <thead>
                                <tr style="background: #f8f9fa; text-align: left;">
                                    <th style="padding: 10px; border: 1px solid #dee2e6;">Subject Track</th>
                                    <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center; width: 120px;">Score</th>
                                    <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center; width: 80px;">Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($midterms as $mid)
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #dee2e6;">Core Program Syllabus</td>
                                    <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; font-weight: 700;">{{ number_format($mid->marks_obtained, 0) }}/{{ number_format($mid->total_marks, 0) }}</td>
                                    <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; font-weight: 800; color: #04AA6D;">{{ $mid->grade_letter }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" style="padding: 15px; text-align: center; color: #999; font-style: italic;">No Midterm evaluations recorded.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div>
                        <h4 style="color: #6f42c1; font-weight: 700; margin-bottom: 12px; font-size: 15px;">Final-Term Evaluations (6-Month Session)</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 14px; border: 1px solid #dee2e6;">
                            <thead>
                                <tr style="background: #f8f9fa; text-align: left;">
                                    <th style="padding: 10px; border: 1px solid #dee2e6;">Subject Track</th>
                                    <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center; width: 120px;">Score</th>
                                    <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center; width: 80px;">Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($finals as $fin)
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #dee2e6;">Core Sessions Final</td>
                                    <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; font-weight: 700;">{{ number_format($fin->marks_obtained, 0) }}/{{ number_format($fin->total_marks, 0) }}</td>
                                    <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; font-weight: 800; color: #04AA6D;">{{ $fin->grade_letter }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" style="padding: 15px; text-align: center; color: #999; font-style: italic;">No Final Term evaluations recorded.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div> <!-- END ACADEMIC TAB PANEL CONTAINER -->
        <!-- ========================================================================= -->
        <!-- 🌟 TAB 3: PAYMENTS & FEES VIEWPORT CONTAINER                              -->
        <!-- ========================================================================= -->
        <div id="payments-tab" class="profile-tab-content">
            
            <!-- HISTORICAL RECEIPTS LEDGER -->
            <div class="custom-card" style="margin-top: 0;">
                <div class="custom-header"><h3 style="margin:0; font-weight:700; color:#333;">Historical Receipts Ledger</h3></div>
                <table style="width:100%; border-collapse: collapse; font-size: 14px; text-align: left;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 1px solid #dee2e6;">
                            <th style="padding: 12px;">Receipt No.</th>
                            <th style="padding: 12px;">Collection Date</th>
                            <th style="padding: 12px; text-align: right;">Amount Remitted</th>
                            <th style="padding: 12px; text-align: center; width: 150px;">Status Matrix</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($receipts as $receipt)
                        <tr style="border-bottom: 1px solid #f1f1f1;">
                            <td style="padding: 12px; font-weight: 700; color: #111;">{{ $receipt->receipt_no ?? 'REC-00'.$receipt->id }}</td>
                            <td style="padding: 12px; color: #555;">{{ \Carbon\Carbon::parse($receipt->payment_date)->format('d-M-Y') }}</td>
                            <td style="padding: 12px; text-align: right; font-weight: 700; color: #04AA6D;">Rs. {{ number_format($receipt->amount, 2) }}</td>
                            <td style="padding: 12px; text-align: center;">
                                <span style="background-color: #e6f4ea; color: #137333; padding: 4px 10px; border-radius: 4px; font-weight: 700; font-size: 11px;">REMITTED</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" style="padding: 25px; text-align: center; color:#999; font-style: italic;">No prior transactional receipt references registered under this student query footprint.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- FEE INSTALLMENTS CALENDAR & PRINT MATRIX -->
            <div style="background: #ffffff; border: 1px solid #dee2e6; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-top: 25px; padding: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f1f5f9; padding-bottom: 12px; margin-bottom: 15px;" class="no-print">
                    <div>
                        <h3 style="margin: 0; font-weight: 800; color: #1e293b; font-size: 16px;">📅 Admissions Fee Installment Schedule</h3>
                        <p style="margin: 3px 0 0 0; color: #64748b; font-size: 12px;">Chronological timeline statement tracker for student payment compliance markers.</p>
                    </div>
                    <a href="{{ route('students.schedule', $student->id) }}" target="_blank" style="background-color: #6f42c1; color: white; text-decoration: none; padding: 8px 16px; border-radius: 6px; font-weight: 700; font-size: 13px; box-shadow: 0 2px 4px rgba(111,66,193,0.2); display: inline-flex; align-items: center; gap: 6px;">
                        🖨️ Download / Print Schedule PDF
                    </a>
                </div>

                @if(isset($installments) && $installments->count() > 0)
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; background: #fff; font-size: 14px; text-align: left;">
                            <thead style="background: #1e293b; color: #ffffff;">
                                <tr>
                                    <th style="padding: 10px; font-weight: 700; text-align: center; border: 1px solid #334155;">Installment</th>
                                    <th style="padding: 10px; font-weight: 700; border: 1px solid #334155;">Due Date Deadline</th>
                                    <th style="padding: 10px; font-weight: 700; text-align: right; border: 1px solid #334155;">Base Portion</th>
                                    <th style="padding: 10px; font-weight: 700; text-align: right; color: #fca5a5; border: 1px solid #334155;">Late Fine</th>
                                    <th style="padding: 10px; font-weight: 700; text-align: center; border: 1px solid #334155;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($installments as $inst)
                                    @php
                                        if ($inst->status == 'Paid') {
                                            $rowBg = 'background: #f0fdf4;';
                                            $badgeBg = '#ceead6;';
                                            $badgeColor = '#137333;';
                                        } elseif ($inst->status == 'Partially Paid') {
                                            $rowBg = 'background: #fffbeb;'; 
                                            $badgeBg = '#ffeacc;';
                                            $badgeColor = '#b06000;';
                                        } else {
                                            $rowBg = 'background: #ffffff;'; 
                                            $badgeBg = '#f1f5f9;';
                                            $badgeColor = '#475569;';
                                        }
                                    @endphp
                                    <tr style="border-bottom: 1px solid #e2e8f0; {{ $rowBg }}">
                                        <td style="padding: 12px 10px; text-align: center; font-weight: 700; color: #64748b;">Month {{ $inst->installment_number }}</td>
                                        <td style="padding: 12px 10px; font-weight: 700; color: #dc3545;">
                                            📅 {{ \Carbon\Carbon::parse($inst->due_date)->format('d-M-Y') }}
                                        </td>
                                        <td style="padding: 12px 10px; text-align: right; font-weight: 700; color: #1e293b;">
                                            Rs. {{ number_format($inst->base_amount, 2) }}
                                        </td>
                                        <td style="padding: 12px 10px; text-align: right; font-weight: 700; color: #dc3545;">
                                            Rs. {{ number_format($inst->fine_charged, 2) }}
                                        </td>
                                        <td style="padding: 12px 10px; text-align: center;">
                                            <span style="font-weight: 800; font-size: 11px; padding: 4px 8px; border-radius: 4px; text-transform: uppercase; background: {{ $badgeBg }}; color: {{ $badgeColor }}; @if($inst->status != 'Paid' && $inst->status != 'Partially Paid') border: 1px solid #cbd5e1; @endif">
                                                {{ $inst->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div style="background-color: #fff8e6; border: 1px dashed #d97706; border-radius: 6px; padding: 12px; margin-top: 15px; font-size: 12px; color: #9a3412;">
                        <b>ℹ️ ADMISSION LOG COMPLIANCE:</b> Monthly payments must be settled at the Allied Bank desk counter before the <b>10th day of each calendar month</b>. Late entries processed past the due date trigger the automated Rs. 500 flat fine enforcement rule.
                    </div>
                @else
                    <p style="padding: 15px; text-align: center; color: #64748b; font-style: italic; margin: 0; background: #f8fafc; border-radius: 6px;">No customized installment payment plans have been initialized for this student record row footprint.</p>
                @endif
            </div>

        </div> <!-- END PAYMENTS TAB PANEL CONTAINER -->
        <!-- ========================================================================= -->
        <!-- 🌟 TAB 4: ATTENDANCE LOGS VIEWPORT CONTAINER                              -->
        <!-- ========================================================================= -->
        <div id="attendance-tab" class="profile-tab-content">
            
            <!-- A. MONTH-WISE ATTENDANCE SUMMARY LEDGER CARD -->
            <div class="custom-card" style="margin-top: 0;">
                <div class="custom-header">
                    <h3 style="margin:0; font-weight:700; color:#333;">Month-Wise Attendance Analytics Matrix</h3>
                </div>
                <div class="custom-body" style="padding: 0;">
                    <table style="width:100%; border-collapse: collapse; font-size: 14px; text-align: left;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 1px solid #dee2e6;">
                                <th style="padding: 12px; width: 80px;">#</th>
                                <th style="padding: 12px;">Calendar Month Period</th>
                                <th style="padding: 12px; text-align: center; width: 180px;">Total Sessions</th>
                                <th style="padding: 12px; text-align: center; width: 180px;">Attended Sessions</th>
                                <th style="padding: 12px; text-align: center; width: 180px;">Monthly Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($monthlyAttendanceSummary as $monthly)
                            <tr style="border-bottom: 1px solid #f1f1f1;">
                                <td style="padding: 12px; color:#666;">{{ $loop->iteration }}</td>
                                <td style="padding: 12px; font-weight: 700; color:#333;">{{ $monthly['month_name'] }}</td>
                                <td style="padding: 12px; text-align: center;">{{ $monthly['total_sessions'] }} Days</td>
                                <td style="padding: 12px; text-align: center; color: #04AA6D; font-weight: 600;">{{ $monthly['present_sessions'] }} Days</td>
                                <td style="padding: 12px; text-align: center;">
                                    <span style="background-color: {{ $monthly['percentage'] < 75 ? '#fce8e6':'#e6f4ea' }}; color: {{ $monthly['percentage'] < 75 ? '#c5221f':'#137333' }}; padding: 4px 10px; border-radius: 4px; font-weight: 700; font-size: 12px; border: 1px solid {{ $monthly['percentage'] < 75 ? '#fad2cf':'#ceead6' }};">
                                        {{ $monthly['percentage'] }}%
                                    </span>
                                end
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="padding: 20px; text-align: center; color:#999; font-style: italic;">No parameterized calendars found in storage memory banks.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- B. LIVE SYSTEM ATTENDANCE CHRONOLOGICAL LOG SHEET CARD -->
            <div class="custom-card" style="margin-top: 25px; margin-bottom: 30px;">
                <div class="custom-header">
                    <h3 style="margin:0; font-weight:700; color:#333;">Live System Daily Attendance Logs</h3>
                </div>
                <div class="custom-body" style="padding: 0;">
                    <table style="width:100%; border-collapse: collapse; font-size: 14px; text-align: left; border-top: 1px solid #dee2e6;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 1px solid #dee2e6;">
                                <th style="padding: 12px; width: 80px; text-align: left;">#</th>
                                <th style="padding: 12px; text-align: left;">Session Evaluation Parameters</th>
                                <th style="padding: 12px; text-align: center; width: 220px;">Marking Timestamp Date</th>
                                <th style="padding: 12px; text-align: center; width: 180px;">Presence Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendanceHistoryList as $log)
                            <tr style="border-bottom: 1px solid #f1f1f1;">
                                <td style="padding: 12px; color: #666;">{{ $loop->iteration }}</td>
                                <td style="padding: 12px; font-weight: 500; color: #333;">Regular Class Lecture Session Track</td>
                                <td style="padding: 12px; text-align: center; color: #555; font-weight: 600;">{{ \Carbon\Carbon::parse($log->date)->format('d-M-Y (l)') }}</td>
                                <td style="padding: 12px; text-align: center;">
                                    @if($log->status == 'Present')
                                        <span style="background-color: #e6f4ea; color: #137333; padding: 4px 12px; border-radius: 4px; font-weight: 700; font-size: 12px; border: 1px solid #ceead6; display: inline-block; width: 90px; text-align: center;">PRESENT</span>
                                    @elseif($log->status == 'Late')
                                        <span style="background-color: #fff4e5; color: #b06000; padding: 4px 12px; border-radius: 4px; font-weight: 700; font-size: 12px; border: 1px solid #ffeacc; display: inline-block; width: 90px; text-align: center;">LATE</span>
                                    @elseif($log->status == 'Leave')
                                        <span style="background-color: #e8f0fe; color: #1a73e8; padding: 4px 12px; border-radius: 4px; font-weight: 700; font-size: 12px; border: 1px solid #d2e3fc; display: inline-block; width: 90px; text-align: center;">LEAVE</span>
                                    @else
                                        <span style="background-color: #fce8e6; color: #c5221f; padding: 4px 12px; border-radius: 4px; font-weight: 700; font-size: 12px; border: 1px solid #fad2cf; display: inline-block; width: 90px; text-align: center;">ABSENT</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" style="padding: 25px; text-align: center; color:#999; font-style: italic;">No daily attendance logs recorded for this student profile.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div> <!-- END ATTENDANCE TAB PANEL CONTAINER -->
        <!-- ========================================================================= -->
        <!-- 🌟 TAB 5: WEEKLY CLASS LECTURE SCHEDULE VIEWPORT CONTAINER                 -->
        <!-- ========================================================================= -->
        <div id="timetable-tab" class="profile-tab-content">
            <div class="custom-card" style="margin-top: 0;">
                <div class="custom-header">
                    <h3 style="margin:0; font-weight:700; color:#333;">Personal Weekly Class Lecture Schedule</h3>
                    <p style="margin: 4px 0 0 0; color: #777; font-size: 12px;">Chronological lecture timeline assigned to your active enrolled course track</p>
                </div>
                <div class="custom-body" style="padding-top: 10px;">
                    @foreach($daysOfWeek as $day)
                        @php
                            $daySlots = $studentTimetableMatrix->where('day_of_week', $day);
                        @endphp
                        
                        <div style="margin-bottom: 15px; background: #ffffff; border: 1px solid #dee2e6; border-radius: 6px; overflow: hidden;">
                            <div style="background: #1e293b; color: #ffffff; padding: 10px 15px; font-weight: 700; font-size: 13px; text-transform: uppercase; display: flex; justify-content: space-between; align-items: center;">
                                <span>📅 {{ $day }}</span>
                                <span style="background: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: 10px; font-size: 11px;">
                                    {{ $daySlots->count() }} Lectures
                                </span>
                            </div>
                            
                            <div style="padding: 10px; background: #fafafa; display: flex; flex-direction: column; gap: 10px;">
                                @forelse($daySlots as $slot)
                                    <div style="background: #ffffff; border: 1px solid #dee2e6; border-left: 4px solid #04AA6D; padding: 12px; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
                                        <div style="font-weight: 700; color: #17a2b8; font-size: 13px; background: #f8f9fa; padding: 4px 8px; border-radius: 4px; border: 1px solid #ced4da; text-align: center; min-width: 160px;">
                                            🕒 {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                        </div>
                                        <div style="flex: 1; padding: 0 15px; font-size: 13px; color: #555;">
                                            <strong style="color:#333; font-size:14px;">{{ $slot->course->name ?? 'Core Syllabus' }}</strong>
                                            <div style="margin-top: 2px;">
                                                <span>Faculty Instructor: Prof. {{ $slot->teacher->name ?? 'Faculty Faculty' }}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <span style="background: #333333; color: #ffffff; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                                🏢 Room: {{ $slot->room_number }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div style="padding: 12px; color: #04AA6D; background-color: #e6f4ea; border: 1px dashed #04AA6D; border-radius: 4px; text-align: center; font-weight: 700; font-size: 13px; letter-spacing: 0.5px;">
                                        🎉 WEEKEND / SCHEDULED HOLIDAY — NO CLASSES ASSIGNED
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div> <!-- CLOSES THE PRIMARY .tab-viewports-wrapper HOLDER CONTAINER -->
</div> <!-- CLOSES THE ROOT COMPONENT CANVAS ELEMENT WRAPPER LAYER (.container-fluid) -->

<!-- 🌟 JAVASCRIPT TAB ACTIVE ROUTING LOGIC STORAGE ENGINE -->
<script>
    function openProfileTabPane(event, targetTabId) {
        // 1. Hide all 5 tab viewports safely within the active content document
        const tabPanes = document.querySelectorAll('.profile-tab-content');
        tabPanes.forEach(pane => {
            pane.classList.remove('active');
        });

        // 2. Deactivate background highlight style layers across all 5 tab navigation triggers
        const tabButtons = document.querySelectorAll('.profile-subnav-btn');
        tabButtons.forEach(btn => {
            btn.classList.remove('active');
        });

        // 3. Inject the active state classes to show the selected container card item layout
        document.getElementById(targetTabId).classList.add('active');
        event.currentTarget.classList.add('active');

        // 4. Save state selection token parameter flags to handle page state cache refreshes seamlessly
        localStorage.setItem('selected_profile_matrix_tab_pane', targetTabId);
    }

    // Run layout verification procedures upon initial document tree assembly execution load
    document.addEventListener("DOMContentLoaded", function() {
        const lastCachedActiveTabId = localStorage.getItem('selected_profile_matrix_tab_pane');
        
        if (lastCachedActiveTabId && document.getElementById(lastCachedActiveTabId)) {
            const targetActiveBtn = document.querySelector(`.profile-subnav-btn[onclick*="${lastCachedActiveTabId}"]`);
            if (targetActiveBtn) {
                targetActiveBtn.click();
                return;
            }
        }
        
        // Fallback default: Loads Tab 1 (Personal Details) if history cache parameters don't exist
        const defaultTriggerBtn = document.querySelector('.profile-subnav-btn');
        if (defaultTriggerBtn) {
            defaultTriggerBtn.click();
        }
    });
</script>

@endsection
