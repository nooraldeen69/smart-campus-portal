<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lms_courses', function (Blueprint $table) {
            $table->string('semester')->default('Fall 2026')->after('credits');
            $table->string('schedule_days')->nullable()->after('semester'); // e.g. "Mon, Wed"
            $table->string('schedule_time')->nullable()->after('schedule_days'); // e.g. "10:00 - 11:30"
            $table->string('room')->nullable()->after('schedule_time'); // e.g. "B201"
        });

        Schema::table('sis_grades', function (Blueprint $table) {
            $table->string('semester')->default('Fall 2026')->after('score');
        });
    }

    public function down(): void
    {
        Schema::table('lms_courses', function (Blueprint $table) {
            $table->dropColumn(['semester', 'schedule_days', 'schedule_time', 'room']);
        });
        Schema::table('sis_grades', function (Blueprint $table) {
            $table->dropColumn('semester');
        });
    }
};
