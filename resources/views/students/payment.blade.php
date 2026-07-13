@extends('layouts.app')
@section('content')

<style>
    /* Direct UI Fixes embedded cleanly to override broken CSS loads */
    .custom-card {
        background: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        margin: 15px 0;
    }
    .custom-header {
        background-color: #f8f9fa;
        padding: 20px;
        border-bottom: 1px solid #dee2e6;
    }
    .custom-body { padding: 20px; }
    .action-btn-group { display: flex; gap: 6px; }
    
    /* Clean text protection utility for long text entries */
    .text-truncate-custom {
        max-width: 260px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    /* System Native Popup Engine styles with Fixed Height Scrolling View */
    .native-popup {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0px 5px 25px rgba(0,0,0,0.3);
        z-index: 10000;
        width: 90%;
        max-width: 500px;
        border: 1px solid #ccc;
        max-height: 85vh;
        overflow-y: auto;
    }
    .popup-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
    }
    .form-group-item { margin-bottom: 15px; }
    .form-group-item label { display: block; margin-bottom: 5px; font-weight: bold; }
    .form-group-item input, .form-group-item select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; background: #fff; }
</style>

<div class="custom-card">
    <div class="custom-header">
        <h2 style="margin:0; font-weight: 700; color: #333;">Payments Management Panel</h2>
    </div>
    <div class="custom-body">
        
        <!-- SYSTEM ERROR EXCEPTION BANNER FEEDBACK MATRIX -->
        @if ($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 20px; padding: 12px; font-size: 14px; border-radius: 4px; background-color: #fce8e6; color: #c5221f; border: 1px solid #fad2cf;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('flash_message'))
            <div class="alert alert-success" style="margin-bottom: 20px; padding: 12px; font-size: 14px; border-radius: 4px; background-color: #e6f4ea; color: #137333; border: 1px solid #ceead6;">
                {{ session('flash_message') }}
            </div>
        @endif
        
        <!-- FORCE CONTAINER: Splits elements cleanly on opposite sides -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; width: 100%;">
            
            <!-- Left Side: Interactive Add Button using your original Green styling -->
            <div>
                <button type="button" class="btn btn-success" onclick="openPopup('addPaymentModal')" style="padding: 8px 16px; font-weight: 600; cursor: pointer; border-radius: 4px; background-color: #04AA6D; border-color: #04AA6D; color: white; border: 1px solid transparent;">
                    + Record New Payment
                </button> 
            </div>
            
            <!-- Right Side: Real-time Instant Search Input Box -->
            <div>
                <div style="display: flex; gap: 6px; width: 320px; margin: 0;">
                    <div style="position: relative; flex: 1; display: flex; align-items: center;">
                        <form method="GET" action="{{ url('/payments') }}" style="width: 100%; margin: 0;">
                            <input type="text" 
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Type receipt or student name..." 
                                   style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px; font-size: 14px;">
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- RESPONSIVE SCROLL WRAPPER: Ensures complete table control and button visibility -->
        <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table class="table table-bordered table-striped" style="width: 100%; border-collapse: collapse; margin-top: 10px; background: #fff; border: 1px solid #dee2e6;">
                <thead style="background: #f1f1f1; font-weight: 600;">
                    <tr>
                        <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; width: 60px;">#</th>
                        <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; width: 140px;">Receipt No.</th>
                        <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left;">Student Target</th>
                        <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; width: 150px;">Total Course Fee</th>
                        <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; width: 150px;">Amount Received</th>
                        <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; width: 240px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($payments as $item)
                    @php
                        $displayFee = $item->total_fee;
                        if (empty($displayFee) || $displayFee == 0) {
                            $displayFee = isset($courses) && count($courses) > 0 ? $courses->first()->fee : 0.00;
                        }

                        // FIXED TARGET MATCHING: Isolates and grabs only the unique registration string directly
                        $linkedStudent = isset($item->student_name) ? $item->student_name : (isset($item->student) ? $item->student->name : 'ID: ' . $item->student_id);
                        $studentDisplayName = $linkedStudent;
                    @endphp
                    <tr class="payment-table-row">
                        <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle;">{{ ($payments->currentPage() - 1) * $payments->perPage() + $loop->iteration }}</td>
                        <!-- SAFE NULL COALESCING GUARD FIXED BELOW TO PREVENT CRASHES -->
                        <td class="receipt-no-cell" style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; font-weight: 600;">{{ $item->receipt_no ?? '—' }}</td>
                        <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; font-weight: 600;">
                            {{ $studentDisplayName }}
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; font-weight: 600; color: #555555;">
                            Rs. {{ number_format($displayFee, 2) }}
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; font-weight: 600; color: #04AA6D;">
                            Rs. {{ number_format($item->amount, 2) }}
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle;">
                            <div class="action-btn-group">
                                <button type="button" class="btn btn-info btn-sm" onclick="openPopup('viewPaymentModal{{ $item->id }}')" style="color: white; padding: 4px 10px; border-radius: 4px; background-color: #17a2b8; border: none; cursor: pointer;">View</button>
                                <button type="button" class="btn btn-primary btn-sm" onclick="openPopup('editPaymentModal{{ $item->id }}')" style="color: white; padding: 4px 10px; border-radius: 4px; background-color: #007bff; border: none; cursor: pointer;">Edit</button>
                                <a href="{{ url('/payment/print/' . $item->id) }}" target="_blank" class="btn btn-dark btn-sm" style="color: white; text-decoration: none; padding: 4px 10px; border-radius: 4px; font-weight: 600; background-color: #343a40; display: inline-block;">Print</a>
                                <form method="POST" action="{{ url('/payments/' . $item->id) }}" style="display:inline; margin: 0;">
                                    {{ method_field('DELETE') }}
                                    {{ csrf_field() }}
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Confirm delete payment record?')" style="color: white; padding: 4px 10px; border-radius: 4px; background-color: #dc3545; border: none; cursor: pointer;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- INDIVIDUAL POPUP LAYER FOR VIEWING -->
                    <div class="popup-overlay" id="viewPaymentModal{{ $item->id }}-overlay" onclick="closePopup('viewPaymentModal{{ $item->id }}')"></div>
                    <div class="native-popup" id="viewPaymentModal{{ $item->id }}">
                        <h3 style="margin-top: 0; font-weight: 700; color: #333;">Payment Receipt Details</h3>
                        <hr style="border: 0; border-top: 1px solid #dee2e6; margin: 15px 0;">
                        <p><strong>Receipt Number:</strong> {{ $item->receipt_no ?? '—' }}</p>
                        <p><strong>Student Registered:</strong> {{ $studentDisplayName }}</p>
                        <p><strong>Total Master Course Fee:</strong> Rs. {{ number_format($displayFee, 2) }}</p>
                        <p><strong>Amount Collected:</strong> Rs. {{ number_format($item->amount, 2) }}</p>
                        <p><strong>Date of Transaction:</strong> {{ \Carbon\Carbon::parse($item->payment_date)->format('M d, Y') }}</p>
                        <hr style="border: 0; border-top: 1px solid #dee2e6; margin: 15px 0;">
                        <div style="text-align: right;">
                            <button type="button" class="btn btn-secondary" onclick="closePopup('viewPaymentModal{{ $item->id }}')" style="padding: 6px 14px; border-radius: 4px; background-color: #6c757d; color: white; border: none; cursor: pointer;">Close</button>
                        </div>
                    </div>

                    <!-- INDIVIDUAL POPUP LAYER FOR EDITING -->
                    <div class="popup-overlay" id="editPaymentModal{{ $item->id }}-overlay" onclick="closePopup('editPaymentModal{{ $item->id }}')"></div>
                    <div class="native-popup" id="editPaymentModal{{ $item->id }}">
                        <h3 style="margin-top: 0; font-weight: 700; color: #333;">Modify Payment Record</h3>
                        <hr style="border: 0; border-top: 1px solid #dee2e6; margin: 15px 0;">
                        
                        <form method="POST" action="{{ url('/payments/' . $item->id) }}">
                            {{ method_field('PATCH') }}
                            {{ csrf_field() }}
                            
                            <div class="form-group-item">
                                <label>Receipt Number</label>
                                <input type="text" name="receipt_no" value="{{ $item->receipt_no ?? '—' }}" readonly style="background: #f8f9fa; width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                            </div>
                            <div class="form-group-item">
                                <label>Select Target Student</label>
                                <select name="student_id" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                                    @if(isset($enrollments))
                                        @foreach($enrollments as $student)
                                            <option value="{{ $student->id }}" {{ $item->student_id == $student->id ? 'selected' : '' }}>
                                                [{{ $student->reg_no ?? 'REG' }}] — {{ $student->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="{{ $item->student_id }}" selected>{{ $studentDisplayName }}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="form-group-item">
                                <label>Select Active Course Enrollment</label>
                                <select class="course-fee-selector" onchange="syncEditModalFee(this, 'edit_total_fee_{{ $item->id }}')" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                                    <option value="">-- Choose Course to View Price --</option>
                                    @if(isset($courses))
                                        @foreach($courses as $course)
                                            <option value="{{ $course->fee }}" {{ $item->total_fee == $course->fee ? 'selected' : '' }}>{{ $course->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group-item">
                                <label>Total Course Fee (Rs.)</label>
                                <input type="text" name="total_fee" id="edit_total_fee_{{ $item->id }}" value="{{ $displayFee }}" readonly style="background: #f8f9fa; font-weight: 600; width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                            </div>
                            <div class="form-group-item">
                                <label>Date of Collection</label>
                                <input type="date" name="payment_date" value="{{ $item->payment_date }}" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                            </div>
                            <div class="form-group-item">
                                <label>Amount Paid (Rs.)</label>
                                <input type="number" step="0.01" name="amount" value="{{ $item->amount }}" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                            </div>
                            <div style="text-align: right; margin-top: 20px; display: flex; gap: 6px; justify-content: flex-end;">
                                <button type="button" class="btn btn-secondary" onclick="closePopup('editPaymentModal{{ $item->id }}')" style="padding: 6px 12px; border-radius: 4px; background-color: #6c757d; color: white; border: none; cursor: pointer;">Cancel</button>
                                <button type="submit" class="btn btn-primary" style="padding: 6px 12px; border-radius: 4px; background-color: #007bff; color: white; border: none; cursor: pointer;">Save Changes</button>
                            </div>
                        </form>
                    </div>
                @empty
                    <tr>
                        <td colspan="6" style="border: 1px solid #dee2e6; padding: 25px; text-align: center; color: #888; background: #fafafa; font-style: italic;">No payment ledger entries recorded inside current session window bounds.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div> <!-- END OF RESPONSIVE SCROLL WRAPPER -->

        <!-- Framework Pagination Links -->
        <div style="margin-top: 20px;">
            {{ $payments->links() }}
        </div>

    </div>
</div>

<!-- GLOBAL POPUP CONTAINER FOR RECORDING NEW PAYMENTS -->
<div class="popup-overlay" id="addPaymentModal-overlay" onclick="closePopup('addPaymentModal')"></div>
<div class="native-popup" id="addPaymentModal">
    <h3 style="margin-top: 0; font-weight: 700; color: #333;">Record New Payment</h3>
    <hr style="border: 0; border-top: 1px solid #dee2e6; margin: 15px 0;">
    
    <form action="{{ url('/payments') }}" method="POST">
        {{ csrf_field() }}
        
        <div class="form-group-item">
            <label>Receipt Number</label>
            <input type="text" name="receipt_no" value="Auto Generated" readonly style="background: #f8f9fa; color: #666; font-style: italic;">
        </div>
        
        <div class="form-group-item">
            <label>Select Target Student</label>
            <select name="student_id" id="paymentStudentSelect" onchange="fetchStudentCourseDetails()" required>
                <option value="">-- Select student profile identity record --</option>
                @if(isset($enrollments))
                    @foreach($enrollments as $student)
                        <option value="{{ $student->id }}">
                            [{{ $student->reg_no ?? 'REG-NEW' }}] — {{ $student->name }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
        
        <div class="form-group-item">
            <label>Select Active Course Enrollment</label>
            <select name="enrollment_id" id="paymentCourseSelect" required style="background-color: #f8f9fa; pointer-events: none;">
                <option value="">-- Select active course blueprint --</option>
            </select>
        </div>
        <div class="form-group-item">
            <label>Total Course Fee (Rs.)</label>
            <input type="text" name="total_fee" id="creation_total_fee" value="0.00" readonly style="background: #f8f9fa; font-weight: 600; color: #111111;">
        </div>
        <div class="form-group-item">
            <label>Date of Collection</label>
            <input type="date" name="paid_date" value="{{ date('Y-m-d') }}" required>
        </div>
        <div class="form-group-item">
            <label>Amount Paid (Rs.) *</label>
            <input type="number" step="0.01" name="amount" placeholder="Enter collected transaction amount" required>
            
            <!-- 🌟 INJECTED PROTECTION GUARD NOTIFICATION BANNER LINKED TO YOUR LATE FINE ENGINES -->
            <small style="color: #04AA6D; font-weight: 700; display: block; font-size: 11px; background: #e6f4ea; padding: 6px 12px; border-radius: 4px; border: 1px dashed #04AA6D; margin-top: 8px; line-height: 1.4;">
                ℹ️ SYSTEM NOTICE: The submitted payment amount will automatically be matched against the oldest unpaid monthly milestone installment and will settle accumulated late fines (Rs. 50/Day past due date) first.
            </small>
        </div>

        <div style="text-align: right; display: flex; gap: 6px; justify-content: flex-end; margin-top: 20px;">
            <button type="button" class="btn btn-secondary" onclick="closePopup('addPaymentModal')" style="padding: 8px 14px; border-radius: 4px; background-color: #6c757d; color: white; border: none; cursor: pointer;">Cancel</button>
            <button type="submit" class="btn btn-success" style="padding: 8px 16px; font-weight: 600; border-radius: 4px; background-color: #04AA6D; border-color: #04AA6D; color: white; border: none; cursor: pointer;">Save Record</button>
        </div>
    </form>
</div>

<!-- JavaScript Embedded Sink Engine Controller Mapping -->
<script>
    function openPopup(modalId) {
        document.getElementById(modalId).style.display = 'block';
        document.getElementById(modalId + '-overlay').style.display = 'block';
    }
    
    function closePopup(modalId) {
        document.getElementById(modalId).style.display = 'none';
        document.getElementById(modalId + '-overlay').style.display = 'none';
    }

    /**
     * 🌟 REAL-TIME FETCH ENGINE
     * Runs automatically when a user changes the target student dropdown.
     */
    function fetchStudentCourseDetails() {
        let studentId = document.getElementById('paymentStudentSelect').value;
        let courseSelect = document.getElementById('paymentCourseSelect');
        let feeInput = document.getElementById('creation_total_fee');

        if (!studentId) {
            courseSelect.innerHTML = '<option value="">-- Select active course blueprint --</option>';
            feeInput.value = "0.00";
            return;
        }

        courseSelect.innerHTML = '<option value="">⌛ Fetching active registration details...</option>';

        // FIXED PATHWAY: Route request through the web endpoint connected to your PaymentController lookup method
        fetch(`/payments/enrollment-details/${studentId}`)
            .then(response => {
                if (!response.ok) throw new Error('Enrollment records absent.');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Auto-load and select the matched enrollment course
                    courseSelect.innerHTML = `<option value="${data.enrollment_id}" selected>${data.course_name}</option>`;
                    // Auto-load the exact total fee baseline
                    feeInput.value = parseFloat(data.total_course_fee).toFixed(2);
                }
            })
            .catch(error => {
                console.warn(error.message);
                courseSelect.innerHTML = '<option value="">⚠️ No active registration found</option>';
                feeInput.value = "0.00";
            });
}

    /**
     * Fallback utility for managing row-level editing overlays
     */
    function syncEditModalFee(selectElement, targetInputId) {
        let feeValue = selectElement.value;
        let feeTargetInput = document.getElementById(targetInputId);
        if (feeValue) {
            feeTargetInput.value = parseFloat(feeValue).toFixed(2);
        } else {
            feeTargetInput.value = "0.00";
        }
    }
</script>

@endsection
