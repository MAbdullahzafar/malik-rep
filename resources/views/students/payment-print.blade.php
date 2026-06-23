@php
    // 🛡️ RECONCILIATION LAYER: Safely maps data fields if loaded from alternative controller methods
    if (!isset($enrollment) && isset($enrollments)) {
        $enrollment = is_iterable($enrollments) ? $enrollments->first() : $enrollments;
    }
    
    // Fallback configurations to prevent empty string variable property lookups
    $studentName = $student->name ?? '—';
    $studentRegNo = $student->reg_no ?? '—';
    $studentContact = $student->mobile ?? $student->contact ?? '—';
    $studentAddress = $student->address ?? '—';
    $admissionDate = isset($student->created_at) ? \Carbon\Carbon::parse($student->created_at)->format('d-M-Y') : now()->format('d-M-Y');
    
    $courseTitle = $enrollment->course_name ?? 'Active Course Track';
    $courseDuration = $enrollment->duration ?? '3 Months';
    $courseTuitionFee = floatval($enrollment->fee ?? ($enrollment->total_course_fee ?? 0.00));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admissions Fee Installment Schedule - {{ $studentRegNo }}</title>
    <style>
        @import url('https://googleapis.com');
        
        body { 
            font-family: 'Nunito', 'Arial', sans-serif; 
            margin: 0; 
            padding: 35px; 
            background: #fff; 
            color: #1e293b;
            font-size: 13px;
            line-height: 1.5;
        }
        
        .header-block {
            text-align: center;
            border-bottom: 3px solid #04AA6D;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        
        .title-main {
            font-size: 24px;
            font-weight: 800;
            margin: 0;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .title-sub {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            margin: 5px 0 0 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .section-heading {
            font-size: 13px;
            font-weight: 800;
            background: #f1f5f9;
            padding: 6px 12px;
            margin: 25px 0 12px 0;
            border-left: 4px solid #04AA6D;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .profile-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .profile-grid td {
            padding: 8px 10px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }
        
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .schedule-table th {
            background: #1e293b;
            color: #ffffff;
            font-weight: 700;
            padding: 10px 12px;
            text-align: left;
            font-size: 12px;
            border: 1px solid #334155;
            text-transform: uppercase;
        }
        
        .schedule-table td {
            padding: 12px;
            border: 1px solid #e2e8f0;
            font-size: 13px;
        }
        
        .notice-box {
            background-color: #fff8e6;
            border: 1px dashed #d97706;
            border-radius: 6px;
            padding: 15px;
            margin-top: 35px;
            font-size: 12px;
            color: #9a3412;
        }
        
        .btn-print-container {
            text-align: right;
            margin-bottom: 25px;
        }
        
        .btn-action-print {
            background: #04AA6D;
            color: white;
            border: none;
            padding: 10px 22px;
            font-size: 14px;
            font-weight: 700;
            border-radius: 4px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(4,170,109,0.15);
            transition: background 0.15s ease;
        }
        
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 0; }
        }
    </style>
</head>
<body>

    <div class="btn-print-container no-print">
        <button class="btn-action-print" onclick="window.print()">📥 Download / Save Schedule PDF</button>
    </div>

    <div class="header-block">
        <h1 class="title-main">SCHOOL MATRIX</h1>
        <p class="title-sub">Official Student Fee Installment Schedule Guide</p>
    </div>

    <div class="section-heading">Student Registration Particulars</div>
    <table class="profile-grid">
        <tr>
            <td style="width: 20%;"><b>Student Name:</b></td>
            <td style="width: 30%; text-transform: uppercase;">{{ $studentName }}</td>
            <td style="width: 20%;"><b>Registration No:</b></td>
            <td style="width: 30%; font-weight: 700; color: #04AA6D;">{{ $studentRegNo }}</td>
        </tr>
        <tr>
            <td><b>Mobile Contact:</b></td>
            <td>{{ $studentContact }}</td>
            <td><b>Admission Date:</b></td>
            <td>{{ $admissionDate }}</td>
        </tr>
        <tr>
            <td><b>Current Address:</b></td>
            <td colspan="3">{{ $studentAddress }}</td>
        </tr>
    </table>
    <div class="section-heading">Enrolled Course Curriculum Details</div>
    <table class="profile-grid">
        <tr>
            <td style="width: 20%;"><b>Enrolled Course:</b></td>
            <td style="width: 30%; font-weight: 700; color: #0284c7; text-transform: uppercase;">{{ $courseTitle }}</td>
            <td style="width: 20%;"><b>Course Duration:</b></td>
            <td style="width: 30%;">{{ $courseDuration }}</td>
        </tr>
        <tr>
            <td><b>Total Tuition Fee:</b></td>
            <td style="font-weight: 700;">Rs. {{ number_format($courseTuitionFee, 2) }}</td>
            <td><b>Payment Plan Type:</b></td>
            <td>{{ isset($installments) ? count($installments) : 3 }} Months Installments Plan</td>
        </tr>
    </table>

    <div class="section-heading">Chronological Fee Installment Milestones Calendar</div>
    <table class="schedule-table">
        <thead>
            <tr>
                <th style="width: 20%; text-align: center;">Billing Element</th>
                <th style="width: 20%;">Due Date Deadline</th>
                <th style="text-align: right; width: 20%;">Base Amount</th>
                <!-- 🌟 NEW INJECTED PAID AMOUNT COLUMN HEADER -->
                <th style="text-align: right; width: 20%; color: #a7f3d0;">Paid Amount</th>
                <th style="text-align: center; width: 20%;">Status</th>
            </tr>
        </thead>
        <tbody>
            <tr style="background: #f8fafc; font-weight: 600;">
                <td style="text-align: center; color: #0284c7;">Admission Fee</td>
                <td>⚡ Paid at Admission</td>
                <td style="text-align: right;">Rs. 5,000.00</td>
                <td style="text-align: right; color: #04AA6D; font-weight: 700;">Rs. 5,000.00</td>
                <td style="text-align: center;">
                    <span style="font-weight: 800; font-size: 11px; padding: 4px 10px; border-radius: 4px; background: #ceead6; color: #137333; text-transform: uppercase;">SETTLED</span>
                </td>
            </tr>

            @if(isset($installments) && count($installments) > 0)
                @foreach($installments as $inst)
                    <tr style="{{ $inst->status == 'Paid' ? 'background: #f0fdf4;' : '' }}">
                        <td style="text-align: center; font-weight: 700; color: #64748b;">Month {{ $inst->installment_number }}</td>
                        <td style="font-weight: 700; color: #dc3545;">
                            📅 {{ \Carbon\Carbon::parse($inst->due_date)->format('d-M-Y') }}
                        </td>
                        <td style="text-align: right; font-weight: 700; color: #1e293b;">
                            Rs. {{ number_format($inst->base_amount, 2) }}
                        </td>
                        <!-- 🌟 NEW INJECTED PAID AMOUNT DYNAMIC DATA VALUE CELL -->
                        <td style="text-align: right; font-weight: 700; color: #04AA6D; background: #fcfdfd;">
                            Rs. {{ number_format($inst->amount_paid ?? 0.00, 2) }}
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: 800; font-size: 11px; padding: 4px 10px; border-radius: 4px; text-transform: uppercase; 
                                background: {{ $inst->status == 'Paid' ? '#ceead6; color: #137333;' : ($inst->status == 'Partially Paid' ? '#ffeacc; color: #b06000;' : '#f1f5f9; color: #475569; border: 1px solid #cbd5e1;') }}">
                                {{ $inst->status }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            @endif

            @php
                $grandTotalPlanCapital = $courseTuitionFee + 5000.00;
            @endphp
            <tr style="background: #f1f5f9; font-weight: 800; font-size: 14px; border-top: 2px solid #1e293b;">
                <td colspan="2" style="text-align: right; padding: 12px;">Total Plan Capital (Inc. Admission Fee):</td>
                <td style="text-align: right; padding: 12px; color: #04AA6D;">Rs. {{ number_format($grandTotalPlanCapital, 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    <div class="notice-box">
        <b>⚠️ CRITICAL ADMISSION COMPLIANCE INSTRUCTIONS:</b>
        <p style="margin: 5px 0 0 0; line-height: 1.4;">
            The **Rs. 5,000.00 Admission Fee** is a fixed non-refundable registration charge settled at entry. All subsequent monthly tuition installment portions must be remitted on or before the **10th day of each calendar month**. Late submissions processed past this threshold automatically apply system penalty values.
        </p>
    </div>

    <table style="width: 100%; margin-top: 70px; font-size: 12px;">
        <tr>
            <td style="width: 40%; border-top: 1px solid #000; text-align: center; padding-top: 6px; font-weight: 600;">Student / Parent Signature</td>
            <td style="width: 20%;"></td>
            <td style="width: 40%; border-top: 1px solid #000; text-align: center; padding-top: 6px; font-weight: 600;">Registrar / Authorized Officer</td>
        </tr>
    </table>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 600);
        }
    </script>
</body>
</html>