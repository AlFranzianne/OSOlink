<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Payroll Management
        </h2>
    </x-slot>

    @php
        $payrolls = $payrolls ?? collect();
        $employees = $employees ?? collect();
        $baseByStatus = $baseByStatus ?? [];
        $editing = isset($payroll);
        $formAction = $editing ? route('payroll.update', $payroll->id) : route('payroll.store');
        $selectedUser = old('user_id', $payroll->user_id ?? '');
    @endphp

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(Auth::check() && Auth::user()->is_admin)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">
                    {{ $editing ? 'Edit Payroll' : 'Add New Payroll' }}
                </h3>

                <form action="{{ $formAction }}" method="POST" class="space-y-4">
                    @csrf
                    @if($editing) @method('PUT') @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Employee</label>
                        <select name="user_id" id="user_id" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-gray-200" required>
                            <option value="">-- select employee --</option>
                            @foreach ($employees as $employee)
                                <option
                                    value="{{ $employee->id }}"
                                    data-status="{{ $employee->employment_status ?? '' }}"
                                    data-hourly="{{ $employee->hourly_rate ?? '' }}"
                                    @selected((string)$employee->id === (string)$selectedUser)
                                >
                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Employment Status</label>
                            <select name="employment_status" id="employment_status" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-gray-200">
                                <option value="">-- select status --</option>
                                <option value="Full-time"   @selected(old('employment_status', $payroll->employment_status ?? '') === 'Full-time')>Full-time</option>
                                <option value="Part-time"   @selected(old('employment_status', $payroll->employment_status ?? '') === 'Part-time')>Part-time</option>
                                <option value="Contractual" @selected(old('employment_status', $payroll->employment_status ?? '') === 'Contractual')>Contractual</option>
                                <option value="Intern"      @selected(old('employment_status', $payroll->employment_status ?? '') === 'Intern')>Intern</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hourly Rate</label>
                            <input type="number" step="0.01" name="hourly_rate" id="hourly_rate" value="{{ old('hourly_rate', $payroll->hourly_rate ?? '') }}" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-gray-200">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Base Pay</label>
                            <input type="number" step="0.01" name="base_pay" id="base_pay" value="{{ old('base_pay', $payroll->base_pay ?? '') }}" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-gray-200">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hours Worked</label>
                            <input type="number" step="0.01" name="hours_worked" id="hours_worked" value="{{ old('hours_worked', $payroll->hours_worked ?? '') }}" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-gray-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gross Pay</label>
                            <input type="number" step="0.01" name="gross_pay" id="gross_pay" value="{{ old('gross_pay', $payroll->gross_pay ?? '') }}" required class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-gray-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deductions</label>
                            <input type="number" step="0.01" name="deductions" id="deductions" value="{{ old('deductions', $payroll->deductions ?? 0) }}" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-gray-200">
                        </div>
                    </div>

                    <div class="flex justify-end items-center gap-3">
                        @if($editing)
                            <a href="{{ route('payroll.index') }}" class="inline-block text-sm px-3 py-2 border rounded text-gray-700 dark:text-gray-200">Cancel</a>
                        @endif
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-md">
                            {{ $editing ? 'Update Payroll' : 'Add Payroll' }}
                        </button>
                    </div>
                </form>
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">Payroll Records</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full w-full table-auto text-sm">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left">Employee</th>
                                <th class="px-4 py-2 text-center">Type</th>
                                <th class="px-4 py-2 text-center">Base</th>
                                <th class="px-4 py-2 text-center">Hourly</th>
                                <th class="px-4 py-2 text-center">Hours</th>
                                <th class="px-4 py-2 text-center">Gross</th>
                                <th class="px-4 py-2 text-center">Deductions</th>
                                <th class="px-4 py-2 text-center">Net</th>
                                <th class="px-4 py-2 text-center">Status</th>
                                <th class="px-4 py-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payrolls as $row)
                                @php
                                    $cur = config('app.currency','PHP');
                                    $sym = ['PHP'=>'₱','USD'=>'$','EUR'=>'€','GBP'=>'£','JPY'=>'¥'][strtoupper($cur)] ?? strtoupper($cur).' ';
                                    $gross = (float)($row->gross_pay ?? 0);
                                    $ded   = (float)($row->deductions ?? 0);
                                    $net   = (float)($row->net_pay ?? max(0, $gross - $ded));
                                    $fmt = fn($v)=>number_format((float)$v, 2);
                                @endphp
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <td class="px-4 py-2">{{ optional($row->user)->first_name }} {{ optional($row->user)->last_name }}</td>
                                    <td class="px-4 py-2 text-center">{{ $row->employment_status ?? '—' }}</td>
                                    <td class="px-4 py-2 text-center">{{ $sym.$fmt($row->base_pay ?? 0) }}</td>
                                    <td class="px-4 py-2 text-center">{{ $sym.$fmt($row->hourly_rate ?? 0) }}</td>
                                    <td class="px-4 py-2 text-center">{{ $fmt($row->hours_worked ?? 0) }}</td>
                                    <td class="px-4 py-2 text-center">{{ $sym.$fmt($gross) }}</td>
                                    <td class="px-4 py-2 text-center">{{ $sym.$fmt($ded) }}</td>
                                    <td class="px-4 py-2 text-center">{{ $sym.$fmt($net) }}</td>
                                    <td class="px-4 py-2 text-center">{{ $row->status }}</td>
                                    <td class="px-4 py-2 text-right">
                                        @if(Auth::user()?->is_admin)
                                            <a href="{{ route('payroll.edit', $row->id) }}" class="text-blue-600 mr-3">Edit</a>
                                            <form action="{{ route('payroll.destroy', $row->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this payroll?');">
                                                @csrf @method('DELETE')
                                                <button class="text-red-600">Delete</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="10" class="text-center py-4 text-gray-500">No payroll records found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    @if(method_exists($payrolls, 'links')) {{ $payrolls->links() }} @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-fill Employment Status, Hourly Rate, and Base Pay (from status mapping)
        document.addEventListener('DOMContentLoaded', function () {
            const userSelect = document.getElementById('user_id');
            const statusEl = document.getElementById('employment_status');
            const hourlyEl = document.getElementById('hourly_rate');
            const baseEl = document.getElementById('base_pay');

            const isEditing = {{ isset($editing) && $editing ? 'true' : 'false' }};
            const baseByStatus = @json($baseByStatus); // {"Full-time":20000,...}

            function applyBaseFromStatus(status) {
                if (!baseEl) return;
                if (status && baseByStatus && baseByStatus[status] !== undefined) {
                    baseEl.value = baseByStatus[status];
                }
            }

            function applyFromSelected() {
                const opt = userSelect?.options[userSelect.selectedIndex];
                if (!opt) return;

                const status = opt.dataset.status || '';
                const hourly = opt.dataset.hourly || '';

                if (statusEl && status) statusEl.value = status;
                if (hourlyEl && hourly !== '') hourlyEl.value = hourly;

                applyBaseFromStatus(status);
            }

            userSelect?.addEventListener('change', applyFromSelected);

            // If admin changes status manually, update base pay from mapping
            statusEl?.addEventListener('change', function () {
                applyBaseFromStatus(statusEl.value || '');
            });

            // Initial fill when creating or fields are empty
            const fieldsEmpty =
                (!statusEl?.value || statusEl.value === '') &&
                (!hourlyEl?.value || hourlyEl.value === '') &&
                (!baseEl?.value || baseEl.value === '');
            if ((!isEditing || fieldsEmpty) && userSelect?.value) {
                applyFromSelected();
            }
        });
    </script>
</x-app-layout>