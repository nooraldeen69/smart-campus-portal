<?php

namespace App\Http\Controllers;

use App\Models\LibraryBook;
use Illuminate\Http\Request;

class AdminBookController extends Controller
{
    public function index()
    {
        $books = LibraryBook::withCount(['loans as active_loans_count' => fn ($q) => $q->whereNull('returned_at')])->orderBy('title')->get();
        return view('admin.books.index', compact('books'));
    }

    public function create()
    {
        return view('admin.books.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'            => 'required',
            'author'           => 'required',
            'available_copies' => 'required|integer|min:0',
        ]);

        LibraryBook::create($request->only('title', 'author', 'isbn', 'available_copies'));

        return redirect()->route('admin.books.index')->with('status', 'Book added.');
    }

    public function edit($id)
    {
        $book = LibraryBook::findOrFail($id);
        return view('admin.books.edit', compact('book'));
    }

    public function update(Request $request, $id)
    {
        $book = LibraryBook::findOrFail($id);

        $request->validate([
            'title'            => 'required',
            'author'           => 'required',
            'available_copies' => 'required|integer|min:0',
        ]);

        $book->update($request->only('title', 'author', 'isbn', 'available_copies'));

        return redirect()->route('admin.books.index')->with('status', 'Book updated.');
    }

    public function destroy($id)
    {
        LibraryBook::findOrFail($id)->delete();
        return redirect()->route('admin.books.index')->with('status', 'Book deleted.');
    }
}
