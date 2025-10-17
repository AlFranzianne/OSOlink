<x-app-layout>
    @php
        $payslips       = $payslips ?? collect();
        $fmt            = fn($v)=>number_format((float)$v, 2);
        $currencySymbol = 'C$';
        $success        = session('success');
    @endphp

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Payslips -->
            <section>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <header class="mb-4">
                            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-gray-100">Payslips</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                View your issued payslips. Admins can see all employees’ payslips.
                            </p>
                            @if($success)
                                <p class="mt-3 text-sm text-green-600 dark:text-green-400">{{ $success }}</p>
                            @endif
                        </header>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700/50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-200">Employee</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-200">Period</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-200">Gross</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-200">Deductions</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-200">Net</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-200">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-200">Issued</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-200">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                    @forelse ($payslips as $p)
                                        @php
                                            $user = $p->user;
                                            $name = trim(($user->first_name ?? '').' '.($user->last_name ?? '')) ?: ($user->name ?? '—');
                                            $gross = (float)($p->total_earnings ?? 0);
                                            $ded   = (float)($p->total_deductions ?? 0);
                                            $net   = (float)($p->net_pay ?? max(0, $gross - $ded));
                                        @endphp
                                        <tr class="bg-white dark:bg-gray-900">
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                                {{ $name }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                                {{ optional($p->period_from)->format('M d, Y') ?? '—' }} — {{ optional($p->period_to)->format('M d, Y') ?? '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">
                                                {{ $currencySymbol.$fmt($gross) }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">
                                                {{ $currencySymbol.$fmt($ded) }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">
                                                {{ $currencySymbol.$fmt($net) }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                                {{ $p->status ?? '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                                {{ optional($p->issued_at)->format('M d, Y') ?? '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                <div class="flex justify-center gap-3">
                                                    <a href="{{ route('payslip.show', $p) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">View</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="bg-white dark:bg-gray-900">
                                            <td colspan="8" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                                No payslips found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if(method_exists($payslips, 'links'))
                            <div class="px-4 py-3">
                                {{ $payslips->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>