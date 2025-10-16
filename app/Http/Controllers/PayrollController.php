<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\User;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index()
    {
        // Include employment_status and hourly_rate from users for auto-fill
        $employees = User::select('id','first_name','last_name','employment_status','hourly_rate')
            ->orderBy('first_name')
            ->get();

        // Status → base mapping from config
        $baseByStatus = config('payroll.default_base_by_status');

        // List payrolls with user relation
        $payrolls = Payroll::with('user')->latest()->paginate(10);

        return view('payroll.index', compact('employees','payrolls','baseByStatus'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'           => ['required','exists:users,id'],
            'employment_status' => ['nullable','string'],
            'base_pay'          => ['nullable','numeric','min:0'],
            'hourly_rate'       => ['nullable','numeric','min:0'],
            'hours_worked'      => ['nullable','numeric','min:0'],
            'gross_pay'         => ['required','numeric','min:0'],
            'deductions'        => ['nullable','numeric','min:0'],
        ]);

        $gross = (float)$data['gross_pay'];
        $ded   = (float)($data['deductions'] ?? 0);
        $net   = max(0, $gross - $ded);

        Payroll::create([
            'user_id'           => (int)$data['user_id'],
            'employment_status' => $data['employment_status'] ?? null,
            'base_pay'          => (float)($data['base_pay'] ?? 0),
            'hourly_rate'       => (float)($data['hourly_rate'] ?? 0),
            'hours_worked'      => (float)($data['hours_worked'] ?? 0),
            'gross_pay'         => $gross,
            'deductions'        => $ded,
            'net_pay'           => $net,
            'status'            => 'Processed',
        ]);

        return redirect()->route('payroll.index')->with('success','Payroll added.');
    }

    public function edit(Payroll $payroll)
    {
        $employees = User::select('id','first_name','last_name','employment_status','hourly_rate')
            ->orderBy('first_name')
            ->get();

        $baseByStatus = config('payroll.default_base_by_status');

        $payrolls = Payroll::with('user')->latest()->paginate(10);

        return view('payroll.index', compact('employees','payrolls','payroll','baseByStatus'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        $data = $request->validate([
            'user_id'           => ['required','exists:users,id'],
            'employment_status' => ['nullable','string'],
            'base_pay'          => ['nullable','numeric','min:0'],
            'hourly_rate'       => ['nullable','numeric','min:0'],
            'hours_worked'      => ['nullable','numeric','min:0'],
            'gross_pay'         => ['required','numeric','min:0'],
            'deductions'        => ['nullable','numeric','min:0'],
        ]);

        $gross = (float)$data['gross_pay'];
        $ded   = (float)($data['deductions'] ?? $payroll->deductions ?? 0);
        $net   = max(0, $gross - $ded);

        $payroll->update([
            'user_id'           => (int)$data['user_id'],
            'employment_status' => $data['employment_status'] ?? $payroll->employment_status,
            'base_pay'          => (float)($data['base_pay'] ?? $payroll->base_pay ?? 0),
            'hourly_rate'       => (float)($data['hourly_rate'] ?? $payroll->hourly_rate ?? 0),
            'hours_worked'      => (float)($data['hours_worked'] ?? $payroll->hours_worked ?? 0),
            'gross_pay'         => $gross,
            'deductions'        => $ded,
            'net_pay'           => $net,
        ]);

        return redirect()->route('payroll.index')->with('success','Payroll updated.');
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return redirect()->route('payroll.index')->with('success','Payroll deleted.');
    }
}