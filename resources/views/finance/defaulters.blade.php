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
    
    /* Stats Indicator Cards Grid System Styling */
    .stats-card {
        background: #ffffff; 
        border: 1px solid #dee2e6; 
        border-radius: 6px; 
        padding: 20px; 
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }
</style>

<!-- 📊 MASTER FINANCIAL CUMULATIVE STATS CARDS GRID -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 25px;">
    <div class="stats-card" style="border-left: 4px solid #dc3545;">
        <div style="font-weight: 700; color: #64748b; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">TOTAL DEFAULTER RECORDS</div>
        <div style="font-size: 28px; font-weight: 800; color: #dc3545; margin-top: 5px;">{{ $defaulters->count() }} Accounts Overdue</div>
    </div>
    <div class="stats-card" style="border-left: 4px solid #ffc107;">
        <div style="font-weight: 700; color: #64748b; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">ACCUMULATED LATE FINES PENALTIES</div>
        <div style="font-size: 28px; font-weight: 800; color: #ffc107; margin-top: 5px;">Rs. {{ number_format($defaulters->sum('fine_charged'), 2) }}</div>
    </div>
    <div class="stats-card" style="border-left: 4px solid #17a2b8;">
        <div style="font-weight: 700; color: #64748b; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">TOTAL OVERDUE PRINCIPAL CAPITAL</div>
        <div style="font-size: 28px; font-weight: 800; color: #17a2b8; margin-top: 5px;">Rs. {{ number_format($defaulters->sum('base_amount') - $defaulters->sum('amount_paid'), 2) }}</div>
    </div>
