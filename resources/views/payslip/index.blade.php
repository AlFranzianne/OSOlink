<x-app-layout>
    @php
        $payslips       = $payslips ?? collect();
        $fmt            = fn($v) => number_format((float)$v, 2);
        $currencySymbol = 'C$';
        $success        = session('success');
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <header class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Manage Payslips') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ __("View your issued payslips.") }}
                        </p>
                    </div>
                    @if($success)
                        <p class="mt-3 text-base text-green-600 dark:text-green-400">{{ $success }}</p>
                    @endif
                </header>
                        
                <div class="mt-6 overflow-x-auto">
                    <table class="rounded-lg overflow-hidden w-full divide-y divide-gray-300 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Employee</th>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Gross</th>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Deductions</th>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Net</th>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Status</th>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Issued</th>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-gray-200 dark:bg-gray-900 text-sm">
                            @forelse ($payslips as $p)
                                @php
                                    $user  = $p->user ?? null;
                                    $name  = trim(($user->first_name ?? '').' '.($user->last_name ?? '')) ?: ($user->name ?? '—');
                                    $gross = (float)($p->total_earnings ?? 0);
                                    $tax   = (float)($p->tax_deduction ?? 0);
                                    $other = (float)($p->other_deductions ?? max(0, ($p->total_deductions ?? 0) - $tax));
                                    $ded   = (float)($p->total_deductions ?? ($tax + $other));
                                    $net   = (float)($p->net_pay ?? max(0, $gross - $ded));
                                    $issuedStr = $p->issued_at ? \Illuminate\Support\Carbon::parse($p->issued_at)->format('M d, Y') : '—';
                                @endphp
                                <tr>
                                    <td class="px-4 py-2 text-center font-medium text-gray-700 dark:text-gray-200">
                                        {{ $name }}
                                    </td>
                                    <td class="px-4 py-2 text-center font-medium text-gray-700 dark:text-gray-200">
                                        {{ $currencySymbol.$fmt($gross) }}
                                    </td>
                                    <td class="px-4 py-2 text-center font-medium text-gray-700 dark:text-gray-200">
                                        {{ $currencySymbol.$fmt($ded) }}
                                    </td>
                                    <td class="px-4 py-2 text-center font-medium text-gray-700 dark:text-gray-200">
                                        {{ $currencySymbol.$fmt($net) }}
                                    </td>
                                    <td class="px-4 py-2 text-center font-medium text-gray-700 dark:text-gray-200">
                                        {{ $p->status ?? '—' }}
                                    </td>
                                    <td class="px-4 py-2 text-center font-medium text-gray-700 dark:text-gray-200">
                                        {{ $issuedStr }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <div class="flex justify-center">
                                            <a href="{{ route('payslip.show', $p->id) }}"
                                                class="text-blue-600 dark:text-blue-400 hover:underline">
                                                View
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr class="bg-white dark:bg-gray-900">
                                    <td colspan="7" class="px-6 py-6 text-center text-base text-gray-500 dark:text-gray-400">
                                        No payslips found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($payslips, 'links'))
                    <div>
                        {{ $payslips->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>