<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Author;
use Illuminate\Http\Request;

class BookController extends Controller
{

    public function index(Request $request)
    {
        $query = Book::with('authors');

        if ($request->filled('title')) {
            $query->searchByTitle($request->title);
        }

        if ($request->filled('author')) {
            $query->searchByAuthor($request->author);
        }

        if ($request->filled('status')) {
            $query->status($request->status);
        }

        $books = $query->orderBy('title')->paginate(10);

        return view('books.index', compact('books'));
    }


    public function create()
    {
        $authors = Author::orderBy('name')->get();
        return view('books.create', compact('authors'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'publication_year' => 'required|integer|min:1000|max:' . date('Y'),
            'status' => 'required|in:Available,Borrowed',
            'authors' => 'required|array|min:1',
            'authors.*' => 'exists:authors,id',
        ]);

        $book = Book::create([
            'title' => $request->title,
            'publication_year' => $request->publication_year,
            'status' => $request->status,
        ]);

        $book->authors()->attach($request->authors);

        return redirect()->route('books.index')
            ->with('success', 'Book created successfully.');
    }


    public function show(Book $book)
    {
        $book->load('authors');
        return view('books.show', compact('book'));
    }


    public function edit(Book $book)
    {
        $authors = Author::orderBy('name')->get();
        $book->load('authors');
        return view('books.edit', compact('book', 'authors'));
    }


    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'publication_year' => 'required|integer|min:1000|max:' . date('Y'),
            'status' => 'required|in:Available,Borrowed',
            'authors' => 'required|array|min:1',
            'authors.*' => 'exists:authors,id',
        ]);

        $book->update([
            'title' => $request->title,
            'publication_year' => $request->publication_year,
            'status' => $request->status,
        ]);

        $book->authors()->sync($request->authors);

        return redirect()->route('books.index')
            ->with('success', 'Book updated successfully.');
    }


    public function destroy(Book $book)
    {
        $book->delete();

        return redirect()->route('books.index')
            ->with('success', 'Book deleted successfully.');
    }
}
