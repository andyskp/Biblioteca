<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            [
                'id' => 1,
                'title' => 'Cien años de soledad',
                'author' => 'Gabriel García Márquez',
                'genre' => 'Realismo mágico',
                'available' => true,
            ],
            [
                'id' => 2,
                'title' => '1984',
                'author' => 'George Orwell',
                'genre' => 'Distopía',
                'available' => true,
            ],
            [
                'id' => 3,
                'title' => 'El nombre del viento',
                'author' => 'Patrick Rothfuss',
                'genre' => 'Fantasía',
                'available' => true,
            ],
            [
                'id' => 4,
                'title' => 'Orgullo y prejuicio',
                'author' => 'Jane Austen',
                'genre' => 'Romance',
                'available' => false,
            ],
            [
                'id' => 5,
                'title' => 'Don Quijote de la Mancha',
                'author' => 'Miguel de Cervantes',
                'genre' => 'Clásico',
                'available' => true,
            ],
            [
                'id' => 6,
                'title' => 'El principito',
                'author' => 'Antoine de Saint-Exupéry',
                'genre' => 'Fantasy',
                'available' => false,
            ],
        ];

        Storage::put('books.json', json_encode($books, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $this->command->info('📚 Archivo books.json creado con éxito.');
    }
}
