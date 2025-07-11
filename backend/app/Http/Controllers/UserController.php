<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    private $path = 'users.json';

    private function loadUsers()
    {
        if (!Storage::exists($this->path)) {
            return [];
        }

        return json_decode(Storage::get($this->path), true);
    }

    private function saveUsers($users)
    {
        Storage::put($this->path, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function index()
    {
        return response()->json($this->loadUsers());
    }

    public function store(Request $request)
    {
        $users = $this->loadUsers();

        $newUser = [
            'id' => count($users) ? max(array_column($users, 'id')) + 1 : 1,
            'name' => $request->input('name'),
            'email' => $request->input('email'),
        ];

        $users[] = $newUser;
        $this->saveUsers($users);

        return response()->json($newUser, 201);
    }

    public function show($id)
    {
        $users = $this->loadUsers();
        $user = collect($users)->firstWhere('id', $id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $users = $this->loadUsers();
        $updated = false;

        foreach ($users as &$user) {
            if ($user['id'] == $id) {
                $user['name'] = $request->input('name', $user['name']);
                $user['email'] = $request->input('email', $user['email']);
                $updated = true;
                break;
            }
        }

        if (!$updated) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $this->saveUsers($users);
        return response()->json(['message' => 'Usuario actualizado correctamente']);
    }

    public function destroy($id)
    {
        $users = $this->loadUsers();
        $filtered = array_filter($users, fn($u) => $u['id'] != $id);

        if (count($users) === count($filtered)) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $this->saveUsers(array_values($filtered));
        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }

    public function statistics()
{
    $loans = $this->getLoans();
    $books = $this->getBooks();

    $total = count($loans);
    $returned = collect($loans)->where('returned', true)->count();
    $active = $total - $returned;

    // Contar préstamos por libro
    $loanCounts = [];
    foreach ($loans as $loan) {
        $bookId = $loan['book_id'];
        if (!isset($loanCounts[$bookId])) {
            $loanCounts[$bookId] = 0;
        }
        $loanCounts[$bookId]++;
    }

    // Ordenar por cantidad de préstamos
    arsort($loanCounts);

    // Mapear los títulos
    $mostLoaned = [];
    foreach ($loanCounts as $bookId => $count) {
        $book = collect($books)->firstWhere('id', $bookId);
        if ($book) {
            $mostLoaned[] = [
                'book_id'      => $bookId,
                'title'        => $book['title'],
                'times_loaned' => $count
            ];
        }
    }

    return response()->json([
        'total_loans'       => $total,
        'active_loans'      => $active,
        'returned_loans'    => $returned,
        'most_loaned_books' => $mostLoaned
    ]);
}

}
