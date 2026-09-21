<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('student')->after('university_id'); // 'student' or 'admin'
            $table->string('phone')->nullable()->after('email');
            $table->string('department')->nullable()->after('phone');
            $table->string('avatar')->nullable()->after('department');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'department', 'avatar']);
        });
    }
};
