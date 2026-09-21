<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LmsCourse;
use App\Models\LibraryBook;
use App\Models\LibraryLoan;
use App\Models\LmsEnrollment;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'users'         => User::where('role', 'student')->count(),
            'courses'       => LmsCourse::count(),
            'books'         => LibraryBook::count(),
            'active_loans'  => LibraryLoan::whereNull('returned_at')->count(),
            'overdue_loans' => LibraryLoan::whereNull('returned_at')
                                          ->where('due_date', '<', Carbon::today())
                                          ->count(),
        ];

        $recentEnrollments = LmsEnrollment::with(['user', 'course'])->latest()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recentEnrollments'));
    }
}
