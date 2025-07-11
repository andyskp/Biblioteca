<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LoanController extends Controller
{
    private $path = 'loans.json';

    private function loadLoans()
    {
        if (!Storage::exists($this->path)) {
            return [];
        }

        return json_decode(Storage::get($this->path), true);
    }

    private function saveLoans($loans)
    {
        Storage::put($this->path, json_encode($loans, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function index()
    {
        return response()->json($this->loadLoans());
    }

    public function store(Request $request)
    {
        $loans = $this->loadLoans();

        $newLoan = [
            'id' => count($loans) ? max(array_column($loans, 'id')) + 1 : 1,
            'book_id' => $request->input('book_id'),
            'user_name' => $request->input('user_name'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'returned' => $request->input('returned', false),
        ];

        $loans[] = $newLoan;
        $this->saveLoans($loans);

        return response()->json($newLoan, 201);
    }

    public function show($id)
    {
        $loans = $this->loadLoans();
        $loan = collect($loans)->firstWhere('id', $id);

        if (!$loan) {
            return response()->json(['message' => 'Préstamo no encontrado'], 404);
        }

        return response()->json($loan);
    }

    public function update(Request $request, $id)
    {
        $loans = $this->loadLoans();
        $updated = false;

        foreach ($loans as &$loan) {
            if ($loan['id'] == $id) {
                $loan['book_id'] = $request->input('book_id', $loan['book_id']);
                $loan['user_name'] = $request->input('user_name', $loan['user_name']);
                $loan['start_date'] = $request->input('start_date', $loan['start_date']);
                $loan['end_date'] = $request->input('end_date', $loan['end_date']);
                $loan['returned'] = $request->input('returned', $loan['returned']);
                $updated = true;
                break;
            }
        }

        if (!$updated) {
            return response()->json(['message' => 'Préstamo no encontrado'], 404);
        }

        $this->saveLoans($loans);
        return response()->json(['message' => 'Préstamo actualizado correctamente']);
    }

    public function destroy($id)
    {
        $loans = $this->loadLoans();
        $filtered = array_filter($loans, fn($loan) => $loan['id'] != $id);

        if (count($loans) === count($filtered)) {
            return response()->json(['message' => 'Préstamo no encontrado'], 404);
        }

        $this->saveLoans(array_values($filtered));
        return response()->json(['message' => 'Préstamo eliminado correctamente']);
    }

    public function statistics()
    {
        $loans = $this->loadLoans();

        $total = count($loans);
        $returned = count(array_filter($loans, fn($l) => $l['returned']));
        $active = $total - $returned;

        return response()->json([
            'total_loans' => $total,
            'returned_loans' => $returned,
            'active_loans' => $active,
        ]);
    }
}
