<?php

namespace App\Http\Controllers;

use App\Services\CampusApiService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(protected CampusApiService $campusApi)
    {
    }

    public function index()
    {
        $user      = Auth::user();
        $studentId = $user->university_id;

        // One aggregation point for SIS + LMS + Library - no more data silos
        $grades  = $this->campusApi->getStudentGrades($studentId);
        $courses = $this->campusApi->getActiveLMSCourses($studentId);
        $loans   = $this->campusApi->getLibraryLoans($studentId);

        return view('portal.dashboard', [
            'user'          => $user,
            'grades'        => $grades,
            'courses'       => $courses,
            'loans'         => $loans,
            'degraded'      => $this->campusApi->degradedSources(),
            'notifications' => $user->unreadNotifications()->limit(5)->get(),
        ]);
    }
}
