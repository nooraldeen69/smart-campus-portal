<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SisGrade;
use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use App\Models\LibraryBook;
use App\Models\LibraryLoan;

class DashboardTest extends PortalTestCase
{
    private function student(): User
    {
        $user = $this->makeUser(['university_id' => '20260001']);

        SisGrade::create(['user_id' => $user->id, 'course_name' => 'Databases', 'score' => 'A']);

        $course = LmsCourse::create(['course_code' => 'IT301', 'title' => 'Web Dev', 'credits' => 3]);
        LmsEnrollment::create(['user_id' => $user->id, 'lms_course_id' => $course->id]);

        $book = LibraryBook::create(['title' => 'Clean Code', 'author' => 'Robert C. Martin', 'available_copies' => 3]);
        LibraryLoan::create(['user_id' => $user->id, 'library_book_id' => $book->id, 'due_date' => '2026-10-04']);

        return $user;
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_dashboard_shows_grades_and_courses_from_db(): void
    {
        $this->actingAs($this->student())->get('/dashboard')
            ->assertOk()
            ->assertSee('Databases')    // SIS grade
            ->assertSee('IT301');       // LMS course
    }

    public function test_layout_is_mobile_first(): void
    {
        $this->actingAs($this->student())->get('/dashboard')
            ->assertSee('name="viewport" content="width=device-width, initial-scale=1"', false)
            ->assertSee('container-fluid', false);
    }
}
