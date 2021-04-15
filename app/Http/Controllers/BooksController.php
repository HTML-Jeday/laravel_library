<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBookRequest;
use App\Http\Requests\UpdateBookRequest;
use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BooksController extends Controller
{
    public function index()
    {
        return view('books');
    }
    public function data()
    {
        $books = Book::with('authors')->paginate(15);

        return response()->json(['books' => $books]);
    }
    public function store(CreateBookRequest $request)
    {
        $validated = $request->validated();
        if ($request->hasFile('image')) {
            $image = $validated['image'];
            $path = Storage::disk('local')->put('public/images', $image);
            $string = explode('/', $path);
            $filename = $string[2];
        }
        $book = Book::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
            'image' => $filename ?? 'book.jpg',
            'created_at' => $validated['date'] ?? ''
        ]);
        $authors = json_decode($validated['authors']);
        if (empty($authors)) {
            return response()->json(['errors' => ['name' => ['Choose author']]], Response::HTTP_BAD_REQUEST);
        }
        $insertArray = [];
        foreach ($authors as $author) {
            array_push($insertArray, ['author_id' => $author->id, 'book_id' => $book->id]);
        }
        DB::table('author_book')->insertOrIgnore($insertArray);
        
        return response()->json(['book' => $book, 'authors' => $book->authors]);
    }
    public function update(UpdateBookRequest $request, Book $book)
    {
        $validated = $request->validated();
        if ($request->hasFile('image')) {
            $image = $validated['image'];
            $path = Storage::disk('local')->put('public/images', $image);
            $string = explode('/', $path);
            $filename = $string[2];
        }
        $book->update([
            'name' => $validated['name'] ?? $book->name,
            'description' => $validated['description'] ?? $book->description,
            'image' => $filename ?? $book->image,
            'date'=> $validated['date'] ?? $book->date,
        ]);

        $authors = json_decode($validated['authors']);
        if (empty($authors)) {
            return response()->json(['errors' => ['name' => ['Choose author']]], Response::HTTP_BAD_REQUEST);
        }
        DB::table('author_book')->where('book_id', '=', $book->id)->delete();
        $insertArray = [];
        foreach ($authors as $author) {
            array_push($insertArray, ['author_id' => $author->id, 'book_id' => $book->id]);
        }
        DB::table('author_book')->insertOrIgnore($insertArray);
        
        return response()->json(['authors' => $book->authors, 'book' => $book]);
    }
    public function delete(Book $book)
    {
        
        $authors = $book->authors;
        if (!empty($authors)) {
            foreach ($authors as $author) {
                DB::table('author_book')
                ->where('author_id', '=', $author->id)
                ->where('book_id', '=', $book->id)->delete();
            }
        }
        $book->delete();
    }
}
