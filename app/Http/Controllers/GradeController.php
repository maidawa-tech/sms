<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GradeController extends Controller
{
    /**
     * Common validation rules for Grade creation/update.
     */
    private function validateGrade(Request $request)
    {
        return $request->validate([
            // Ensure section_id is present and valid
            'section_id'     => 'required|exists:sections,section_id', 
            'min_score'      => 'required|numeric|min:0',
            'max_score'      => 'required|numeric|min:0|gte:min_score',
            'grade_letter'   => 'required|string|max:5',
            'remark'         => 'required|string|max:100',
            // 'is_waec' is required and must be 0 or 1
            'is_waec'        => 'required|boolean', 
        ]);
    }

    /**
     * Display the grading index page.
     */
    public function index()
    {
        $sections = Section::orderBy('section_name')->get();
        return view('grades.index', compact('sections'));
    }

    /**
     * AJAX endpoint to fetch grades based on section and grading type.
     */
    public function fetchGrades(Request $request)
    {
        try {
            $request->validate([
                'section_id' => 'required|exists:sections,section_id',
                'type'       => 'required|in:custom,waec'
            ]);

            $section = Section::find($request->section_id);
            $isWaec = $request->type === 'waec';

            $grades = Grade::where('section_id', $section->section_id)
                          ->where('is_waec', $isWaec)
                          ->orderBy('min_score', 'DESC')
                          ->get();

            return response()->json([
                'success' => true,
                'grades' => $grades,
                'section_short_name' => $section->section_short_name,
                'grading_type' => $request->type,
                'message' => 'Grades fetched successfully' 
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed: ' . $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch grades: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created grade in storage (Standard Form Submission).
     */
    public function store(Request $request)
    {
        try {
            // --- NEW: INITIAL CHECK FOR SECTION SELECTION ---
            if (!$request->filled('selected_section')) {
                return redirect()->back()
                    ->with('error', 'Please select a section before adding a grade.')
                    ->withInput();
            }

            // Merge form data with is_waec from hidden input
            $request->merge([
                'section_id' => $request->selected_section,
                'is_waec' => (int) $request->is_waec
            ]);

            // Validate the grade data using existing helper
            $validated = $this->validateGrade($request);
            
            // Check if WAEC grading is being used for non-SSS section
            if ($validated['is_waec'] == 1) {
                $section = Section::find($validated['section_id']);
                if ($section && $section->section_short_name !== 'SSS') {
                    return redirect()->back()
                        ->with('error', 'WAEC grading can only be used for SSS section.')
                        ->withInput();
                }
            }

            // Create the grade
            Grade::create($validated);

            return redirect()->route('grades.index')
                ->with('success', 'Grade added successfully!');

        } catch (ValidationException $e) {
            return redirect()->back()
                ->with('error', 'Validation failed. Please check your inputs.')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while creating the grade.')
                ->withInput();
        }
    }

    /**
     * Update the specified grade in storage (AJAX Submission).
     */
    public function update(Request $request, $id)
    {
        try {
            $grade = Grade::findOrFail($id);
            
            if (!$request->has('is_waec')) {
                $request->merge(['is_waec' => 0]);
            }

            $request->merge(['is_waec' => (int) $request->is_waec]);

            $validated = $this->validateGrade($request);

            if ($validated['is_waec'] == 1) {
                $section = Section::find($validated['section_id']);
                if (!$section || $section->section_short_name !== 'SSS') {
                    return response()->json([
                        'success' => false, 
                        'error' => 'WAEC grading can only belong to SSS section.'
                    ], 403);
                }
            }

            $grade->update($validated);

            return response()->json([
                'success' => true, 
                'message' => 'Grade updated successfully!'
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false, 
                'error' => 'Validation failed. Please check your inputs.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'error' => 'An error occurred during update: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified grade from storage (AJAX Submission).
     */
    public function destroy($id)
    {
        try {
            $grade = Grade::findOrFail($id);
            $grade->delete();
            
            return response()->json([
                'success' => true, 
                'message' => 'Grade deleted successfully!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'error' => 'Could not delete grade: ' . $e->getMessage()
            ], 500);
        }
    }

    // ... getGradingPreference and setGradingPreference remain unchanged ...
    public function getGradingPreference(Request $request) { /* Existing code */ }
    public function setGradingPreference(Request $request) { /* Existing code */ }
}