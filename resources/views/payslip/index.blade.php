<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Payslip') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto px-6 py-8">
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Company Name</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Address · Contact</p>
                </div>

                <div class="text-right">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Payslip #{{ $payslip->id }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Issued: {{ optional($payslip->issued_at)->format('M d, Y') ?? '—' }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Status: {{ $payslip->status }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Employee</h4>
                    <p class="text-md font-medium text-gray-900 dark:text-gray-100">{{ $payslip->user->name ?? ($payslip->user->first_name . ' ' . $payslip->user->last_name ?? 'N/A') }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $payslip->user->email ?? '' }}</p>
                </div>

                <div>
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Payroll Summary</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Payroll ID: {{ $payslip->payroll_id ?? '—' }}</p>
                </div>
            </div>

            @php
                $payroll = $payslip->payroll ?? null;
                $gross = $payslip->total_earnings ?? ($payroll->gross_pay ?? 0);
                $taxDed = $payroll->tax_deduction ?? ($payslip->tax_deduction ?? 0);
                $otherDed = $payroll->other_deductions ?? ($payslip->total_deductions - $taxDed ?? 0);
                $totalDed = $payslip->total_deductions ?? ($taxDed + $otherDed);
                $net = $payslip->net_pay ?? ($gross - $totalDed);
            @endphp

            <div class="overflow-x-auto mb-6">
                <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2">Description</th>
                            <th class="px-4 py-2 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t">
                            <td class="px-4 py-3">Gross Pay</td>
                            <td class="px-4 py-3 text-right">₱{{ number_format($gross, 2) }}</td>
                        </tr>

                        <tr>
                            <td class="px-4 py-3 font-medium">Deductions</td>
                            <td class="px-4 py-3 text-right">₱{{ number_format($totalDed, 2) }}</td>
                        </tr>

                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <td class="px-4 py-3"> - Tax</td>
                            <td class="px-4 py-3 text-right">₱{{ number_format($taxDed, 2) }}</td>
                        </tr>

                        <tr>
                            <td class="px-4 py-3"> - Other Deductions</td>
                            <td class="px-4 py-3 text-right">₱{{ number_format($otherDed, 2) }}</td>
                        </tr>

                        <tr class="border-t font-semibold">
                            <td class="px-4 py-3">Net Pay</td>
                            <td class="px-4 py-3 text-right text-green-600">₱{{ number_format($net, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <p>Prepared by: {{ auth()->user()->name ?? 'Admin' }}</p>
                </div>

                <div class="space-x-3">
                    <a href="{{ route('payslip.index') }}" class="inline-block px-4 py-2 border rounded text-sm">Back</a>
                    <button onclick="window.print()" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded text-sm">Print</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>