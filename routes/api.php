<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\SectionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Authentication Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');


Route::middleware(['auth:sanctum', 'role:author'])->group(function (){

    // Get Collaborators
    Route::get('collaborators', [BookController::class, 'getAllCollaboratores']);

    // Book Routes
    Route::post('/books', [BookController::class, 'store']);
    Route::put('/books/{book}', [BookController::class, 'update']);
    Route::delete('/books/{book}', [BookController::class, 'destroy']);

    // Section Routes
    Route::get('/sections/{book}', [SectionController::class, 'index']);
    Route::post('/sections', [SectionController::class, 'store']);
    Route::delete('/sections/{section}', [SectionController::class, 'destroy']);

    // Collaborator Routes
    Route::post('/books/{book}/collaborators', [BookController::class, 'addCollaborator']);
    Route::delete('/books/{book}/collaborators/{user}', [BookController::class, 'removeCollaborator']);

});


Route::middleware(['auth:sanctum', 'role:author|collaborator'])->group(function (){
    // Get all books
    Route::get('/books', [BookController::class, 'index']);
    // Update section
    Route::put('/sections/{section}', [SectionController::class, 'update']);
});



