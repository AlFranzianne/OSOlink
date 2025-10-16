<x-app-layout>
    @php
        $employees      = $employees      ?? collect();
        $payrolls       = $payrolls       ?? collect();
        $baseByStatus   = $baseByStatus   ?? [];
        $payroll        = $payroll        ?? null;
        $editing        = isset($payroll);
        $formAction     = $editing ? route('payroll.update', $payroll->id) : route('payroll.store');
        $selectedUser   = old('user_id', $payroll->user_id ?? '');
        $success        = session('success');
        $fmt            = fn($v)=>number_format((float)$v, 2);

        // Force CAD for display
        $currencySymbol = 'C$';
    @endphp

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Add Payroll -->
            <section>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <header class="mb-6">
                            <h3 class="text-lg sm:text-xl font-semibold">
                                {{ $editing ? 'Edit Payroll' : 'Add Payroll' }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Select an employee and enter payroll details.
                            </p>
                        </header>

                        <form action="{{ $formAction }}" method="POST" class="space-y-6">
                            @csrf
                            @if($editing) @method('PUT') @endif

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <!-- Employee -->
                                <div>
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
                                    <x-text-input id="hours_worked" name="hours_worked" type="number" step="0.01" min="0"
                                                  class="mt-1 block w-full"
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
                                    <a href="{{ route('payroll.index') }}" class="text-sm text-gray-600 dark:text-gray-300 hover:underline">Cancel</a>
                                @endif

                                <x-primary-button>
                                    {{ $editing ? 'Update Payroll' : 'Add Payroll' }}
                                </x-primary-button>

                                @if($success)
                                    <p class="text-sm text-green-600 dark:text-green-400">{{ $success }}</p>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <!-- Payroll Records -->
            <section>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <header class="mb-4">
                            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-gray-100">Payroll Records</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Review, edit, or delete payroll entries.</p>
                        </header>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700/50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-200">Employee</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-200">Type</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-200">Base</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-200">Hourly</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-200">Hours</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-200">Gross</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-200">Deductions</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-200">Net</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-200">Status</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-200">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                    @forelse ($payrolls as $row)
                                        @php
                                            $gross = (float)($row->gross_pay ?? 0);
                                            $ded   = (float)($row->deductions ?? 0);
                                            $net   = (float)($row->net_pay ?? max(0, $gross - $ded));
                                        @endphp
                                        <tr class="bg-white dark:bg-gray-900">
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                                {{ optional($row->user)->first_name }} {{ optional($row->user)->last_name }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                                {{ $row->employment_status ?? '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">
                                                {{ $currencySymbol.$fmt($row->base_pay ?? 0) }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">
                                                {{ $currencySymbol.$fmt($row->hourly_rate ?? 0) }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">
                                                {{ $fmt($row->hours_worked ?? 0) }}
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
                                                {{ $row->status ?? '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                <div class="flex justify-center gap-3">
                                                    @if(Auth::user()?->is_admin)
                                                        <a href="{{ route('payroll.edit', $row->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Edit</a>
                                                        <form action="{{ route('payroll.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Delete this payroll?');">
                                                            @csrf @method('DELETE')
                                                            <button class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="bg-white dark:bg-gray-900">
                                            <td colspan="10" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                                No payroll records found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if(method_exists($payrolls, 'links'))
                            <div class="px-4 py-3">
                                {{ $payrolls->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script>
        // Auto-fill and auto-calc
        document.addEventListener('DOMContentLoaded', function () {
            const userSelect   = document.getElementById('user_id');
            const statusEl     = document.getElementById('employment_status');
            const hourlyEl     = document.getElementById('hourly_rate');
            const baseEl       = document.getElementById('base_pay');
            const hoursEl      = document.getElementById('hours_worked');
            const grossEl      = document.getElementById('gross_pay');
            const isEditing    = @json($editing);
            const baseByStatus = @json($baseByStatus);

            function toNum(el) { const v = parseFloat(el?.value ?? ''); return isFinite(v) ? v : 0; }

            function applyBaseFromStatus(status) {
                if (!baseEl) return;
                if (status && baseByStatus && Object.prototype.hasOwnProperty.call(baseByStatus, status)) {
                    baseEl.value = baseByStatus[status];
                }
            }

            function applyFromSelected() {
                const opt = userSelect?.options[userSelect.selectedIndex];
                if (!opt) return;
                const status = opt.dataset.status || '';
                const hourly = opt.dataset.hourly || '';
                if (status && statusEl) statusEl.value = status;
                if (hourly !== '' && hourlyEl) hourlyEl.value = hourly;
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
                    grossEl.value = gross.toFixed(2);
                }
            }

            grossEl?.addEventListener('input', () => { grossEl.dataset.touched = '1'; });

            userSelect?.addEventListener('change', applyFromSelected);
            statusEl?.addEventListener('change', () => { applyBaseFromStatus(statusEl.value || ''); calcGross(); });
            hourlyEl?.addEventListener('input', calcGross);
            baseEl?.addEventListener('input', calcGross);
            hoursEl?.addEventListener('input', calcGross);

            const fieldsEmpty = (!statusEl?.value) && (!hourlyEl?.value) && (!baseEl?.value);

            if ((!isEditing || fieldsEmpty) && userSelect?.value) applyFromSelected();
            else calcGross();
        });
    </script>
</x-app-layout>