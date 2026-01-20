<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category') && $request->category !== 'Semua') {
            $query->where('category', $request->category);
        }

        // Add average rating to the query
        $books = $query->withAvg('reviews', 'rating')->get();

        // Map to include average_rating attribute
        $books->transform(function ($book) {
            $book->average_rating = $book->reviews_avg_rating ? round($book->reviews_avg_rating, 1) : 0;
            unset($book->reviews_avg_rating);
            return $book;
        });

        return response()->json($books);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'stock' => 'required|integer|min:0',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'book_file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->all();

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('covers', 'public');
            $data['cover_image'] = asset('storage/' . $path);
        }

        if ($request->hasFile('book_file')) {
            $path = $request->file('book_file')->store('books', 'public');
            $data['book_file'] = asset('storage/' . $path);
        }

        $book = Book::create($data);

        return response()->json($book, 201);
    }

    public function show(Book $book)
    {
        $book->load(['reviews' => function($query) {
            $query->orderBy('created_at', 'desc');
        }, 'reviews.user:id,name,profile_photo']);
        
        $book->average_rating = $book->reviews()->avg('rating');
        return response()->json($book);
    }

    public function update(Request $request, Book $book)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'author' => 'sometimes|string|max:255',
            'stock' => 'sometimes|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $book->update($request->all());

        return response()->json($book);
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return response()->json(['message' => 'Book deleted successfully']);
    }
}
