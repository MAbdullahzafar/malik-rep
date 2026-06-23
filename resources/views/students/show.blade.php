
@extends('layouts.app')

@section('content')

<div class="card" style="border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #eaeaea; margin: 20px 0;">
  <div class="card-header" style="background-color: #f8f9fa; font-weight: 700; font-size: 1.1rem; border-bottom: 1px solid #eaeaea; padding: 15px 20px; color: #333;">
    Student Profile Workspace
  </div>
  <div class="card-body" style="padding: 30px;">
    
    <div class="row g-4">
        <!-- Left Side Pane: Personal Matrix Identification Columns -->
        <div class="col-12 col-md-4 border-end pe-md-4">
            <h4 class="fw-bold mb-4" style="color: #222; font-size: 1.25rem; border-bottom: 2px solid #eaeaea; padding-bottom: 10px;">Personal Matrix</h4>
            
            <!-- Live Database Thumbnail Display Cell Profile Frame Layout -->
            <div class="text-center mb-4">
                @if(!empty($students->photo))
                    <img src="{{ asset($students->photo) }}" style="width: 140px; height: 140px; border-radius: 50%; object-fit: cover; border: 3px solid #eaeaea; box-shadow: 0 4px 8px rgba(0,0,0,0.05);">
                @else
                    <div style="width: 140px; height: 140px; border-radius: 50%; background: #eaeaea; display: inline-flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: 700; color: #888; border: 3px solid #eaeaea;">
                        {{ strtoupper(substr($students->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <!-- SYSTEM INTERNAL DATABASE PRIMARY KEY ROW -->
            <p class="text-muted small mb-1" style="text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">System Identity Key:</p>
            <h6 class="fw-bold mb-3" style="color: #555;">ID #{{ $students->id }}</h6>

            <!-- ✨ INJECTED: Unique Generated Registration Key Field Display Matrix -->
            <p class="text-muted small mb-1" style="text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Registration ID:</p>
            <h6 class="fw-bold mb-4" style="color: #04AA6D; font-size: 1.15rem; letter-spacing: 0.5px;">{{ $students->reg_no ?? 'PENDING' }}</h6>

            <!-- Student Profile Attributes Fields Layout Rows -->
            <div class="mb-3">
                <span class="text-muted small d-block" style="text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 2px;">Full Name:</span>
                <span class="fw-semibold text-dark fs-5">{{ $students->name }}</span>
            </div>
            <div class="mb-3" style="margin-top: 15px;">
                <span class="text-muted small d-block" style="text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 2px;">Residential Address:</span>
                <span class="text-secondary" style="font-size: 0.95rem; line-height: 1.5; display: block;">{{ $students->address }}</span>
            </div>

            <div class="mb-3" style="margin-top: 15px;">
                <span class="text-muted small d-block" style="text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 2px;">Mobile Contact:</span>
                <span class="text-secondary" style="font-weight: 500;">{{ $students->contact ?? $students->mobile }}</span>
            </div>
        </div>
        <!-- Right Side Pane: Active Curriculum Enrollment Matrix Log -->
        <div class="col-12 col-md-8 ps-md-4">
            <h4 class="fw-bold mb-4" style="color: #222; font-size: 1.25rem; border-bottom: 2px solid #eaeaea; padding-bottom: 10px;">Active Course Enrollments</h4>
            
            <div class="table-responsive">
                <table class="table table-bordered table-striped" style="width: 100%; border-collapse: collapse; background: #fff; font-size: 0.95rem;">
                    <thead style="background: #f8f9fa; font-weight: 600; color: #555;">
                        <tr>
                            <th style="border: 1px solid #dee2e6; padding: 12px;">Enrollment No</th>
                            <th style="border: 1px solid #dee2e6; padding: 12px;">Course Name</th>
                            <th style="border: 1px solid #dee2e6; padding: 12px; width: 160px;">Registration Date</th>
                            <th style="border: 1px solid #dee2e6; padding: 12px; width: 160px; text-align: right;">Total Course Fee</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($students->enrollments && $students->enrollments->count() > 0)
                            @foreach($students->enrollments as $enroll)
                                <tr>
                                    <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; font-weight: 500; color: #666;">{{ $enroll->enroll_no }}</td>
                                    <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; font-weight: 600; color: #111;">
                                        {{ $enroll->course->name ?? 'Course Program Mapping Absent' }}
                                    </td>
                                    <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; color: #555;">
                                        {{ \Carbon\Carbon::parse($enroll->join_date)->format('M d, Y') }}
                                    </td>
                                    <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; text-align: right; font-weight: 700; color: #333;">
                                        Rs. {{ number_format($enroll->fee ?? ($students->course->fee ?? 0.00), 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            @if($students->course)
                                <tr>
                                    <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; font-weight: 500; color: #666;">E-001</td>
                                    <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; font-weight: 600; color: #111;">
                                        {{ $students->course->name }}
                                    </td>
                                    <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; color: #555;">
                                        {{ $students->created_at ? $students->created_at->format('M d, Y') : date('M d, Y') }}
                                    </td>
                                    <td style="border: 1px solid #dee2e6; padding: 12px; vertical-align: middle; text-align: right; font-weight: 700; color: #333;">
                                        Rs. {{ number_format($students->course->fee ?? 0.00, 2) }}
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="4" style="border: 1px solid #dee2e6; padding: 25px; text-align: center; color: #888; background: #fafafa; font-style: italic;">
                                        No curriculum admission profiles linked to this student entity directory record.
                                    </td>
                                </tr>
                            @endif
                        @endif
                    </tbody>
                </table>
            </div>
            
            <!-- Quick Back Control Shortcut Navigation Button Group -->
            <div class="mt-4 pt-3 border-top text-end">
                <a href="{{ url('/students') }}" class="btn btn-secondary" style="padding: 8px 18px; border-radius: 4px; font-weight: 600; text-decoration: none;">
                    ← Return to Students Directory
                </a>
            </div>
        </div>
    </div>
    
  </div>
</div>

@endsection
