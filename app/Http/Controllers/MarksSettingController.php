<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarksSetting;
use App\Models\Section;
use Illuminate\Validation\ValidationException;

class MarksSettingController extends Controller
{
    /**
     * Display the Marks Setting page with sections.
     */
    public function index()
    {
        $sections = Section::orderBy('section_name')->get();
        return view('marks_settings.index', compact('sections'));
    }

    /**
     * Fetch all marks settings for a given section (AJAX).
     */
    public function fetch($sectionId)
    {
        $settings = MarksSetting::where('section_id', $sectionId)
                        ->orderBy('id', 'desc')
                        ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $settings
        ]);
    }

    /**
     * Store a new marks setting.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'section_id' => 'required|exists:sections,section_id',
                'max_ca1'    => 'required|numeric|min:0|max:100',
                'max_ca2'    => 'required|numeric|min:0|max:100',
                'max_exam'   => 'required|numeric|min:0|max:100',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => implode(' ', $e->errors())
            ], 422);
        }

        // Prevent multiple settings per section
        if (MarksSetting::where('section_id', $validated['section_id'])->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'A marks setting for this section already exists.'
            ], 409);
        }

        $total = $validated['max_ca1'] + $validated['max_ca2'] + $validated['max_exam'];

        // Friendly total check
        if ($total > 100) {
            return response()->json([
                'status'  => 'error',
                'message' => "The total score of CA1 + CA2 + Exam is {$total}, which exceeds the allowed maximum of 100. Please adjust the values."
            ], 422);
        }

        MarksSetting::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Marks setting added successfully.'
        ], 201);
    }

    /**
     * Update an existing marks setting.
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'section_id' => 'required|exists:sections,section_id',
                'max_ca1'    => 'required|numeric|min:0|max:100',
                'max_ca2'    => 'required|numeric|min:0|max:100',
                'max_exam'   => 'required|numeric|min:0|max:100',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => implode(' ', $e->errors())
            ], 422);
        }

        $row = MarksSetting::findOrFail($id);

        // Prevent duplicate section_id for another record
        if (MarksSetting::where('section_id', $validated['section_id'])
                        ->where('id', '!=', $id)
                        ->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Another marks setting already exists for this section.'
            ], 409);
        }

        $total = $validated['max_ca1'] + $validated['max_ca2'] + $validated['max_exam'];

        if ($total > 100) {
            return response()->json([
                'status'  => 'error',
                'message' => "The total score of CA1 + CA2 + Exam is {$total}, which exceeds the allowed maximum of 100. Please adjust the values."
            ], 422);
        }

        $row->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Marks setting updated successfully.'
        ]);
    }

    /**
     * Delete a marks setting.
     */
    public function destroy($id)
    {
        MarksSetting::findOrFail($id)->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Marks setting deleted successfully.'
        ]);
    }
}
