<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    public function index()
    {
        $payrolls = Auth::user()->is_admin
            ? Payroll::with('user')->latest()->paginate(10)
            : Payroll::with('user')->where('user_id', Auth::id())->latest()->paginate(10);

        $employees = User::where('is_admin', false)->where('is_active', true)->get();

        return view('payroll.index', compact('payrolls', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'gross_pay' => 'required|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'pay_period_start' => 'nullable|date',
            'pay_period_end' => 'nullable|date',
        ]);

        $deductions = $request->input('deductions', 0);
        $tax = 0;
        $other = $deductions;

        $netPay = $request->gross_pay - ($tax + $other);

        Payroll::create([
            'user_id' => $request->user_id,
            'hours_worked' => $request->hours_worked ?? 0,
            'hourly_rate' => $request->hourly_rate ?? 0,
            'gross_pay' => $request->gross_pay,
            'tax_deduction' => $tax,
            'other_deductions' => $other,
            'net_pay' => $netPay,
            'pay_period_start' => $request->pay_period_start ?? now()->startOfMonth()->toDateString(),
            'pay_period_end' => $request->pay_period_end ?? now()->endOfMonth()->toDateString(),
            'status' => 'Pending',
        ]);

        return redirect()->route('payroll.payroll')->with('success', 'Payroll record created successfully.');
    }

    public function edit(Payroll $payroll)
    {
        $employees = User::where('is_admin', false)->where('is_active', true)->get();
        return view('payroll.index', compact('payroll', 'employees'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'gross_pay' => 'required|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'status' => 'required|string',
        ]);

        $deductions = $request->input('deductions', null);

        if ($deductions !== null) {
            $tax = $payroll->tax_deduction ?? 0;
            $other = $deductions;
        } else {
            $tax = $request->input('tax_deduction', $payroll->tax_deduction ?? 0);
            $other = $request->input('other_deductions', $payroll->other_deductions ?? 0);
        }

        $netPay = $request->gross_pay - ($tax + $other);

        $payroll->update([
            'user_id' => $request->user_id,
            'hours_worked' => $request->hours_worked ?? $payroll->hours_worked,
            'hourly_rate' => $request->hourly_rate ?? $payroll->hourly_rate,
            'gross_pay' => $request->gross_pay,
            'tax_deduction' => $tax,
            'other_deductions' => $other,
            'net_pay' => $netPay,
            'status' => $request->status,
        ]);

        return redirect()->route('payroll.payroll')->with('success', 'Payroll record updated successfully.');
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return redirect()->route('payroll.payroll')->with('success', 'Payroll record deleted successfully.');
    }
}