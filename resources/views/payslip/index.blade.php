<x-app-layout>
    @php
        $payslips       = $payslips ?? collect();
        $fmt            = fn($v) => number_format((float)$v, 2);
        $currencySymbol = 'C$';
        $success        = session('success');
    @endphp

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <section>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <header class="mb-4">
                            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-gray-100">Payslips</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                View your issued payslips.
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
                                            $user  = $p->user ?? null;
                                            $name  = trim(($user->first_name ?? '').' '.($user->last_name ?? '')) ?: ($user->name ?? '—');
                                            $gross = (float)($p->total_earnings ?? 0);
                                            $tax   = (float)($p->tax_deduction ?? 0);
                                            $other = (float)($p->other_deductions ?? max(0, ($p->total_deductions ?? 0) - $tax));
                                            $ded   = (float)($p->total_deductions ?? ($tax + $other));
                                            $net   = (float)($p->net_pay ?? max(0, $gross - $ded));
                                            $issuedStr = $p->issued_at ? \Illuminate\Support\Carbon::parse($p->issued_at)->format('M d, Y') : '—';
                                        @endphp
                                        <tr class="bg-white dark:bg-gray-900">
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                                {{ $name }}
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
                                                {{ $issuedStr }}
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                <div class="flex justify-center gap-3">
                                                    <button type="button"
                                                            class="text-indigo-600 dark:text-indigo-400 hover:underline"
                                                            data-employee="{{ $name }}"
                                                            data-gross="{{ $gross }}"
                                                            data-tax="{{ $tax }}"
                                                            data-other="{{ $other }}"
                                                            data-deductions="{{ $ded }}"
                                                            data-net="{{ $net }}"
                                                            data-status="{{ $p->status ?? '—' }}"
                                                            data-issued="{{ $issuedStr }}">
                                                        View
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="bg-white dark:bg-gray-900">
                                            <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
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

    <!-- Modal (centered) -->
    <div id="payslipModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4" aria-hidden="true">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

        <!-- Dialog -->
        <div id="ps-dialog" class="relative z-10 w-full max-w-xl max-h-[85vh] overflow-y-auto rounded-lg bg-white dark:bg-gray-800 shadow-xl">
            <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 px-4 py-3">
                <h4 id="ps-employee" class="text-lg font-semibold text-gray-900 dark:text-gray-100">Employee</h4>
                <button id="ps-close" class="inline-flex items-center rounded-md px-2 py-1 text-gray-500 hover:text-gray-900 dark:hover:text-gray-100 focus:outline-none">✕</button>
            </div>

            <div class="px-4 py-4">
                <dl class="grid grid-cols-1 gap-3">
                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">Gross Earnings</dt>
                        <dd id="ps-gross" class="text-sm font-medium text-gray-900 dark:text-gray-100">—</dd>
                    </div>

                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">Tax Deduction</dt>
                        <dd id="ps-tax" class="text-sm font-medium text-gray-900 dark:text-gray-100">—</dd>
                    </div>

                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">Other Deductions</dt>
                        <dd id="ps-other" class="text-sm font-medium text-gray-900 dark:text-gray-100">—</dd>
                    </div>

                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">Total Deductions</dt>
                        <dd id="ps-deductions" class="text-sm font-semibold text-gray-900 dark:text-gray-100">—</dd>
                    </div>

                    <div class="mt-2 h-px bg-gray-200 dark:bg-gray-700"></div>

                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">Net Pay</dt>
                        <dd id="ps-net" class="text-base font-bold text-gray-900 dark:text-gray-100">—</dd>
                    </div>

                    <div class="mt-2 h-px bg-gray-200 dark:bg-gray-700"></div>

                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">Status</dt>
                        <dd id="ps-status" class="text-sm text-gray-900 dark:text-gray-100">—</dd>
                    </div>

                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">Issued At</dt>
                        <dd id="ps-issued" class="text-sm text-gray-900 dark:text-gray-100">—</dd>
                    </div>
                </dl>
            </div>

            <div class="flex justify-end gap-2 border-t border-gray-200 dark:border-gray-700 px-4 py-3">
                <button id="ps-close-footer" class="rounded-md bg-gray-100 px-4 py-2 text-sm text-gray-800 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600">Close</button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const currencySymbol = @json($currencySymbol);

            const $modal    = document.getElementById('payslipModal');
            const $backdrop = $modal?.firstElementChild;
            const $dialog   = document.getElementById('ps-dialog');
            const $close    = document.getElementById('ps-close');
            const $close2   = document.getElementById('ps-close-footer');

            const $employee = document.getElementById('ps-employee');
            const $gross    = document.getElementById('ps-gross');
            const $tax      = document.getElementById('ps-tax');
            const $other    = document.getElementById('ps-other');
            const $ded      = document.getElementById('ps-deductions');
            const $net      = document.getElementById('ps-net');
            const $status   = document.getElementById('ps-status');
            const $issued   = document.getElementById('ps-issued');

            function fmtMoney(n) {
                const num = Number(n ?? 0);
                return currencySymbol + (isFinite(num) ? num.toFixed(2) : '0.00');
            }

            function openModal(payload) {
                if (!$modal) return;
                $employee.textContent = payload.employee || '—';
                $gross.textContent    = fmtMoney(payload.gross);
                $tax.textContent      = fmtMoney(payload.tax);
                $other.textContent    = fmtMoney(payload.other);
                $ded.textContent      = fmtMoney(payload.deductions);
                $net.textContent      = fmtMoney(payload.net);
                $status.textContent   = payload.status || '—';
                $issued.textContent   = payload.issued || '—';

                $modal.classList.remove('hidden');
                document.documentElement.classList.add('overflow-hidden');
            }

            function closeModal() {
                if (!$modal) return;
                $modal.classList.add('hidden');
                document.documentElement.classList.remove('overflow-hidden');
            }

            document.addEventListener('click', (e) => {
                const btn = e.target.closest('button[data-employee]');
                if (btn) {
                    e.preventDefault();
                    openModal({
                        employee:   btn.dataset.employee,
                        gross:      btn.dataset.gross,
                        tax:        btn.dataset.tax,
                        other:      btn.dataset.other,
                        deductions: btn.dataset.deductions,
                        net:        btn.dataset.net,
                        status:     btn.dataset.status,
                        issued:     btn.dataset.issued,
                    });
                    return;
                }

                const clickedBackdrop = (e.target === $backdrop);
                const clickedOutsideDialog = ($modal && !$dialog.contains(e.target) && $modal.contains(e.target));
                if (clickedBackdrop || clickedOutsideDialog) {
                    closeModal();
                }
            });

            $close?.addEventListener('click', closeModal);
            $close2?.addEventListener('click', closeModal);
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeModal();
            });
        })();
    </script>
</x-app-layout>