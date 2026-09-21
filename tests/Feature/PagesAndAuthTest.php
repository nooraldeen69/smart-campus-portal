<?php

namespace Tests\Feature;

use App\Models\LibraryBook;
use App\Models\LibraryLoan;
use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use App\Models\SisGrade;
use App\Models\User;

/** Regression tests for the pages that used to crash (500) and for login / library / registration rules. */
class PagesAndAuthTest extends PortalTestCase
{
    private function student(array $a = []): User
    {
        return $this->makeUser($a + ['university_id' => 'S'.uniqid(), 'role' => 'student', 'name' => 'Sam Student']);
    }

    private function admin(): User
    {
        return $this->makeUser(['university_id' => 'A'.uniqid(), 'role' => 'admin', 'name' => 'Admin User']);
    }

    private function course(array $a = []): LmsCourse
    {
        static $n = 0;
        $n++;

        return LmsCourse::create($a + ['course_code' => "T{$n}00", 'title' => "Course {$n}", 'credits' => 3, 'semester' => 'Fall 2026',
            'schedule_days' => 'Mon, Wed', 'schedule_time' => '10:00 - 11:30', 'room' => 'B-1', 'instructor' => 'Dr. Who']);
    }

    private function book(int $copies = 1, string $title = 'A Book'): LibraryBook
    {
        return LibraryBook::create(['title' => $title, 'author' => 'Someone', 'available_copies' => $copies]);
    }

    // ---------------- every page renders ----------------
    public function test_every_student_page_renders(): void
    {
        $s = $this->student();
        $c = $this->course();
        LmsEnrollment::create(['user_id' => $s->id, 'lms_course_id' => $c->id]);
        SisGrade::create(['user_id' => $s->id, 'course_name' => 'Databases', 'score' => 'A']);
        $this->book();

        foreach (['/dashboard', '/sis/transcript', '/sis/registration', '/lms', "/lms/course/{$c->id}", '/schedule', '/library', '/profile', '/profile/edit'] as $url) {
            $this->actingAs($s)->get($url)->assertOk();
        }
    }

    public function test_every_admin_page_renders(): void
    {
        $a = $this->admin();
        $u = $this->student();
        $c = $this->course();
        $b = $this->book();

        foreach (['/admin', '/admin/users', '/admin/users/create', "/admin/users/{$u->id}/edit", '/admin/grades', '/admin/grades/create',
            '/admin/courses', '/admin/courses/create', "/admin/courses/{$c->id}/edit", '/admin/books', '/admin/books/create', "/admin/books/{$b->id}/edit"] as $url) {
            $this->actingAs($a)->get($url)->assertOk();
        }
    }

    public function test_students_cannot_open_the_admin_panel_and_guests_are_redirected(): void
    {
        foreach (['/admin', '/admin/users', '/admin/books', '/admin/courses', '/admin/grades'] as $url) {
            $this->actingAs($this->student())->get($url)->assertForbidden();
        }
        foreach (['/dashboard', '/library', '/profile', '/schedule', '/admin'] as $url) {
            auth()->logout();
            $this->get($url)->assertRedirect(route('login'));
        }
    }

    public function test_admin_sees_the_admin_link_students_do_not(): void
    {
        $this->actingAs($this->admin())->get('/dashboard')->assertSee('Admin</a>', false);
        $this->actingAs($this->student())->get('/dashboard')->assertDontSee(route('admin.dashboard'));
    }

    // ---------------- login ----------------
    public function test_login_with_university_id_or_email(): void
    {
        $u = $this->student(['university_id' => '20261234', 'email' => 'sam@example.edu']);

        $this->post('/login', ['login' => '20261234', 'password' => 'password'])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($u);

        auth()->logout();
        $this->post('/login', ['login' => 'sam@example.edu', 'password' => 'password'])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($u);
    }

