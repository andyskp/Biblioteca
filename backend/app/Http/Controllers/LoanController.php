<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LoanController extends Controller
{
    protected $file = 'data/loans.json';
    protected $booksFile = 'data/books.json';

    public function index()
    {
        $loans = $this->getLoans();
        return response()->json($loans, 200);
    }

    public function store(Request $request)
    {
        $loans = $this->getLoans();
        $books = $this->getBooks();

        $bookId = $request->input('book_id');
        $userId = $request->input('user_id');

        // Verificar disponibilidad
        $bookKey = collect($books)->search(fn($b) => $b['id'] == $bookId);

        if ($bookKey === false || $books[$bookKey]['available'] === false) {
            return response()->json(['error' => 'Libro no disponible o no existe.'], 400);
        }

        // Crear préstamo
        $newLoan = [
            'id'       => Str::uuid()->toString(),
            'user_id'  => $userId,
            'book_id'  => $bookId,
            'date'     => now()->toDateString(),
            'returned' => false
        ];

        $loans[] = $newLoan;

        // Marcar libro como no disponible
        $books[$bookKey]['available'] = false;

        // Guardar cambios
        $this->saveLoans($loans);
        $this->saveBooks($books);

        return response()->json($newLoan, 201);
    }

    public function show($id)
    {
        $loan = collect($this->getLoans())->firstWhere('id', $id);
        return $loan ? response()->json($loan, 200)
                     : response()->json(['error' => 'Préstamo no encontrado'], 404);
    }

    public function update(Request $request, $id)
    {
        $loans = $this->getLoans();
        $books = $this->getBooks();

        $index = collect($loans)->search(fn($l) => $l['id'] == $id);

        if ($index === false) {
            return response()->json(['error' => 'Préstamo no encontrado'], 404);
        }

        // Actualizar datos
        $loans[$index]['returned'] = $request->input('returned', $loans[$index]['returned']);

        // Si se devolvió el libro, actualizar disponibilidad
        if ($loans[$index]['returned']) {
            $bookKey = collect($books)->search(fn($b) => $b['id'] == $loans[$index]['book_id']);
            if ($bookKey !== false) {
                $books[$bookKey]['available'] = true;
            }
        }

        $this->saveLoans($loans);
        $this->saveBooks($books);

        return response()->json($loans[$index], 200);
    }

    public function destroy($id)
    {
        $loans = $this->getLoans();
        $filtered = array_filter($loans, fn($l) => $l['id'] != $id);

        if (count($loans) === count($filtered)) {
            return response()->json(['error' => 'Préstamo no encontrado'], 404);
        }

        $this->saveLoans(array_values($filtered));
        return response()->json(['message' => 'Préstamo eliminado'], 200);
    }

    private function getLoans()
    {
        return file_exists(storage_path("app/{$this->file}"))
            ? json_decode(file_get_contents(storage_path("app/{$this->file}")), true)
            : [];
    }

    private function saveLoans($loans)
    {
        file_put_contents(storage_path("app/{$this->file}"), json_encode($loans, JSON_PRETTY_PRINT));
    }

    private function getBooks()
    {
        return file_exists(storage_path("app/{$this->booksFile}"))
            ? json_decode(file_get_contents(storage_path("app/{$this->booksFile}")), true)
            : [];
    }

    private function saveBooks($books)
    {
        file_put_contents(storage_path("app/{$this->booksFile}"), json_encode($books, JSON_PRETTY_PRINT));
    }
}
