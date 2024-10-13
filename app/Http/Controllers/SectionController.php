<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    // Get section for the specific book
    public function index(Book $book)
    {
        return response()->json(['message' => 'success', 'sections' => $book->sections, 'status' => 200],200);
    }

    // Create a new section
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:191',
            'content' => 'required|string',
            'book_id' => 'required|exists:books,id',
        ]);

        $book = Book::find($request->book_id);

        if($book->author_id !== auth()->id())
        {
            return response()->json(['message' => 'unauthorized', 'status' => 403], 403);
        }

        $section = Section::create([
            'title' => $request->title,
            'content' => $request->content,
            'book_id' => $request->book_id
        ]);

       return response()->json(['message' => 'Section added successfully.', 'section' => $section, 'status' => 201], 201);
    }

    // Update the existing section
    public function update(Request $request, Section $section)
    {
        $request->validate([
            'title' => 'required|string|max:191',
            'content' => 'required|string',
        ]);

        if($section?->book?->author_id !== auth()->id() && !$section?->book->collaborators()->where('user_id', auth()->id())->exists())
        {
            return response()->json(['message' => 'unauthorized', 'status' => 403], 403);
        }

        $section->update($request->only('title', 'content'));

        return response()->json(['message' => 'Section updated successfully.', 'section' => $section, 'status' => 200], 200);
    }

    // Delete a section
    public function destroy(Section $section)
    {
        if($section?->book?->author_id !== auth()->id())
        {
            return response()->json(['message' => 'unauthorized', 'status' => 403], 403);
        }
    
        $section->delete();

        return response()->json(['message' => 'Section deleted successfully.', 'status' => 200], 200);
    }
}
