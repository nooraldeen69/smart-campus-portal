<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryLoan extends Model
{
    protected $guarded = [];

    public function book()
    {
        return $this->belongsTo(LibraryBook::class, 'library_book_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
