<x-app-layout>
    @php
        $p              = $payslip ?? null;
        $user           = $p?->user;
        $name           = trim(($user->first_name ?? '').' '.($user->last_name ?? '')) ?: ($user->name ?? '—');
        $gross          = (float)($p?->total_earnings ?? 0);
        $tax            = (float)($p?->tax_deduction ?? 0);
        $other          = (float)($p?->other_deductions ?? max(0, ($p?->total_deductions ?? 0) - $tax));
        $ded            = (float)($p?->total_deductions ?? ($tax + $other));
        $net            = (float)($p?->net_pay ?? max(0, $gross - $ded));
        $status         = $p?->status ?? '—';
        $issuedStr      = $p?->issued_at ? \Illuminate\Support\Carbon::parse($p->issued_at)->format('M d, Y') : '—';
        $periodFromStr  = $p?->period_from ? \Illuminate\Support\Carbon::parse($p->period_from)->format('M d, Y') : null;
        $periodToStr    = $p?->period_to ? \Illuminate\Support\Carbon::parse($p->period_to)->format('M d, Y') : null;
        $currencySymbol = 'C$';
        $fmt            = fn($v) => number_format((float)$v, 2);
    @endphp

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <!-- Aligns with navbar (logo to profile) like other pages -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <section>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <header class="mb-4 flex items-center justify-between">
                            <div>
                                <h3 class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-gray-100">Payslip Details</h3>
                                <p class="mt-1 text-base text-gray-600 dark:text-gray-400">
                                    {{ $name }}
                                    @if($periodFromStr || $periodToStr)
                                        • Period: {{ $periodFromStr ?? '—' }} — {{ $periodToStr ?? '—' }}
                                    @endif
                                </p>
                            </div>
                            <a href="{{ route('payslip.index') }}" class="text-base text-indigo-600 dark:text-indigo-400 hover:underline">Back</a>
                        </header>

                        <div class="space-y-4">
                            <dl class="grid grid-cols-1 gap-3">
                                <div class="flex items-center justify-between">
                                    <dt class="text-base text-gray-600 dark:text-gray-400">Gross Earnings</dt>
                                    <dd class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $currencySymbol.$fmt($gross) }}</dd>
                                </div>

                                <div class="flex items-center justify-between">
                                    <dt class="text-base text-gray-600 dark:text-gray-400">Tax Deduction</dt>
                                    <dd class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $currencySymbol.$fmt($tax) }}</dd>
                                </div>

                                <div class="flex items-center justify-between">
                                    <dt class="text-base text-gray-600 dark:text-gray-400">Other Deductions</dt>
                                    <dd class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $currencySymbol.$fmt($other) }}</dd>
                                </div>

                                <div class="flex items-center justify-between">
                                    <dt class="text-base text-gray-600 dark:text-gray-400">Total Deductions</dt>
                                    <dd class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $currencySymbol.$fmt($ded) }}</dd>
                                </div>

                                <div class="mt-2 h-px bg-gray-200 dark:bg-gray-700"></div>

                                <div class="flex items-center justify-between">
                                    <dt class="text-base text-gray-600 dark:text-gray-400">Net Pay</dt>
                                    <dd class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $currencySymbol.$fmt($net) }}</dd>
                                </div>

                                <div class="mt-2 h-px bg-gray-200 dark:bg-gray-700"></div>

                                <div class="flex items-center justify-between">
                                    <dt class="text-base text-gray-600 dark:text-gray-400">Status</dt>
                                    <dd class="text-base text-gray-900 dark:text-gray-100">{{ $status }}</dd>
                                </div>

                                <div class="flex items-center justify-between">
                                    <dt class="text-base text-gray-600 dark:text-gray-400">Issued At</dt>
                                    <dd class="text-base text-gray-900 dark:text-gray-100">{{ $issuedStr }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>