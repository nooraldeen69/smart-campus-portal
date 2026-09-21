<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsEnrollment extends Model
{
    protected $guarded = [];

    public function course()
    {
        return $this->belongsTo(LmsCourse::class, 'lms_course_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
