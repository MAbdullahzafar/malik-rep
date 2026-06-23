@extends('layouts.app')

@section('content')
<style>
    /* Premium Modern Minimalist Table Extensions */
    .staff-card {
        background: #ffffff !important;
        border: 1px solid #eaeaea !important;
        border-radius: 12px !important;
        padding: 24px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02) !important;
    }
    .staff-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
    }
    .staff-table thead tr th {
        color: #888888;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 16px;
        border-bottom: 1px solid #eaeaea;
    }
    .staff-table tbody tr td {
        padding: 16px;
        border-top: 1px solid #eaeaea;
        border-bottom: 1px solid #eaeaea;
        color: #111111;
        font-size: 0.9rem;
        vertical-align: middle;
    }
    .staff-table tbody tr td:first-child {
        border-left: 1px solid #eaeaea;
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
    }
    .staff-table tbody tr td:last-child {
        border-right: 1px solid #eaeaea;
        border-top-right-radius: 8px;
        border-bottom-right-radius: 8px;
    }
    .staff-table tbody tr:hover td {
        background: #fafafa;
    }
    .badge-role {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .role-guard { background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
    .role-peon { background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .role-sweeper { background-color: #f3e8ff; color: #6b21a8; border: 1px solid #e9d5ff; }

    /* 🌟 AUTOMATED STAFF DIRECTORY NON-SCROLLING PORTION VIEWPORTS */
    .staff-tab-content {
        display: none !important;
    }
    .staff-tab-content.active {
        display: block !important;
    }
    .staff-subnav-btn {
        padding: 10px 20px;
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .staff-subnav-btn:hover {
        background: #f1f5f9;
        color: #1e293b;
    }
    .staff-subnav-btn.active {
        background: #04AA6D !important;
        color: #ffffff !important;
        border-color: #04AA6D !important;
        box-shadow: 0 4px 6px rgba(4,170,109,0.15);
    }
</style>

<div class="container-fluid p-0">
    <!-- Header Block Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold tracking-tight text-black mb-1" style="font-size: 1.75rem;">Support Staff Roster</h2>
            <p class="text-muted small mb-0">Manage non-faculty personnel records including Guards, Peons, and Sweepers.</p>
        </div>
        <a href="{{ route('staff.create') }}" style="background-color: #04AA6D; color: white; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-weight: 700; font-size: 14px; box-shadow: 0 4px 6px rgba(4,170,109,0.15);">
            ➕ Appoint New Staff
        </a>
    </div>

    <!-- 🌟 TOP VIEWPORT OVERVIEW HORIZONTAL MENU TABS MATRIX -->
    <div style="background: #ffffff; border: 1px solid #dee2e6; border-radius: 8px; padding: 6px; display: flex; gap: 10px; margin-bottom: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <button type="button" class="staff-subnav-btn active" onclick="openStaffTabPane(event, 'all-staff-tab')">
            📋 All Support Staff
        </button>
        <button type="button" class="staff-subnav-btn" onclick="openStaffTabPane(event, 'guards-tab')">
            🛡️ Security Guards
        </button>
        <button type="button" class="staff-subnav-btn" onclick="openStaffTabPane(event, 'peons-tab')">
            💼 Office Peons
        </button>
        <button type="button" class="staff-subnav-btn" onclick="openStaffTabPane(event, 'sweepers-tab')">
            🧹 Janitorial Sweepers
        </button>
    </div>

    <!-- MAIN VIEWPORTS LAYER CONTAINER HOLDS ALL TABLES -->
    <div class="staff-viewports-wrapper">

        <!-- ========================================================================= -->
        <!-- 🌟 TAB 1: ALL SUPPORT STAFF MODULE CONTAINER                             -->
        <!-- ========================================================================= -->
        <div id="all-staff-tab" class="staff-tab-content active">
            <div class="card staff-card">
                <div class="table-responsive">
                    <table class="staff-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">ID Key</th>
                                <th>Staff Member Name</th>
                                <th style="width: 180px;">Designated Role</th>
                                <th style="width: 200px;">Mobile Contact</th>
                                <th style="width: 180px; text-align: right;">Base Monthly Salary</th>
                                <th style="width: 150px; text-align: right;">System State</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($staff as $member)
                                <tr>
                                    <td style="font-weight: 700; color: #64748b;">#ST-00{{ $member->id }}</td>
                                    <td style="font-weight: 700; color: #1e293b;">{{ $member->name }}</td>
                                    <td>
                                        @if($member->role == 'Guard')
                                            <span class="badge-role role-guard">🛡️ Guard</span>
                                        @elseif($member->role == 'Peon')
                                            <span class="badge-role role-peon">📋 Peon</span>
                                        @else
                                            <span class="badge-role role-sweeper">🧹 Sweeper</span>
                                        @endif
                                    </td>
                                    <td style="color: #475569; font-weight: 500;">{{ $member->contact ?? 'No Number' }}</td>
                                    <td style="text-align: right; font-weight: 700; color: #0f172a;">Rs. {{ number_format($member->base_salary, 2) }}</td>
                                    <td style="text-align: right;">
                                        <span style="background-color: {{ $member->status == 'Active' ? '#e6f4ea':'#fce8e6' }}; color: {{ $member->status == 'Active' ? '#137333':'#c5221f' }}; padding: 4px 10px; border-radius: 4px; font-weight: 700; font-size: 12px; border: 1px solid {{ $member->status == 'Active' ? '#ceead6':'#fad2cf' }};">
                                            {{ strtoupper($member->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-4 text-muted" style="font-style: italic;">No records logged inside system registers.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- ========================================================================= -->
        <!-- 🌟 TAB 2: SECURITY GUARDS FILTER VIEWPORT CONTAINER                      -->
        <!-- ========================================================================= -->
        <div id="guards-tab" class="staff-tab-content">
            <div class="card staff-card">
                <div class="table-responsive">
                    <table class="staff-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">ID Key</th>
                                <th>Staff Member Name</th>
                                <th style="width: 180px;">Designated Role</th>
                                <th style="width: 200px;">Mobile Contact</th>
                                <th style="width: 180px; text-align: right;">Base Monthly Salary</th>
                                <th style="width: 150px; text-align: right;">System State</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $guardCount = 0; @endphp
                            @foreach($staff as $member)
                                @if($member->role == 'Guard')
                                    @php $guardCount++; @endphp
                                    <tr>
                                        <td style="font-weight: 700; color: #64748b;">#ST-00{{ $member->id }}</td>
                                        <td style="font-weight: 700; color: #1e293b;">{{ $member->name }}</td>
                                        <td>
                                            <span class="badge-role role-guard">🛡️ Guard</span>
                                        </td>
                                        <td style="color: #475569; font-weight: 500;">{{ $member->contact ?? 'No Number' }}</td>
                                        <td style="text-align: right; font-weight: 700; color: #0f172a;">Rs. {{ number_format($member->base_salary, 2) }}</td>
                                        <td style="text-align: right;">
                                            <span style="background-color: {{ $member->status == 'Active' ? '#e6f4ea':'#fce8e6' }}; color: {{ $member->status == 'Active' ? '#137333':'#c5221f' }}; padding: 4px 10px; border-radius: 4px; font-weight: 700; font-size: 12px; border: 1px solid {{ $member->status == 'Active' ? '#ceead6':'#fad2cf' }};">
                                                {{ strtoupper($member->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            
                            @if($guardCount == 0)
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted" style="font-style: italic;">
                                        No security guards currently appointed inside the system directory.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- ========================================================================= -->
        <!-- 🌟 TAB 3: OFFICE PEONS FILTER VIEWPORT CONTAINER                          -->
        <!-- ========================================================================= -->
        <div id="peons-tab" class="staff-tab-content">
            <div class="card staff-card">
                <div class="table-responsive">
                    <table class="staff-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">ID Key</th>
                                <th>Staff Member Name</th>
                                <th style="width: 180px;">Designated Role</th>
                                <th style="width: 200px;">Mobile Contact</th>
                                <th style="width: 180px; text-align: right;">Base Monthly Salary</th>
                                <th style="width: 150px; text-align: right;">System State</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $peonCount = 0; @endphp
                            @foreach($staff as $member)
                                @if($member->role == 'Peon')
                                    @php $peonCount++; @endphp
                                    <tr>
                                        <td style="font-weight: 700; color: #64748b;">#ST-00{{ $member->id }}</td>
                                        <td style="font-weight: 700; color: #1e293b;">{{ $member->name }}</td>
                                        <td>
                                            <span class="badge-role role-peon">📋 Peon</span>
                                        </td>
                                        <td style="color: #475569; font-weight: 500;">{{ $member->contact ?? 'No Number' }}</td>
                                        <td style="text-align: right; font-weight: 700; color: #0f172a;">Rs. {{ number_format($member->base_salary, 2) }}</td>
                                        <td style="text-align: right;">
                                            <span style="background-color: {{ $member->status == 'Active' ? '#e6f4ea':'#fce8e6' }}; color: {{ $member->status == 'Active' ? '#137333':'#c5221f' }}; padding: 4px 10px; border-radius: 4px; font-weight: 700; font-size: 12px; border: 1px solid {{ $member->status == 'Active' ? '#ceead6':'#fad2cf' }};">
                                                {{ strtoupper($member->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            
                            @if($peonCount == 0)
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted" style="font-style: italic;">
                                        No office peons currently appointed inside the system directory.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- ========================================================================= -->
        <!-- 🌟 TAB 4: JANITORIAL SWEEPERS FILTER VIEWPORT CONTAINER                     -->
        <!-- ========================================================================= -->
        <div id="sweepers-tab" class="staff-tab-content">
            <div class="card staff-card">
                <div class="table-responsive">
                    <table class="staff-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">ID Key</th>
                                <th>Staff Member Name</th>
                                <th style="width: 180px;">Designated Role</th>
                                <th style="width: 200px;">Mobile Contact</th>
                                <th style="width: 180px; text-align: right;">Base Monthly Salary</th>
                                <th style="width: 150px; text-align: right;">System State</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $sweeperCount = 0; @endphp
                            @foreach($staff as $member)
                                @if($member->role == 'Sweeper')
                                    @php $sweeperCount++; @endphp
                                    <tr>
                                        <td style="font-weight: 700; color: #64748b;">#ST-00{{ $member->id }}</td>
                                        <td style="font-weight: 700; color: #1e293b;">{{ $member->name }}</td>
                                        <td>
                                            <span class="badge-role role-sweeper">🧹 Sweeper</span>
                                        </td>
                                        <td style="color: #475569; font-weight: 500;">{{ $member->contact ?? 'No Number' }}</td>
                                        <td style="text-align: right; font-weight: 700; color: #0f172a;">Rs. {{ number_format($member->base_salary, 2) }}</td>
                                        <td style="text-align: right;">
                                            <span style="background-color: {{ $member->status == 'Active' ? '#e6f4ea':'#fce8e6' }}; color: {{ $member->status == 'Active' ? '#137333':'#c5221f' }}; padding: 4px 10px; border-radius: 4px; font-weight: 700; font-size: 12px; border: 1px solid {{ $member->status == 'Active' ? '#ceead6':'#fad2cf' }};">
                                                {{ strtoupper($member->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            
                            @if($sweeperCount == 0)
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted" style="font-style: italic;">
                                        No janitorial sweepers currently appointed inside the system directory.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div> <!-- CLOSES THE PRIMARY .staff-viewports-wrapper INNER HOLDER CONTAINER -->
</div> <!-- CLOSES THE ROOT COMPONENT CANVAS ELEMENT WRAPPER LAYER (.container-fluid) -->

<!-- 🌟 JAVASCRIPT TAB ACTIVE ROUTING LOGIC STORAGE ENGINE -->
<script>
    function openStaffTabPane(event, targetTabId) {
        // 1. Hide all tab viewports safely within the active content document
        const tabPanes = document.querySelectorAll('.staff-tab-content');
        tabPanes.forEach(pane => {
            pane.classList.remove('active');
        });

        // 2. Deactivate background highlight style layers across all tab navigation triggers
        const tabButtons = document.querySelectorAll('.staff-subnav-btn');
        tabButtons.forEach(btn => {
            btn.classList.remove('active');
        });

        // 3. Inject the active state classes to show the selected container card item layout
        document.getElementById(targetTabId).classList.add('active');
        event.currentTarget.classList.add('active');

        // 4. Save state selection token parameter flags to handle page state cache refreshes seamlessly
        localStorage.setItem('selected_staff_matrix_tab_pane', targetTabId);
    }

    // Run layout verification procedures upon initial document tree assembly execution load
    document.addEventListener("DOMContentLoaded", function() {
        // Retrieve localized tab parameters flag snapshot index from current local storage banks
        const lastCachedActiveTabId = localStorage.getItem('selected_staff_matrix_tab_pane');
        
        if (lastCachedActiveTabId && document.getElementById(lastCachedActiveTabId)) {
            // Find the matching trigger button link pointing to the cached ID routing target matrix
            const targetActiveBtn = document.querySelector(`.staff-subnav-btn[onclick*="${lastCachedActiveTabId}"]`);
            if (targetActiveBtn) {
                // Synthetically trigger an operational browser click event interaction
                targetActiveBtn.click();
                return;
            }
        }
        
        // Fallback default: Loads Tab 1 (All Staff) if history cache parameters don't exist
        const defaultTriggerBtn = document.querySelector('.staff-subnav-btn');
        if (defaultTriggerBtn) {
            defaultTriggerBtn.click();
        }
    });
</script>

@endsection
