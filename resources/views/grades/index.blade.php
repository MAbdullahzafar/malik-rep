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
    
    /* System Native Popup Engine styles with Fixed Height Scrolling View Override */
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
    
    /* Statistical Dashboard Counter Widget Cards styling */
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
        font-size: 26px;
        font-weight: 700;
        margin-top: 5px;
    }
</style>

<!-- Live Performance Matrix Analytical Summary Ribbon counters -->
<div class="stats-grid">
    <div class="stats-card" style="border-left: 4px solid #04AA6D;">
        <div style="font-weight: 600; color: #666; font-size: 14px;">Class Passing Percentage</div>
        <div class="stats-value" style="color: #04AA6D;">{{ $classPassingPercentage }}%</div>
    </div>
    <div class="stats-card" style="border-left: 4px solid #17a2b8;">
        <div style="font-weight: 600; color: #666; font-size: 14px;">Total Passed Records</div>
        <div class="stats-value" style="color: #17a2b8;">{{ $totalPassed }} Students</div>
    </div>
    <div class="stats-card" style="border-left: 4px solid #dc3545;">
        <div style="font-weight: 600; color: #666; font-size: 14px;">Total Failed Records</div>
        <div class="stats-value" style="color: #dc3545;">{{ $totalFailed }} Students</div>
    </div>
</div>
<div class="custom-card">
    <div class="custom-header">
        <h2 style="margin:0; font-weight: 700; color: #333;">Grades & Performance Management Panel</h2>
    </div>
    <div class="custom-body">
        
        <!-- FORCE CONTAINER: Splits elements cleanly on opposite sides -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; width: 100%; gap: 15px; flex-wrap: wrap;">
            
            <!-- Left Side: Interactive Add Entry Button -->
            <div>
                <button type="button" class="btn btn-success" onclick="openPopup('addGradeModal')" style="padding: 8px 16px; font-weight: 600; cursor: pointer; border-radius: 4px; background-color: #04AA6D; color: white; border: none;">
                    + Log New Marks Entry
                </button> 
            </div>
            
            <!-- Right Side: Course Sync Filter Selection Dropdown Box -->
            <div>
                <form action="{{ url('/grades') }}" method="GET" style="display: flex; gap: 8px; margin: 0; align-items: center;">
                    <label style="font-weight: 600; color: #555; white-space: nowrap; margin: 0;">Filter By Course:</label>
                    <select name="course_id" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px; font-size: 14px; background: #fff; width: 220px; cursor: pointer;">
                        <option value="">-- All Active Courses --</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ $selectedCourseId == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            
        </div>
        <table class="table table-bordered table-striped" style="width: 100%; border-collapse: collapse; margin-top: 10px; background: #fff;">
            <thead style="background: #f1f1f1; font-weight: 600;">
                <tr>
                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; width: 60px;">#</th>
                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; width: 140px;">Reg No.</th>
                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left;">Student Name</th>
                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; width: 120px;">Course</th>
                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; width: 130px;">Exam Category</th>
                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: center; width: 140px;">Obtained / Total</th>
                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: center; width: 80px;">Grade</th>
                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: center; width: 90px;">Status</th>
                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: center; width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($grades as $grade)
                <tr>
                    <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle;">
                        {{ ($grades->currentPage() - 1) * $grades->perPage() + $loop->iteration }}
                    </td>
                    
                    <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; font-weight: 700; color: #111;">
                        {{ $grade->student->reg_no ?? 'PENDING' }}
                    </td>

                    <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; font-weight: 500;">
                        {{ $grade->student->name ?? 'N/A' }}
                    </td>
                    
                    <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle;">
                        <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.85rem; font-weight: 600;">
                            {{ $grade->course->name ?? 'N/A' }}
                        </span>
                    </td>

                    <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; font-weight: 600; color: #555;">
                        {{ $grade->exam_type }}
                    </td>

                    <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; text-align: center; font-weight: 700;">
                        {{ number_format($grade->marks_obtained, 0) }} / {{ number_format($grade->total_marks, 0) }}
                    </td>

                    <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; text-align: center; font-weight: 800; color: {{ $grade->grade_letter == 'F' ? '#dc3545' : '#04AA6D' }};">
                        {{ $grade->grade_letter }}
                    </td>

                    <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; text-align: center;">
                        @if($grade->status == 'Pass')
                            <span style="background-color: #e6f4ea; color: #137333; padding: 4px 8px; border-radius: 4px; font-weight: 700; font-size: 12px; border: 1px solid #ceead6;">PASS</span>
                        @else
                            <span style="background-color: #fce8e6; color: #c5221f; padding: 4px 8px; border-radius: 4px; font-weight: 700; font-size: 12px; border: 1px solid #fad2cf;">FAIL</span>
                        @endif
                    </td>
                    
                    <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; text-align: center;">
                        <form method="POST" action="{{ url('/grades/' . $grade->id) }}" style="display:inline; margin: 0;">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Confirm remove this grade entry from master registers?')" style="padding: 4px 10px; border-radius: 4px; background-color: #dc3545; color: white; border: none; font-size: 12px; cursor: pointer;">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="border: 1px solid #dee2e6; padding: 25px; text-align: center; color: #888; background: #fafafa; font-style: italic;">No performance marks records matching criteria logged inside system registers.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $grades->appends(request()->query())->links() }}
        </div>
    </div>
