<div class="space-y-2">
    <h3 class="text-base font-semibold mb-2">Recent Approved Leaves</h3>

    <table class="w-full text-sm text-left border border-gray-200 dark:bg-gray-900 dark:border-gray-700 rounded shadow">
        <thead class="bg-gray-100 dark:bg-gray-900 font-medium">
            <tr>
                <th class="px-4 py-2 font-semibold">Leave Type</th>
                <th class="px-4 py-2 font-semibold">Duration</th>
                <th class="px-4 py-2 font-semibold">Reason</th>
                <th class="px-4 py-2 font-semibold">Approval Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leaves as $leave)
                <tr class="border-t dark:border-gray-700">
                    <td class="px-4 py-2">{{ $leave->leave_type }}</td>
                    <td class="px-4 py-2">{{ $leave->duration }}</td>
                    <td class="px-4 py-2">{{ $leave->leave_reason }}</td>
                    <td class="px-4 py-2">{{ $leave->approved_at }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-2 text-center text-gray-500">No approved leaves found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
