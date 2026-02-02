<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\Section;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    /**
     * Display a listing of the classes.
     */
    public function index()
    {
        $sections = Section::all();
        $classes = ClassModel::with('section')->orderBy('class_name')->get();

        return view('classes.index', compact('sections', 'classes'));
    }

    /**
     * Store a newly created class in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,section_id',
            'class_name' => 'required|string|max:100',
            'class_short_name' => 'required|string|max:20',
        ]);

        // Check for duplicates within the same section
        $exists = ClassModel::where('section_id', $request->section_id)
            ->where('class_name', $request->class_name)
            ->exists();

        if ($exists) {
            return redirect()->route('classes.index')->with('error', 'This class already exists in the selected section.');
        }

        ClassModel::create($request->only(['section_id', 'class_name', 'class_short_name']));

        return redirect()->route('classes.index')->with('success', 'Class added successfully.');
    }

    /**
     * Update the specified class.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,section_id',
            'class_name' => 'required|string|max:100',
            'class_short_name' => 'required|string|max:20',
        ]);

        $class = ClassModel::findOrFail($id);

        // Prevent duplicate class name within same section (excluding current one)
        $exists = ClassModel::where('section_id', $request->section_id)
            ->where('class_name', $request->class_name)
            ->where('class_id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->route('classes.index')->with('error', 'Another class with this name already exists in the selected section.');
        }

        $class->update($request->only(['section_id', 'class_name', 'class_short_name']));

        return redirect()->route('classes.index')->with('success', 'Class updated successfully.');
    }

    /**
     * Remove the specified class from storage.
     */
    public function destroy($id)
    {
        $class = ClassModel::findOrFail($id);
        $class->delete();

        // Use 'success' to trigger the global dashboard toast
        return redirect()->route('classes.index')->with('success', 'Class deleted successfully.');
    }
}
