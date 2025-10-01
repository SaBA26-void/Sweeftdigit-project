<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{

    public function index()
    {
        $authors = Author::withCount('books')
            ->orderBy('name')
            ->paginate(10);

        return view('authors.index', compact('authors'));
    }


    public function create()
    {
        return view('authors.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:authors,name',
        ]);

        Author::create($request->all());

        return redirect()->route('authors.index')
            ->with('success', 'Author created successfully.');
    }


    public function show(Author $author)
    {
        $author->load('books');
        return view('authors.show', compact('author'));
    }


    public function edit(Author $author)
    {
        return view('authors.edit', compact('author'));
    }


    public function update(Request $request, Author $author)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:authors,name,' . $author->id,
        ]);

        $author->update($request->all());

        return redirect()->route('authors.index')
            ->with('success', 'Author updated successfully.');
    }


    public function destroy(Author $author)
    {
        if ($author->books()->count() > 0) {
            return redirect()->route('authors.index')
                ->with('error', 'Cannot delete author with existing books. Please remove all books first.');
        }

        $author->delete();

        return redirect()->route('authors.index')
            ->with('success', 'Author deleted successfully.');
    }
}
