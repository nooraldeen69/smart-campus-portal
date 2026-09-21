<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SisGrade;
use App\Models\LmsEnrollment;
use App\Models\LibraryLoan;

class ProfileController extends Controller
{
    public function show()
    {
        $user        = Auth::user();
        $grades      = SisGrade::where('user_id', $user->id)->get();
        $enrollments = LmsEnrollment::with('course')->where('user_id', $user->id)->get();
        $loans       = LibraryLoan::with('book')->where('user_id', $user->id)->whereNull('returned_at')->get();

        $gradePoints = [
            'A'  => 4.0, 'A-' => 3.7,
            'B+' => 3.3, 'B'  => 3.0, 'B-' => 2.7,
            'C+' => 2.3, 'C'  => 2.0,
            'D'  => 1.0, 'F'  => 0.0,
        ];

        $gpa = $grades->count() > 0
            ? round($grades->map(fn($g) => $gradePoints[$g->score] ?? 0)->average(), 2)
            : null;

        return view('profile.show', compact('user', 'grades', 'enrollments', 'loans', 'gpa'));
    }

    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'phone'      => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
        ]);

        Auth::user()->update($request->only('name', 'phone', 'department'));

        return redirect()->route('profile.show')->with('status', 'Profile updated successfully.');
    }
}
