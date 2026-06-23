@extends('layouts.app')

@section('content')
<style>
    /* Premium Minimalist Payroll Ledger Styles */
    .payroll-card {
        background: #ffffff !important;
        border: 1px solid #eaeaea !important;
        border-radius: 12px !important;
        padding: 24px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02) !important;
    }
    .payroll-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
    }
    .payroll-table thead tr th {
        color: #888888;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 16px;
        border-bottom: 1px solid #eaeaea;
    }
    .payroll-table tbody tr td {
        padding: 16px;
        border-top: 1px solid #eaeaea;
        border-bottom: 1px solid #eaeaea;
        color: #111111;
        font-size: 0.9rem;
        vertical-align: middle;
    }
    .payroll-table tbody tr td:first-child {
        border-left: 1px solid #eaeaea;
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
    }
    .payroll-table tbody tr td:last-child {
        border-right: 1px solid #eaeaea;
        border-top-right-radius: 8px;
        border-bottom-right-radius: 8px;
    }
    .payroll-table tbody tr:hover td {
        background: #fafafa;
    }
    .badge-type {
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .type-teacher { background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
    .type-staff { background-color: #f3e8ff; color: #6b21a8; border: 1px solid #e9d5ff; }
</style>

<div class="container-fluid p-0">
    <!-- Header Block Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold tracking-tight text-black mb-1" style="font-size: 1.75rem;">Institutional Salaries Ledger</h2>
            <p class="text-muted small mb-0">Track monthly salary releases, deductions, and polymorphic transactions across the campus directory.</p>
        </div>
        <a href="{{ route('payrolls.create') }}" style="background-color: #6f42c1; color: white; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-weight: 700; font-size: 14px; box-shadow: 0 4px 6px rgba(111,66,193,0.15);">
            ⚙️ Generate Monthly Payroll
        </a>
    </div>

    <!-- Master Payroll Table Sheet -->
    <div class="card payroll-card">
        <div class="table-responsive">
            <table class="payroll-table">
                <thead>
                    <tr>
                        <th style="width: 120px;">Salary Month</th>
                        <th>Employee Details</th>
                        <th style="width: 150px;">Personnel Type</th>
                        <th style="width: 160px; text-align: right;">Base Earnings</th>
                        <th style="width: 150px; text-align: center;">Payment State</th>
                        <th style="width: 180px; text-align: right;" class="no-print">Disbursement Terminal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrolls as $pay)
                        <tr>
                            <td style="font-weight: 700; color: #475569;">{{ $pay->salary_month }}</td>
                            <td>
                                <div style="font-weight: 700; color: #1e293b;">{{ $pay->payable->name ?? 'Unknown Profile Record' }}</div>
                                <small style="color: #64748b;">
                                    @if(get_class($pay->payable) == 'App\Models\Teacher')
                                        {{ $pay->payable->designation ?? 'Faculty Instructor' }}
                                    @else
                                        {{ $pay->payable->role ?? 'Support Staff' }}
                                    // custom layout logic tags closed
                                    @endif
                                </small>
                            </td>
                            <td>
                                @if(get_class($pay->payable) == 'App\Models\Teacher')
                                    <span class="badge-type type-teacher">👨‍🏫 Faculty</span>
                                @else
                                    <span class="badge-type type-staff">🧹 Support Staff</span>
                                @endif
                            </td>
                            <td style="text-align: right; font-weight: 700; color: #0f172a;">Rs. {{ number_format($pay->base_amount, 2) }}</td>
                            <td style="text-align: center;">
                                <span style="background-color: {{ $pay->status == 'Paid' ? '#e6f4ea':'#fff4e5' }}; color: {{ $pay->status == 'Paid' ? '#137333':'#b06000' }}; padding: 4px 12px; border-radius: 4px; font-weight: 700; font-size: 11px; border: 1px solid {{ $pay->status == 'Paid' ? '#ceead6':'#ffeacc' }};">
                                    {{ strtoupper($pay->status) }}
                                </span>
                                @if($pay->payment_date)
                                    <div style="font-size: 11px; color: #64748b; margin-top: 2px;">{{ \Carbon\Carbon::parse($pay->payment_date)->format('d-M-Y') }}</div>
                                @endif
                            </td>
                            <td style="text-align: right;" class="no-print">
                                @if($pay->status != 'Paid')
                                    <form action="{{ route('payrolls.pay', $pay->id) }}" method="POST" style="margin: 0; display: inline-block;">
                                        @csrf
                                        <button type="submit" style="background: #04AA6D; color: white; border: none; padding: 6px 12px; font-weight: 700; font-size: 12px; border-radius: 4px; cursor: pointer; box-shadow: 0 2px 4px rgba(4,170,109,0.15);">
                                            💵 Pay Net Salary
                                        </button>
                                    </form>
                                @else
                                    <span style="color: #10b981; font-weight: 700; font-size: 12px;">✔️ Settled Ledger</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted" style="font-style: italic;">
                                No institutional payroll registers compiled under active memory storage slots.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
