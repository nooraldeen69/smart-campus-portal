<?php

namespace App\Http\Controllers;

use App\Models\SisGrade;
use App\Models\User;
use Illuminate\Http\Request;

class AdminGradeController extends Controller
{
    public function index()
    {
        $grades = SisGrade::with('user')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.grades.index', compact('grades'));
    }

    public function create()
    {
        $students = User::where('role', 'student')->orderBy('name')->get();
        return view('admin.grades.create', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'     => 'required|exists:users,id',
            'course_name' => 'required|string',
            'score'       => 'required|in:A,A-,B+,B,B-,C+,C,D,F',
            'semester'    => 'required',
        ]);

        SisGrade::create($request->only('user_id', 'course_name', 'score', 'semester'));

        return redirect()->route('admin.grades.index')->with('status', 'Grade posted successfully.');
    }

    public function destroy($id)
    {
        SisGrade::findOrFail($id)->delete();
        return redirect()->back()->with('status', 'Grade deleted.');
    }
}
