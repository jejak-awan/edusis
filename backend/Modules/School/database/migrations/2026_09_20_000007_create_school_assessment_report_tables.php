<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tujuan Pembelajaran (TP) / Capaian Pembelajaran Kurikulum Merdeka
        Schema::create('sch_acad_learning_objectives', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('sch_acad_subjects')->cascadeOnDelete();
            $table->string('code', 20); // e.g. "TP-01", "TP-02"
            $table->text('description'); // Deskripsi kompetensi tujuan pembelajaran
            $table->enum('phase', ['E', 'F'])->default('E'); // Fase E (Kelas 10) atau Fase F (Kelas 11/12)
            $table->unsignedSmallInteger('order_index')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['subject_id', 'phase']);
        });

        // 2. Definisi Asesmen (Formatif, Sumatif Lingkup Materi / STS, Sumatif Akhir Semester / SAS)
        Schema::create('sch_acad_assessments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('sch_acad_years')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('sch_acad_semesters')->cascadeOnDelete();
            $table->foreignId('study_group_id')->constrained('sch_acad_study_groups')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('sch_acad_subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('sch_hr_staff')->cascadeOnDelete();
            $table->foreignId('learning_objective_id')->nullable()->constrained('sch_acad_learning_objectives')->nullOnDelete();

            $table->string('title', 150); // e.g. "Formatif 1: Dasar HTML", "Sumatif Bab 1", "SAS Genap"
            $table->enum('type', ['formative', 'summative_material', 'summative_final'])->default('formative');
            $table->unsignedSmallInteger('weight')->default(1); // Bobot perhitungan nilai akhir
            $table->unsignedSmallInteger('max_score')->default(100);
            $table->date('date');
            $table->text('description')->nullable();

            $table->timestamps();

            $table->index(['study_group_id', 'subject_id', 'type']);
            $table->index(['semester_id', 'subject_id']);
        });

        // 3. Rekaman Skor / Nilai Siswa per Asesmen
        Schema::create('sch_acad_grades', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('assessment_id')->constrained('sch_acad_assessments')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('sch_std_students')->cascadeOnDelete();

            $table->decimal('score', 5, 2)->default(0); // Nilai asli siswa
            $table->decimal('remedial_score', 5, 2)->nullable(); // Nilai setelah perbaikan jika di bawah KKTP
            $table->text('notes')->nullable(); // Catatan pembinaan guru

            $table->timestamps();

            $table->unique(['assessment_id', 'student_id']);
            $table->index('student_id');
        });

        // 4. Rekap Nilai Raport Akhir Semester (Report Card) Kurikulum Merdeka
        Schema::create('sch_acad_report_cards', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('sch_acad_years')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('sch_acad_semesters')->cascadeOnDelete();
            $table->foreignId('study_group_id')->constrained('sch_acad_study_groups')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('sch_std_students')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('sch_acad_subjects')->cascadeOnDelete();

            $table->decimal('final_score', 5, 2)->default(0); // Rata-rata terbobot formatif & sumatif
            $table->string('letter_grade', 5)->default('B'); // A, B, C, D
            $table->text('achieved_competency')->nullable(); // Deskripsi capaian kompetensi tertinggi
            $table->text('improved_competency')->nullable(); // Deskripsi kompetensi yang perlu ditingkatkan

            $table->timestamps();

            $table->unique(['semester_id', 'study_group_id', 'student_id', 'subject_id'], 'sch_acad_report_unique');
            $table->index(['study_group_id', 'semester_id']);
            $table->index('student_id');
        });

        // 5. Proyek Penguatan Profil Pelajar Pancasila (P5)
        Schema::create('sch_acad_p5_projects', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('sch_acad_years')->cascadeOnDelete();
            $table->foreignId('coordinator_id')->nullable()->constrained('sch_hr_staff')->nullOnDelete();

            $table->string('title', 200); // e.g. "Pengelolaan Sampah Organik Berbasis Eco-Enzyme"
            $table->string('theme', 100); // Gaya Hidup Berkelanjutan, Kearifan Lokal, Rekayasa & Teknologi, Kewirausahaan, dll.
            $table->enum('phase', ['E', 'F'])->default('E');
            $table->text('description')->nullable();

            $table->timestamps();

            $table->index(['academic_year_id', 'phase']);
        });

        // 6. Penilaian Dimensi P5 Siswa (MB, SB, BSH, SAB)
        Schema::create('sch_acad_p5_assessments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained('sch_acad_p5_projects')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('sch_std_students')->cascadeOnDelete();

            // Dimensi: Beriman, Berkebinekaan Global, Bergotong Royong, Mandiri, Bernalar Kritis, Kreatif
            $table->string('dimension', 80);
            // Predikat P5 Kemendikbud: MB (Mulai Berkembang), SB (Sedang Berkembang), BSH (Berkembang Sesuai Harapan), SAB (Sangat Berkembang)
            $table->enum('predicate', ['MB', 'SB', 'BSH', 'SAB'])->default('BSH');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['project_id', 'student_id', 'dimension'], 'sch_p5_student_dim_unique');
            $table->index(['project_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sch_acad_p5_assessments');
        Schema::dropIfExists('sch_acad_p5_projects');
        Schema::dropIfExists('sch_acad_report_cards');
        Schema::dropIfExists('sch_acad_grades');
        Schema::dropIfExists('sch_acad_assessments');
        Schema::dropIfExists('sch_acad_learning_objectives');
    }
};
