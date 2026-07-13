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
    
    /* Safe stats styling blocks */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-bottom: 25px; }
    .stats-card { background: #fff; padding: 15px; border: 1px solid #dee2e6; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    .stats-value { font-size: 26px; font-weight: 700; margin-top: 5px; }
</style>

<!-- SAFE COUNTER BLOCK FOR STUDENT SCREEN -->
<div class="stats-grid">
    <div class="stats-card" style="border-left: 4px solid #04AA6D;">
        <div style="font-weight: 600; color: #666; font-size: 14px;">Total Students Managed</div>
        <div class="stats-value" style="color: #04AA6D;">{{ $students->total() }} Profiles</div>
    </div>
    <div class="stats-card" style="border-left: 4px solid #17a2b8;">
        <div style="font-weight: 600; color: #666; font-size: 14px;">Active Courses Listed</div>
        <div class="stats-value" style="color: #17a2b8;">{{ $courses->count() }} Tracks</div>
    </div>
</div>

<div class="custom-card">
    <div class="custom-header">
        <h2 style="margin:0; font-weight: 700; color: #333;">Students Management Project</h2>
    </div>
    <div class="custom-body">
        
        <!-- FORCE CONTAINER: Splits elements cleanly on opposite sides -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; width: 100%;">
            
            <!-- Left Side: Interactive Add Button -->
            <div>
                <button type="button" class="btn btn-success" onclick="openPopup('addStudentModal')" style="padding: 8px 16px; font-weight: 600; cursor: pointer; border-radius: 4px; background-color: #04AA6D; border-color: #04AA6D; color: white;">
                    + Add New Student
                </button> 
            </div>
            
            <!-- Right Side: Real-time Instant Search Input Box -->
            <div>
                <div style="display: flex; gap: 6px; width: 320px; margin: 0;">
                    <div style="position: relative; flex: 1; display: flex; align-items: center;">
                        <input type="text" 
                               id="instantSearchBox" 
                               onkeyup="runInstantSearch()"
                               placeholder="Type name or registration code..." 
                               style="width: 100%; padding: 8px 30px 8px 12px; border: 1px solid #ced4da; border-radius: 4px; font-size: 14px;">
                                <span id="clearSearchBtn" onclick="clearSearchField()" style="display: none; position: absolute; right: 12px; cursor: pointer; color: #999; font-weight: bold; font-size: 14px;" title="Clear Search">✕</span>
                    </div>
                </div>
            </div>
            
        </div>
        
        <table class="table table-bordered table-striped" style="width: 100%; border-collapse: collapse; margin-top: 10px; background: #fff; border: 1px solid #dee2e6;">
           <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
 
        <thead style="background: #f1f1f1; font-weight: 600; color: #475569;">
                <tr>
                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; width: 60px;">#</th>
                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; width: 80px;">Photo</th>
                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; width: 130px;">Reg No.</th>
                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left;">Name</th>
                    <!-- <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; width: 120px;">Course</th> -->
                     <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; width: 140px;">Course</th>

                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left;">Address</th>
                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; width: 140px;">Mobile</th>
                    <!-- <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; width: 200px;">Actions</th> -->
                     <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; width: 240px;">Actions</th>

                </tr>
            </thead>
            <tbody>
            @forelse($students as $item)
                <tr class="student-table-row">
                    <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; color: #64748b; font-weight: 600;">{{ ($students->currentPage() - 1) * $students->perPage() + $loop->iteration }}</td>
                    
                    <td style="border: 1px solid #dee2e6; padding: 6px; vertical-align: middle; text-align: center;">
                        @if(!empty($item->photo))
                            <img src="{{ asset($item->photo) }}" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 1px solid #ddd;">
                        @else
                            <div style="width: 45px; height: 45px; border-radius: 50%; background: #eaeaea; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; color: #777;">
                                {{ strtoupper(substr($item->name, 0, 1)) }}
                            </div>
                        @endif
                    </td>

                    <td class="student-reg-cell" style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; font-weight: 700; color: #04AA6D;">
                        {{ $item->reg_no ?? 'PENDING' }}
                    </td>

                    <td class="student-name-cell" style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; font-weight: 700; color: #1e293b;">{{ $item->name }}</td>
                    
                    <!-- 🛡️ FIXED CORE PAYLOAD BINDING LOOKUP TO MATCH RAW JOINS -->
                    <td class="student-course-cell" style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; font-weight: 600; color: #0284c7;">
                        @if(!empty($item->course_name))
                            <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.85rem; font-weight: 600;">
                                {{ $item->course_name }}
                            </span>
                        @else
                            <span style="color: #999; font-style: italic; font-size: 0.85rem;">No Enrollment</span>
                        @endif
                    </td>
                    <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; color: #475569;">
                        <div class="text-truncate-custom" title="{{ $item->address }}">
                            {{ $item->address }}
                        </div>
                    </td>
                    <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; font-weight: 600; color: #334155;">{{ $item->mobile ?? $item->contact ?? '—' }}</td>
                    <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; text-align: center;">
                        <div class="action-btn-group" style="justify-content: center;">
                            <a href="{{ url('/students/' . $item->id . '/profile') }}" class="btn btn-info btn-sm" style="color: white; text-decoration: none; padding: 4px 10px; border-radius: 4px; background-color: #17a2b8; border: 1px solid #17a2b8; font-size: 12px; font-weight: 600; display: inline-block;">
                                Profile
                            </a>
                            <button type="button" class="btn btn-primary btn-sm" onclick="openPopup('editModal{{ $item->id }}')" style="padding: 4px 10px; border-radius: 4px; background-color: #007bff; border: none; color: white; cursor: pointer; font-size: 12px; font-weight: 600;">Edit</button>
                            
                            <form method="POST" action="{{ url('/students' . '/' . $item->id) }}" style="display:inline; margin: 0;">
                                {{ method_field('DELETE') }}
                                {{ csrf_field() }}
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Confirm delete student record?')" style="padding: 4px 10px; border-radius: 4px; background-color: #dc3545; border: none; color: white; cursor: pointer; font-size: 12px; font-weight: 600;">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>

                <!-- INDIVIDUAL POPUP LAYER FOR VIEWING -->
                <div class="popup-overlay" id="viewModal{{ $item->id }}-overlay" onclick="closePopup('viewModal{{ $item->id }}')"></div>
                <div class="native-popup" id="viewModal{{ $item->id }}">
                    <h3 style="margin-top: 0; font-weight: 700; color: #1e293b;">Student Details</h3>
                    <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">
                    
                    @if($item->photo)
    <img src="{{ $item->photo }}"
         alt="Student Photo"
         style="width:45px;height:45px;border-radius:50%;object-fit:cover;">
@else
    <img src="{{ asset('images/default-avatar.png') }}"
         alt="No Photo"
         style="width:45px;height:45px;border-radius:50%;object-fit:cover;">
@endif

                    <p><strong>Registration Code:</strong> <span style="font-weight:700; color: #04AA6D;">{{ $item->reg_no ?? 'PENDING' }}</span></p>
                    <p><strong>Full Name:</strong> {{ $item->name }}</p>
                    <p><strong>Enrolled Course:</strong> {{ $item->course_name ?? 'No Course Assigned' }}</p>
                    <p><strong>Address:</strong> {{ $item->address }}</p>
                    <p><strong>Mobile Contact:</strong> {{ $item->mobile ?? $item->contact ?? '—' }}</p>
                    <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">
                    <div style="text-align: right;">
                        <button type="button" class="btn btn-secondary" onclick="closePopup('viewModal{{ $item->id }}')" style="padding: 6px 14px; background: #6c757d; border: none; color: white; border-radius: 4px; cursor: pointer;">Close View Window</button>
                    </div>
                </div>
                <!-- INDIVIDUAL POPUP LAYER FOR EDITING -->
                <div class="popup-overlay" id="editModal{{ $item->id }}-overlay" onclick="closePopup('editModal{{ $item->id }}')"></div>
                <div class="native-popup" id="editModal{{ $item->id }}">
                    <h3 style="margin-top: 0; font-weight: 700; color: #333;">Edit Student Entry</h3>
                    <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">
                    <form action="{{ url('/students/' . $item->id) }}" method="POST" enctype="multipart/form-data">
                        {{ method_field('PATCH') }}
                        {{ csrf_field() }}
                        
                        <div class="form-group-item">
                            <label>Registration Number</label>
                            <input type="text" value="{{ $item->reg_no ?? 'GEN-ON-SAVE' }}" readonly style="background: #f8f9fa; font-weight: 700; color: #04AA6D;">
                        </div>
                        <div class="form-group-item">
                            <label>Name</label>
                            <input type="text" name="name" value="{{ $item->name }}" required>
                        </div>
                        <div class="form-group-item">
                            <label>Address</label>
                            <input type="text" name="address" value="{{ $item->address }}" required>
                        </div>
                        <div class="form-group-item">
                            <label>Mobile</label>
                            <input type="text" name="contact" value="{{ $item->mobile ?? $item->contact }}" required>
                        </div>
                        <div class="form-group-item">
                            <label>Enrolled Course Curriculum</label>
                            <select name="course_id" required>
                                <option value="">-- Select Admission Course --</option>
                                @foreach($courses as $course)
                                    <!-- 🛡️ FIXED LOOKUP: Comparing flat course ID fields to prevent relation mapping crash -->
                                    <option value="{{ $course->id }}" {{ (isset($item->course_id) && $item->course_id == $course->id) ? 'selected' : '' }}>
                                        {{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group-item">
                            <label>Update Profile Photo</label>
                            <input type="file" name="photo" accept="image/*">
                        </div>
                        <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">
                        <div style="text-align: right; display: flex; gap: 6px; justify-content: flex-end;">
                            <button type="button" class="btn btn-secondary" onclick="closePopup('editModal{{ $item->id }}')" style="padding: 6px 12px; border-radius: 4px; background: #6c757d; color: white; border: none; cursor: pointer;">Cancel</button>
                            <button type="submit" class="btn btn-success" style="padding: 6px 16px; font-weight: 600; border-radius: 4px; background-color: #04AA6D; color: white; border: none; cursor: pointer;">Save Changes</button>
                        </div>
                    </form>
                </div>
            @empty
                <tr>
                    <td colspan="8" style="border: 1px solid #dee2e6; padding: 25px; text-align: center; color: #888; background: #fafafa; font-style: italic;">No student records found.</td>
                </tr>
            @endforelse
            <tr id="noResultsRow" style="display: none;">
                <td colspan="8" style="border: 1px solid #dee2e6; padding: 25px; text-align: center; color: #888; background: #fafafa; font-style: italic;">No student records found matching your query.</td>
            </tr>
            </tbody>
        </table>
        </div>


        <div style="margin-top: 20px;">
            {{ $students->links() }}
        </div>
    </div>
</div>

<!-- COMPONENT MODAL LAYER FOR ADDING NEW STUDENTS -->
<div class="popup-overlay" id="addStudentModal-overlay" onclick="closePopup('addStudentModal')"></div>
<div class="native-popup" id="addStudentModal">
    <h3 style="margin-top: 0; font-weight: 700; color: #333;">Add New Student</h3>
    <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">
    <form action="{{ url('/students') }}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        
        <div class="form-group-item">
            <label>Generated Unique ID</label>
            <input type="text" id="previewRegistrationId" value="Select course below..." readonly style="background: #f8f9fa; font-weight: 700; color: #04AA6D; border-left: 3px solid #04AA6D;">
        </div>
        <div class="form-group-item">
            <label>Full Name</label>
            <input type="text" name="name" placeholder="Enter student's name" required>
        </div>
        <div class="form-group-item">
            <label>Address</label>
            <input type="text" name="address" placeholder="Enter current address" required>
        </div>
        <div class="form-group-item">
            <label>Mobile Number</label>
            <input type="text" name="contact" placeholder="Enter mobile contact phone" required>
        </div>
        
        <div class="form-group-item">
            <label>Select Admission Course</label>
            <select name="course_id" id="creationCourseSelect" onchange="calculateLiveRegistrationPreview()" required>
                <option value="">-- Choose Enrolled Course --</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- 🌟 DYNAMIC FEE PAYMENT INSTALLMENTS PREFERENCE PLAN PICKER -->
        <div class="form-group-item">
            <label>Fee Payment Installments Plan *</label>
            <select name="requested_installments" id="requested_installments" required style="font-weight: 600; color: #1e293b;">
                <option value="1">Full One-Time Fee Payment (No Installments)</option>
                <option value="2">2 Easy Monthly Installments Plan</option>
                <option value="3" selected>3 Easy Monthly Installments Plan (Standard 3-Months Plan)</option>
                <option value="4">4 Easy Monthly Installments Plan (Extended Scope)</option>
                <option value="6">6 Easy Monthly Installments Plan (Custom Extended Plan)</option>
            </select>
            <small style="color: #64748b; font-size: 11px; display: block; margin-top: 4px;">
                The system will automatically divide your course fees evenly and set the installment due dates on the 10th of every consecutive month.
            </small>
        </div>
         <!-- 🌟 CORE PROFILE PICTURE MEDIA ATTACHMENT SLIP -->
        <div class="form-group-item" style="margin-bottom: 25px;">
            <label>Profile Picture Upload</label>
            <input type="file" name="photo" accept="image/*" style="width: 100%; padding: 8px; border: 1px dashed #cbd5e1; border-radius: 4px; background: #f8fafc; box-sizing: border-box;">
        </div>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">
        <div style="text-align: right; display: flex; gap: 6px; justify-content: flex-end; margin-top: 20px;">
            <button type="button" class="btn btn-secondary" onclick="closePopup('addStudentModal')" style="padding: 8px 14px; border-radius: 4px; background: #6c757d; color: white; border: none; cursor: pointer;">Cancel</button>
            <button type="submit" class="btn btn-success" style="padding: 8px 16px; font-weight: 600; border-radius: 4px; background-color: #04AA6D; border-color: #04AA6D; color: white; border: none; cursor: pointer;">Save Admission</button>
        </div>
    </form>
</div>

<!-- Embedded JavaScript Controller Engine -->
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
     * 🌟 AUTOMATED LIVE REG_NO CALCULATOR PREVIEW
     * Runs instantly in the background and computes sequential numbers globally.
     */
    function calculateLiveRegistrationPreview() {
        let selectElement = document.getElementById('creationCourseSelect');
        let previewInput = document.getElementById('previewRegistrationId');
        
        if (!selectElement.value) {
            previewInput.value = "Select course below...";
            return;
        }

        let selectedOptionText = selectElement.options[selectElement.selectedIndex].text;
        previewInput.value = "Analyzing identity sequence registers...";

        let courseName = selectedOptionText.trim();
        let coursePrefix = "ST";

        if (/^[A-Z]{2,4}$/i.test(courseName)) {
            coursePrefix = courseName.toUpperCase();
        } else {
            let words = courseName.split(' ');
            let prefix = '';
            for (let i = 0; i < words.length; i++) {
                if (words[i].trim().length > 0) {
                    prefix += words[i].trim().substr(0, 1).toUpperCase();
                }
            }
            coursePrefix = prefix.length > 0 ? prefix : "ST";
        }

        let rows = document.getElementsByClassName('student-table-row');
        let maxSequence = 1999; 

        for (let i = 0; i < rows.length; i++) {
            let regCell = rows[i].getElementsByClassName('student-reg-cell');
            if (regCell && regCell.length > 0) {
                let cellText = (regCell[0].textContent || regCell[0].innerText).trim();
                let parts = cellText.split('-');
                if (parts.length === 2) {
                    let serialNumber = parseInt(parts[1]);
                    if (!isNaN(serialNumber) && serialNumber > maxSequence) {
                        maxSequence = serialNumber;
                    }
                }
            }
        }

        let nextSequence = maxSequence + 1;
        previewInput.value = coursePrefix + '-' + nextSequence;
    }

    /**
     * 🌟 REAL-TIME DYNAMIC STRICT SEARCH ENGINE (FIXED)
     * Isolates exact cell string checking to separate Name, Code, and Course parameters.
     */
    function runInstantSearch() {
        let query = document.getElementById("instantSearchBox").value.toLowerCase().trim();
        let rows = document.getElementsByClassName("student-table-row");
        let closeBtn = document.getElementById("clearSearchBtn");
        let noResultsRow = document.getElementById("noResultsRow");
        let matchingCount = 0;

        if (closeBtn) {
            closeBtn.style.display = query.length > 0 ? "block" : "none";
        }

        for (let i = 0; i < rows.length; i++) {
            let nameField = rows[i].getElementsByClassName("student-name-cell");
            let regField = rows[i].getElementsByClassName("student-reg-cell");
            let courseField = rows[i].getElementsByClassName("student-course-cell");
            
            let nameValue = (nameField && nameField.length > 0) ? (nameField[0].textContent || nameField[0].innerText).toLowerCase().trim() : '';
            let regValue = (regField && regField.length > 0) ? (regField[0].textContent || regField[0].innerText).toLowerCase().trim() : '';
            let courseValue = (courseField && courseField.length > 0) ? (courseField[0].textContent || courseField[0].innerText).toLowerCase().trim() : '';
            
            // Apply a precise match rule when searching 2-character module codes like 'ai', 'wd', 'gd'
            let isCourseMatch = false;
            if (query.length === 2) {
                isCourseMatch = (courseValue === query);
            } else {
                isCourseMatch = (courseValue.indexOf(query) > -1);
            }

            if (nameValue.indexOf(query) > -1 || regValue.indexOf(query) > -1 || isCourseMatch) {
                rows[i].style.display = "";
                matchingCount++;
            } else {
                rows[i].style.display = "none";
            }
        }

        if (noResultsRow) {
            noResultsRow.style.display = (matchingCount === 0 && rows.length > 0) ? "" : "none";
        }
    }

    function clearSearchField() {
        document.getElementById("instantSearchBox").value = "";
        runInstantSearch();
    }
</script>

@endsection