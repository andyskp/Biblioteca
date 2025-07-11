<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    private $path = 'books.json';

    private function loadBooks()
    {
        if (!Storage::exists($this->path)) {
            return [];
        }

        return json_decode(Storage::get($this->path), true);
    }

    private function saveBooks($books)
    {
        dd($books);
        Storage::put($this->path, json_encode($books, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    }

    public function index()
    {
        return response()->json($this->loadBooks());
    }

    public function store(Request $request)
    {
        $books = $this->loadBooks();

        $newBook = [
            'id' => count($books) ? max(array_column($books, 'id')) + 1 : 1,
            'title' => $request->input('title'),
            'author' => $request->input('author'),
            'genre' => $request->input('genre'),
            'available' => $request->input('available', true),
        ];

        $books[] = $newBook;
        $this->saveBooks($books);

        return response()->json($newBook, 201);
    }

    public function show($id)
    {
        $books = $this->loadBooks();
        $book = collect($books)->firstWhere('id', $id);

        if (!$book) {
            return response()->json(['message' => 'Libro no encontrado'], 404);
        }

        return response()->json($book);
    }

    public function update(Request $request, $id)
    {
        $books = $this->loadBooks();
        $updated = false;

        foreach ($books as &$book) {
            if ($book['id'] == $id) {
                $book['title'] = $request->input('title', $book['title']);
                $book['author'] = $request->input('author', $book['author']);
                $book['genre'] = $request->input('genre', $book['genre']);
                $book['available'] = $request->input('available', $book['available']);
                $updated = true;
                break;
            }
        }

        if (!$updated) {
            return response()->json(['message' => 'Libro no encontrado'], 404);
        }

        $this->saveBooks($books);
        return response()->json(['message' => 'Libro actualizado correctamente']);
    }

    public function destroy($id)
    {
        $books = $this->loadBooks();
        $filtered = array_filter($books, fn($book) => $book['id'] != $id);

        if (count($books) === count($filtered)) {
            return response()->json(['message' => 'Libro no encontrado'], 404);
        }

        $this->saveBooks(array_values($filtered));
        return response()->json(['message' => 'Libro eliminado correctamente']);
    }
}
