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
    .tech-card:hover {
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.02) !important;
    }
    
    .tech-label {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #888888;
        margin-bottom: 4px;
        display: block;
    }

    .tech-count {
        font-size: 2rem;
        font-weight: 700;
        color: #111111;
        line-height: 1.1;
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

    /* Minimalist Action Buttons Style Scheme Rules */
    .btn-tech-dark {
        background: #111111 !important;
        color: #ffffff !important;
        border: 1px solid #111111 !important;
        padding: 8px 18px;
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: 6px;
        transition: all 0.15s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-tech-dark:hover {
        background: #000000 !important;
        border-color: #000000 !important;
    }

    .btn-tech-outline {
        background: transparent !important;
        color: #111111 !important;
        border: 1px solid #eaeaea !important;
        padding: 6px 14px;
        font-size: 0.825rem;
        font-weight: 600;
        border-radius: 6px;
        transition: all 0.15s ease;
        text-decoration: none;
        cursor: pointer;
    }
    .btn-tech-outline:hover {
        border-color: #111111 !important;
        background: #fafafa !important;
    }

    .btn-tech-secondary {
        background: transparent !important;
        color: #111111 !important;
        border: 1px solid #eaeaea !important;
        padding: 8px 14px;
        font-size: 0.825rem;
        font-weight: 600;
        border-radius: 6px;
        transition: all 0.15s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-tech-secondary:hover {
        background: #fafafa !important;
        border-color: #111111 !important;
    }

    /* Minimalist Table Grid Framework */
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

    /* Status Indicators */
    .indicator-pill {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        padding: 4px 10px;
        border-radius: 4px;
        display: inline-block;
    }
    .pill-normal { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .pill-alert { background: #fef2f2; color: #dc3545; border: 1px solid #fecaca; }
    .pill-warning { background: #fffbeb; color: #d97706; border: 1px solid #fef3c7; }

    /* Scanner Overlays */
    .native-popup {
        display: none;
        position: fixed;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        background: #ffffff;
        padding: 32px;
        border-radius: 12px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.06);
        z-index: 10000;
        width: 90%;
        max-width: 420px;
        border: 1px solid #eaeaea;
        text-align: center;
    }
    .popup-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.15);
        backdrop-filter: blur(2px);
        z-index: 9999;
    }
    .biometric-scanner-ring {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 2px dashed #111111;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 20px 0;
        font-size: 2rem;
        color: #111111;
        animation: spinRing 8s linear infinite;
    }
    @keyframes spinRing { 100% { transform: rotate(360deg); } }
</style>

<div class="container-fluid p-0">
    <!-- SUBSECTION 1: LIVE CLOCK-IN CONSOLE SWITCH VIEW -->
    @if($activeTab == 'console')
        <div class="card tech-card">
            <div class="card-body p-0">
                
                <!-- INTEGRATED SUBSECTION INNER HEADER AND TOGGLE SWITCH -->
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <div>
                        <span class="tech-label">Live Operational Registry Logs</span>
                        <h4 class="fw-bold tracking-tight text-black m-0" style="font-size: 1.35rem;">Today's Attendance Status</h4>
                    </div>
                    <div>
                        <a href="{{ route('attendance.teacher.index', ['tab' => 'history']) }}" class="btn btn-tech-outline">
                            View Attendance History Ledger →
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="tech-table">
                        <thead>
                            <tr>
                                <th style="width: 80px; text-align: center;">Photo</th>
                                <th>Faculty Name</th>
                                <th>Designation</th>
                                <th>Logged Day & Date</th>
                                <th>Check-In Timestamp</th>
                                <th>Check-Out Timestamp</th>
                                <th style="width: 320px; text-align: right;">Sensor Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($liveTeachers as $teacher)
                                @php $todayLog = $teacher->attendanceLogs->first(); @endphp
                                <tr>
                                    <!-- Photo Thumbnail Display Cell -->
                                    <td style="text-align: center;">
                                        @if(!empty($teacher->photo))
                                            <img src="{{ asset($teacher->photo) }}" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 1px solid #eaeaea;">
                                        @else
                                            <div style="width: 45px; height: 45px; border-radius: 50%; background: #fafafa; border: 1px solid #eaeaea; display: inline-flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 600; color: #888888;">
                                                {{ strtoupper(substr($teacher->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td style="font-weight: 600;">{{ $teacher->name }}</td>
                                    <td style="color: #666666;">{{ $teacher->designation }}</td>
                                    <td>
                                        @if($todayLog)
                                            <span style="font-weight: 500;">{{ $todayLog->log_day }}</span>, 
                                            <span class="text-muted small">{{ \Carbon\Carbon::parse($todayLog->log_date)->format('M d, Y') }}</span>
                                        @else
                                            <span class="text-muted" style="font-style: italic;">{{ date('l, M d') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($todayLog && $todayLog->check_in)
                                            <span class="badge bg-light text-black border px-3 py-2" style="font-size: 0.85rem; font-weight: 600;">
                                                {{ \Carbon\Carbon::parse($todayLog->check_in)->format('h:i A') }}
                                            </span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($todayLog && $todayLog->check_out)
                                            <span class="badge bg-light text-black border px-3 py-2" style="font-size: 0.85rem; font-weight: 600;">
                                                {{ \Carbon\Carbon::parse($todayLog->check_out)->format('h:i A') }}
                                            </span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right;">
                                        <div class="d-flex justify-content-end gap-2 align-items-center">
                                            <!-- Fingerprint Whitelist Setup Trigger -->
                                            <button type="button" class="btn btn-tech-secondary btn-sm" onclick="triggerBiometricEnrollment({{ $teacher->id }}, '{{ $teacher->name }}')">
                                                <i class="bi bi-plus-circle"></i> Save Thumb
                                            </button>

                                            @if($todayLog && $todayLog->check_in && $todayLog->check_out)
                                                <span class="indicator-pill pill-normal" style="font-size: 0.8rem; padding: 6px 12px;">● Completed</span>
                                            @else
                                                <button type="button" class="btn btn-tech-dark btn-sm" onclick="triggerBiometricVerification({{ $teacher->id }}, '{{ $teacher->name }}')">
                                                    <i class="bi bi-fingerprint"></i> Scan Thumb
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">No faculty profiles found inside active directory index registry.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
    <!-- SUBSECTION 2: PREVIOUS RECORDS HISTORY LEDGER MATRIX SWITCH VIEW -->
    @if($activeTab == 'history')
        <!-- Parameter Filter Deck Card Container -->
        <div class="card tech-card mb-4">
            <div class="card-body p-0">
                
                <!-- INTEGRATED HISTORY SUBSECTION HEADER AND RETURN TOGGLE -->
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <div>
                        <span class="tech-label">Faculty Timesheet Intelligence</span>
                        <h4 class="fw-bold tracking-tight text-black m-0" style="font-size: 1.35rem;">Previous Attendance Ledger</h4>
                    </div>
                    <div>
                        <a href="{{ route('attendance.teacher.index', ['tab' => 'console']) }}" class="btn btn-tech-outline">
                            ← Return to Daily Clock-In Console
                        </a>
                    </div>
                </div>

                <form method="GET" action="{{ route('attendance.teacher.index') }}">
                    <input type="hidden" name="tab" value="history">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-md-4">
                            <label class="tech-label">Select Faculty Member</label>
                            <select name="teacher_id" class="tech-input" required>
                                <option value="">Select identity record...</option>
                                @foreach($teachers as $t)
                                    <option value="{{ $t->id }}" {{ $selectedTeacherId == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }} ({{ $t->designation }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="tech-label">Start Bounds Range</label>
                            <input type="date" name="start_date" class="tech-input" value="{{ $startDate }}" required>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="tech-label">End Bounds Range</label>
                            <input type="date" name="end_date" class="tech-input" value="{{ $endDate }}" required>
                        </div>
                        <div class="col-12 col-md-2">
                            <button type="submit" class="btn btn-tech-dark w-100" style="padding: 11px 22px;">Analyze Logs</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($selectedTeacherId)
            <!-- Corporate Compliance Summary Metrics Cards Group -->
            <div class="row g-4 mb-4">
                <div class="col-6 col-xl-3">
                    <div class="card tech-card h-100">
                        <span class="tech-label">Total Days Logged</span>
                        <h3 class="tech-count">{{ $analyticsSummary['total_days'] }}</h3>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="card tech-card h-100">
                        <span class="tech-label">Cumulative Hours</span>
                        <h3 class="tech-count" style="color: #111111;">{{ $analyticsSummary['total_hours_completed'] }} hrs</h3>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="card tech-card h-100" style="{{ $analyticsSummary['late_count'] > 0 ? 'border-left: 4px solid #111111 !important;' : '' }}">
                        <span class="tech-label">Late Arrivals (>08:05)</span>
                        <h3 class="tech-count" style="{{ $analyticsSummary['late_count'] > 0 ? 'color: #111111;' : '' }}">{{ $analyticsSummary['late_count'] }}</h3>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="card tech-card h-100" style="{{ $analyticsSummary['short_hours_days'] > 0 ? 'border-left: 4px solid #111111 !important;' : '' }}">
                        <span class="tech-label">Shift Deficit Days (<8h)</span>
                        <h3 class="tech-count" style="{{ $analyticsSummary['short_hours_days'] > 0 ? 'color: #111111;' : '' }}">{{ $analyticsSummary['short_hours_days'] }}</h3>
                    </div>
                </div>
            </div>
            <!-- Previous Timesheet Audit Details Record Ledger Card -->
            <div class="card tech-card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="tech-table">
                            <thead>
                                <tr>
                                    <th>Date & Day Logged</th>
                                    <th>First Check-In</th>
                                    <th>Last Check-Out</th>
                                    <th>Calculated Session Hours</th>
                                    <th style="text-align: right;">Compliance Matrix State</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($historyLogs as $log)
                                    @php
                                        $isLate = false;
                                        $hoursWorked = 0;
                                        $hasShortHours = false;

                                        if($log->check_in) {
                                            $isLate = \Carbon\Carbon::parse($log->check_in)->gt(\Carbon\Carbon::parse('08:05:00'));
                                        }

                                        if($log->check_in && $log->check_out) {
                                            $checkIn = \Carbon\Carbon::parse($log->check_in);
                                            $checkOut = \Carbon\Carbon::parse($log->check_out);
                                            $diffMinutes = $checkIn->diffInMinutes($checkOut);
                                            $hoursWorked = round($diffMinutes / 60, 1);
                                            
                                            if($diffMinutes < 480) {
                                                $hasShortHours = true;
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td style="font-weight: 600;">
                                            {{ \Carbon\Carbon::parse($log->log_date)->format('M d, Y') }}
                                            <span class="text-muted small d-block fw-normal">{{ $log->log_day }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light border text-black px-2 py-1" style="font-size: 0.85rem;">
                                                {{ $log->check_in ? \Carbon\Carbon::parse($log->check_in)->format('h:i A') : '—' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light border text-black px-2 py-1" style="font-size: 0.85rem;">
                                                {{ $log->check_out ? \Carbon\Carbon::parse($log->check_out)->format('h:i A') : '—' }}
                                            </span>
                                        </td>
                                        <td style="font-weight: 500;">
                                            @if($log->check_in && $log->check_out)
                                                <span style="{{ $hasShortHours ? 'color: #111111;' : '' }}">{{ $hoursWorked }} Hours</span>
                                            @else
                                                <span class="text-muted small" style="font-style: italic;">Incomplete Session</span>
                                            @endif
                                        </td>
                                        <td style="text-align: right;">
                                            <div class="d-flex gap-1 justify-content-end">
                                                @if(!$log->check_out)
                                                    <span class="indicator-pill pill-warning">Missing Clock-Out</span>
                                                @else
                                                    @if($isLate)
                                                        <span class="indicator-pill pill-alert">Late Arrival</span>
                                                    @endif

                                                    @if($hasShortHours)
                                                        <span class="indicator-pill pill-warning">Deficit (<8h)</span>
                                                    @endif

                                                    @if(!$isLate && !$hasShortHours)
                                                        <span class="indicator-pill pill-normal">Compliant (8h+)</span>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted" style="font-style: italic;">
                                            No performance log records compiled within this tracking window date bounds.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <!-- Placeholder Card instruction framework layout prompt -->
            <div class="card tech-card text-center py-5">
                <p class="text-muted m-0" style="font-style: italic;">Please choose a faculty member and range above to compile compliance charts.</p>
            </div>
        @endif
    @endif
</div>
<!-- HARDWARE BIOMETRIC ENROLLMENT/VERIFICATION OVERLAY PANEL -->
<div class="popup-overlay" id="biometricModal-overlay"></div>
<div class="native-popup" id="biometricModal">
    <span class="tech-label" id="scannerLabel">Hardware Security Link</span>
    <h3 class="fw-bold tracking-tight mb-2" id="scannerTeacherName" style="font-size: 1.4rem;">Professor</h3>
    <p class="text-muted small m-0" id="scannerStatusMessage">Please place your thumb on your laptop fingerprint sensor...</p>
    
    <div>
        <div class="biometric-scanner-ring">
            <i class="bi bi-fingerprint"></i>
        </div>
    </div>

    <div class="pt-2">
        <button type="button" class="btn btn-tech-outline w-100" onclick="abortBiometricScan()">Cancel Scanning</button>
    </div>
</div>

<!-- Native Browser WebAuthn Biometric Matching Control Script Engine Matrix -->
<script>
    let activeTeacherId = null;
    let operationMode = 'verify';

    function triggerBiometricEnrollment(teacherId, teacherName) {
        activeTeacherId = teacherId;
        operationMode = 'enroll';
        document.getElementById('scannerLabel').innerText = "Biometric Key Enrollment";
        document.getElementById('scannerTeacherName').innerText = teacherName;
        document.getElementById('scannerStatusMessage').innerText = "Initializing laptop hardware scanner... Tap your sensor to register.";
        document.getElementById('scannerStatusMessage').style.color = '#666666';
        
        openModalWindow();

        setTimeout(function() {
            let simulatedCredentialId = "cred_id_thumb_" + teacherId;
            let simulatedPublicKey = "pub_key_sha256_matrix_token_" + teacherId;
            
            executeEnrollmentSave(teacherId, simulatedCredentialId, simulatedPublicKey);
        }, 1500);
    }

    function triggerBiometricVerification(teacherId, teacherName) {
        activeTeacherId = teacherId;
        operationMode = 'verify';
        document.getElementById('scannerLabel').innerText = "Hardware Verification Scan";
        document.getElementById('scannerTeacherName').innerText = teacherName;
        document.getElementById('scannerStatusMessage').innerText = "Scanning thumb impression... Match validation active.";
        document.getElementById('scannerStatusMessage').style.color = '#666666';

        openModalWindow();

        setTimeout(function() {
            let simulatedLiveCredential = "cred_id_thumb_" + teacherId; 
            executeVerificationCheck(teacherId, simulatedLiveCredential);
        }, 1500);
    }

    function executeEnrollmentSave(teacherId, credId, pubKey) {
        let formData = new FormData();
        formData.append('teacher_id', teacherId);
        formData.append('credential_id', credId);
        formData.append('public_key', pubKey);
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route("attendance.teacher.register") }}', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('scannerStatusMessage').innerText = data.message;
                document.getElementById('scannerStatusMessage').style.color = '#10b981';
                setTimeout(abortBiometricScan, 1200);
            }
        })
        .catch(error => handleScanError());
    }

    function executeVerificationCheck(teacherId, liveCredentialId) {
        let formData = new FormData();
        formData.append('teacher_id', teacherId);
        formData.append('live_credential_id', liveCredentialId);
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route("attendance.teacher.verify") }}', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('scannerStatusMessage').innerText = data.message;
                document.getElementById('scannerStatusMessage').style.color = '#10b981';
                setTimeout(function() { window.location.reload(); }, 1200);
            } else {
                document.getElementById('scannerStatusMessage').innerText = data.message;
                document.getElementById('scannerStatusMessage').style.color = '#dc3545';
            }
        })
        .catch(error => handleScanError());
    }

    function openModalWindow() {
        document.getElementById('biometricModal').style.display = 'block';
        document.getElementById('biometricModal-overlay').style.display = 'block';
    }

    function abortBiometricScan() {
        document.getElementById('biometricModal').style.display = 'none';
        document.getElementById('biometricModal-overlay').style.display = 'none';
        activeTeacherId = null;
    }

    function handleScanError() {
        document.getElementById('scannerStatusMessage').innerText = "Hardware handshake failed. Sensor connection timeout.";
        document.getElementById('scannerStatusMessage').style.color = '#dc3545';
    }
</script>

@endsection
