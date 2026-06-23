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
    
    /* Clean text protection utility for long syllabus text entries */
    .text-truncate-custom {
        max-width: 300px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    /* System Native Popup Engine styles */
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
    .form-group-item input, .form-group-item textarea { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
    .form-group-item textarea { resize: vertical; height: 80px; }
</style>

<div class="custom-card">
    <div class="custom-header">
        <h2 style="margin:0; font-weight: 700; color: #333;">Courses Management Panel</h2>
    </div>
    <div class="custom-body">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; width: 100%;">
            <div>
                <button type="button" class="btn btn-success" onclick="openPopup('addCourseModal')" style="padding: 8px 16px; font-weight: 600; cursor: pointer; border-radius: 4px;">
                    + Add New Course
                </button> 
            </div>
            
            <div>
                <div style="display: flex; gap: 6px; width: 320px; margin: 0;">
                    <div style="position: relative; flex: 1; display: flex; align-items: center;">
                        <input type="text" 
                               id="instantCourseSearchBox" 
                               onkeyup="runCourseInstantSearch()"
                               placeholder="Type course name to filter..." 
                               style="width: 100%; padding: 8px 30px 8px 12px; border: 1px solid #ced4da; border-radius: 4px; font-size: 14px;">
                        
                        <span id="clearCourseSearchBtn" onclick="clearCourseSearchField()" style="display: none; position: absolute; right: 12px; cursor: pointer; color: #999; font-weight: bold; font-size: 14px;" title="Clear Search">✕</span>
                    </div>
                </div>
            </div>
        </div>
        
        <table class="table table-bordered table-striped" style="width: 100%; border-collapse: collapse; margin-top: 10px; background: #fff;">
            <thead style="background: #f1f1f1; font-weight: 600;">
                <tr>
                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; width: 60px;">#</th>
                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left;">Course Name</th>
                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left;">Syllabus Outline</th>
                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; width: 120px;">Duration</th>
                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; width: 120px;">Course Fee</th>
                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; width: 200px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($courses as $item)
                <tr class="course-table-row">
                    <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle;">{{ ($courses->currentPage() - 1) * $courses->perPage() + $loop->iteration }}</td>
                    <td class="course-name-cell" style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; font-weight: 500;">{{ $item->name }}</td>
                    <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle;">
                        <div class="text-truncate-custom" title="{{ $item->syllabus }}">
                            {{ $item->syllabus }}
                        </div>
                    </td>
                    <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle;">
                        <span class="badge bg-light text-dark border" style="font-size: 13px; font-weight: 500; padding: 6px 10px; border-radius: 4px;">
                             {{ $item->duration }}
                        </span>
                    </td>
                    <!-- CRITICAL FIX: Safe fallback mapping prevents property-undefined crash windows -->
                    <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; font-weight: 500;">
                        {{ number_format($item->fee ?? 0, 2) }}
                    </td>
                    <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle;">
                        <div class="action-btn-group">
                            <button type="button" class="btn btn-info btn-sm" onclick="openPopup('viewCourseModal{{ $item->id }}')" style="padding: 4px 10px; border-radius: 4px; color: white;">View</button>
                            <button type="button" class="btn btn-primary btn-sm" onclick="openPopup('editCourseModal{{ $item->id }}')" style="padding: 4px 10px; border-radius: 4px;">Edit</button>
                            
                            <form method="POST" action="{{ url('/course' . '/' . $item->id) }}" style="display:inline; margin: 0;">
                                {{ method_field('DELETE') }}
                                {{ csrf_field() }}
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Confirm delete course record?')" style="padding: 4px 10px; border-radius: 4px;">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>

                <!-- INDIVIDUAL POPUP LAYER FOR VIEWING -->
                <div class="popup-overlay" id="viewCourseModal{{ $item->id }}-overlay" onclick="closePopup('viewCourseModal{{ $item->id }}')"></div>
                <div class="native-popup" id="viewCourseModal{{ $item->id }}">
                    <h3 style="margin-top: 0; font-weight: 700;">Course Details</h3>
                    <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">
                    <p><strong>Course Name:</strong> {{ $item->name }}</p>
                    <p><strong>Duration Timeframe:</strong> {{ $item->duration }}</p>
                    <p><strong>Total Course Fee:</strong> {{ number_format($item->fee ?? 0, 2) }}</p>
                    <p><strong>Syllabus Description:</strong></p>
                    <div style="background: #f8f9fa; padding: 12px; border-radius: 4px; border: 1px solid #e9ecef; white-space: pre-line; max-height: 120px; overflow-y: auto;">{{ $item->syllabus }}</div>
                    <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">
                    <button type="button" class="btn btn-secondary" onclick="closePopup('viewCourseModal{{ $item->id }}')">Close</button>
                </div>
                <!-- INDIVIDUAL POPUP LAYER FOR EDITING (FIXED AND RE-COMPILATION CAPABLE) -->
                <div class="popup-overlay" id="editCourseModal{{ $item->id }}-overlay" onclick="closePopup('editCourseModal{{ $item->id }}')"></div>
                <div class="native-popup" id="editCourseModal{{ $item->id }}">
                    <h3 style="margin-top: 0; font-weight: 700;">Edit Course Entry</h3>
                    <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">
                    <form action="{{ url('/course/' . $item->id) }}" method="POST">
                        {{ method_field('PATCH') }}
                        {{ csrf_field() }}
                        <div class="form-group-item">
                            <label>Course Name</label>
                            <input type="text" name="name" value="{{ $item->name }}" required>
                        </div>
                        <div class="form-group-item">
                            <label>Syllabus Outline</label>
                            <textarea name="syllabus" required>{{ $item->syllabus }}</textarea>
                        </div>
                        <div class="form-group-item">
                            <label>Duration Timeframe</label>
                            <input type="text" name="duration" value="{{ $item->duration }}" required>
                        </div>
                        <div class="form-group-item">
                            <label>Total Course Fee</label>
                            <input type="number" step="0.01" name="fee" value="{{ $item->fee ?? 0 }}" required>
                        </div>
                        <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">
                        <button type="button" class="btn btn-secondary" onclick="closePopup('editCourseModal{{ $item->id }}')">Cancel</button>
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </form>
                </div>
            @empty
                <tr>
                    <td colspan="6" style="border: 1px solid #dee2e6; padding: 25px; text-align: center; color: #888; background: #fafafa; font-style: italic;">
                        No course records found.
                    </td>
                </tr>
            @endforelse
            
            <tr id="noCourseResultsRow" style="display: none;">
                <td colspan="6" style="border: 1px solid #dee2e6; padding: 25px; text-align: center; color: #888; background: #fafafa; font-style: italic;">
                    No course records found matching your query.
                </td>
            </tr>
            </tbody>
        </table>

        <!-- Interactive Bootstrap Pagination navigation buttons -->
        <div class="d-flex justify-content-center mt-4">
            {!! $courses->links() !!}
        </div>

    </div>
</div>

<!-- COMPONENT MODAL LAYER FOR ADDING NEW COURSES -->
<div class="popup-overlay" id="addCourseModal-overlay" onclick="closePopup('addCourseModal')"></div>
<div class="native-popup" id="addCourseModal">
    <h3 style="margin-top: 0; font-weight: 700;">Add New Course</h3>
    <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">
    <form action="{{ url('/course') }}" method="POST">
        {{ csrf_field() }}
        <div class="form-group-item">
            <label>Course Name</label>
            <input type="text" name="name" placeholder="e.g. Web Development" required>
        </div>
        <div class="form-group-item">
            <label>Syllabus Outline</label>
            <textarea name="syllabus" placeholder="Enter course syllabus summary details..." required></textarea>
        </div>
        <div class="form-group-item">
            <label>Duration Timeframe</label>
            <input type="text" name="duration" placeholder="e.g. 3 Months, 6 Weeks" required>
        </div>
        <div class="form-group-item">
            <label>Total Course Fee</label>
            <input type="number" step="0.01" name="fee" placeholder="e.g. 15000" required>
        </div>
        <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">
        <button type="button" class="btn btn-secondary" onclick="closePopup('addCourseModal')">Cancel</button>
        <button type="submit" class="btn btn-success">Save Record</button>
    </form>
</div>
<!-- Integrated Local JavaScript Engines -->
<script>
    function openPopup(modalId) {
        document.getElementById(modalId).style.display = 'block';
        document.getElementById(modalId + '-overlay').style.display = 'block';
    }
    
    function closePopup(modalId) {
        document.getElementById(modalId).style.display = 'none';
        document.getElementById(modalId + '-overlay').style.display = 'none';
    }

    function runCourseInstantSearch() {
        let query = document.getElementById("instantCourseSearchBox").value.toLowerCase();
        let rows = document.getElementsByClassName("course-table-row");
        let closeBtn = document.getElementById("clearCourseSearchBtn");
        let noResultsRow = document.getElementById("noCourseResultsRow");
        let matchingCount = 0;

        closeBtn.style.display = query.length > 0 ? "block" : "none";

        for (let i = 0; i < rows.length; i++) {
            let nameField = rows[i].querySelector(".course-name-cell");
            if (nameField) {
                let nameValue = nameField.textContent || nameField.innerText;
                if (nameValue.toLowerCase().indexOf(query) > -1) {
                    rows[i].style.display = "";
                    matchingCount++;
                } else {
                    rows[i].style.display = "none";
                }
            }
        }

        if (noResultsRow) {
            noResultsRow.style.display = (matchingCount === 0 && rows.length > 0) ? "" : "none";
        }
    }

    function clearCourseSearchField() {
        document.getElementById("instantCourseSearchBox").value = "";
        runCourseInstantSearch();
    }
</script>

@endsection
