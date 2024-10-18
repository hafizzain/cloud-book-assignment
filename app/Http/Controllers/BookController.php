<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use FFI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    // Get all books for authenticate user
    public function index()
    {
        $user = auth()->user();
        $cacheKey = 'books_user_{$user->id}';
        $books = Cache::remember($cacheKey, 60, function() use($user){
           return Book::with('sections.subsections.childSubsections', 'collaborators')->where('author_id', $user->id)
                     ->orWhereHas('collaborators', function($query) use($user){
                      $query->where('user_id', $user->id);
                     })->get();
        });
        
        return response()->json(['message' => 'success', 'books' => $books, 'status' => 200],200);

    }

    // Create a book
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:191',
        ]);

        $book = Book::create([
            'title' => $request->title,
            'author_id' => auth()->id()
        ]);

       return response()->json(['message' => 'Book created successfully.', 'book' => $book, 'status' => 201], 201);
        
    }

      // Update the existing book
      public function update(Request $request, Book $book)
      {
          $request->validate([
              'title' => 'required|string|max:191',
          ]);
  
          if($book?->author_id !== auth()->id())
          {
              return response()->json(['message' => 'unauthorized', 'status' => 403], 403);
          }
  
          $book->update($request->only('title'));
  
          return response()->json(['message' => 'Book updated successfully.', 'book' => $book, 'status' => 200], 200);
      }
  
    // Delete existing book
    public function destroy(Book $book)
    {
        if($book->author_id !== auth()->id())
        {
            return response()->json(['message' => 'unauthorized', 'status' => 403], 403);
        }
        
        $book->delete();

        return response()->json(['message' => 'Book deleted successfully.', 'status' => 200], 200);
    }

    public function getAllCollaboratores()
    {
        $cacheKey = 'collaborators';
        $collaborators = Cache::remember($cacheKey, 60, function(){
            return User::role('collaborator')->get(['id', 'name', 'email']);
        });
        return response()->json(['message' => 'success', 'collaborators' => $collaborators,  'status' => 200], 200);
    }

    // Add collaborator to a book
    public function addCollaborator(Request $request, Book $book)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'permission' => 'required|in:read,write',
        ]);

        if($book->author_id !== auth()->id())
        {
            return response()->json(['message' => 'unauthorized', 'status' => 403], 403);
        }

        $book->collaborators()->attach($request->user_id, ['permission' => $request->permission]);

        return response()->json(['message' => 'Collaborator added.', 'status' => 200], 200);
    }

    // Remove a collaborator from a book
    public function removeCollaborator(Book $book, User $user)
    {
        if($book->author_id !== auth()->id())
        {
            return response()->json(['message' => 'unauthorized', 'status' => 403], 403);
        }

        $book->collaborators()->detach($user->id);

        return response()->json(['message' => 'Collaborator removed.', 'status' => 200], 200);
    }
}
