<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Section;
use App\Models\SubSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SubSectionController extends Controller
{
     // Get section for the specific book
     public function index(Book $book)
     {
         $cacheKey = 'sub_section_book_{$book->id}';
         $subSections = Cache::remember($cacheKey, 60, function() use($book){
             return $book->subSections;
         });
         return response()->json(['message' => 'success', 'sub_sections' => $subSections, 'status' => 200],200);
     }
 
     // Create a new Sub section
     public function store(Request $request)
     {
         $request->validate([
             'title' => 'required|string|max:191',
             'content' => 'required|string',
             'book_id' => 'required|exists:books,id',
             'section_id' => 'required|exists:sections,id',
             'parent_subsection_id' => 'nullable|exists:sub_sections,id'
         ]);
 
         $book = Book::find($request->book_id);
         $section = Section::findOrFail($request->section_id);
 
         if($book->author_id !== auth()->id())
         {
             return response()->json(['message' => 'unauthorized', 'status' => 403], 403);
         }

         $subsection = SubSection::create([
            'title' => $request->title,
            'content' => $request->content,
            'book_id' => $book->id,
            'section_id' => $section->id,
            'parent_subsection_id' => $request->parent_subsection_id
        ]);
 
        return response()->json(['message' => 'Sub Section added successfully.', 'sub_section' => $subsection, 'status' => 201], 201);
     }
 
     // Update the existing Sub section
     public function update(Request $request, SubSection $subSection)
     {
         $request->validate([
             'title' => 'required|string|max:191',
             'content' => 'required|string',
         ]);
 
         if($subSection->book->author_id !== auth()->id() && !$subSection->book->collaborators()->where('user_id', auth()->id())->exists())
         {
             return response()->json(['message' => 'unauthorized', 'status' => 403], 403);
         }
 
         $subSection->update($request->only('title', 'content'));
 
         return response()->json(['message' => 'Sub Section updated successfully.', 'sub_section' => $subSection, 'status' => 200], 200);
     }
 
     // Delete a Sub section
     public function destroy(SubSection $subSection)
     {
         if($subSection->book->author_id !== auth()->id())
         {
             return response()->json(['message' => 'unauthorized', 'status' => 403], 403);
         }
     
         $subSection->delete();
 
         return response()->json(['message' => 'Sub Section deleted successfully.', 'status' => 200], 200);
     }
}
