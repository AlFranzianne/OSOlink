<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Search by name or email
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status (active/inactive)
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Sorting (default: name asc)
        $sortField = $request->get('sort', 'name');  // allowed: name, email, created_at
        $sortOrder = $request->get('order', 'asc');  // asc or desc

        if (in_array($sortField, ['name', 'email', 'created_at'])) {
            $query->orderBy($sortField, $sortOrder);
        }

        $logQuery = AuditLog::with('user');

        // Audit Logs

        if ($request->filled('log_user')) {
            $logQuery->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->log_user}%");
            });
        }

        if ($request->filled('log_action')) {
            $logQuery->where('action', 'like', "%{$request->log_action}%");
        }

        $logs = $logQuery
            ->orderBy($request->get('log_sort', 'created_at'), $request->get('log_order', 'desc'))
            ->paginate(10, ['*'], 'logs_page');

        $users = $query->paginate(10)->withQueryString();

        return view('adminpanel', compact('users', 'logs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        $this->logAction('Created User', $user);

        return redirect()->route('adminpanel')->with('success', 'User created successfully!');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('adminpanel')->with('error', 'You cannot deactivate your own account!');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $this->logAction($user->is_active ? 'Activated User' : 'Deactivated User', $user);

        return redirect()->route('adminpanel')->with('success', 'User status updated!');
    }

    protected function logAction($action, $target = null, $changes = null)
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'target_type' => $target ? get_class($target) : null,
            'target_id' => $target->id ?? null,
            'changes' => $changes,
        ]);
    }
}
