<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Layanan Kasus & Sesi Bimbingan Konseling (BK) Siswa
        Schema::create('sch_csl_cases', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('sch_std_students')->cascadeOnDelete();
            $table->foreignId('counselor_id')->constrained('sch_hr_staff')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('sch_acad_years')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('sch_acad_semesters')->cascadeOnDelete();
            $table->foreignId('violation_id')->nullable()->constrained('sch_std_violations')->nullOnDelete();

            $table->string('case_number', 50)->unique(); // e.g. "BK/2026/09/001"
            $table->date('date');
            $table->enum('category', ['pribadi', 'sosial', 'belajar', 'karir'])->default('belajar');
            $table->enum('confidentiality', ['biasa', 'internal', 'rahasia', 'sangat_rahasia'])->default('internal');

            $table->text('problem_description'); // Uraian pokok permasalahan siswa
            $table->text('counseling_steps')->nullable(); // Tahapan / teknik bimbingan yang dilakukan
            $table->text('solution_plan')->nullable(); // Kesepakatan rencana penyelesaian
            $table->text('follow_up_notes')->nullable(); // Evaluasi & tindak lanjut hasil konseling

            $table->enum('status', ['open', 'in_progress', 'resolved', 'referred'])->default('open');
            $table->timestamps();

            $table->index(['student_id', 'status']);
            $table->index(['counselor_id', 'date']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sch_csl_cases');
    }
};
