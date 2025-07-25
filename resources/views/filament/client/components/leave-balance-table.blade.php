<table class="w-full text-sm border rounded dark:bg-gray-900 dark:border-gray-700">
    <h3 class="text-base font-semibold mb-2">Total Leave Balance</h3>
    <thead class="bg-gray-100 dark:bg-gray-900 text-left">
        <tr>
            <th class="px-4 py-2 font-semibold">Leave Type</th>
            <th class="px-4 py-2 font-semibold">Total</th>
            <th class="px-4 py-2 font-semibold">Used</th>
            <th class="px-4 py-2 font-semibold">Remaining</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($balances as $balance)
            <tr class="border-t dark:border-gray-700">
                <td class="px-4 py-2">{{ $balance['name'] }}</td>
                <td class="px-4 py-2">{{ $balance['total'] }}</td>
                <td class="px-4 py-2">{{ $balance['used'] }}</td>
                <td class="px-4 py-2">{{ $balance['remaining'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
