<x-app-layout>
    @php
        $payrolls       = $payrolls ?? collect();
        $fmt            = fn($v)=>number_format((float)$v, 2);
        $currencySymbol = 'C$';
        $success        = session('success');
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <header class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Payroll Records</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Review, edit, or delete payroll entries.</p>
                        @if($success)
                            <p class="mt-1 text-sm text-green-600 dark:text-green-400">{{ $success }}</p>
                        @endif
                    </div>
                    <a href="{{ route('payroll.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-700 border border-transparent 
                    rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 
                    dark:hover:bg-indigo-800 focus:bg-indigo-700 dark:focus:bg-indigo-800 focus:outline-none 
                    focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    + Add Payroll
                    </a>
                </header>

                <div class="mt-6 overflow-x-auto">
                    <table class="rounded-lg overflow-hidden w-full divide-y divide-gray-300 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Employee</th>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Type</th>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Base</th>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Hourly</th>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Hours</th>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Gross</th>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Deductions</th>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Net</th>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Status</th>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-gray-200 dark:bg-gray-900 text-sm">
                            @forelse ($payrolls as $row)
                                @php
                                    $gross = (float)($row->gross_pay ?? 0);
                                    $ded   = (float)($row->deductions ?? 0);
                                    $net   = (float)($row->net_pay ?? max(0, $gross - $ded));
                                @endphp
                                <tr>
                                    <td class="px-4 py-2 text-center font-medium text-gray-700 dark:text-gray-200">
                                        {{ optional($row->user)->first_name }} {{ optional($row->user)->last_name }}
                                    </td>
                                    <td class="px-4 py-2 text-center font-medium text-gray-700 dark:text-gray-200">
                                        {{ $row->employment_status ?? '—' }}
                                    </td>
                                    <td class="px-4 py-2 text-center font-medium text-gray-700 dark:text-gray-200">
                                        {{ $currencySymbol.$fmt($row->base_pay ?? 0) }}
                                    </td>
                                    <td class="px-4 py-2 text-center font-medium text-gray-700 dark:text-gray-200">
                                        {{ $currencySymbol.$fmt($row->hourly_rate ?? 0) }}
                                    </td>
                                    <td class="px-4 py-2 text-center font-medium text-gray-700 dark:text-gray-200">
                                        {{ $fmt($row->hours_worked ?? 0) }}
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
                                        {{ $row->status ?? '—' }}
                                    </td>
                                    <td class="px-4 py-2 text-center font-medium text-gray-700 dark:text-gray-200">
                                        <div class="flex justify-center gap-3">
                                            @if(auth()->user()?->is_admin)
                                                <a href="{{ route('payroll.edit', $row->id) }}"
                                                    class="text-green-600 dark:text-green-400 hover:underline">
                                                    Edit
                                                </a>
                                                <form action="{{ route('payroll.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Delete this payroll?');">
                                                    @csrf @method('DELETE')
                                                    <button
                                                        class="text-red-600 hover:underline ml-2">
                                                        Delete
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-4 py-2 text-center font-medium text-gray-700 dark:text-gray-200">
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
    </div>
</x-app-layout>