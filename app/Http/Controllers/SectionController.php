<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;

class SectionController extends Controller
{
    /**
     * Display all sections.
     */
    public function index()
    {
        $sections = Section::all();
        return view('sections.index', compact('sections'));
    }

    /**
     * Store a newly created section.
     */
    public function store(Request $request)
    {
        $request->validate([
            'section_name' => 'required|string|max:100|unique:sections,section_name',
            'section_short_name' => 'required|string|max:20|unique:sections,section_short_name',
        ]);

        Section::create($request->only('section_name', 'section_short_name'));

        return back()->with('success', 'Section added successfully.');
    }

    /**
     * Update an existing section.
     */
    public function update(Request $request, $id)
    {
        $section = Section::findOrFail($id);

        $request->validate([
            'section_name' => 'required|string|max:100|unique:sections,section_name,' . $section->section_id . ',section_id',
            'section_short_name' => 'required|string|max:20|unique:sections,section_short_name,' . $section->section_id . ',section_id',
        ]);

        $section->update($request->only('section_name', 'section_short_name'));

        return back()->with('success', 'Section updated successfully.');
    }

    /**
     * Delete a section.
     */
    public function destroy($id)
    {
        $section = Section::findOrFail($id);
        $section->delete();

        return back()->with('success', 'Section deleted successfully.');
    }
}
