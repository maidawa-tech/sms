<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\AcademicSession;
use App\Models\Term;
use App\Models\Section;
use App\Models\ClassModel;
use App\Models\Arm;
use App\Models\MarksSetting;

class MarksEntryController extends Controller
{
    /* ===============================
     * MARKS ENTRY PAGE
     * =============================== */
    public function index()
    {
        return view('marks_entry.index', [
            'sections'      => Section::orderBy('section_name')->get(),
            'sessions'      => AcademicSession::orderBy('session_name', 'desc')->get(),
            'terms'         => Term::orderBy('term_name')->get(),
            'activeSession' => AcademicSession::where('is_active', 1)->first(),
            'activeTerm'    => Term::where('is_active', 1)->first(),
        ]);
    }

    /* ===============================
     * AJAX LOADERS
     * =============================== */
    public function getClasses($section_id)
    {
        return response()->json(
            ClassModel::where('section_id', $section_id)
                ->orderBy('class_name')
                ->get()
        );
    }

    public function getArms($class_id)
    {
        return response()->json(
            Arm::where('class_id', $class_id)
                ->orderBy('arm_name')
                ->get()
        );
    }

    public function getSubjects(Request $request)
    {
        if (!$request->arm_id) {
            return response()->json([]);
        }

        return response()->json(
            DB::table('arm_subjects as a')
                ->join('subjects as s', 'a.subject_id', '=', 's.subject_id')
                ->where('a.arm_id', $request->arm_id)
                ->orderBy('s.subject_name')
                ->select('s.subject_id', 's.subject_name')
                ->get()
        );
    }

    /* ===============================
     * GRADE CALCULATION
     * =============================== */
    public function getGrade(Request $request)
    {
        $total = (float) $request->total;

        if (!$request->section_id) {
            return response()->json(['grade' => '', 'remark' => '']);
        }

        $grade = DB::table('grades')
            ->where('section_id', $request->section_id)
            ->where('min_score', '<=', $total)
            ->where('max_score', '>=', $total)
            ->first();

        return response()->json([
            'grade'  => $grade->grade_letter ?? '',
            'remark' => $grade->remark ?? '',
        ]);
    }

