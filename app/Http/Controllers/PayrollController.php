<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\User;
use App\Models\Payslip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class PayrollController extends Controller
{
    // Add Payroll form (also used for editing a single payroll)
    public function index()
    {
        $employees = User::select('id', 'first_name', 'last_name', 'employment_status', 'hourly_rate')
            ->orderBy('first_name')
            ->get();

        $baseByStatus = config('payroll.default_base_by_status', []);

        return view('payroll.index', compact('employees', 'baseByStatus'));
    }

    // Payroll Records list (default page)
    public function records()
    {
        $payrolls = Payroll::with(['user'])->latest()->paginate(10);
        return view('payroll.edit', compact('payrolls'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'           => ['required', 'exists:users,id'],
            'employment_status' => ['nullable', 'string'],
            'base_pay'          => ['nullable', 'numeric', 'min:0'],
            'hourly_rate'       => ['nullable', 'numeric', 'min:0'],
            'hours_worked'      => ['nullable', 'numeric', 'min:0'],
            'gross_pay'         => ['required', 'numeric', 'min:0'],
            'deductions'        => ['nullable', 'numeric', 'min:0'],
            'period_from'       => ['nullable', 'date'],
            'period_to'         => ['nullable', 'date', 'after_or_equal:period_from'],
            'tax_deduction'     => ['nullable', 'numeric', 'min:0'],
        ]);

        $gross = (float) $data['gross_pay'];
        $ded   = (float) ($data['deductions'] ?? 0);
        $net   = max(0, $gross - $ded);

        $attrs = [
            'user_id'           => (int) $data['user_id'],
            'employment_status' => $data['employment_status'] ?? null,
            'base_pay'          => (float) ($data['base_pay'] ?? 0),
            'hourly_rate'       => (float) ($data['hourly_rate'] ?? 0),
            'hours_worked'      => (float) ($data['hours_worked'] ?? 0),
            'gross_pay'         => $gross,
            'deductions'        => $ded,
            'net_pay'           => $net,
            'status'            => 'Processed',
        ];
        if (Schema::hasColumn('payrolls', 'period_from')) $attrs['period_from'] = $request->input('period_from');
        if (Schema::hasColumn('payrolls', 'period_to'))   $attrs['period_to']   = $request->input('period_to');
        if (Schema::hasColumn('payrolls', 'tax_deduction')) $attrs['tax_deduction'] = $request->input('tax_deduction', 0);

        $payroll = Payroll::create($attrs);

        // Sync payslip (store period_* even if payrolls table lacks those columns)
        $this->syncPayslipFromPayroll($payroll, [
            'period_from' => $request->input('period_from'),
            'period_to'   => $request->input('period_to'),
        ]);

        return redirect()->route('payroll.index')->with('success', 'Payroll added.');
    }

    // Open the same form (index) in edit mode for a specific payroll
    public function edit(Payroll $payroll)
    {
        $employees = User::select('id', 'first_name', 'last_name', 'employment_status', 'hourly_rate')
            ->orderBy('first_name')
            ->get();

        $baseByStatus = config('payroll.default_base_by_status', []);
        $payroll->load(['user', 'payslip']);

        return view('payroll.index', compact('employees', 'payroll', 'baseByStatus'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        $data = $request->validate([
            'user_id'           => ['required', 'exists:users,id'],
            'employment_status' => ['nullable', 'string'],
            'base_pay'          => ['nullable', 'numeric', 'min:0'],
            'hourly_rate'       => ['nullable', 'numeric', 'min:0'],
            'hours_worked'      => ['nullable', 'numeric', 'min:0'],
            'gross_pay'         => ['required', 'numeric', 'min:0'],
            'deductions'        => ['nullable', 'numeric', 'min:0'],
            'period_from'       => ['nullable', 'date'],
            'period_to'         => ['nullable', 'date', 'after_or_equal:period_from'],
            'tax_deduction'     => ['nullable', 'numeric', 'min:0'],
        ]);

        $gross = (float) $data['gross_pay'];
        $ded   = (float) ($data['deductions'] ?? $payroll->deductions ?? 0);
        $net   = max(0, $gross - $ded);

        $attrs = [
            'user_id'           => (int) $data['user_id'],
            'employment_status' => $data['employment_status'] ?? $payroll->employment_status,
            'base_pay'          => (float) ($data['base_pay'] ?? $payroll->base_pay ?? 0),
            'hourly_rate'       => (float) ($data['hourly_rate'] ?? $payroll->hourly_rate ?? 0),
            'hours_worked'      => (float) ($data['hours_worked'] ?? $payroll->hours_worked ?? 0),
            'gross_pay'         => $gross,
            'deductions'        => $ded,
            'net_pay'           => $net,
        ];
        if (Schema::hasColumn('payrolls', 'period_from')) $attrs['period_from'] = $request->input('period_from', $payroll->period_from);
        if (Schema::hasColumn('payrolls', 'period_to'))   $attrs['period_to']   = $request->input('period_to', $payroll->period_to);
        if (Schema::hasColumn('payrolls', 'tax_deduction')) $attrs['tax_deduction'] = $request->input('tax_deduction', $payroll->tax_deduction ?? 0);

        $payroll->update($attrs);

        // Keep payslip in sync
        $this->syncPayslipFromPayroll($payroll->fresh(), [
            'period_from' => $request->input('period_from'),
            'period_to'   => $request->input('period_to'),
        ]);

        return redirect()->route('payroll.index')->with('success', 'Payroll updated.');
    }

    public function destroy(Payroll $payroll)
    {
        // Clean up payslip if exists
        if (Schema::hasTable((new Payslip)->getTable())) {
            Payslip::where('payroll_id', $payroll->id)->delete();
        }

        $payroll->delete();

        return redirect()->route('payroll.index')->with('success', 'Payroll deleted.');
    }

    // GET /payroll/hours?user_id=ID&from=YYYY-MM-DD&to=YYYY-MM-DD
    public function hours(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'from'    => ['required', 'date_format:Y-m-d'],
            'to'      => ['required', 'date_format:Y-m-d', 'after_or_equal:from'],
        ]);

        // Choose time log table
        $table = null;
        foreach (['time_logs', 'timelogs'] as $t) {
            if (Schema::hasTable($t)) { $table = $t; break; }
        }
        if (!$table || !Schema::hasColumn($table, 'user_id')) {
            return response()->json(['hours' => 0]);
        }

        // Date column heuristic
        $dateCol = null;
        foreach (['work_date', 'date', 'log_date'] as $c) {
            if (Schema::hasColumn($table, $c)) { $dateCol = $c; break; }
        }

        $q = DB::table($table)->where('user_id', $data['user_id']);
        if ($dateCol) {
            $q->whereBetween($dateCol, [$data['from'], $data['to']]);
        } elseif (Schema::hasColumn($table, 'created_at')) {
            $q->whereBetween(DB::raw('DATE(created_at)'), [$data['from'], $data['to']]);
        }

        // Sum hours using available schema
        if (Schema::hasColumn($table, 'hours')) {
            $total = (float) $q->sum('hours');
        } elseif (Schema::hasColumn($table, 'duration_minutes')) {
            $total = ((float) $q->sum('duration_minutes')) / 60.0;
        } else {
            // Derive from time ranges if available
            $cols = array_values(array_filter([
                Schema::hasColumn($table, 'start_time') ? 'start_time' : null,
                Schema::hasColumn($table, 'end_time') ? 'end_time' : null,
                Schema::hasColumn($table, 'clock_in') ? 'clock_in' : null,
                Schema::hasColumn($table, 'clock_out') ? 'clock_out' : null,
                Schema::hasColumn($table, 'created_at') ? 'created_at' : null,
            ]));
            $logs = $q->get($cols);
            $total = 0.0;
            foreach ($logs as $log) {
                $start = $log->start_time ?? $log->clock_in ?? null;
                $end   = $log->end_time   ?? $log->clock_out ?? null;
                if ($start && $end) {
                    $total += Carbon::parse($start)->diffInMinutes(Carbon::parse($end)) / 60.0;
                }
            }
        }

        return response()->json(['hours' => round($total, 2)]);
    }

    // Sync payslip, accepting optional overrides for period_* so we can save them even if payrolls table lacks those columns
    private function syncPayslipFromPayroll(Payroll $payroll, array $overrides = []): ?Payslip
    {
        $payslipsTable = (new Payslip)->getTable();
        if (!Schema::hasTable($payslipsTable)) {
            return null;
        }

        $gross = (float) ($payroll->gross_pay ?? 0);
        $ded   = (float) ($payroll->deductions ?? 0);
        $tax   = (float) ($payroll->tax_deduction ?? 0);
        $net   = (float) ($payroll->net_pay ?? max(0, $gross - $ded));

        $periodFrom = $overrides['period_from'] ?? (Schema::hasColumn($payroll->getTable(), 'period_from') ? $payroll->period_from : null);
        $periodTo   = $overrides['period_to']   ?? (Schema::hasColumn($payroll->getTable(), 'period_to')   ? $payroll->period_to   : null);

        $data = [];
        $add = function (string $col, $val) use (&$data, $payslipsTable) {
            if (Schema::hasColumn($payslipsTable, $col)) $data[$col] = $val;
        };

        $add('user_id', $payroll->user_id);
        $add('period_from', $periodFrom);
        $add('period_to', $periodTo);
        $add('total_earnings', $gross);
        $add('tax_deduction', $tax);
        $add('other_deductions', max(0, $ded - $tax));
        $add('total_deductions', $ded);
        $add('net_pay', $net);
        $add('status', 'Issued');
        $add('issued_at', now());

        return Payslip::updateOrCreate(
            ['payroll_id' => $payroll->id],
            $data
        );
    }
}