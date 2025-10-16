<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\User;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    /**
     * Display all payroll records.
     */
    public function index()
    {
        $payrolls = Payroll::with('user')->latest()->paginate(10);
        $employees = User::where('is_admin', false)->where('is_active', true)->get();

        // ✅ Reference the correct file: resources/views/payroll/payroll.blade.php
        return view('payroll.payroll', compact('payrolls', 'employees'));
    }

    /**
     * Store a newly created payroll record.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'gross_pay' => 'required|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
        ]);

        $netPay = $request->gross_pay - ($request->deductions ?? 0);

        Payroll::create([
            'user_id' => $request->user_id,
            'gross_pay' => $request->gross_pay,
            'deductions' => $request->deductions ?? 0,
            'net_pay' => $netPay,
            'status' => 'Pending',
        ]);

        return redirect()->route('payroll')->with('success', 'Payroll record created successfully.');
    }

    /**
     * Edit payroll record.
     */
    public function edit(Payroll $payroll)
    {
        $employees = User::where('is_admin', false)->where('is_active', true)->get();
        return view('payroll.payroll', compact('payroll', 'employees'));
    }

    /**
     * Update payroll record.
     */
    public function update(Request $request, Payroll $payroll)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'gross_pay' => 'required|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'status' => 'required|string',
        ]);

        $netPay = $request->gross_pay - ($request->deductions ?? 0);

        $payroll->update([
            'user_id' => $request->user_id,
            'gross_pay' => $request->gross_pay,
            'deductions' => $request->deductions ?? 0,
            'net_pay' => $netPay,
            'status' => $request->status,
        ]);

        return redirect()->route('payroll')->with('success', 'Payroll record updated successfully.');
    }

    /**
     * Delete payroll record.
     */
    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return redirect()->route('payroll')->with('success', 'Payroll record deleted successfully.');
    }
}