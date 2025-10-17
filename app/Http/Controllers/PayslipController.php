<?php

namespace App\Http\Controllers;

use App\Models\Payslip;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayslipController extends Controller
{
    // List payslips: admin sees all, employee sees only their own
    public function index()
    {
        $query = Payslip::with(['user','payroll'])->latest();

        if (!Auth::user()?->is_admin) {
            $query->where('user_id', Auth::id());
        }

        $payslips = $query->paginate(12);

        return view('payslip.index', compact('payslips'));
    }

    // Show a specific payslip (owner or admin)
    public function show(Payslip $payslip)
    {
        $user = Auth::user();
        abort_unless($user && ($user->is_admin || (int)$payslip->user_id === (int)$user->id), 403);

        return view('payslip.show', compact('payslip'));
    }

    // Admin: create or refresh a payslip from a Payroll record
    // Route: POST /payslip with { payroll_id }
    public function store(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && $user->is_admin, 403);

        $data = $request->validate([
            'payroll_id' => ['required','exists:payrolls,id'],
        ]);

        $payroll = Payroll::with('user')->findOrFail($data['payroll_id']);

        $gross = (float) ($payroll->gross_pay ?? 0);
        $ded   = (float) ($payroll->deductions ?? 0);
        $net   = (float) ($payroll->net_pay ?? max(0, $gross - $ded));

        $payslip = Payslip::updateOrCreate(
            ['payroll_id' => $payroll->id],
            [
                'user_id'          => $payroll->user_id,
                'period_from'      => $payroll->period_from ?? null,
                'period_to'        => $payroll->period_to ?? null,
                'total_earnings'   => $gross,
                'tax_deduction'    => (float) ($payroll->tax_deduction ?? 0),
                'other_deductions' => max(0, $ded - (float)($payroll->tax_deduction ?? 0)),
                'total_deductions' => $ded,
                'net_pay'          => $net,
                'status'           => 'Issued',
                'issued_at'        => now(),
            ]
        );

        return redirect()->route('payslip.show', $payslip)->with('success', 'Payslip generated.');
    }
}