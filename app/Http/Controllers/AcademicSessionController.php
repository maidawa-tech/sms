<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use Illuminate\Http\Request;

class AcademicSessionController extends Controller
{
    public function index()
    {
        $sessions = AcademicSession::orderByDesc('created_at')->get();
        return view('academic_sessions.index', compact('sessions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'session_name' => 'required|unique:academic_sessions,session_name',
        ]);

        AcademicSession::create($request->all());

        return back()->with('success', 'Academic session created successfully.');
    }

    public function update(Request $request, $id)
    {
        $session = AcademicSession::findOrFail($id);

        $request->validate([
            'session_name' => 'required|unique:academic_sessions,session_name,' . $id . ',session_id',
        ]);

        $session->update([
            'session_name' => $request->session_name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return back()->with('success', 'Academic session updated successfully.');
    }

    public function activate($id)
    {
        // Deactivate all sessions first
        AcademicSession::query()->update(['is_active' => 0]);

        // Activate selected session
        AcademicSession::where('session_id', $id)->update(['is_active' => 1]);

        return back()->with('success', 'Academic session activated successfully.');
    }

    public function destroy($id)
    {
        $session = AcademicSession::findOrFail($id);

        // Prevent deleting the currently active session
        if ($session->is_active) {
            return back()->with('error', 'You cannot delete an active session.');
        }

        $session->delete();

        return back()->with('success', 'Academic session deleted successfully.');
    }
}
