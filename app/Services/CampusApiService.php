<?php

namespace App\Services;

use App\Models\User;
use App\Models\SisGrade;
use App\Models\LmsEnrollment;
use App\Models\LibraryLoan;

class CampusApiService
{
    private array $degraded = [];

    public function getStudentGrades($studentId): array
    {
        $user = User::where('university_id', $studentId)->first();
        if (!$user) return [];
        
        return SisGrade::where('user_id', $user->id)->get()->toArray();
    }

    public function getActiveLMSCourses($studentId): array
    {
        $user = User::where('university_id', $studentId)->first();
        if (!$user) return [];
        
        $enrollments = LmsEnrollment::with('course')->where('user_id', $user->id)->get();
        return $enrollments->map(function ($enrollment) {
            return $enrollment->course->toArray();
        })->toArray();
    }

    public function getLibraryLoans($studentId): array
    {
        $user = User::where('university_id', $studentId)->first();
        if (!$user) return [];
        
        return LibraryLoan::where('user_id', $user->id)->get()->toArray();
    }

    public function degradedSources(): array
    {
        return $this->degraded;
    }

    public function refreshGrades($studentId): array
    {
        return $this->getStudentGrades($studentId);
    }
}
