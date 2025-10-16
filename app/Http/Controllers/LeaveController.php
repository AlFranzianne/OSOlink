<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // List leaves (admins see all, regular users see their own)
    public function index(Request $request)
    {
        $user   = Auth::user();
        $search = $request->query('search');
        $status = $request->query('status');
        $sort   = $request->query('sort', 'start_date');
        $order  = strtolower($request->query('order', 'desc'));

        // normalize status input to match DB values
        if ($status) {
            $normalized = strtolower(trim($status));
            $map = [
                'approved' => 'Approved',
                'rejected' => 'Rejected',
                'pending'  => 'Pending',
            ];
            $status = $map[$normalized] ?? null;
        }

        // PERSONAL leaves (always use fixed ordering; separate paginator name)
        $personalQuery = Leave::with('user')
            ->where('user_id', $user->id)
            ->orderBy('start_date', 'desc');

        $personalLeaves = $personalQuery->paginate(10, ['*'], 'personal_page');

        // GLOBAL leaves (only for admins) — apply filters/sort/pagination separately
        $globalLeaves = null;
        if ($user->is_admin ?? false) {
            $globalQuery = Leave::query()->with('user');

            $needUserJoin = $search || $sort === 'user';
            if ($needUserJoin) {
                $globalQuery->leftJoin('users', 'leaves.user_id', '=', 'users.id')
                            ->select('leaves.*');
            }

            if ($search) {
                $term = '%'.$search.'%';
                $globalQuery->where(function ($q) use ($term) {
                    $q->where('leaves.reason', 'like', $term)
                      ->orWhere('leaves.type', 'like', $term)
                      ->orWhere('users.first_name', 'like', $term)
                      ->orWhere('users.last_name', 'like', $term)
                      ->orWhere('users.email', 'like', $term);
                });
            }

            if ($status) {
                $globalQuery->where('leaves.status', $status);
            }

            $allowedSorts = ['start_date', 'end_date', 'type', 'user', 'created_at'];
            if (! in_array($sort, $allowedSorts)) {
                $sort = 'start_date';
            }
            if (! in_array($order, ['asc', 'desc'])) {
                $order = 'desc';
            }

            if ($sort === 'user') {
                $globalQuery->orderBy('users.first_name', $order)->orderBy('users.last_name', $order);
            } else {
                $globalQuery->orderBy("leaves.{$sort}", $order);
            }

            // use a different page name for global paginator so query strings don't clash
            $globalLeaves = $globalQuery->paginate(15, ['*'], 'global_page')->appends($request->except('personal_page'));
        }

        return view('leaves.index', compact('personalLeaves', 'globalLeaves'));
    }

    public function create()
    {
        return view('leaves.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'type'       => 'required|string|max:100',
            'reason'     => 'nullable|string',
            'status'     => 'required|string',
        ]);

        Auth::user()->leaves()->create($request->only('start_date', 'end_date', 'type', 'reason', 'status'));

        return redirect()->route('leaves.index')->with('create_success', 'Leave request created.');
    }

    public function show($id)
    {
        $leave = Leave::findOrFail($id);
        $this->authorizeAccess($leave);
        return view('leaves.show', compact('leave'));
    }

    public function edit(Leave $leave)
    {
        $this->authorizeAccess($leave);
        return view('leaves.edit', compact('leave'));
    }

    public function update(Request $request, Leave $leave)
    {
        $this->authorizeAccess($leave);

        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'type'       => 'required|string|max:100',
            'reason'     => 'nullable|string',
            'status'     => 'nullable|string', // e.g. approved/denied (admins)
        ]);

        $leave->update($request->only('start_date', 'end_date', 'type', 'reason', 'status'));

        return redirect()->route('leaves.index')->with('update_success', 'Leave updated.');
    }

    public function destroy(Leave $leave)
    {
        $this->authorizeAccess($leave);
        $leave->delete();

        return redirect()->route('leaves.index')->with('remove_success', 'Leave deleted.');
    }

    // Approve a leave (admin only)
    public function approve(Leave $leave)
    {
        $user = Auth::user();
        if (! ($user->is_admin ?? false)) {
            abort(403);
        }

        $leave->status = 'Approved';
        $leave->save();

        return redirect()->route('leaves.index')->with('admin_update_success', 'Leave status set to Approved.');
    }

    // Reject a leave (admin only)
    public function reject(Leave $leave)
    {
        $user = Auth::user();
        if (! ($user->is_admin ?? false)) {
            abort(403);
        }

        $leave->status = 'Rejected';
        $leave->save();

        return redirect()->route('leaves.index')->with('admin_update_success', 'Leave status set to Rejected.');
    }

    public function pending(Leave $leave)
    {
        // optional: authorize if you have policies
        // $this->authorize('update', $leave);

        $leave->status = 'Pending';
        $leave->save();

        return redirect()->back()->with('admin_update_success', 'Leave status set to Pending.');
    }

    // simple owner/admin check (avoids policy setup)
    private function authorizeAccess(Leave $leave)
    {
        $user = Auth::user();
        if ($leave->user_id !== $user->id && ! ($user->is_admin ?? false)) {
            abort(403, 'Unauthorized action.');
        }
    }
}
