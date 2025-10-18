<x-app-layout>
    @php
        $employees      = $employees      ?? collect();
        $baseByStatus   = $baseByStatus   ?? [];
        $payroll        = $payroll        ?? null;
        $editing        = isset($payroll);
        $formAction     = $editing ? route('payroll.update', $payroll->id) : route('payroll.store');
        $selectedUser   = old('user_id', $payroll->user_id ?? '');
        $success        = session('success');

        $toYmd = function ($v) {
            if (!$v) return '';
            if ($v instanceof \DateTimeInterface) return $v->format('Y-m-d');
            try { return \Illuminate\Support\Carbon::parse($v)->toDateString(); } catch (\Throwable $e) { return ''; }
        };

        $payslip = $payroll?->payslip;
        $periodFromValue = old('period_from', $toYmd($payroll?->period_from ?? $payslip?->period_from ?? null));
        $periodToValue   = old('period_to',   $toYmd($payroll?->period_to   ?? $payslip?->period_to   ?? null));
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <header class="flex items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ $editing ? 'Edit Payroll' : 'Add Payroll' }}
                    </h2>
                    @if($success)
                        <p class="mt-3 text-sm text-green-600 dark:text-green-400">{{ $success }}</p>
                    @endif
                </header>

                <form action="{{ $formAction }}" method="POST" class="space-y-6">
                    @csrf
                    @if($editing) @method('PUT') @endif

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Employee -->
                        <div class="md:col-span-2">
                            <x-input-label for="user_id" :value="__('Employee')" />
                            <select id="user_id" name="user_id" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- select employee --</option>
                                @foreach ($employees as $e)
                                    <option value="{{ $e->id }}"
                                        data-status="{{ $e->employment_status ?? '' }}"
                                        data-hourly="{{ $e->hourly_rate ?? '' }}"
                                        @selected((string)$e->id === (string)$selectedUser)
                                    >
                                        {{ $e->first_name }} {{ $e->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('user_id')" />
                        </div>

                        <!-- Period From -->
                        <div>
                            <x-input-label for="period_from" :value="__('Period From')" />
                            <x-text-input id="period_from" name="period_from" type="date"
                                            class="mt-1 block w-full"
                                            :value="$periodFromValue" />
                            <x-input-error class="mt-2" :messages="$errors->get('period_from')" />
                        </div>

                        <!-- Period To -->
                        <div>
                            <x-input-label for="period_to" :value="__('Period To')" />
                            <x-text-input id="period_to" name="period_to" type="date"
                                            class="mt-1 block w-full"
                                            :value="$periodToValue" />
                            <x-input-error class="mt-2" :messages="$errors->get('period_to')" />
                        </div>

                        <!-- Employment Status -->
                        <div>
                            <x-input-label for="employment_status" :value="__('Employment Status')" />
                            <select id="employment_status" name="employment_status"
                                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select Employment Status</option>
                                <option value="Full-time"   @selected(old('employment_status', $payroll->employment_status ?? '') == 'Full-time')>Full-time</option>
                                <option value="Part-time"   @selected(old('employment_status', $payroll->employment_status ?? '') == 'Part-time')>Part-time</option>
                                <option value="Contractual" @selected(old('employment_status', $payroll->employment_status ?? '') == 'Contractual')>Contractual</option>
                                <option value="Intern"      @selected(old('employment_status', $payroll->employment_status ?? '') == 'Intern')>Intern</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('employment_status')" />
                        </div>

                        <!-- Hourly Rate -->
                        <div>
                            <x-input-label for="hourly_rate" :value="__('Hourly Rate')" />
                            <x-text-input id="hourly_rate" name="hourly_rate" type="number" step="0.01" min="0"
                                            class="mt-1 block w-full"
                                            :value="old('hourly_rate', $payroll->hourly_rate ?? '')" />
                            <x-input-error class="mt-2" :messages="$errors->get('hourly_rate')" />
                        </div>

                        <!-- Base Pay -->
                        <div>
                            <x-input-label for="base_pay" :value="__('Base Pay')" />
                            <x-text-input id="base_pay" name="base_pay" type="number" step="0.01" min="0"
                                            class="mt-1 block w-full"
                                            :value="old('base_pay', $payroll->base_pay ?? '')" />
                            <x-input-error class="mt-2" :messages="$errors->get('base_pay')" />
                        </div>

                        <!-- Hours Worked -->
                        <div>
                            <x-input-label for="hours_worked" :value="__('Hours Worked')" />
                            <x-text-input id="hours_worked" name="hours_worked" type="number" step="0.01" min="0" readonly
                                            class="mt-1 block w-full bg-gray-100 dark:bg-gray-700"
                                            :value="old('hours_worked', $payroll->hours_worked ?? '')" />
                            <x-input-error class="mt-2" :messages="$errors->get('hours_worked')" />
                        </div>

                        <!-- Gross Pay -->
                        <div>
                            <x-input-label for="gross_pay" :value="__('Gross Pay')" />
                            <x-text-input id="gross_pay" name="gross_pay" type="number" step="0.01" min="0"
                                            class="mt-1 block w-full"
                                            :value="old('gross_pay', $payroll->gross_pay ?? '')" />
                            <x-input-error class="mt-2" :messages="$errors->get('gross_pay')" />
                        </div>

                        <!-- Deductions -->
                        <div class="md:col-span-2">
                            <x-input-label for="deductions" :value="__('Deductions')" />
                            <x-text-input id="deductions" name="deductions" type="number" step="0.01" min="0"
                                            class="mt-1 block w-full"
                                            :value="old('deductions', $payroll->deductions ?? 0)" />
                            <x-input-error class="mt-2" :messages="$errors->get('deductions')" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        @if($editing)
                            <a href="{{ url()->previous() }}" class="text-sm text-gray-600 dark:text-gray-300 hover:underline">Cancel</a>
                        @endif

                        <x-primary-button>
                            {{ $editing ? 'Update Payroll' : 'Add Payroll' }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const userSelect   = document.getElementById('user_id');
            const statusEl     = document.getElementById('employment_status');
            const hourlyEl     = document.getElementById('hourly_rate');
            const baseEl       = document.getElementById('base_pay');
            const hoursEl      = document.getElementById('hours_worked');
            const grossEl      = document.getElementById('gross_pay');
            const periodFromEl = document.getElementById('period_from');
            const periodToEl   = document.getElementById('period_to');
            const isEditing    = @json($editing);
            const baseByStatus = @json($baseByStatus);
            const hoursUrl     = @json(route('payroll.hours'));

            function toNum(el) { const v = parseFloat(el?.value ?? ''); return isFinite(v) ? v : 0; }

            function applyBaseFromStatus(status) {
                if (!baseEl) return;
                if (status && baseByStatus && Object.prototype.hasOwnProperty.call(baseByStatus, status)) {
                    baseEl.value = baseByStatus[status];
                }
            }

            function applyFromSelectedEmployee() {
                const opt = userSelect?.options[userSelect.selectedIndex];
                if (!opt) return;
                const status = opt.dataset.status || '';
                const hourly = opt.dataset.hourly || '';
                if (status && statusEl) statusEl.value = status;
                if (hourly !== '' && hourlyEl) hourlyEl.value = parseFloat(hourly);
                applyBaseFromStatus(status);
                calcGross();
            }

            function calcGross() {
                if (!grossEl) return;
                const base   = toNum(baseEl);
                const hourly = toNum(hourlyEl);
                const hours  = toNum(hoursEl);
                const gross  = base + (hourly * hours);
                if (!grossEl.dataset.touched || !isEditing) {
                    grossEl.value = (isFinite(gross) ? gross : 0).toFixed(2);
                }
            }

            async function fetchHours() {
                try {
                    const uid  = userSelect?.value;
                    const from = periodFromEl?.value;
                    const to   = periodToEl?.value;
                    if (!uid || !from || !to) return;

                    const url = new URL(hoursUrl, window.location.origin);
                    url.searchParams.set('user_id', uid);
                    url.searchParams.set('from', from);
                    url.searchParams.set('to', to);

                    const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) throw new Error('Failed to fetch hours');
                    const data = await res.json();
                    const total = Number(data?.hours ?? 0);
                    hoursEl.value = (isFinite(total) ? total : 0).toFixed(2);
                    calcGross();
                } catch (e) {
                    console.error(e);
                }
            }

            grossEl?.addEventListener('input', () => { grossEl.dataset.touched = '1'; });

            userSelect?.addEventListener('change', () => { applyFromSelectedEmployee(); fetchHours(); });
            statusEl?.addEventListener('change', () => { applyBaseFromStatus(statusEl.value || ''); calcGross(); });
            hourlyEl?.addEventListener('input', calcGross);
            baseEl?.addEventListener('input', calcGross);
            periodFromEl?.addEventListener('change', fetchHours);
            periodToEl?.addEventListener('change', fetchHours);

            const fieldsEmpty = (!statusEl?.value) && (!hourlyEl?.value) && (!baseEl?.value);
            if ((!isEditing || fieldsEmpty) && userSelect?.value) applyFromSelectedEmployee();
            calcGross();

            if (userSelect?.value && periodFromEl?.value && periodToEl?.value) {
                fetchHours();
            }
        });
    </script>
</x-app-layout>