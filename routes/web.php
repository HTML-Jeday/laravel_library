<?php

use App\Http\Controllers\AuthorsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BooksController;

Route::get('/books', [BooksController::class, 'index']);
Route::get('/books/data', [BooksController::class, 'data']);
Route::post('/books', [BooksController::class, 'store']);
Route::put('/books/{book}', [BooksController::class, 'update']);
Route::delete('/books/{book}', [BooksController::class, 'delete']);

Route::get('/authors', [AuthorsController::class, 'index']);
Route::get('/authors/data', [AuthorsController::class, 'data']);
Route::post('/authors', [AuthorsController::class, 'store']);
Route::put('/authors/{author}', [AuthorsController::class, 'update']);
Route::delete('/authors/{author}', [AuthorsController::class, 'delete']);
