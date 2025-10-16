<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Payroll Management') }}
        </h2>
    </x-slot>

    @php
        // ensure variables exist to avoid "undefined variable" errors
        $payrolls = $payrolls ?? collect();
        $employees = $employees ?? collect();
        $editing = isset($payroll);
        // form action and method depending on create vs edit
        $formAction = $editing ? route('payroll.update', $payroll->id) : route('payroll.store');
        $formMethod = $editing ? 'PUT' : 'POST';
        $selectedUser = old('user_id', $payroll->user_id ?? '');
        $grossValue = old('gross_pay', $payroll->gross_pay ?? '');
        $deductionsValue = old('deductions', $payroll->deductions ?? ($payroll->other_deductions ?? 0));
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Add / Edit Payroll Form (admin only) -->
            @if(Auth::check() && Auth::user()->is_admin)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">
                    {{ $editing ? 'Edit Payroll' : 'Add New Payroll' }}
                </h3>

                <form action="{{ $formAction }}" method="POST" class="space-y-4">
                    @csrf
                    @if($editing)
                        @method('PUT')
                    @endif

                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Employee</label>
                        <select name="user_id" id="user_id" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-gray-200" required>
                            <option value="">-- select employee --</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" @selected((string)$employee->id === (string)$selectedUser)>{{ $employee->first_name }} {{ $employee->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="gross_pay" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gross Pay</label>
                        <input type="number" step="0.01" name="gross_pay" id="gross_pay" value="{{ $grossValue }}" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-gray-200" required>
                    </div>

                    <div>
                        <label for="deductions" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deductions</label>
                        <input type="number" step="0.01" name="deductions" id="deductions" value="{{ $deductionsValue }}" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-gray-200">
                    </div>

                    <div class="flex justify-end items-center gap-3">
                        @if($editing)
                            <a href="{{ route('payroll.payroll') }}" class="inline-block text-sm px-3 py-2 border rounded text-gray-700 dark:text-gray-200">Cancel</a>
                        @endif

                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-md">
                            {{ $editing ? 'Update Payroll' : 'Add Payroll' }}
                        </button>
                    </div>
                </form>
            </div>
            @endif

            <!-- Payroll List -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">Payroll Records</h3>

                <div class="overflow-x-auto">
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
                            @forelse ($payrolls as $payrollRow)
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <td class="px-4 py-2">
                                        {{ optional($payrollRow->user)->first_name }} {{ optional($payrollRow->user)->last_name }}
                                    </td>

                                    @php
                                        // determine display currency (employee -> current user -> default)
                                        $displayCurrency = optional($payrollRow->user)->currency
                                            ?? optional(auth()->user())->currency
                                            ?? 'PHP';

                                        // simple symbol map (no conversion)
                                        $symbols = [
                                            'PHP' => '₱',
                                            'USD' => '$',
                                            'EUR' => '€',
                                            'GBP' => '£',
                                            'JPY' => '¥',
                                        ];
                                        $symbol = $symbols[strtoupper($displayCurrency)] ?? strtoupper($displayCurrency) . ' ';

                                        // compute numeric values and format
                                        $deductions = $payrollRow->deductions ?? (($payrollRow->tax_deduction ?? 0) + ($payrollRow->other_deductions ?? 0));
                                        $grossFormatted = number_format((float) ($payrollRow->gross_pay ?? 0), 2);
                                        $dedFormatted = number_format((float) ($deductions ?? 0), 2);
                                        $netFormatted = number_format((float) ($payrollRow->net_pay ?? 0), 2);
                                    @endphp

                                    <td class="px-4 py-2">{{ $symbol . $grossFormatted }}</td>
                                    <td class="px-4 py-2">{{ $symbol . $dedFormatted }}</td>
                                    <td class="px-4 py-2">{{ $symbol . $netFormatted }}</td>

                                    <td class="px-4 py-2">{{ $payrollRow->status }}</td>
                                    <td class="px-4 py-2 flex space-x-2">
                                        @if(Auth::check() && Auth::user()->is_admin)
                                            <a href="{{ route('payroll.edit', $payrollRow->id) }}" class="text-blue-600 hover:underline">Edit</a>

                                            <form action="{{ route('payroll.destroy', $payrollRow->id) }}" method="POST" onsubmit="return confirm('Delete this payroll?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                            </form>
                                        @endif
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

                <div class="mt-4">
                    @if(method_exists($payrolls, 'links'))
                        {{ $payrolls->links() }}
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>