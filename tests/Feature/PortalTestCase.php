<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

/** Shared setup: in-memory SQLite DB migrated fresh for each test. */
abstract class PortalTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // In-memory SQLite is brand new for every test.
        Artisan::call('migrate', ['--force' => true]);
    }

    /** Plain create (no model factory / Faker needed). */
    protected function makeUser(array $attributes = []): User
    {
        return User::create($attributes + [
            'name'     => 'Test Student',
            'email'    => uniqid('student') . '@example.edu',
            'password' => bcrypt('password'),
        ]);
    }
}
