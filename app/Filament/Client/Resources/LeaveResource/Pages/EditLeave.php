<?php

namespace App\Filament\Client\Resources\LeaveResource\Pages;

use App\Filament\Client\Resources\LeaveResource;
use App\Models\LeaveLog;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EditLeave extends EditRecord
{
    protected static string $resource = LeaveResource::class;

    protected static ?string $title = 'Edit Leave Request';

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $originalRecord = $this->getRecord();
        $changes = [];

        // Only log changes if the editor is NOT the person who requested the leave.
        if (Auth::id() !== $originalRecord->user_id) {
            $fieldsToTrack = [
                'type' => 'Duration',
                'leave_type' => 'Type',
                'paid' => 'Payment Status',
                'starting_date' => 'Starting Date',
                'ending_date' => 'Ending Date',
                'leave_reason' => 'Reason',
            ];

            foreach ($fieldsToTrack as $field => $label) {
                $oldValue = $originalRecord->{$field};
                $newValue = $data[$field] ?? null;

                // Properly compare different data types
                if ($field === 'paid') {
                    if ((bool)$oldValue !== (bool)$newValue) {
                        $changes[] = "{$label} from '" . ((bool)$oldValue ? 'Paid' : 'Unpaid') . "' to '" . ((bool)$newValue ? 'Paid' : 'Unpaid') . "'";
                    }
                } elseif (in_array($field, ['starting_date', 'ending_date'])) {
                    if ($oldValue && $newValue && !\Carbon\Carbon::parse($oldValue)->isSameDay(\Carbon\Carbon::parse($newValue))) {
                        $changes[] = "{$label} from '{$oldValue}' to '{$newValue}'";
                    }
                } elseif ($oldValue != $newValue) {
                    $changes[] = "{$label} from '{$oldValue}' to '{$newValue}'";
                }
            }

            // Log if a new document was uploaded
            if (isset($data['document']) && $data['document'] !== $originalRecord->document) {
                $changes[] = "attached document was updated";
            }

            if (!empty($changes)) {
                $editor = Auth::user();
                $editorRole = $editor->roles->first();

                LeaveLog::create([
                    'leave_id' => $originalRecord->id,
                    'role_id'  => $editorRole ? $editorRole->id : $editor->id,
                    'level'    => $originalRecord->leaveLogs()->max('level') ?? 1, // Log at the current stage
                    'status'   => $originalRecord->status,
                    'remarks'  => "Updated by {$editor->name}: Changed " . implode(', ', $changes) . ".",
                ]);
            }
        }

        // For half-day and short leave, ensure ending_date equals starting_date
        if (isset($data['type']) && in_array($data['type'], ['half_day', 'short_leave'])) {
            $data['ending_date'] = $data['starting_date'];
        }

        // For regular leave, get the user's shift times
        if (isset($data['type']) && $data['type'] === 'regular') {
            $user = Filament::getTenant()->users()->where('id', $this->record->user_id)->with(['assignedShift.shift', 'assignedDepartment.department'])->first();

            if ($user && $user->assignedShift?->shift) {
                $shift = $user->assignedShift->shift;
                $data['starting_time'] = $shift->starting_time;
                $data['ending_time'] = $shift->ending_time;
            }
        }

        // For half day leave, get the user's shift times
        if (isset($data['type']) && $data['type'] === 'half_day') {
            $user = Filament::getTenant()->users()->where('id', $this->record->user_id)->with(['assignedShift.shift', 'assignedDepartment.department'])->first();

            if ($user && $user->assignedShift?->shift) {
                $shift = $user->assignedShift->shift;
                $data['starting_time'] = $shift->half_day_check_in;
                $data['ending_time'] = $shift->half_day_check_out;
            }
        }

        $leave = $this->record;

        $approvalStatus = $data['approval_status'] ?? 'pending';

        if ($approvalStatus === 'cancelled') {
            $leave->update([
                'status' => 'cancelled',
            ]);
        } elseif ($approvalStatus === 'rejected_cancellation') {
            $leave->update([
                'status' => 'approved',
            ]);
        }
        return $data;
    }

    protected function afterSave(): void
    {
        $approvalStatus = $this->data['approval_status'] ?? 'pending';
        $originalStatus = $this->record->getOriginal('status');

        // Only log if the approval status has changed or if other fields were modified
        if ($approvalStatus !== 'pending' && $approvalStatus !== $originalStatus) {
            $currentUser = Auth::user();
            $currentRole = $currentUser->roles->first();
            $roleId = $currentRole ? $currentRole->id : $currentUser->id;

            $maxRecordedLevel = $this->record->leaveLogs()->max('level') ?? 0;
            $currentApprovalLevel = $maxRecordedLevel + 1;

            // Log the current action
            \App\Models\LeaveLog::create([
                'leave_id' => $this->record->id,
                'role_id'  => $roleId,
                'level'    => $currentApprovalLevel,
                'status'   => $approvalStatus,
            ]);

            if ($this->record->status === 'pending_cancellation') {
                // Handle cancellation approvals
                if ($approvalStatus === 'cancelled') {
                    $this->record->update(['status' => 'cancelled']);
                } elseif ($approvalStatus === 'rejected_cancellation') {
                    $this->record->update(['status' => 'approved']);
                }
            } else {
                // Normal approval workflow
                $leaveUserId = $this->record->user_id;

                // Find the next approver for next level
                $nextStep = DB::table('approval_steps')
                    ->where('team_id', Filament::getTenant()->id)
                    ->where('user_id', $leaveUserId)
                    ->where('level', $currentApprovalLevel + 1)
                    ->first();

                if ($approvalStatus === 'approved') {
                    if ($nextStep) {
                        // Forward to next approver
                        $this->record->update(['status' => 'forwarded']);

                        \App\Models\LeaveLog::create([
                            'leave_id' => $this->record->id,
                            'role_id'  => $nextStep->role_id, // ✅ Proper next approver
                            'level'    => $currentApprovalLevel + 1,
                            'status'   => 'pending',
                        ]);
                    } else {
                        // No next approver, final approval
                        $this->record->update(['status' => 'approved']);
                    }
                } elseif ($approvalStatus === 'rejected') {
                    $this->record->update([
                        'status' => 'rejected',
                        'rejection_reason' => $this->data['rejection_reason'],
                    ]);
                }
            }
        }
    }
    protected function beforeSave(): void
    {
        $leaveUserId = $this->data['user_id'];
        $url = LeaveResource::getUrl('edit', ['record' => $this->data['id']]);
        $approvalStatus = $this->data['approval_status'] ?? 'pending';
        if ($approvalStatus !== 'pending') {
            if ($this->record->status === 'pending_cancellation') {
                if ($approvalStatus === 'cancelled') {
                    $message = 'Leave Request cancellation Approved';
                } elseif ($approvalStatus === 'rejected_cancellation') {
                    $message = 'Leave Request cancellation Rejected';
                }
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
