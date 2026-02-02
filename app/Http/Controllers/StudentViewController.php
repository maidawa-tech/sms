<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentViewController extends Controller
{
    public function index()
    {
        $sections = DB::table('sections')->orderBy('section_name')->get();
        return view('class_members.index', compact('sections'));
    }

    public function fetchStudents(Request $request)
    {
        $section_id = $request->section_id;
        $class_id   = $request->class_id;
        $arm_id     = $request->arm_id;

        // ============================================
        // 1. Prevent showing results unless all selected
        // ============================================
        if (empty($section_id) || empty($class_id) || empty($arm_id)) {
            return response()->json([
                "draw" => intval(request('draw')),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
                "total_class_students" => 0
            ]);
        }

        // ============================================
        // 2. Query to fetch class members
        // ============================================
        $query = DB::table('student_enrollments as se')
            ->join('students as s', 'se.reg_number', '=', 's.reg_number')
            ->join('sections as sec', 'se.section_id', '=', 'sec.section_id')
            ->join('classes as c', 'se.class_id', '=', 'c.class_id')
            ->join('arms as a', 'se.arm_id', '=', 'a.arm_id')
            ->join('academic_sessions as sess', 'se.session_id', '=', 'sess.session_id')
            ->select(
                'se.reg_number',
                DB::raw("CONCAT(s.surname, ' ', s.first_name, ' ', COALESCE(s.other_name, '')) AS full_name"),
                'sec.section_name',
                'c.class_name',
                'a.arm_name',
                'sess.session_name'
            )
            ->where('se.section_id', $section_id)
            ->where('se.class_id', $class_id)
            ->where('se.arm_id', $arm_id);

        // Total count
        $totalRecords = $query->count();

        // Ordering
        $columns = [
            "se.reg_number",
            "full_name",
            "sec.section_name",
            "c.class_name",
            "a.arm_name",
            "sess.session_name"
        ];

        $orderColumnIndex = request('order.0.column', 0);
        $orderDirection   = request('order.0.dir', 'asc');

        if (isset($columns[$orderColumnIndex])) {
            $query->orderByRaw($columns[$orderColumnIndex] . " " . $orderDirection);
        }

        // Pagination
        $start  = request('start', 0);
        $length = request('length', 10);

        $data = $query->skip($start)->take($length)->get();

        return response()->json([
            "draw" => intval(request('draw')),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $data,
            "total_class_students" => $totalRecords
        ]);
    }
}
