<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TraitModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TraitController extends Controller
{
    public function index()
    {
        return view('admin.traits.index');
    }

    public function list()
    {
        return response()->json(
            TraitModel::orderBy('trait_type')->orderBy('trait_name')->get()
        );
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trait_name' => 'required|string|max:255',
            'trait_type' => 'required|in:affective,psychomotor',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        TraitModel::create($request->only('trait_name', 'trait_type'));

        return response()->json(['message' => 'Trait added successfully']);
    }

    public function update(Request $request, $id)
    {
        $trait = TraitModel::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'trait_name' => 'required|string|max:255',
            'trait_type' => 'required|in:affective,psychomotor',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $trait->update($request->only('trait_name', 'trait_type'));

        return response()->json(['message' => 'Trait updated successfully']);
    }

    public function toggle($id)
    {
        $trait = TraitModel::findOrFail($id);
        $trait->is_active = !$trait->is_active;
        $trait->save();

        return response()->json(['message' => 'Trait status updated']);
    }

    public function destroy($id)
    {
        TraitModel::findOrFail($id)->delete();
        return response()->json(['message' => 'Trait deleted']);
    }
}
