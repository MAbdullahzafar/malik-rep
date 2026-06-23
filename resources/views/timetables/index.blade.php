@extends('layouts.app')

@section('content')

<style>
    .timetable-card { background: #ffffff; border: 1px solid #dee2e6; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin: 15px 0; overflow: hidden; }
    .timetable-header { background-color: #f8f9fa; padding: 20px; border-bottom: 2px solid #f1f1f1; }
    .timetable-body { padding: 20px; }
    
    /* Premium Weekday Headers & Card Layout Styles Aligned with Previous Schemes */
    .day-wrapper { margin-bottom: 25px; background: #ffffff; border: 1px solid #dee2e6; border-radius: 6px; overflow: hidden; }
    .day-title-strip { background: #1e293b; color: #ffffff; padding: 12px 20px; font-weight: 700; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; display: flex; justify-content: space-between; align-items: center; }
    .slots-container { padding: 15px; background: #fafafa; display: flex; flex-direction: column; gap: 12px; }
    
    /* Professional Left-Border Highlight Accents */
    .slot-item-card { background: #ffffff; border: 1px solid #dee2e6; border-left: 4px solid #17a2b8; border-radius: 4px; padding: 15px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    .slot-time-badge { font-weight: 700; color: #333; font-size: 14px; background: #f8f9fa; padding: 6px 12px; border-radius: 4px; border: 1px solid #ced4da; min-width: 180px; text-align: center; }
    .slot-main-details { flex: 1; padding: 0 20px; font-size: 14px; color: #555; }
    .room-tag { background: #333333; color: #ffffff; padding: 2px 6px; border-radius: 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
    
    /* Scorecards Summary Counters */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-bottom: 25px; }
    .stats-card { background: #ffffff; border: 1px solid #dee2e6; border-radius: 6px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    .stats-value { font-size: 24px; font-weight: 700; margin-top: 5px; }
    
    /* Interactive Layer Modals Overlays Styles */
    .native-popup { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 25px; border-radius: 8px; box-shadow: 0px 5px 25px rgba(0,0,0,0.3); z-index: 10000; width: 90%; max-width: 500px; border: 1px solid #ccc; max-height: 85vh; overflow-y: auto; }
    .popup-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; }
    .form-group-item { margin-bottom: 15px; }
    .form-group-item label { display: block; margin-bottom: 5px; font-weight: bold; font-size: 13px; color: #333; }
    .form-group-item input, .form-group-item select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; background: #fff; font-size: 14px; }
</style>

@if($errors->any())
    <div style="background-color: #fce8e6; border: 1px solid #fad2cf; color: #c5221f; padding: 12px 15px; border-radius: 4px; margin-bottom: 20px; font-weight: 600; font-size: 14px;">
        ⚠️ {{ $errors->first() }}
    </div>
@endif

<div class="stats-grid">
    <div class="stats-card" style="border-left: 4px solid #17a2b8;">
        <div style="font-weight: 600; color: #666; font-size: 13px;">TOTAL TRACKS MANAGED</div>
        <div class="stats-value" style="color: #17a2b8;">{{ $courses->count() }} Active</div>
    </div>
    <div class="stats-card" style="border-left: 4px solid #04AA6D;">
        <div style="font-weight: 600; color: #666; font-size: 13px;">CONFLICT PROTECTION GUARD</div>
        <div class="stats-value" style="color: #04AA6D;">Shield Active</div>
    </div>
</div>
<div class="timetable-card">
    <div class="timetable-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h2 style="margin:0; font-weight: 700; color: #333; font-size: 22px;">Institutional Timetable Scheduler</h2>
            <p style="margin: 4px 0 0 0; color: #777; font-size: 13px;">Conflict-free scheduling blocks with dynamic course filters</p>
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
            <form action="{{ url('/timetables') }}" method="GET" style="display: flex; gap: 8px; margin: 0; align-items: center;">
                <label style="font-weight: 600; color: #555; white-space: nowrap; font-size: 14px;">Filter Class Track:</label>
                <select name="course_id" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px; font-size: 14px; background: #fff; width: 220px; cursor: pointer; font-weight: 600; color: #555;">
                    <option value="">-- Full Institutional Schedule --</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ $selectedCourseId == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                    @endforeach
                </select>
            </form>
            <button type="button" onclick="openPopup('addTimetableModal')" style="padding: 8px 16px; font-weight: 600; cursor: pointer; border-radius: 4px; background-color: #04AA6D; color: white; border: none; font-size: 13px; box-shadow: 0 2px 4px rgba(4,170,109,0.2);">
                + Create Time Slot
            </button> 
        </div>
    </div>

    <div class="timetable-body">
        @foreach($daysOfWeek as $day)
            @php
                $daySlots = $schedules->where('day_of_week', $day);
            @endphp
            
            <div class="day-wrapper">
                <div class="day-title-strip">
                    <span>📅 {{ $day }}</span>
                    <span style="background: rgba(255,255,255,0.2); padding: 2px 10px; border-radius: 12px; font-size: 11px;">{{ $daySlots->count() }} Sessions</span>
                </div>
                
                <div class="slots-container">
                    @forelse($daySlots as $slot)
                        <div class="slot-item-card" style="border-left-color: {{ $loop->index % 2 == 0 ? '#17a2b8' : '#04AA6D' }};">
                            <div class="slot-time-badge">
                                🕒 {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                            </div>
                            
                            <div class="slot-main-details">
                                <strong style="color: #333; font-size: 15px;">{{ $slot->course->name ?? 'Course Program' }}</strong>
                                <div style="margin-top: 4px; font-size: 13px; color: #666;">
                                    <span>👨‍🏫 Instructor: Prof. {{ $slot->teacher->name ?? 'Faculty' }}</span>
                                    <span style="margin: 0 10px; color: #ddd;">|</span>
                                    <span>🏢 Location: <span class="room-tag">{{ $slot->room_number }}</span></span>
                                </div>
                            </div>
                            
                            <div>
                                <form method="POST" action="{{ url('/timetables/' . $slot->id) }}" style="margin: 0;">
                                    {{ method_field('DELETE') }} {{ csrf_field() }}
                                    <button type="submit" onclick="return confirm('Remove this class timeframe block slot?')" style="padding: 6px 12px; border-radius: 4px; background-color: #fee2e2; color: #ef4444; border: 1px solid #fec2c2; font-size: 12px; font-weight: 600; cursor: pointer;">
                                        Drop Slot
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div style="padding: 15px; color: #888; font-style: italic; font-size: 13px; text-align: center; background: #fafafa; border-radius: 4px;">
                            No class schedules recorded for {{ $day }} inside the log registers.
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
<div class="popup-overlay" id="addTimetableModal-overlay" onclick="closePopup('addTimetableModal')"></div>
<div class="native-popup" id="addTimetableModal">
    <h3 style="margin-top: 0; font-weight: 700; color: #333; font-size: 18px;">Schedule New Class Slot</h3>
    <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">
    
    <form action="{{ url('/timetables') }}" method="POST">
        {{ csrf_field() }}
        
        <div class="form-group-item">
            <label>Select Active Course Track</label>
            <select name="course_id" required>
                <option value="">-- Choose Program Syllabus --</option>
                @foreach($courses as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group-item">
            <label>Assign Teacher / Lecturer</label>
            <select name="teacher_id" required>
                <option value="">-- Choose Professor Faculty --</option>
                @foreach($teachers as $t)
                    <option value="{{ $t->id }}">Prof. {{ $t->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group-item">
            <label>Target Weekday</label>
            <select name="day_of_week" required>
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
                <option value="Saturday">Saturday</option>
                <option value="Sunday">Sunday</option>
            </select>
        </div>

        <div style="display: flex; gap: 15px;">
            <div class="form-group-item" style="flex: 1;">
                <label>Class Start Time</label>
                <input type="time" name="start_time" value="09:00" required>
            </div>
            <div class="form-group-item" style="flex: 1;">
                <label>Class End Time</label>
                <input type="time" name="end_time" value="10:30" required>
            </div>
        </div>

        <div class="form-group-item">
            <label>Classroom Allocation / Lecture Room No.</label>
            <input type="text" name="room_number" placeholder="e.g. Lab-3, Room-502, Main Aud" required>
        </div>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">
        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <button type="button" onclick="closePopup('addTimetableModal')" style="padding: 8px 16px; border-radius: 4px; background-color: #6c757d; color: white; border: none; cursor: pointer;">Cancel</button>
            <button type="submit" style="padding: 8px 16px; border-radius: 4px; background-color: #04AA6D; color: white; border: none; cursor: pointer; font-weight: 600;">Publish Slot</button>
        </div>
    </form>
</div>

<script>
    function openPopup(modalId) {
        document.getElementById(modalId).style.display = 'block';
        document.getElementById(modalId + '-overlay').style.display = 'block';
    }

    function closePopup(modalId) {
        document.getElementById(modalId).style.display = 'none';
        document.getElementById(modalId + '-overlay').style.display = 'none';
    }
</script>

@endsection
