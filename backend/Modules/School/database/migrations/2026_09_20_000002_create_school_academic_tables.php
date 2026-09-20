<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Academic Years (Tahun Ajaran)
        Schema::create('sch_acad_years', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->string('name', 30); // e.g. 2026/2027
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->index(['school_id', 'is_active']);
        });

        // Semesters
        Schema::create('sch_acad_semesters', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('sch_acad_years')->cascadeOnDelete();
            $table->string('name', 20); // Ganjil / Genap
            $table->unsignedTinyInteger('semester_number')->default(1); // 1 / 2
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->index(['academic_year_id', 'is_active']);
        });

        // Departments (Kompetensi Keahlian / Jurusan)
        Schema::create('sch_acad_departments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->string('code', 20); // e.g. PPLG, TJKT, DKV
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['school_id', 'code']);
        });

        // Subjects (Mata Pelajaran)
        Schema::create('sch_acad_subjects', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->string('code', 30)->nullable();
            $table->string('name', 100);
            $table->string('group', 30)->default('Umum'); // Umum, Kejuruan, Muatan Lokal, P5
            $table->unsignedSmallInteger('kkm')->default(75);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['school_id', 'is_active']);
        });

        // Study Groups (Rombongan Belajar / Kelas)
        Schema::create('sch_acad_study_groups', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->foreignId('school_level_id')->nullable()->constrained('sch_ins_levels')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('sch_acad_departments')->nullOnDelete();
            $table->foreignId('academic_year_id')->constrained('sch_acad_years')->cascadeOnDelete();
            $table->string('name', 50); // e.g. X PPLG 1, XI TJKT 2
            $table->unsignedBigInteger('homeroom_teacher_id')->nullable(); // linked to staff/user in Phase 2
            $table->unsignedSmallInteger('capacity')->default(36);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['academic_year_id', 'is_active']);
        });

        // Study Group Members (Pivot Rombel & Siswa)
        Schema::create('sch_acad_study_group_members', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('study_group_id')->constrained('sch_acad_study_groups')->cascadeOnDelete();
            $table->unsignedBigInteger('student_id'); // constrained in student migration
            $table->string('status', 20)->default('active'); // active, transferred, dropped
            $table->timestamps();

            $table->unique(['study_group_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sch_acad_study_group_members');
        Schema::dropIfExists('sch_acad_study_groups');
        Schema::dropIfExists('sch_acad_subjects');
        Schema::dropIfExists('sch_acad_departments');
        Schema::dropIfExists('sch_acad_semesters');
        Schema::dropIfExists('sch_acad_years');
    }
};
