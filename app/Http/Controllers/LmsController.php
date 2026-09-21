<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LmsEnrollment;
use App\Models\LmsCourse;

class LmsController extends Controller
{
    public function index()
    {
        $enrollments = LmsEnrollment::with('course')->where('user_id', Auth::id())->get();
        return view('lms.index', compact('enrollments'));
    }

    public function show($id)
    {
        $enrollment = LmsEnrollment::where('user_id', Auth::id())->where('lms_course_id', $id)->firstOrFail();
        $course = LmsCourse::findOrFail($id);

        return view('lms.show', compact('course'));
    }

    public function schedule()
    {
        $enrollments = LmsEnrollment::with('course')->where('user_id', Auth::id())->get();
        return view('lms.schedule', compact('enrollments'));
    }
}
