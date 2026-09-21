<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SisGrade;
use App\Models\LmsCourse;
use App\Models\LmsEnrollment;

class SisController extends Controller
{
    public function transcript()
    {
        $grades = SisGrade::where('user_id', Auth::id())->get();
        return view('sis.transcript', compact('grades'));
    }

    public function registration()
    {
        $enrolledIds = LmsEnrollment::where('user_id', Auth::id())->pluck('lms_course_id');
        $availableCourses = LmsCourse::whereNotIn('id', $enrolledIds)->get();
        return view('sis.registration', compact('availableCourses'));
    }

    public function enroll(Request $request)
    {
        $request->validate(['course_id' => 'required|exists:lms_courses,id']);
        
        $already = LmsEnrollment::where('user_id', Auth::id())->where('lms_course_id', $request->course_id)->first();
        if (!$already) {
            LmsEnrollment::create([
                'user_id' => Auth::id(),
                'lms_course_id' => $request->course_id,
            ]);
            return redirect()->route('lms.index')->with('status', 'Successfully enrolled in course!');
        }
        
        return back()->with('error', 'Already enrolled.');
    }

    public function drop(Request $request)
    {
        $request->validate(['course_id' => 'required|exists:lms_courses,id']);

        $deleted = LmsEnrollment::where('user_id', Auth::id())->where('lms_course_id', $request->course_id)->delete();

        return $deleted
            ? redirect()->route('lms.index')->with('status', 'Course dropped.')
            : back()->with('error', 'You are not registered for that course.');
    }
}
