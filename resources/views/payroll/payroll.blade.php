<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Add Payroll Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">Add New Payroll</h3>

                <form action="{{ route('payroll.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Employee</label>
                        <select name="user_id" id="user_id" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-gray-200">
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="gross_pay" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gross Pay</label>
                        <input type="number" step="0.01" name="gross_pay" id="gross_pay" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-gray-200" required>
                    </div>

                    <div>
                        <label for="deductions" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deductions</label>
                        <input type="number" step="0.01" name="deductions" id="deductions" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-gray-200">
                    </div>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-md">
                        Add Payroll
                    </button>
                </form>
            </div>

            <!-- Payroll List -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">Payroll Records</h3>

                <table class="min-w-full text-sm text-left text-gray-700 dark:text-gray-300">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2">Employee</th>
                            <th class="px-4 py-2">Gross Pay</th>
                            <th class="px-4 py-2">Deductions</th>
                            <th class="px-4 py-2">Net Pay</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payrolls as $payroll)
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <td class="px-4 py-2">{{ $payroll->user->first_name }} {{ $payroll->user->last_name }}</td>
                                <td class="px-4 py-2">₱{{ number_format($payroll->gross_pay, 2) }}</td>
                                <td class="px-4 py-2">₱{{ number_format($payroll->deductions, 2) }}</td>
                                <td class="px-4 py-2">₱{{ number_format($payroll->net_pay, 2) }}</td>
                                <td class="px-4 py-2">{{ $payroll->status }}</td>
                                <td class="px-4 py-2 flex space-x-2">
                                    <a href="{{ route('payroll.edit', $payroll->id) }}" class="text-blue-600 hover:underline">Edit</a>
                                    <form action="{{ route('payroll.destroy', $payroll->id) }}" method="POST" onsubmit="return confirm('Delete this payroll?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-gray-500 dark:text-gray-400">No payroll records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>