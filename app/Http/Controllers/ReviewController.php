<?php

namespace App\Http\Controllers;

use App\Models\Reviews;
use App\Http\Resources\ReviewResource;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;

class ReviewsController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'book_id' => 'required|exists:books,id',
                'rating' => 'required|integer|between:1,5',
                'comment' => 'required|string|max:255',
            ]);
            $review = Reviews::create([
                'book_id' => $validated['book_id'],
                'user_id' => Auth::user()->id,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'],
            ]);

            return new ReviewResource($review);
        } catch (Exception $e) {
            return response()->json(['message' => 'Server Error', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $review = Reviews::findOrFail($id);
            return new ReviewResource($review);
        } catch (Exception $e) {
            return response()->json(['message' => 'Server Error', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'rating' => 'required|integer|between:1,5',
                'comment' => 'required|string|max:255',
            ]);
            $review = Reviews::findOrFail($id);
            if ($review->user_id !== Auth::user()->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            $review->update($validated);
            return new ReviewResource($review);
        } catch (Exception $e) {
            return response()->json(['message' => 'Server Error', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $review = Reviews::findOrFail($id);
            if ($review->user_id !== Auth::user()->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            $review->delete();
            return response()->json(['message' => 'Review deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Server Error', 'error' => $e->getMessage()], 500);
        }
    }
}
