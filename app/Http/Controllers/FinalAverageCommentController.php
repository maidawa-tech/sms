<?php

namespace App\Http\Controllers;

use App\Models\FinalAverageComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FinalAverageCommentController extends Controller
{
    public function index()
    {
        return view('final-average-comments.index');
    }

    public function fetch()
    {
        $comments = FinalAverageComment::orderBy('min_score')->get();

        return response()->json($comments);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'min_score' => 'required|numeric|min:0|max:100',
            'max_score' => 'required|numeric|min:0|max:100|gte:min_score',
            'comment'   => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        FinalAverageComment::create([
            'min_score' => $request->min_score,
            'max_score' => $request->max_score,
            'comment'   => $request->comment,
            'is_active' => true,
        ]);

        return response()->json([
            'type' => 'success',
            'message' => 'Final average comment added successfully.'
        ]);
    }

    public function update(Request $request, $id)
    {
        $comment = FinalAverageComment::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'min_score' => 'required|numeric|min:0|max:100',
            'max_score' => 'required|numeric|min:0|max:100|gte:min_score',
            'comment'   => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $comment->update($request->only('min_score', 'max_score', 'comment'));

        return response()->json([
            'type' => 'success',
            'message' => 'Final average comment updated successfully.'
        ]);
    }

    public function destroy($id)
    {
        FinalAverageComment::findOrFail($id)->delete();

        return response()->json([
            'type' => 'success',
            'message' => 'Final average comment deleted successfully.'
        ]);
    }

    public function toggleStatus($id)
    {
        $comment = FinalAverageComment::findOrFail($id);
        $comment->is_active = !$comment->is_active;
        $comment->save();

        return response()->json([
            'type' => 'success',
            'message' => 'Status updated successfully.'
        ]);
    }
}
