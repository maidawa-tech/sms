<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\ClassModel;
use App\Models\Arm;
use App\Models\Subject;
use App\Models\StudentEnrollment;
use App\Models\Result;
use App\Models\Term;
use App\Models\AcademicSession;
use Illuminate\Support\Facades\DB;

class BroadSheetController extends Controller
{
    public function index()
    {
        return view('broadsheet.index', [
            'sections'      => Section::all(),
            'activeTerm'    => Term::where('is_active', 1)->first(),
            'activeSession' => AcademicSession::where('is_active', 1)->first(),
        ]);
    }

    public function load(Request $request)
    {
        try {

            $request->validate([
                'section_id' => 'required|integer',
                'class_id'   => 'required|integer',
                'arm_id'     => 'required|integer',
                'term_id'    => 'required|integer',
                'session_id' => 'required|integer',
            ]);

            $sectionId = $request->section_id;
            $classId   = $request->class_id;
            $armId     = $request->arm_id;
            $termId    = $request->term_id;
            $sessionId = $request->session_id;

            /**
             * ==========================================================
             * 1️⃣ FETCH STUDENTS (with student profile)
             * ==========================================================
             */
            $students = StudentEnrollment::with('student')
                ->where([
                    'section_id' => $sectionId,
                    'class_id'   => $classId,
                    'arm_id'     => $armId,
                    'session_id' => $sessionId
                ])->get();

            if ($students->isEmpty()) {
                return response()->json([
                    'status'   => true,
                    'subjects' => [],
                    'students' => []
                ]);
            }

            $enrollmentIds = $students->pluck('id');


            /**
             * ==========================================================
             * 2️⃣ FETCH RESULTS FOR ALL STUDENTS (one query)
             * ==========================================================
             */
            $results = Result::whereIn('enrollment_id', $enrollmentIds)
                ->where('term_id', $termId)
                ->where('session_id', $sessionId)
                ->get()
                ->groupBy('enrollment_id');


            /**
             * ==========================================================
             * 3️⃣ FETCH SUBJECTS ASSIGNED TO ARM
             * ==========================================================
             */
            $subjectIds = DB::table('arm_subjects')
                ->where('arm_id', $armId)
                ->pluck('subject_id');

            $subjects = Subject::whereIn('subject_id', $subjectIds)
                ->orderBy('subject_name')
                ->get();

            // for frontend
            $subjectsFrontend = $subjects->map(fn($s) => (object)['subject' => $s]);


            /**
             * ==========================================================
             * 4️⃣ BUILD STUDENT BROADSHEET ROWS
             * ==========================================================
             */
            $rows = [];

            foreach ($students as $stu) {

                $stuResults = $results->get($stu->id) ?? collect();

                $scores = [];
                $total = 0;
                $countScores = 0;

                foreach ($subjects as $sub) {
                    $r = $stuResults->firstWhere('subject_id', $sub->subject_id);

                    if ($r && $r->total !== null) {
                        $scores[$sub->subject_id] = $r->total;
                        $total += $r->total;
                        $countScores++;
                    } else {
                        $scores[$sub->subject_id] = "-";
                    }
                }

                $avg = $countScores ? round($total / $countScores, 2) : 0;

                $rows[] = [
                    'enrollment_id' => $stu->id,
                    'reg'     => $stu->student->reg_number,
                    'name'    => $stu->student->surname . " " . $stu->student->first_name,
                    'scores'  => $scores,
                    'total'   => $total,
                    'avg'     => $avg,
                    'position'=> 0,
                ];
            }


            /**
             * ==========================================================
             * 5️⃣ RANKING (handles ties)
             * ==========================================================
             */
            $sorted = collect($rows)->sortByDesc('total')->values();
            $positioned = [];
            $pos = 1;

            foreach ($sorted as $i => $row) {

                if ($i > 0 && $row['total'] === $sorted[$i - 1]['total']) {
                    $row['position'] = $positioned[$i - 1]['position'];  // tie
                } else {
                    $row['position'] = $pos;
                }

                // update results table
                Result::where('enrollment_id', $row['enrollment_id'])
                    ->where('term_id', $termId)
                    ->where('session_id', $sessionId)
                    ->update([
                        'average'           => $row['avg'],
                        'overall_position'  => $row['position']
                    ]);

                $positioned[] = $row;
                $pos++;
            }


            /**
             * ==========================================================
             * 6️⃣ SORT FINAL OUTPUT BY REG NUMBER
             * ==========================================================
             */
            $final = collect($positioned)->sortBy('reg')->values()->all();


            return response()->json([
                'status'   => true,
                'subjects' => $subjectsFrontend,
                'students' => $final,
            ]);


        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 200); // always return 200 so AJAX .fail() never fires
        }
    }
}
