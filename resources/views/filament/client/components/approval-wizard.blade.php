{{-- Save as resources/views/components/approval-flow.blade.php --}}
<style>
    .approval-container {
        overflow-x: auto;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }

    .approval-steps {
        display: flex;
        align-items: flex-start;
        gap: 1.5rem;
    }

    .approval-step {
        position: relative;
        flex: 0 0 180px;
        min-height: 110px;
        /* Increased min-height */
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        background-color: #f8fafc;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        box-sizing: border-box;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .approval-line {
        position: absolute;
        top: 50%;
        left: 100%;
        width: 1.5rem;
        height: 3px;
        background-color: #e2e8f0;
        transform: translateY(-50%);
        z-index: -1;
    }

    .step-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex-grow: 1;
    }

    .step-name {
        font-weight: 700;
        font-size: 0.9rem;
        color: #1e293b;
        line-height: 1.2;
        /* Adjust line-height for wrapping */
    }

    .step-status {
        margin-top: 0.5rem;
        font-size: 0.8rem;
        /* Larger status font */
        font-weight: 600;
        padding: 0;
        border-radius: 0;
        background-color: transparent !important;
        /* Remove background */
    }

    .step-date {
        font-size: 0.7rem;
        color: #64748b;
        margin-top: 0.25rem;
    }

    .dark .approval-step {
        background-color: #1f2937;
        border-color: #374151;
    }

    .dark .step-name {
        color: #f3f4f6;
    }

    .dark .approval-line {
        background-color: #374151;
    }

    .dark .step-date {
        color: #9ca3af;
    }

    /* Status Color Variants */
    .status-positive {
        border-color: #bbf7d0;
    }

    .status-positive .step-name {
        color: #15803d;
    }

    .status-positive .step-status {
        color: #22c55e;
    }

    .dark .status-positive {
        border-color: #22c55e;
    }

    .dark .status-positive .step-name {
        color: #dcfce7;
    }

    .dark .status-positive .step-status {
        color: #4ade80;
    }

    .status-active {
        border-color: #fde047;
    }

    .status-active .step-name {
        color: #a16207;
    }

    .status-active .step-status {
        color: #f59e0b;
    }

    .dark .status-active {
        border-color: #facc15;
    }

    .dark .status-active .step-name {
        color: #fef9c3;
    }

    .dark .status-active .step-status {
        color: #f59e0b;
    }

    .status-neutral {
        border-color: #e2e8f0;
    }

    .status-neutral .step-name {
        color: #475569;
    }

    .status-neutral .step-status {
        color: #64748b;
    }

    .dark .status-neutral {
        border-color: #374151;
    }

    .dark .status-neutral .step-name {
        color: #9ca3af;
    }

    .dark .status-neutral .step-status {
        color: #9ca3af;
    }

    .status-negative {
        border-color: #fecaca;
    }

    .status-negative .step-name {
        color: #b91c1c;
    }

    .status-negative .step-status {
        color: #ef4444;
    }

    .dark .status-negative {
        border-color: #ef4444;
    }

    .dark .status-negative .step-name {
        color: #fee2e2;
    }

    .dark .status-negative .step-status {
        color: #f87171;
    }
</style>

@php
    $hasLeave = isset($leave) && $leave?->id;
    $logsByLevel = $hasLeave ? $logs->keyBy('level') : collect();
    $finalStatus = $hasLeave ? strtolower($leave->status) : 'pending';
    $rejectionLog = $logsByLevel->firstWhere('status', 'rejected');
@endphp

<div class="approval-container">
    <div class="approval-steps">

        <!-- 1. Requestor Step -->
        @php
            $requestorStatusClass = $hasLeave ? 'status-positive' : 'status-active';
            $requestorStatusLabel = $hasLeave ? 'Submitted' : 'Draft';
            $requestorDate = $hasLeave ? $leave->created_at->format('M d, Y') : null;
        @endphp
        <div class="approval-step {{ $requestorStatusClass }}">
            <div class="step-content">
                <div class="step-name">{{ $leaveUser->name }}</div>
                <div class="step-status">{{ $requestorStatusLabel }}</div>
                @if ($requestorDate)
                    <div class="step-date">{{ $requestorDate }}</div>
                @endif
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
                $actionDate = $log && !in_array($status, ['pending']) ? $log->created_at->format('M d, Y') : null;

                $statusClass = 'status-neutral';
                $statusLabel = 'Pending';

                if ($rejectionLog) {
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
                    @if ($actionDate)
                        <div class="step-date">{{ $actionDate }}</div>
                    @endif
                </div>
                @if ($index < $hierarchySteps->count() - 1)
                    <div class="approval-line"></div>
                @endif
            </div>
        @endforeach

    </div>
</div>
