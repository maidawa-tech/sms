<?php

namespace App\Http\Controllers;

use App\Models\StudentSubject;
use App\Models\Student;
use App\Models\Subject;
use App\Models\AcademicSession;
use Illuminate\Http\Request;

class StudentSubjectController extends Controller
{
    /**
     * Display a list of subjects assigned to a student.
     */
    public function index($reg_number)
    {
        $student = Student::where('reg_number', $reg_number)->firstOrFail();

        $subjects = StudentSubject::where('reg_number', $reg_number)
            ->with(['subject', 'arm'])
            ->orderBy('subject_id')
            ->get();

        return view('student_subjects.index', compact('student', 'subjects'));
    }

    /**
     * Assign a subject to a student manually.
     */
    public function store(Request $request)
    {
        $request->validate([
            'reg_number' => 'required|exists:students,reg_number',
            'subject_id' => 'required|exists:subjects,subject_id',
            'arm_id'     => 'required|exists:arms,arm_id',
            'session_id' => 'required',
        ]);

        // Prevent duplicate subject assignment
        $exists = StudentSubject::where([
            'reg_number' => $request->reg_number,
            'subject_id' => $request->subject_id,
            'arm_id'     => $request->arm_id,
            'session_id' => $request->session_id,
        ])->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Subject already assigned to this student.'
            ]);
        }

        StudentSubject::create([
            'reg_number' => $request->reg_number,
            'subject_id' => $request->subject_id,
            'arm_id'     => $request->arm_id,
            'session_id' => $request->session_id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Subject assigned successfully.'
        ]);
    }

    /**
     * Remove a subject from a student.
     */
    public function destroy($id)
    {
        $record = StudentSubject::findOrFail($id);
        $record->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Subject removed successfully.'
        ]);
    }
}
