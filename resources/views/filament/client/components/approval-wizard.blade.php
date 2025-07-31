{{-- Save as resources/views/components/approval-flow.blade.php --}}
<style>
    /* General container styling */
    .approval-container {
        overflow-x: auto;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }

    .approval-steps {
        display: flex;
        align-items: flex-start;
        /* Align items to the top */
        gap: 1.5rem;
        /* Increased gap for better spacing */
    }

    /* Individual step styling */
    .approval-step {
        position: relative;
        flex: 0 0 180px;
        /* Wider steps for more content */
        min-height: 90px;
        /* Taller steps */
        border: 2px solid #e2e8f0;
        /* Lighter border */
        border-radius: 12px;
        /* Softer corners */
        background-color: #f8fafc;
        /* Very light background */
        display: flex;
        flex-direction: column;
        /* Stack content vertically */
        align-items: center;
        justify-content: center;
        padding: 1rem;
        box-sizing: border-box;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    /* Connecting line */
    .approval-line {
        position: absolute;
        top: 50%;
        left: 100%;
        width: 1.5rem;
        /* Matches the gap */
        height: 3px;
        background-color: #e2e8f0;
        transform: translateY(-50%);
        z-index: -1;
        /* Behind the steps */
    }

    /* Content inside each step */
    .step-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .step-name {
        font-weight: 700;
        /* Bolder name */
        font-size: 0.9rem;
        color: #1e293b;
    }

    .step-status {
        margin-top: 0.5rem;
        font-size: 0.75rem;
        font-weight: 500;
        padding: 0.2rem 0.6rem;
        border-radius: 9999px;
        /* Pill shape */
    }

    /* Dark mode adjustments */
    .dark .approval-step {
        background-color: #1f2937;
        border-color: #374151;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .dark .step-name {
        color: #f3f4f6;
    }

    .dark .approval-line {
        background-color: #374151;
    }

    /* --- Status Color Variants --- */

    /* Submitted / Approved / Forwarded (Positive) */
    .status-positive {
        background-color: #f0fdf4;
        /* Light green */
        border-color: #bbf7d0;
    }

    .status-positive .step-name {
        color: #15803d;
    }

    .status-positive .step-status {
        background-color: #22c55e;
        color: #ffffff;
    }

    .dark .status-positive {
        background-color: #166534;
        border-color: #22c55e;
    }

    .dark .status-positive .step-name {
        color: #dcfce7;
    }

    /* Current Pending Step (Active) */
    .status-active {
        background-color: #fefce8;
        /* Light yellow */
        border-color: #fde047;
    }

    .status-active .step-name {
        color: #a16207;
    }

    .status-active .step-status {
        background-color: #f59e0b;
        color: #ffffff;
    }

    .dark .status-active {
        background-color: #854d0e;
        border-color: #facc15;
    }

    .dark .status-active .step-name {
        color: #fef9c3;
    }

    /* Future Pending Step (Neutral) */
    .status-neutral {
        background-color: #f8fafc;
        border-color: #e2e8f0;
    }

    .status-neutral .step-name {
        color: #475569;
    }

    .status-neutral .step-status {
        background-color: #e2e8f0;
        color: #475569;
    }

    .dark .status-neutral {
        background-color: #1f2937;
        border-color: #374151;
    }

    .dark .status-neutral .step-name {
        color: #9ca3af;
    }

    .dark .status-neutral .step-status {
        background-color: #4b5563;
        color: #d1d5db;
    }

    /* Rejected (Negative) */
    .status-negative {
        background-color: #fef2f2;
        /* Light red */
        border-color: #fecaca;
    }

    .status-negative .step-name {
        color: #b91c1c;
    }

    .status-negative .step-status {
        background-color: #ef4444;
        color: #ffffff;
    }

    .dark .status-negative {
        background-color: #991b1b;
        border-color: #ef4444;
    }

    .dark .status-negative .step-name {
        color: #fee2e2;
    }
</style>

@php
    $hasLeave = isset($leave) && $leave?->id;
    $logsByLevel = $hasLeave ? $logs->keyBy('level') : collect();
    $finalStatus = $hasLeave ? strtolower($leave->status) : 'pending';
    $maxLevel = $hierarchySteps->max('level') ?? 0;
    $rejectionLog = $logsByLevel->firstWhere('status', 'rejected');
@endphp

<div class="approval-container">
    <div class="approval-steps">

        <!-- 1. Requestor Step -->
        @php
            $requestorStatusClass = $hasLeave ? 'status-positive' : 'status-active';
            $requestorStatusLabel = $hasLeave ? 'Submitted' : 'Draft';
        @endphp
        <div class="approval-step {{ $requestorStatusClass }}">
            <div class="step-content">
                <div class="step-name">{{ $leaveUser->name }}</div>
                <div class="step-status">{{ $requestorStatusLabel }}</div>
            </div>
            @if ($hierarchySteps->isNotEmpty())
                <div class="approval-line"></div>
            @endif
        </div>

        <!-- 2. Approval Hierarchy Steps -->
        @foreach ($hierarchySteps as $index => $step)
            @php
                $level = $step->level;
                $log = $logsByLevel->get($level);
                $status = $log ? strtolower($log->status) : 'pending';
                $roleName = \App\Models\Role::find($step->role_id)?->name ?? 'Role not found';

                $statusClass = 'status-neutral';
                $statusLabel = 'Pending';

                if ($rejectionLog) {
                    // If there is a rejection, color steps based on their status before rejection.
                    if ($level < $rejectionLog->level) {
                        $statusClass = 'status-positive';
                        $statusLabel = 'Forwarded';
                    } elseif ($level == $rejectionLog->level) {
                        $statusClass = 'status-negative';
                        $statusLabel = 'Rejected';
                    } else {
                        $statusClass = 'status-neutral';
                        $statusLabel = 'Not Reached';
                    }
                } elseif ($finalStatus === 'approved') {
                    $statusClass = 'status-positive';
                    $statusLabel = 'Approved';
                } else {
                    // Normal flow: submitted -> pending -> approved/forwarded
                    $currentPendingLevel = $logsByLevel->where('status', 'pending')->min('level');

                    if ($status === 'forwarded' || $status === 'approved') {
                        $statusClass = 'status-positive';
                        $statusLabel = ucfirst($status);
                    } elseif ($level == $currentPendingLevel) {
                        $statusClass = 'status-active';
                        $statusLabel = 'Pending Action';
                    } elseif ($level > $currentPendingLevel) {
                        $statusClass = 'status-neutral';
                        $statusLabel = 'Pending';
                    }
                }

            @endphp

            <div class="approval-step {{ $statusClass }}">
                <div class="step-content">
                    <div class="step-name">{{ $roleName }}</div>
                    <div class="step-status">{{ $statusLabel }}</div>
                </div>
                @if ($index < $hierarchySteps->count() - 1)
                    <div class="approval-line"></div>
                @endif
            </div>
        @endforeach

    </div>
</div>
