<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Arm;
use App\Models\ClassModel; // model for classes table
use Illuminate\Validation\Rule;

class ArmController extends Controller
{
    public function index()
    {
        $arms = Arm::with('class')->orderBy('arm_name', 'asc')->get();
        $classes = ClassModel::orderBy('class_name', 'asc')->get();

        return view('arms.index', compact('arms', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|integer',
            'arm_name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('arms')->where(function ($query) use ($request) {
                    return $query->where('class_id', $request->class_id);
                }),
            ],
        ], [
            'arm_name.unique' => 'This arm already exists for the selected class.',
        ]);

        Arm::create([
            'class_id' => $request->class_id,
            'arm_name' => trim($request->arm_name),
        ]);

        // global toast message
        return redirect()->back()->with('success', 'Arm added successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'class_id' => 'required|integer',
            'arm_name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('arms')->where(function ($query) use ($request) {
                    return $query->where('class_id', $request->class_id);
                })->ignore($id, 'arm_id'),
            ],
        ], [
            'arm_name.unique' => 'This arm already exists for the selected class.',
        ]);

        $arm = Arm::findOrFail($id);
        $arm->update([
            'class_id' => $request->class_id,
            'arm_name' => trim($request->arm_name),
        ]);

        // global toast message
        return redirect()->back()->with('success', 'Arm updated successfully!');
    }

    public function destroy($id)
    {
        $arm = Arm::findOrFail($id);
        $arm->delete();

        // global toast message
        return redirect()->back()->with('delete', 'Arm deleted successfully!');
    }
}