</div>
<!-- CONTAINER CARD -->
<div class="custom-card">
    <div class="custom-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h2 style="margin:0; font-weight: 700; color: #1e293b;">Institutional Monthly Fee Defaulters Roster</h2>
            <p style="margin: 4px 0 0 0; color: #64748b; font-size: 13px;">Live tracking roster for non-compliant student fee milestones past the 10th of the month.</p>
        </div>
        
        <!-- Action Control Group Buttons Panel -->
        <div style="display: flex; gap: 10px;">
            <!-- 🌟 MASS REMINDER BULK BUTTON ENGINE LINK -->
            @if($defaulters->count() > 0)
                <button type="button" class="btn btn-warning" onclick="sendMassReminders()" style="padding: 10px 18px; font-weight: 700; cursor: pointer; border-radius: 4px; background-color: #ffc107; border: 1px solid #e0a800; color: #212529;">
                    📢 Send All Reminders (One-Click)
                </button>
            @endif
            
            <button onclick="window.print()" class="btn btn-dark" style="background: #1e293b; color: white; border: none; padding: 10px 18px; border-radius: 4px; font-weight: 700; cursor: pointer;">
                🖨️ Print Defaulter Report
            </button>
        </div>
    </div>
    
    <div class="custom-body">
        <div style="overflow-x: auto;">
            <table class="table table-bordered table-striped" style="width: 100%; border-collapse: collapse; background: #fff; border: 1px solid #dee2e6; font-size: 14px; text-align: left;">
                <thead style="background: #f1f1f1; font-weight: 600; color: #475569;">
                    <tr>
                        <th style="border: 1px solid #dee2e6; padding: 12px;">ID #</th>
                        <th style="border: 1px solid #dee2e6; padding: 12px;">Student Name</th>
                        <th style="border: 1px solid #dee2e6; padding: 12px;">Mobile Contact</th>
                        <th style="border: 1px solid #dee2e6; padding: 12px;">Course Track</th>
                        <th style="border: 1px solid #dee2e6; padding: 12px; text-align: center;">Milestone Slot</th>
                        <th style="border: 1px solid #dee2e6; padding: 12px; text-align: center;">Due Date</th>
                        <th style="border: 1px solid #dee2e6; padding: 12px; text-align: right;">Base Overdue</th>
                        <th style="border: 1px solid #dee2e6; padding: 12px; text-align: right; color: #dc3545;">Late Fine (Rs. 50/Day)</th>
                        <th style="border: 1px solid #dee2e6; padding: 12px; text-align: right; font-weight: 700;">Total Payable</th>
                        <th style="border: 1px solid #dee2e6; padding: 12px; text-align: center; width: 120px;" class="no-print">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($defaulters as $row)
                    @php
                        $totalDueWithFine = ($row->base_amount - $row->amount_paid) + $row->fine_charged;
                        
                        // Format the local dynamic target numbers for WhatsApp Web APIs
                        $cleanMobile = preg_replace('/[^0-9]/', '', $row->student_mobile);
                        if (str_starts_with($cleanMobile, '0')) {
                            $cleanMobile = '92' . substr($cleanMobile, 1);
                        }
                        
                        // Individual encoded text reminder string template
                        $messageText = "Dear " . $row->student_name . ",\n\nThis is a notification from the Student Management System. Your fee installment for *" . $row->course_name . "* is overdue.\n\n▪️ Overdue Amount: Rs. " . number_format($row->base_amount - $row->amount_paid, 2) . "\n▪️ Late Fine: Rs. " . number_format($row->fine_charged, 2) . " (Rs. 50/Day Fine)\n▪️ Total Payable: *Rs. " . number_format($totalDueWithFine, 2) . "*\n\nPlease clear your balance at the Allied Bank counter immediately to prevent additional fine penalties. Thank you.";
                    @endphp
                    <tr style="border-bottom: 1px solid #edf2f7;" class="defaulter-row-item" 
                        data-mobile="{{ $cleanMobile }}" 
                        data-message="{{ rawurlencode($messageText) }}">
                        
                        <td style="border: 1px solid #dee2e6; padding: 12px; font-weight: 700; color: #64748b;">S-00{{ $row->student_id }}</td>
                        <td style="border: 1px solid #dee2e6; padding: 12px; font-weight: 700; color: #1e293b;">{{ $row->student_name }}</td>
                        <td style="border: 1px solid #dee2e6; padding: 12px; color: #475569;">{{ $row->student_mobile ?? '—' }}</td>
                        <td style="border: 1px solid #dee2e6; padding: 12px; font-weight: 600; color: #0284c7;">{{ $row->course_name }}</td>
                        <td style="border: 1px solid #dee2e6; padding: 12px; text-align: center;">
                            <span style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-weight: 700; font-size: 11px; border: 1px solid #cbd5e1;">
                                Month {{ $row->installment_number }} of {{ $row->total_milestones_configured }}
                            </span>
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 12px; text-align: center; color: #dc3545; font-weight: 700;">
                            📅 {{ \Carbon\Carbon::parse($row->due_date)->format('d-M-Y') }}
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 12px; text-align: right; font-weight: 600;">
                            Rs. {{ number_format($row->base_amount - $row->amount_paid, 2) }}
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 12px; text-align: right; color: #dc3545; font-weight: 700;">
                            + Rs. {{ number_format($row->fine_charged, 2) }}
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 12px; text-align: right; font-weight: 800; color: #b91c1c; font-size: 15px; background: rgba(220, 53, 69, 0.02);">
                            Rs. {{ number_format($totalDueWithFine, 2) }}
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 12px; text-align: center;" class="no-print">
                            @if(!empty($row->student_mobile))
                                <a href="https://wa.me{{ $cleanMobile }}?text={{ rawurlencode($messageText) }}" target="_blank" class="btn btn-success btn-sm" style="padding: 5px 10px; font-weight: 600; border-radius: 4px; background-color: #04AA6D; border: none; color: white; text-decoration: none; display: inline-block; font-size: 12px; cursor: pointer;">
                                    💬 Notice
                                </a>
                            @else
                                <span style="color: #94a3b8; font-style: italic; font-size: 12px;">No Mobile</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="padding: 35px; text-align: center; color: #04AA6D; font-weight: 700; font-size: 15px; background: #f0fdf4;">
                            🎉 Awesome! There are currently 0 overdue accounts. All student milestones are completely paid and up to date!
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 🌟 AUTOMATED MASS ALERT TRIGGER SCRIPT -->
<script>
    function sendMassReminders() {
        let rows = document.querySelectorAll('.defaulter-row-item');
        if (rows.length === 0) return;

        if (confirm(`Confirm launching notifications engine to dispatch notices to all ${rows.length} active fee defaulters?`)) {
            rows.forEach((row, index) => {
                let mobile = row.getAttribute('data-mobile');
                let encodedMessage = row.getAttribute('data-message');
                
                if (mobile && mobile.trim() !== "") {
                    // Triggers successive tab executions with small timing intervals to circumvent pop-up blockers cleanly
                    setTimeout(() => {
                        window.open(`https://wa.me${mobile}?text=${encodedMessage}`, '_blank');
                    }, index * 1200);
                }
            });
        }
    }
</script>
@endsection
