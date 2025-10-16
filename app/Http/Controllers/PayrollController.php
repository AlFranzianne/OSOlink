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
        $payrolls = Payroll::with('user')->paginate(10);
        $employees = User::all();
        return view('payroll.index', compact('payrolls', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'gross_pay' => 'required|numeric',
            'deductions' => 'nullable|numeric',
        ]);

        $netPay = $request->gross_pay - ($request->deductions ?? 0);

        Payroll::create([
            'user_id' => $request->user_id,
            'gross_pay' => $request->gross_pay,
            'deductions' => $request->deductions ?? 0,
            'net_pay' => $netPay,
            'status' => 'Processed',
        ]);

        return redirect()->route('payroll.index')->with('success', 'Payroll added successfully.');
    }

    public function edit($id)
    {
        $payroll = Payroll::findOrFail($id);
        $employees = User::all();
        $payrolls = Payroll::with('user')->paginate(10);

        return view('payroll.index', compact('payroll', 'payrolls', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'gross_pay' => 'required|numeric',
            'deductions' => 'nullable|numeric',
        ]);

        $payroll = Payroll::findOrFail($id);
        $netPay = $request->gross_pay - ($request->deductions ?? 0);

        $payroll->update([
            'user_id' => $request->user_id,
            'gross_pay' => $request->gross_pay,
            'deductions' => $request->deductions ?? 0,
            'net_pay' => $netPay,
        ]);

        return redirect()->route('payroll.index')->with('success', 'Payroll updated successfully.');
    }

    public function destroy($id)
    {
        $payroll = Payroll::findOrFail($id);
        $payroll->delete();

        return redirect()->route('payroll.index')->with('success', 'Payroll deleted successfully.');
    }
}