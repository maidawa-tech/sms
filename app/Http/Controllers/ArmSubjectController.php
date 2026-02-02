<?php

namespace App\Http\Controllers;

use App\Models\Arm;
use App\Models\Subject;
use App\Models\ArmSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArmSubjectController extends Controller
{
    public function index()
    {
        $arms = Arm::orderBy('arm_name')->get();
        $subjects = Subject::orderBy('subject_name')->get();

        if (request()->ajax()) {
            $data = ArmSubject::with(['arm', 'subject'])
                ->orderByDesc('created_at')
                ->get();

            return response()->json(['data' => $data]);
        }

        return view('arm_subjects.index', compact('arms', 'subjects'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'arm_id'     => 'required|exists:arms,arm_id',
            'subject_id' => 'required|exists:subjects,subject_id',
            'teacher_id' => 'nullable|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        ArmSubject::updateOrCreate(
            ['id' => $request->id],
            $request->only('arm_id', 'subject_id', 'teacher_id')
        );

        return response()->json([
            'success' => true,
            'message' => $request->id ? 'Updated successfully!' : 'Assigned successfully!'
        ]);
    }

    public function show($id)
    {
        $record = ArmSubject::findOrFail($id);
        return response()->json($record);
    }

    public function destroy($id)
    {
        ArmSubject::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Record deleted successfully!'
        ]);
    }
}
