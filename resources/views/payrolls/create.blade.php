@extends('layouts.app')

@section('content')
<div class="container-fluid p-0" style="max-width: 500px; margin: 0 auto;">
    <div class="mb-4">
        <h2 class="fw-bold tracking-tight text-black mb-1" style="font-size: 1.75rem;">Initialize Payroll</h2>
        <p class="text-muted small mb-0">Batch compile active staff and teacher salaries into tracking ledgers.</p>
    </div>

    <div class="card p-4" style="background: #ffffff; border: 1px solid #dee2e6; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <form action="{{ route('payrolls.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="form-label fw-bold small text-secondary" style="text-transform: uppercase;">Target Billing Month</label>
                <select name="salary_month" class="form-select" style="border-radius: 6px; padding: 12px;" required>
                    <option value="" disabled selected>Choose billing target parameters...</option>
                    @php
                        // Dynamically look ahead and behind to structure proper payroll target drops
                        for ($i = 0; $i < 6; $i++) {
                            $monthStr = date('F-Y', strtotime("-$i month"));
                            echo "<option value='{$monthStr}'>📅 {$monthStr}</option>";
                        }
                    @endphp
                </select>
                <small class="text-muted mt-2 d-block" style="font-size: 11px; line-height: 1.4;">
                    * This processing rule fetches all active teachers and support staff listed inside your directory matrices and generates un-disbursed salary statements.
                </small>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" style="background-color: #6f42c1; color: white; border: none; padding: 12px 24px; border-radius: 6px; font-weight: 700; font-size: 14px; flex-grow: 1; cursor: pointer;">
                    ⚡ Batch Compile Salary Ledger
                </button>
                <a href="{{ route('payrolls.index') }}" style="background-color: #64748b; color: white; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 700; font-size: 14px; text-align: center;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
