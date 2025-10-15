<?php

namespace App\Http\Controllers;

use App\Models\Dependent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DependentController extends Controller
{
    // List all dependents for the authenticated user
    public function index()
    {
        $dependents = Auth::user()->dependents;
        return view('profile.partials.update-dependents-form', compact('dependents'));
    }

    // Store a new dependent
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'relationship' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
        ]);

        Auth::user()->dependents()->create($request->only('name', 'relationship', 'date_of_birth'));

        return redirect()->route('profile.edit')->with('create_success', 'Dependent added.');
    }

    // Update an existing dependent
    public function update(Request $request, Dependent $dependent)
    {
        if ($dependent->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'relationship' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
        ]);

        $dependent->update($request->only('name', 'relationship', 'date_of_birth'));

        return redirect()->route('profile.edit')->with('update_success', 'Dependent updated.');
    }

    public function destroy(Dependent $dependent)
    {
        if ($dependent->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $dependent->delete();

        return redirect()->route('profile.edit')->with('remove_success', 'Dependent removed.');
    }

    public function edit(Dependent $dependent)
    {
        if ($dependent->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        return view('profile.edit-dependent', compact('dependent'));
    }

    public function create()
    {
        return view('profile.create-dependent');
    }
}