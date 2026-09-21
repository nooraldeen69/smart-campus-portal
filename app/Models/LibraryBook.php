<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryBook extends Model
{
    protected $guarded = [];

    public function loans()
    {
        return $this->hasMany(LibraryLoan::class, 'library_book_id');
    }
}
