@extends('layouts.app')

@section('content')
<div class="container-fluid p-0" style="max-width: 600px; margin: 0 auto;">
    <div class="mb-4">
        <h2 class="fw-bold tracking-tight text-black mb-1" style="font-size: 1.75rem;">Appoint Support Staff</h2>
        <p class="text-muted small mb-0">Register operational personnel profile variables into institutional records.</p>
    </div>

    <div class="card p-4" style="background: #ffffff; border: 1px solid #dee2e6; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <form action="{{ route('staff.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-bold small text-secondary" style="text-transform: uppercase;">Full Staff Name</label>
                <input type="text" name="name" class="form-control" style="border-radius: 6px; padding: 10px;" placeholder="e.g. Asif Khan" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small text-secondary" style="text-transform: uppercase;">Operational Role</label>
                <select name="role" class="form-select" style="border-radius: 6px; padding: 10px;" required>
                    <option value="" disabled selected>Select assigned designation...</option>
                    <option value="Guard">🛡️ Security Guard</option>
                    <option value="Peon">📋 Office Peon</option>
                    <option value="Sweeper">🧹 Janitorial Sweeper</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small text-secondary" style="text-transform: uppercase;">Mobile Contact Number</label>
                <input type="text" name="contact" class="form-control" style="border-radius: 6px; padding: 10px;" placeholder="e.g. 03001234567">
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold small text-secondary" style="text-transform: uppercase;">Base Monthly Contract Salary (Rs.)</label>
                <input type="number" name="base_salary" class="form-control" style="border-radius: 6px; padding: 10px;" placeholder="e.g. 25000" min="0" required>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" style="background-color: #04AA6D; color: white; border: none; padding: 12px 24px; border-radius: 6px; font-weight: 700; font-size: 14px; flex-grow: 1; cursor: pointer;">
                    🚀 Confirm Institutional Appointment
                </button>
                <a href="{{ route('staff.index') }}" style="background-color: #64748b; color: white; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 700; font-size: 14px; text-align: center;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
