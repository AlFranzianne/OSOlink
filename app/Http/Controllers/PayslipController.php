<?php

namespace App\Http\Controllers;

use App\Models\Payslip;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayslipController extends Controller
{
    public function index()
    {
        $payslips = Auth::user()->is_admin
            ? Payslip::with('user')->latest()->paginate(12)
            : Payslip::with('user')->where('user_id', Auth::id())->latest()->paginate(12);

        return view('payslip.index', compact('payslips'));
    }

    public function show(Payslip $payslip)
    {
        if (!Auth::user()->is_admin && $payslip->user_id !== Auth::id()) {
            abort(403);
        }
        return view('payslip.show', compact('payslip'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'payroll_id' => 'required|exists:payrolls,id',
        ]);

        $payroll = Payroll::findOrFail($request->payroll_id);

        $p = Payslip::create([
            'payroll_id' => $payroll->id,
            'user_id' => $payroll->user_id,
            'issued_at' => now(),
            'total_earnings' => $payroll->gross_pay,
            'total_deductions' => ($payroll->tax_deduction ?? 0) + ($payroll->other_deductions ?? 0),
            'net_pay' => $payroll->net_pay,
            'status' => 'Generated',
        ]);

        return redirect()->back()->with('success', 'Payslip generated.');
    }

    // optional resource methods left empty (edit/update/destroy) if not needed
}