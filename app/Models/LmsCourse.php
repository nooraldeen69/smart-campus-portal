<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsCourse extends Model
{
    protected $guarded = [];

    public function enrollments()
    {
        return $this->hasMany(LmsEnrollment::class);
    }
}
