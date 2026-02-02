<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $students = Student::latest()->get();
            return response()->json(['data' => $students]);
        }

        return view('students.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reg_number'   => 'required|unique:students,reg_number,' . $request->student_id . ',student_id',
            'first_name'   => 'required|string|max:100',
            'surname'      => 'required|string|max:100',
            'other_name'   => 'nullable|string|max:100',
            'parent_phone' => 'required|string|max:20',
            'address'      => 'required|string',
            'passport'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except('passport');

        // Handle passport upload
        if ($request->hasFile('passport')) {
            $file = $request->file('passport');
            $filename = 'passport_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/students'), $filename);
            $data['passport'] = $filename;
        }

        $student = Student::updateOrCreate(
            ['student_id' => $request->student_id],
            $data
        );

        return response()->json([
            'success' => true,
            'message' => $request->student_id ? 'Student updated successfully!' : 'Student added successfully!',
            'student' => $student,
        ]);
    }

    // Fetch single student for edit
    public function show($id)
    {
        $student = Student::findOrFail($id);
        return response()->json($student);
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);

        if ($student->passport && file_exists(public_path('uploads/students/' . $student->passport))) {
            unlink(public_path('uploads/students/' . $student->passport));
        }

        $student->delete();

        return response()->json([
            'success' => true,
            'message' => 'Student deleted successfully!',
        ]);
    }
}
