<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\StudentEnrollment;
use App\Models\Student;
use App\Models\Section;
use App\Models\ClassModel;
use App\Models\Arm;
use App\Models\AcademicSession;
use App\Models\ArmSubject;

class MoveStudentsController extends Controller
{
    /**
     * Show move students page
     */
    public function index()
    {
        $sections = Section::all();
        $sessions = AcademicSession::orderBy('session_name', 'desc')->get();

        return view('move_students.index', compact('sections', 'sessions'));
    }

    /**
     * AJAX: Get classes for a selected section.
     */
    public function getClasses($section_id)
    {
        $classes = ClassModel::where('section_id', $section_id)->get();
        return response()->json($classes);
    }

    /**
     * AJAX: Get arms for a selected class.
     */
    public function getArms($class_id)
    {
        $arms = Arm::where('class_id', $class_id)->get();
        return response()->json($arms);
    }

    /**
     * AJAX: Load enrolled students.
     */
    public function loadStudents(Request $request)
    {
        try {

            $query = DB::table('student_enrollments as se')
                ->join('students as s', 'se.reg_number', '=', 's.reg_number')
                ->join('sections as sec', 'se.section_id', '=', 'sec.section_id')
                ->join('classes as c', 'se.class_id', '=', 'c.class_id')
                ->join('arms as a', 'se.arm_id', '=', 'a.arm_id')
                ->leftJoin('academic_sessions as asess', 'se.session_id', '=', 'asess.session_id')
                ->select(
                    'se.id',
                    'se.reg_number',

                    // Build full name using student table fields
                    DB::raw("CONCAT(s.surname, ' ', s.first_name, ' ', COALESCE(s.other_name, '')) AS full_name"),

                    'sec.section_name',
                    'c.class_name',
                    'a.arm_name',
                    DB::raw("COALESCE(asess.session_name, se.session_id) as session_name")
                );

            // Apply filters
            if ($request->filled('section_id')) $query->where('se.section_id', $request->section_id);
            if ($request->filled('class_id')) $query->where('se.class_id', $request->class_id);
            if ($request->filled('arm_id')) $query->where('se.arm_id', $request->arm_id);
            if ($request->filled('session_id')) $query->where('se.session_id', $request->session_id);

            // Sort properly by surname and first name
            $query->orderBy('s.surname')->orderBy('s.first_name');

            $students = $query->get();

            return response()->json(['data' => $students]);

        } catch (\Throwable $e) {

            \Log::error('Load Students Error: ' . $e->getMessage());

            return response()->json([
                'data' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX: Move selected students
     */
    public function moveStudents(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reg_numbers'     => 'required|array|min:1',
            'reg_numbers.*'   => 'required|string|exists:students,reg_number',
            'new_section_id'  => 'required|exists:sections,section_id',
            'new_class_id'    => 'required|exists:classes,class_id',
            'new_arm_id'      => 'required|exists:arms,arm_id',
            'session_id'      => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors()
            ], 422);
        }

        $regNumbers = $request->reg_numbers;
        $newSection = $request->new_section_id;
        $newClass   = $request->new_class_id;
        $newArm     = $request->new_arm_id;
        $sessionId  = $request->session_id;

        DB::beginTransaction();

        try {

            // Update enrollments
            DB::table('student_enrollments')
                ->whereIn('reg_number', $regNumbers)
                ->where('session_id', $sessionId)
                ->update([
                    'section_id' => $newSection,
                    'class_id'   => $newClass,
                    'arm_id'     => $newArm,
                    'updated_at' => now()
                ]);

            // Remove old subjects and assign new subjects based on arm
            foreach ($regNumbers as $reg) {

                // Delete previous subjects
                DB::table('student_subjects')
                    ->where('reg_number', $reg)
                    ->where('session_id', $sessionId)
                    ->delete();

                // Fetch the subjects for the new arm
                $armSubjectIds = ArmSubject::where('arm_id', $newArm)
                    ->pluck('subject_id');

                $insertData = [];

                foreach ($armSubjectIds as $subId) {
                    $insertData[] = [
                        'reg_number' => $reg,
                        'subject_id' => $subId,
                        'arm_id'     => $newArm,
                        'session_id' => $sessionId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }

                if (!empty($insertData)) {
                    DB::table('student_subjects')->insert($insertData);
                }
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => count($regNumbers) . ' student(s) moved successfully.'
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            \Log::error('Move Students Error: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while moving students. ' . $e->getMessage()
            ], 500);
        }
    }
}
