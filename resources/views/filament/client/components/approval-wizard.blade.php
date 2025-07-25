{{-- Save as resources/views/components/approval-flow.blade.php --}}
<style>
    .approval-container {
        overflow-x: auto;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }

    .approval-steps {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .approval-step {
        position: relative;
        flex: 0 0 160px;
        /* exactly 160px wide */
        height: 80px;
        /* or min-height if you prefer */
        border: 2px solid #cbd5e0;
        border-radius: 8px;
        background-color: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem;
        box-sizing: border-box;
        transition: background-color 0.2s, border-color 0.2s;
    }

    .approval-line {
        position: absolute;
        top: 50%;
        left: 100%;
        width: 1.2rem;
        height: 3px;
        background-color: #cbd5e0;
        transform: translateY(-50%);
        z-index: 1;
    }

    .step-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        /* ensure wrapping: */
        overflow-wrap: break-word;
        word-break: break-word;
        hyphens: auto;
    }

    .step-name {
        font-weight: 600;
        font-size: 0.8rem;
    }

    .step-status {
        margin-top: 0.25rem;
        font-size: 0.7rem;
        color: #4a5568;
    }

    .dark .step-status {
        color: whitesmoke;
    }

    /* Status color variants */
    .status-approved,
    .status-forwarded,
    .status-submitted {
        background-color: #dcfce7;
        border-color: #15803d33;
    }

    .dark .status-approved,
    .dark .status-forwarded,
    .dark .status-submitted {
        background-color: #288d4f;
        border-color: #5eea85;
    }

    .dark .status-pending {
        background-color: gray;
        border-color: whitesmoke;
    }

    .status-next-pending {
        background-color: #fef9c3;
        border-color: #fde047;
    }

    .dark .status-next-pending {
        background-color: #cd7621;
        border-color: #facc15;
    }

    .status-rejected {
        background-color: #fee2e2;
        border-color: #fca5a5;
    }

    .dark .status-rejected {
        background-color: #7f1d1d;
        border-color: #f87171;
    }
</style>


@php
    $hasLeave = isset($leave) && $leave?->id;
    // cancellation logs only if we have a leave
    $cancellationLog = $hasLeave
        ? $logs
            ->filter(fn($log) => in_array($log->status, ['pending_cancellation', 'cancelled', 'rejected_cancellation']))
            ->sortBy('level')
            ->first()
        : null;
    $finalCancelLog =
        $hasLeave && $cancellationLog
            ? $logs
                ->whereIn('status', ['cancelled', 'rejected_cancellation'])
                ->sortByDesc('level')
                ->first()
            : null;

    // Track if we need to hide status on subsequent steps
    $hideNextStatus = false;
@endphp

<div class="approval-container">
    <div class="approval-steps">

        <!-- Requestor -->
        @php
            $requestorStatusClass = $hasLeave ? 'status-submitted' : 'status-submitted';
            $requestorStatusLabel = $hasLeave ? 'Submitted' : 'Pending';
        @endphp

        <div class="approval-step {{ $requestorStatusClass }}">
            <div class="step-content">
                <div class="step-name">{{ $leave?->user->name ?? auth()->user()->name }}</div>
                <div class="step-status">{{ $requestorStatusLabel }}</div>
            </div>
            <div class="approval-line"></div>
        </div>


        <!-- Dynamic approval roles -->
        @php $nextPendingFound = false; @endphp
        @foreach ($hierarchySteps as $index => $step)
            @php
                $lvl = $step->level + 1;
                $rawStatus = strtolower($logs[$lvl]->status ?? '');
                $isPending = $rawStatus === 'pending' || !$rawStatus;

                $statusClass = 'status-pending';
                $statusLabel = '';

                if (!$hideNextStatus) {
                    switch ($rawStatus) {
                        case 'approved':
                            $statusClass = 'status-approved';
                            $statusLabel = 'Approved';
                            $hideNextStatus = true;
                            break;
                        case 'rejected':
                            $statusClass = 'status-rejected';
                            $statusLabel = 'Rejected';
                            $hideNextStatus = true;
                            break;
                        case 'forwarded':
                            $statusClass = 'status-forwarded';
                            $statusLabel = 'Forwarded';
                            break;
                        default:
                            if ($isPending && !$nextPendingFound) {
                                $statusClass = 'status-next-pending';
                                $statusLabel = 'Pending';
                                $nextPendingFound = true;
                            } elseif ($rawStatus) {
                                $statusLabel = ucfirst($rawStatus);
                            }
                            break;
                    }
                }

                $roleName = \App\Models\Role::find($step->role_id)?->name ?? 'Unknown Role';
            @endphp

            <div class="approval-step {{ $statusClass }}">
                <div class="step-content">
                    <div class="step-name">{{ $roleName }}</div>
                    @if ($statusLabel)
                        <div class="step-status">{{ $statusLabel }}</div>
                    @endif
                </div>
                @if ($index < count($hierarchySteps) - 1 || $cancellationLog)
                    <div class="approval-line"></div>
                @endif
            </div>
        @endforeach



        <!-- Cancellation flow (if any) -->
        @if ($cancellationLog)
            <!-- Cancel request by user -->
            <div class="approval-step status-submitted">
                <div class="step-content">
                    <div class="step-name">{{ $leave->user->name }}</div>
                    <div class="step-status">Cancellation Requested</div>
                </div>
                <div class="approval-line"></div>
            </div>

            <!-- Final cancellation decision -->
            @if ($finalCancelLog)
                @php
                    $actorRole = \App\Models\Role::find($finalCancelLog->role_id)?->name;
                    $wasCancel = $finalCancelLog->status === 'cancelled';
                    $finalClass = $wasCancel ? 'status-approved' : 'status-rejected';
                    $finalLabel = $wasCancel ? 'Cancelled' : 'Cancellation Rejected';
                @endphp

                <div class="approval-step {{ $finalClass }}">
                    <div class="step-content">
                        <div class="step-name">{{ $actorRole }}</div>
                        <div class="step-status">{{ $finalLabel }}</div>
                    </div>
                </div>
            @endif
        @endif

    </div>
</div>
