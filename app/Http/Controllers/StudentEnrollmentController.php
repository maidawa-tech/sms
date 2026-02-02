<?php

namespace App\Http\Controllers;

use App\Models\StudentEnrollment;
use App\Models\Student;
use App\Models\Section;
use App\Models\ClassModel;
use App\Models\Arm;
use App\Models\AcademicSession;
use App\Models\ArmSubject;
use App\Models\StudentSubject;
use Illuminate\Http\Request;

class StudentEnrollmentController extends Controller
{
    /**
     * Display the student enrollment page.
     */
    public function index()
    {
        $sections = Section::all();
        $sessions = AcademicSession::orderBy('session_name', 'DESC')->get();

        return view('student_enrollments.index', compact('sections', 'sessions'));
    }

    /**
     * AJAX: Get classes for a selected section.
     */
    public function getClasses($section_id)
    {
        return ClassModel::where('section_id', $section_id)->get();
    }

    /**
     * AJAX: Get arms for a selected class.
     */
    public function getArms($class_id)
    {
        return Arm::where('class_id', $class_id)->get();
    }

    /**
     * AJAX: Enroll a student and automatically assign subjects for the arm.
     */
    public function store(Request $request)
    {
        // Validate inputs
        $request->validate([
            'reg_number' => 'required|exists:students,reg_number',
            'section_id' => 'required|exists:sections,section_id',
            'class_id'   => 'required|exists:classes,class_id',
            'arm_id'     => 'required|exists:arms,arm_id',
            'session_id' => 'required|exists:academic_sessions,session_id',
        ]);

        // Prevent duplicate enrollment
        if (StudentEnrollment::where('reg_number', $request->reg_number)
            ->where('session_id', $request->session_id)
            ->exists()) {

            return response()->json([
                'status'  => 'error',
                'message' => 'Student is already enrolled for this academic session.'
            ]);
        }

        // Enroll student
        StudentEnrollment::create([
            'reg_number' => $request->reg_number,
            'section_id' => $request->section_id,
            'class_id'   => $request->class_id,
            'arm_id'     => $request->arm_id,
            'session_id' => $request->session_id,
        ]);

        // Auto-assign subjects for the student's arm
        $armSubjects = ArmSubject::where('arm_id', $request->arm_id)->pluck('subject_id');

        foreach ($armSubjects as $subjectId) {
            StudentSubject::firstOrCreate([
                'reg_number' => $request->reg_number,
                'subject_id' => $subjectId,
                'arm_id'     => $request->arm_id,
                'session_id' => $request->session_id,
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Student enrolled successfully and subjects assigned!'
        ]);
    }
}
