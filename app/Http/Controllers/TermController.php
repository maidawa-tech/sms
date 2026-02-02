<?php

namespace App\Http\Controllers;

use App\Models\Term;
use App\Models\AcademicSession;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function index()
    {
        $terms = Term::with('session')->orderByDesc('created_at')->get();
        $sessions = AcademicSession::orderByDesc('created_at')->get();
        return view('terms.index', compact('terms', 'sessions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'term_name' => 'required|string|max:50',
            'session_id' => 'required|exists:academic_sessions,session_id',
        ]);

        // Prevent duplicate term for same session
        $exists = Term::where('term_name', $request->term_name)
                      ->where('session_id', $request->session_id)
                      ->exists();
        if ($exists) {
            return back()->with('error', 'This term already exists for the selected session.');
        }

        Term::create($request->all());
        return back()->with('success', 'Term created successfully.');
    }

    public function update(Request $request, $id)
    {
        $term = Term::findOrFail($id);

        $request->validate([
            'term_name' => 'required|string|max:50',
            'session_id' => 'required|exists:academic_sessions,session_id',
        ]);

        // Check for duplicates excluding current term
        $exists = Term::where('term_name', $request->term_name)
                      ->where('session_id', $request->session_id)
                      ->where('term_id', '!=', $id)
                      ->exists();
        if ($exists) {
            return back()->with('error', 'This term already exists for the selected session.');
        }

        $term->update([
            'term_name' => $request->term_name,
            'session_id' => $request->session_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return back()->with('success', 'Term updated successfully.');
    }

    public function activate($id)
    {
        // Deactivate all terms first
        Term::query()->update(['is_active' => 0]);

        // Activate selected term
        Term::where('term_id', $id)->update(['is_active' => 1]);

        return back()->with('success', 'Term activated successfully.');
    }

    public function destroy($id)
    {
        $term = Term::findOrFail($id);

        if ($term->is_active) {
            return back()->with('error', 'You cannot delete an active term.');
        }

        $term->delete();

        return back()->with('success', 'Term deleted successfully.');
    }
}