    /* ===============================
     * LOAD STUDENTS
     * =============================== */
    public function loadStudents(Request $request)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,section_id',
            'class_id'   => 'required',
            'arm_id'     => 'required',
            'term_id'    => 'required',
            'session_id' => 'required',
            'subject_id' => 'required',
        ]);

        $marksSetting = MarksSetting::where('section_id', $request->section_id)->first();

        if (!$marksSetting) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Marks setting not configured for this section.'
            ], 422);
        }

        $students = DB::table('student_enrollments as se')
            ->join('students as s', 'se.reg_number', '=', 's.reg_number')
            ->leftJoin('results as r', function ($j) use ($request) {
                $j->on('r.enrollment_id', '=', 'se.id')
                  ->where('r.subject_id', $request->subject_id)
                  ->where('r.term_id', $request->term_id)
                  ->where('r.session_id', $request->session_id);
            })
            ->where([
                'se.section_id' => $request->section_id,
                'se.class_id'   => $request->class_id,
                'se.arm_id'     => $request->arm_id,
                'se.session_id' => $request->session_id,
            ])
            ->orderBy('s.surname')
            ->select(
                'se.id as enrollment_id',
                'se.reg_number',
                DB::raw("CONCAT(s.surname,' ',s.first_name,' ',COALESCE(s.other_name,'')) AS full_name"),
                'r.ca1','r.ca2','r.exam','r.total','r.grade','r.remark'
            )
            ->get();

        return response()->json([
            'status'       => 'success',
            'students'     => $students,
            'marksSetting' => $marksSetting
        ]);
    }

    /* ===============================
     * SAVE RESULTS
     * =============================== */
    public function saveResults(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'section_id' => 'required|exists:sections,section_id',
            'subject_id' => 'required',
            'class_id'   => 'required',
            'arm_id'     => 'required',
            'term_id'    => 'required',
            'session_id' => 'required',
            'entries'    => 'required|array|min:1',
            'entries.*.enrollment_id' => 'required',
            'entries.*.reg_number'    => 'required',
            'entries.*.ca1'  => 'nullable|numeric|min:0',
            'entries.*.ca2'  => 'nullable|numeric|min:0',
            'entries.*.exam' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['status'=>'error','message'=>$validator->errors()->first()],422);
        }

        $limits = MarksSetting::where('section_id', $request->section_id)->first();

        DB::transaction(function () use ($request, $limits) {

            foreach ($request->entries as $row) {

                $ca1   = min((float)($row['ca1'] ?? 0), $limits->max_ca1);
                $ca2   = min((float)($row['ca2'] ?? 0), $limits->max_ca2);
                $exam  = min((float)($row['exam'] ?? 0), $limits->max_exam);
                $total = $ca1 + $ca2 + $exam;

                $grade = DB::table('grades')
                    ->where('section_id', $request->section_id)
                    ->where('min_score','<=',$total)
                    ->where('max_score','>=',$total)
                    ->first();

                DB::table('results')->updateOrInsert(
                    [
                        'enrollment_id' => $row['enrollment_id'],
                        'subject_id'    => $request->subject_id,
                        'term_id'       => $request->term_id,
                        'session_id'    => $request->session_id,
                    ],
                    [
                        'reg_number' => $row['reg_number'],
                        'ca1'        => $ca1,
                        'ca2'        => $ca2,
                        'exam'       => $exam,
                        'total'      => $total,
                        'grade'      => $grade->grade_letter ?? null,
                        'remark'     => $grade->remark ?? null,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }

            // Subject ranking
            $this->rankSubject(
                $request->subject_id,
                $request->class_id,
                $request->arm_id,
                $request->term_id,
                $request->session_id
            );

            // Overall average & position
            $this->calculateAverageAndOverallPosition(
                $request->class_id,
                $request->arm_id,
                $request->term_id,
                $request->session_id
            );
        });

        return response()->json(['status'=>'success','message'=>'Results saved successfully']);
    }

    /* ===============================
     * SUBJECT RANKING
     * =============================== */
    private function rankSubject($subject_id, $class_id, $arm_id, $term_id, $session_id)
    {
        $rows = DB::table('results as r')
            ->join('student_enrollments as se', 'se.id', '=', 'r.enrollment_id')
            ->where([
                'r.subject_id' => $subject_id,
                'r.term_id'    => $term_id,
                'r.session_id' => $session_id,
                'se.class_id'  => $class_id,
                'se.arm_id'    => $arm_id,
            ])
            ->orderByDesc('r.total')
            ->select('r.id', 'r.total')
            ->get();

        $rank = 1;
        $previous = null;

        foreach ($rows as $index => $row) {
            if ($previous !== null && $row->total < $previous) {
                $rank = $index + 1;
            }

            DB::table('results')
                ->where('id', $row->id)
                ->update(['position_in_subject' => $rank]);

            $previous = $row->total;
        }
    }

    /* ===============================
     * AVERAGE & OVERALL POSITION
     * MySQL 8 WINDOW FUNCTION
     * =============================== */
    private function calculateAverageAndOverallPosition($class_id, $arm_id, $term_id, $session_id)
    {
        DB::statement("
            UPDATE results r
            JOIN (
                SELECT 
                    r.enrollment_id,
                    ROUND(AVG(r.total), 2) AS avg_score,
                    DENSE_RANK() OVER (ORDER BY AVG(r.total) DESC) AS overall_pos
                FROM results r
                INNER JOIN student_enrollments se 
                    ON se.id = r.enrollment_id
                WHERE r.term_id = ?
                  AND r.session_id = ?
                  AND se.class_id = ?
                  AND se.arm_id = ?
                GROUP BY r.enrollment_id
            ) x ON x.enrollment_id = r.enrollment_id
            SET 
                r.average = x.avg_score,
                r.overall_position = x.overall_pos
            WHERE r.term_id = ?
              AND r.session_id = ?
        ", [
            $term_id,
            $session_id,
            $class_id,
            $arm_id,
            $term_id,
            $session_id
        ]);
    }
}
