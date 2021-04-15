<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use Illuminate\Http\Request;
use App\Models\Author;
use Illuminate\Http\JsonResponse;

class AuthorsController extends Controller
{
    public function index()
    {
        return view('authors');
    }
    public function data()
    {
        $authors = Author::paginate(15);
        return response()->json(['authors' => $authors]);
    }
    public function store(CreateAuthorRequest $request)
    {
        $author = $request->validated();
        $newAuthor = Author::create([
            'firstName' => $author['firstName'],
            'secondName' => $author['secondName'],
            'middleName' => $author['middleName'],
        ]);
        return response()->json(['author' => $newAuthor]);
    }
    public function update(Author $author, UpdateAuthorRequest $request)
    {
        $validated = $request->validated();
        $author->update([
            'firstName' => $validated['firstName'] ?? $author->firstName,
            'secondName' => $validated['secondName'] ?? $author->secondName,
            'middleName' => $validated['middleName']?? $author->middleName
        ]);
        
        return response()->json(['author' => $author]);
    }
    public function delete(Author $author)
    {
        $author->delete();

        return response()->json(['author' => $author]);
    }
}
