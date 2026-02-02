<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return response()->json(['data' => Subject::latest()->get()]);
        }

        return view('subjects.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_name' => 'required|string|max:100',
            'short_name'   => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $subject = Subject::updateOrCreate(
            ['subject_id' => $request->subject_id],
            $request->only('subject_name', 'short_name')
        );

        return response()->json([
            'success' => true,
            'message' => $request->subject_id ? 'Subject updated successfully!' : 'Subject added successfully!',
            'data' => $subject
        ]);
    }

    public function show($id)
    {
        $subject = Subject::findOrFail($id);
        return response()->json($subject);
    }

    public function destroy($id)
    {
        Subject::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subject deleted successfully!'
        ]);
    }
}
