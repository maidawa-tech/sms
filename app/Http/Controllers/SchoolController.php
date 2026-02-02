<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use Illuminate\Support\Facades\File;

class SchoolController extends Controller
{
    /**
     * Display the school form and details.
     */
    public function index()
    {
        $school = School::first();
        return view('school.index', compact('school'));
    }

    /**
     * Store or update the single school record.
     */
    public function store(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'motto' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $school = School::first();
        $data = $request->only(['school_name', 'motto', 'address']);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/school_logo'), $filename);
            $data['logo'] = $filename;

            // Delete old logo if it exists
            if ($school && $school->logo && File::exists(public_path('uploads/school_logo/' . $school->logo))) {
                File::delete(public_path('uploads/school_logo/' . $school->logo));
            }
        }

        // Create or update school record
        if ($school) {
            $school->update($data);
            return redirect()->route('school.index')
                ->with('success', 'School details updated successfully.');
        } else {
            School::create($data);
            return redirect()->route('school.index')
                ->with('success', 'School created successfully.');
        }
    }

    /**
     * Delete the school record and logo.
     */
    public function destroy()
    {
        $school = School::first();

        if (!$school) {
            return redirect()->route('school.index')
                ->with('error', 'No school record found.');
        }

        // Delete logo if exists
        if ($school->logo && File::exists(public_path('uploads/school_logo/' . $school->logo))) {
            File::delete(public_path('uploads/school_logo/' . $school->logo));
        }

        $school->delete();

        // Trigger delete success toast
        return redirect()->route('school.index')
            ->with('success', 'School deleted successfully.');
    }
}
