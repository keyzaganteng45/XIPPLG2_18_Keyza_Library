<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    public function index()
    {
        try {
            $books = Book::all();
            return response()->json($books, 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'writer' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'publisher' => 'nullable|string|max:255',
                'year' => 'nullable|date_format:Y',
            ]);

            $book = Book::create([
                'title' => $validated['title'],
                'writer' => $validated['writer'],
                'category_id' => $validated['category_id'],
                'publisher' => $validated['publisher'] ?? null,
                'year' => $validated['year'] ?? null,
                'user_id' => Auth::user()->id,
            ]);

            return response()->json($book, 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $book = Book::findOrFail($id);
            return response()->json($book, 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $book = Book::findOrFail($id);
            $book->update($request->all());
            return response()->json($book, 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $book = Book::findOrFail($id);
            $book->delete();
            return response()->json(['message' => 'Book deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
