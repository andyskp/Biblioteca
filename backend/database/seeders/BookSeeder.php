<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        Book::insert([
            [
                'title'     => 'Cien años de soledad',
                'author'    => 'Gabriel García Márquez',
                'genre'     => 'Realismo mágico',
                'available' => true,
            ],
            [
                'title'     => '1984',
                'author'    => 'George Orwell',
                'genre'     => 'Distopía',
                'available' => true,
            ],
            [
                'title'     => 'El nombre del viento',
                'author'    => 'Patrick Rothfuss',
                'genre'     => 'Fantasía',
                'available' => true,
            ],
            [
                'title'     => 'Orgullo y prejuicio',
                'author'    => 'Jane Austen',
                'genre'     => 'Romance',
                'available' => false,
            ],
            [
                'title'     => 'Don Quijote de la Mancha',
                'author'    => 'Miguel de Cervantes',
                'genre'     => 'Clásico',
                'available' => true,
            ],
        ]);
    }
}