    public function test_wrong_password_is_rejected(): void
    {
        $this->student(['university_id' => '20261234']);

        $this->from('/')->post('/login', ['login' => '20261234', 'password' => 'nope'])->assertRedirect('/')->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_repeated_failures_lock_the_login_for_a_while(): void
    {
        $this->student(['university_id' => 'lockme']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['login' => 'lockme', 'password' => 'bad']);
        }

        $this->post('/login', ['login' => 'lockme', 'password' => 'password'])->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_logout_works_and_get_logout_is_not_allowed(): void
    {
        $s = $this->student();

        $this->actingAs($s)->get('/logout')->assertStatus(405);
        $this->actingAs($s)->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_demo_accounts_are_only_advertised_in_demo_mode(): void
    {
        config(['services.campus.demo_mode' => false]);
        $this->get('/')->assertOk()->assertDontSee('Demo accounts')->assertDontSee('Layla Hassan');

        config(['services.campus.demo_mode' => true]);
        $this->get('/')->assertSee('Demo accounts')->assertSee('20260001');
    }

    // ---------------- registration ----------------
    public function test_student_registers_for_and_drops_a_course(): void
    {
        $s = $this->student();
        $c = $this->course();

        $this->actingAs($s)->post('/sis/enroll', ['course_id' => $c->id])->assertRedirect(route('lms.index'));
        $this->assertSame(1, LmsEnrollment::count());
        $this->actingAs($s)->post('/sis/enroll', ['course_id' => $c->id])->assertSessionHas('error');
        $this->assertSame(1, LmsEnrollment::count());

        $this->actingAs($s)->post('/sis/drop', ['course_id' => $c->id])->assertSessionHas('status');
        $this->assertSame(0, LmsEnrollment::count());
        $this->actingAs($s)->post('/sis/drop', ['course_id' => $c->id])->assertSessionHas('error');
    }

    public function test_dropping_only_affects_your_own_registration(): void
    {
        $mine = $this->student();
        $other = $this->student();
        $c = $this->course();
        LmsEnrollment::create(['user_id' => $other->id, 'lms_course_id' => $c->id]);

        $this->actingAs($mine)->post('/sis/drop', ['course_id' => $c->id])->assertSessionHas('error');
        $this->assertSame(1, LmsEnrollment::count());
    }

    public function test_course_pages_are_only_for_registered_students(): void
    {
        $c = $this->course();

        $this->actingAs($this->student())->get("/lms/course/{$c->id}")->assertNotFound();
    }

    public function test_schedule_places_courses_on_the_right_days(): void
    {
        $s = $this->student();
        $c = $this->course(['title' => 'Timetable Theory', 'schedule_days' => 'Sun, Tue', 'schedule_time' => '08:00 - 09:30']);
        LmsEnrollment::create(['user_id' => $s->id, 'lms_course_id' => $c->id]);

        $html = $this->actingAs($s)->get('/schedule')->assertOk()->assertSee('Timetable Theory')->getContent();
        $this->assertSame(2, substr_count($html, 'Timetable Theory'));   // Sunday + Tuesday only
    }

    // ---------------- library ----------------
    public function test_borrow_and_return_keep_the_copy_count_consistent(): void
    {
        $s = $this->student();
        $b = $this->book(2);

        $this->actingAs($s)->post('/library/borrow', ['book_id' => $b->id])->assertSessionHas('status');
        $this->assertSame(1, $b->fresh()->available_copies);

        $loan = LibraryLoan::first();
        $this->actingAs($s)->post('/library/return', ['loan_id' => $loan->id])->assertSessionHas('status');
        $this->assertSame(2, $b->fresh()->available_copies);

        // double click / replay must not create an extra copy
        $this->actingAs($s)->post('/library/return', ['loan_id' => $loan->id])->assertSessionHas('error');
        $this->assertSame(2, $b->fresh()->available_copies);
    }

    public function test_last_copy_goes_to_one_student_only_and_duplicates_are_refused(): void
    {
        $b = $this->book(1);
        $first = $this->student();

        $this->actingAs($first)->post('/library/borrow', ['book_id' => $b->id])->assertSessionHas('status');
        $this->actingAs($this->student())->post('/library/borrow', ['book_id' => $b->id])->assertSessionHas('error');
        $this->assertSame(0, $b->fresh()->available_copies);
        $this->assertSame(1, LibraryLoan::count());

        $b2 = $this->book(5, 'Another');
        $this->actingAs($first)->post('/library/borrow', ['book_id' => $b2->id]);
        $this->actingAs($first)->post('/library/borrow', ['book_id' => $b2->id])->assertSessionHas('error', 'You already have this book on loan.');
    }

    public function test_loan_limit_and_overdue_block(): void
    {
        $s = $this->student();
        foreach (range(1, 3) as $i) {
            $this->actingAs($s)->post('/library/borrow', ['book_id' => $this->book(1, "Book {$i}")->id])->assertSessionHas('status');
        }
        $this->actingAs($s)->post('/library/borrow', ['book_id' => $this->book(1, 'Fourth')->id])->assertSessionHas('error');
        $this->assertSame(3, LibraryLoan::count());

        $late = $this->student();
        LibraryLoan::create(['user_id' => $late->id, 'library_book_id' => $this->book(1, 'Old')->id, 'due_date' => now()->subDays(3)->toDateString()]);
        $this->actingAs($late)->post('/library/borrow', ['book_id' => $this->book(1, 'New')->id])->assertSessionHas('error');
    }

    public function test_nobody_can_return_someone_elses_loan(): void
    {
        $owner = $this->student();
        $b = $this->book(1);
        $this->actingAs($owner)->post('/library/borrow', ['book_id' => $b->id]);
        $loan = LibraryLoan::first();

        $this->actingAs($this->student())->post('/library/return', ['loan_id' => $loan->id])->assertNotFound();
        $this->assertNull($loan->fresh()->returned_at);
    }

    // ---------------- profile ----------------
    public function test_profile_update_cannot_change_role_or_identity(): void
    {
        $s = $this->student(['university_id' => '20269999', 'email' => 'keep@example.edu']);

        $this->actingAs($s)->post('/profile/update', [
            'name' => 'New Name', 'phone' => '0555', 'department' => 'IT',
            'role' => 'admin', 'email' => 'evil@example.edu', 'university_id' => 'HACK',
        ])->assertRedirect(route('profile.show'));

        $s->refresh();
        $this->assertSame('New Name', $s->name);
        $this->assertSame('student', $s->role);
        $this->assertSame('keep@example.edu', $s->email);
        $this->assertSame('20269999', $s->university_id);
    }

    // ---------------- admin: books ----------------
    public function test_admin_manages_books(): void
    {
        $a = $this->admin();

        $this->actingAs($a)->post('/admin/books', ['title' => 'Fresh Book', 'author' => 'Me', 'isbn' => '123', 'available_copies' => 3])
            ->assertRedirect(route('admin.books.index'));
        $b = LibraryBook::where('title', 'Fresh Book')->first();
        $this->assertSame(3, $b->available_copies);

        $this->actingAs($a)->post("/admin/books/{$b->id}", ['title' => 'Fresh Book 2', 'author' => 'Me', 'available_copies' => 5]);
        $this->assertSame('Fresh Book 2', $b->fresh()->title);

        $this->actingAs($a)->get('/admin/books')->assertSee('Fresh Book 2');
        $this->actingAs($a)->post("/admin/books/{$b->id}/delete");
        $this->assertNull(LibraryBook::find($b->id));

        $this->actingAs($a)->post('/admin/books', ['title' => '', 'author' => '', 'available_copies' => -1])->assertSessionHasErrors(['title', 'author', 'available_copies']);
    }

    // ---------------- seeded demo ----------------
    public function test_seeded_demo_works_end_to_end_and_admin_password_comes_from_config(): void
    {
        config(['services.campus.admin_password' => 'Sup3r-Secret']);
        $this->assertSame(0, \Illuminate\Support\Facades\Artisan::call('portal:bootstrap'));

        $this->assertGreaterThan(0, User::count());
        $this->post('/login', ['login' => 'ADMIN001', 'password' => 'password'])->assertSessionHasErrors('login');
        $this->post('/login', ['login' => 'ADMIN001', 'password' => 'Sup3r-Secret'])->assertRedirect(route('dashboard'));
        auth()->logout();
        $this->post('/login', ['login' => '20260001', 'password' => 'password'])->assertRedirect(route('dashboard'));
        $this->get('/dashboard')->assertOk()->assertSee('Layla');
    }
}
