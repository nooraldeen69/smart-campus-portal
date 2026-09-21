<?php

namespace App\Http\Controllers;

use App\Models\LmsCourse;
use Illuminate\Http\Request;

class AdminCourseController extends Controller
{
    public function index()
    {
        $courses = LmsCourse::withCount('enrollments')->orderBy('course_code')->get();
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('admin.courses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_code' => 'required|unique:lms_courses',
            'title'       => 'required',
            'credits'     => 'required|integer|min:1',
            'semester'    => 'required',
        ]);

        LmsCourse::create($request->only(
            'course_code', 'title', 'instructor', 'credits',
            'description', 'semester', 'schedule_days', 'schedule_time', 'room', 'lms_link'
        ));

        return redirect()->route('admin.courses.index')->with('status', 'Course created.');
    }

    public function edit($id)
    {
        $course = LmsCourse::findOrFail($id);
        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $request, $id)
    {
        $course = LmsCourse::findOrFail($id);

        $request->validate([
            'course_code' => 'required|unique:lms_courses,course_code,' . $id,
            'title'       => 'required',
            'credits'     => 'required|integer|min:1',
            'semester'    => 'required',
        ]);

        $course->update($request->only(
            'course_code', 'title', 'instructor', 'credits',
            'description', 'semester', 'schedule_days', 'schedule_time', 'room', 'lms_link'
        ));

        return redirect()->route('admin.courses.index')->with('status', 'Course updated.');
    }

    public function destroy($id)
    {
        LmsCourse::findOrFail($id)->delete();
        return redirect()->route('admin.courses.index')->with('status', 'Course deleted.');
    }
}
