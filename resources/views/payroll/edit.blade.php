<x-app-layout>
    @php
        $payrolls       = $payrolls ?? collect();
        $fmt            = fn($v)=>number_format((float)$v, 2);
        $currencySymbol = 'C$';
        $success        = session('success');
    @endphp

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <section>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <header class="mb-4 flex items-center justify-between">
                            <div>
                                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-gray-100">Payroll Records</h3>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Review, edit, or delete payroll entries.</p>
                                @if($success)
                                    <p class="mt-3 text-sm text-green-600 dark:text-green-400">{{ $success }}</p>
                                @endif
                            </div>
                            <a href="{{ route('payroll.create') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                Add Payroll
                            </a>
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
                                                    @if(auth()->user()?->is_admin)
                                                        <a href="{{ route('payroll.edit', $row->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Edit</a>
                                                        <form action="{{ route('payroll.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Delete this payroll?');">
                                                            @csrf @method('DELETE')
                                                            <button class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                                                        </form>
                                                    @else
                                                        <span class="text-gray-400">—</span>
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
</x-app-layout>