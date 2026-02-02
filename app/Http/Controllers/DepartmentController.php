<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments.
     */
    public function index()
    {
        $departments = Department::orderBy('department_name', 'asc')->get();
        return view('departments.index', compact('departments'));
    }

    /**
     * Store a newly created department.
     */
    public function store(Request $request)
    {
        $request->validate([
            'department_name' => 'required|string|max:100|unique:departments,department_name',
            'department_short_name' => 'required|string|max:20|unique:departments,department_short_name',
        ]);

        Department::create([
            'department_name' => $request->department_name,
            'department_short_name' => $request->department_short_name,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Department added successfully!');
    }

    /**
     * Update an existing department.
     */
    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        $request->validate([
            'department_name' => 'required|string|max:100|unique:departments,department_name,' . $id . ',department_id',
            'department_short_name' => 'required|string|max:20|unique:departments,department_short_name,' . $id . ',department_id',
        ]);

        $department->update([
            'department_name' => $request->department_name,
            'department_short_name' => $request->department_short_name,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Department updated successfully!');
    }

    /**
     * Remove a department.
     */
    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        $department->delete();

        return redirect()->back()->with('success', 'Department deleted successfully!');
    }
}
