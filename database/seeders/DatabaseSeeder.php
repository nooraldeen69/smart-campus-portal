<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SisGrade;
use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use App\Models\LibraryBook;
use App\Models\LibraryLoan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        // ── Users ─────────────────────────────────────────────────────────────
        User::create([
            'name'          => 'Administrator',
            'email'         => 'admin@campus.edu',
            'university_id' => 'ADMIN001',
            'role'          => 'admin',
            'department'    => 'Administration',
            'password'      => Hash::make(config('services.campus.admin_password', 'password')),
        ]);

        $layla = User::create([
            'name'          => 'Layla Hassan',
            'university_id' => '20260001',
            'email'         => 'layla.hassan@student.example.edu',
            'role'          => 'student',
            'department'    => 'Information Technology',
            'phone'         => '+966501234567',
            'password'      => $password,
        ]);

        $omar = User::create([
            'name'          => 'Omar Khalid',
            'university_id' => '20260002',
            'email'         => 'omar.khalid@student.example.edu',
            'role'          => 'student',
            'department'    => 'Computer Science',
            'phone'         => '+966507654321',
            'password'      => $password,
        ]);

        $sara = User::create([
            'name'          => 'Sara Ahmed',
            'university_id' => '20260003',
            'email'         => 'sara.ahmed@student.example.edu',
            'role'          => 'student',
            'department'    => 'Software Engineering',
            'phone'         => '+966509876543',
            'password'      => $password,
        ]);

        // ── Library Books ──────────────────────────────────────────────────────
        $books = [
            LibraryBook::create(['title' => 'PMBOK Guide, 5th Edition',               'author' => 'PMI',                  'isbn' => '978-1935589679', 'available_copies' => 2]),
            LibraryBook::create(['title' => 'Clean Code',                              'author' => 'Robert C. Martin',     'isbn' => '978-0132350884', 'available_copies' => 5]),
            LibraryBook::create(['title' => 'Database System Concepts',                'author' => 'Silberschatz',         'isbn' => '978-0078022159', 'available_copies' => 3]),
            LibraryBook::create(['title' => 'Artificial Intelligence: A Modern Approach', 'author' => 'Stuart Russell',   'isbn' => '978-0134610993', 'available_copies' => 1]),
            LibraryBook::create(['title' => 'Design Patterns',                         'author' => 'Erich Gamma',          'isbn' => '978-0201633610', 'available_copies' => 4]),
            LibraryBook::create(['title' => 'Introduction to Algorithms',              'author' => 'Thomas H. Cormen',     'isbn' => '978-0262033848', 'available_copies' => 2]),
            LibraryBook::create(['title' => 'Computer Networking',                     'author' => 'James Kurose',         'isbn' => '978-0133594140', 'available_copies' => 4]),
            LibraryBook::create(['title' => 'Software Engineering',                    'author' => 'Ian Sommerville',      'isbn' => '978-0133943030', 'available_copies' => 3]),
            LibraryBook::create(['title' => 'Operating System Concepts',               'author' => 'Silberschatz',         'isbn' => '978-1118063330', 'available_copies' => 5]),
        ];

        // ── LMS Courses ────────────────────────────────────────────────────────
        $courses = [
            'IT301' => LmsCourse::create([
                'course_code'   => 'IT301',
                'title'         => 'Database Systems',
                'description'   => 'Introduction to relational databases, SQL, and database design.',
                'instructor'    => 'Dr. Ahmed Mansour',
                'credits'       => 3,
                'semester'      => 'Fall 2026',
                'schedule_days' => 'Mon, Wed',
                'schedule_time' => '10:00 - 11:30',
                'room'          => 'B-201',
            ]),
            'IT340' => LmsCourse::create([
                'course_code'   => 'IT340',
                'title'         => 'IT Project Management',
                'description'   => 'Principles of IT project management, agile methodologies, and SDLC.',
                'instructor'    => 'Dr. Senan A. Ghallab',
                'credits'       => 3,
                'semester'      => 'Fall 2026',
                'schedule_days' => 'Sun, Tue',
                'schedule_time' => '12:00 - 13:30',
                'room'          => 'A-101',
            ]),
            'IT315' => LmsCourse::create([
                'course_code'   => 'IT315',
                'title'         => 'Operating Systems',
                'description'   => 'Fundamentals of operating systems, memory management, and concurrency.',
                'instructor'    => 'Dr. Fatma Al-Zubaidi',
                'credits'       => 4,
                'semester'      => 'Fall 2026',
                'schedule_days' => 'Mon, Wed, Thu',
                'schedule_time' => '09:00 - 10:00',
                'room'          => 'C-305',
            ]),
            'CS401' => LmsCourse::create([
                'course_code'   => 'CS401',
                'title'         => 'Artificial Intelligence',
                'description'   => 'Foundations of AI, machine learning, and search algorithms.',
                'instructor'    => 'Dr. Nabil Hassan',
                'credits'       => 4,
                'semester'      => 'Fall 2026',
                'schedule_days' => 'Tue, Thu',
                'schedule_time' => '14:00 - 15:30',
                'room'          => 'D-402',
            ]),
            'CS202' => LmsCourse::create([
                'course_code'   => 'CS202',
                'title'         => 'Data Structures',
                'description'   => 'Study of core data structures: trees, graphs, and hash tables.',
                'instructor'    => 'Dr. Yasser Al-Rashidi',
                'credits'       => 3,
                'semester'      => 'Fall 2026',
                'schedule_days' => 'Sun, Tue, Thu',
                'schedule_time' => '11:00 - 12:00',
                'room'          => 'B-102',
            ]),
            'SE320' => LmsCourse::create([
                'course_code'   => 'SE320',
                'title'         => 'Software Engineering',
                'description'   => 'Software architecture, design patterns, and testing.',
                'instructor'    => 'Dr. Mona Al-Ghamdi',
                'credits'       => 3,
                'semester'      => 'Fall 2026',
                'schedule_days' => 'Mon, Wed',
                'schedule_time' => '13:00 - 14:30',
                'room'          => 'A-203',
            ]),
            'IT410' => LmsCourse::create([
                'course_code'   => 'IT410',
                'title'         => 'Computer Networks',
                'description'   => 'OSI model, TCP/IP protocols, and network programming.',
                'instructor'    => 'Dr. Khaled Ibrahim',
                'credits'       => 3,
                'semester'      => 'Fall 2026',
                'schedule_days' => 'Sun, Tue',
                'schedule_time' => '08:00 - 09:30',
                'room'          => 'C-101',
            ]),
        ];

        // ── Grades ─────────────────────────────────────────────────────────────
        // Layla
        SisGrade::create(['user_id' => $layla->id, 'course_name' => 'Database Systems',     'score' => 'A',  'semester' => 'Fall 2026']);
        SisGrade::create(['user_id' => $layla->id, 'course_name' => 'IT Project Management','score' => 'A-', 'semester' => 'Fall 2026']);
        SisGrade::create(['user_id' => $layla->id, 'course_name' => 'Computer Networks',    'score' => 'B+', 'semester' => 'Fall 2026']);

        // Omar
        SisGrade::create(['user_id' => $omar->id, 'course_name' => 'Operating Systems', 'score' => 'B', 'semester' => 'Fall 2026']);
        SisGrade::create(['user_id' => $omar->id, 'course_name' => 'Web Engineering',   'score' => 'A', 'semester' => 'Fall 2026']);

        // ── Enrollments ────────────────────────────────────────────────────────
        // Layla
        LmsEnrollment::create(['user_id' => $layla->id, 'lms_course_id' => $courses['IT301']->id]);
        LmsEnrollment::create(['user_id' => $layla->id, 'lms_course_id' => $courses['IT340']->id]);
        LmsEnrollment::create(['user_id' => $layla->id, 'lms_course_id' => $courses['SE320']->id]);

        // Omar
        LmsEnrollment::create(['user_id' => $omar->id, 'lms_course_id' => $courses['IT315']->id]);
        LmsEnrollment::create(['user_id' => $omar->id, 'lms_course_id' => $courses['CS401']->id]);

        // Sara
        LmsEnrollment::create(['user_id' => $sara->id, 'lms_course_id' => $courses['CS202']->id]);
        LmsEnrollment::create(['user_id' => $sara->id, 'lms_course_id' => $courses['IT410']->id]);

        // ── Library Loans ──────────────────────────────────────────────────────
        // Layla – active loan (due in 14 days)
        LibraryLoan::create(['user_id' => $layla->id, 'library_book_id' => $books[0]->id, 'due_date' => Carbon::now()->addDays(14)->toDateString()]);
        // Layla – overdue (due 2 days ago)
        LibraryLoan::create(['user_id' => $layla->id, 'library_book_id' => $books[1]->id, 'due_date' => Carbon::now()->subDays(2)->toDateString()]);

        // Omar – active loan (due in 5 days)
        LibraryLoan::create(['user_id' => $omar->id, 'library_book_id' => $books[2]->id, 'due_date' => Carbon::now()->addDays(5)->toDateString()]);
    }
}
