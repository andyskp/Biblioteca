<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = ['title', 'author', 'genre', 'available'];

    protected $casts = [
        'available' => 'boolean',
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