</div>
<!-- LOG PERFORMANCE ENTRY COMPONENT MODAL LAYER -->
<div class="popup-overlay" id="addGradeModal-overlay" onclick="closePopup('addGradeModal')"></div>
<div class="native-popup" id="addGradeModal">
    <h3 style="margin-top: 0; font-weight: 700; color: #333;">Log Student Evaluation Mark</h3>
    <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">
    
    <form action="{{ url('/grades') }}" method="POST">
        {{ csrf_field() }}
        
        <div class="form-group-item">
            <label>Select Enrolled Student</label>
            <select name="student_id" id="gradeStudentSelect" onchange="syncStudentCourseField()" required>
                <option value="">-- Choose Student Profile --</option>
                @foreach($students as $student)
                    <!-- 🌟 FRONTEND DATA BINDING MATRIX RESOLVED: Maps course relation safely via your registration indices -->
                    @php 
                        $assignedCourse = DB::table('enrollments')
                            ->join('courses', 'enrollments.course_id', '=', 'courses.id')
                            ->where('enrollments.student_id', $student->id)
                            ->select('courses.id', 'courses.name')
                            ->first();
                    @endphp
                    <option value="{{ $student->id }}" 
                            data-course-id="{{ $assignedCourse ? $assignedCourse->id : '' }}"
                            data-course-name="{{ $assignedCourse ? $assignedCourse->name : 'No assigned course found' }}">
                        {{ $student->reg_no ?? 'PENDING' }} - {{ $student->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group-item">
            <label>Academic Course Track</label>
            <!-- Hidden real input to transmit target model bindings to controller parameters -->
            <input type="hidden" name="course_id" id="hiddenCourseId">
            <input type="text" id="displayCourseName" value="Select student profile above..." readonly style="background: #f8f9fa; font-weight: 600; color: #555;">
        </div>

        <div class="form-group-item">
            <label>Evaluation Parameter Category</label>
            <select name="exam_type" id="examTypeSelect" onchange="adjustDefaultThresholdMarks()" required>
                <option value="Daily Test">Daily Test (50 Marks Baseline)</option>
                <option value="Midterm">Midterm (3 Months Exam)</option>
                <option value="Final Term">Final Term (6 Months Exam)</option>
            </select>
        </div>

        <div class="form-group-item">
            <label>Evaluation Date</label>
            <input type="date" name="evaluation_date" value="{{ date('Y-m-d') }}" required>
        </div>

        <div style="display: flex; gap: 15px;">
            <div class="form-group-item" style="flex: 1;">
                <label>Marks Obtained</label>
                <input type="number" step="0.01" name="marks_obtained" placeholder="e.g. 42" min="0" required>
            </div>
            <div class="form-group-item" style="flex: 1;">
                <label>Total Scale Marks</label>
                <input type="number" step="0.01" name="total_marks" id="totalMarksInput" value="50" min="1" required>
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">
        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <button type="button" class="btn btn-secondary" onclick="closePopup('addGradeModal')" style="padding: 8px 16px; border-radius: 4px; background-color: #6c757d; color: white; border: none; cursor: pointer;">Cancel</button>
            <button type="submit" class="btn btn-success" style="padding: 8px 16px; border-radius: 4px; background-color: #04AA6D; color: white; border: none; cursor: pointer; font-weight: 600;">Save Evaluation Entry</button>
        </div>
    </form>
</div>
<!-- CLIENT POPUP INTERACTIVE HANDLERS -->
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
     * Automatically sets total marks based on the exam type selection
     */
    function adjustDefaultThresholdMarks() {
        let category = document.getElementById('examTypeSelect').value;
        let totalInput = document.getElementById('totalMarksInput');
        
        if (category === 'Daily Test') {
            totalInput.value = "50";
        } else if (category === 'Midterm') {
            totalInput.value = "100";
        } else {
            totalInput.value = "100";
        }
    }

    /**
     * Dynamic client-side lookup mapping student options to course records instantly
     */
    function syncStudentCourseField() {
        let studentSelect = document.getElementById('gradeStudentSelect');
        let selectedOption = studentSelect.options[studentSelect.selectedIndex];
        let courseIdInput = document.getElementById('hiddenCourseId');
        let courseDisplay = document.getElementById('displayCourseName');

        // Extract values natively from data attributes
        let assignedCourseId = selectedOption.getAttribute('data-course-id');
        let assignedCourseName = selectedOption.getAttribute('data-course-name');

        if (assignedCourseId && assignedCourseId !== "") {
            courseIdInput.value = assignedCourseId;
            courseDisplay.value = assignedCourseName;
        } else {
            courseIdInput.value = "";
            courseDisplay.value = "No course profile attached to this student";
        }
    }
</script>

@endsection
