<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Timetable / Jadwal KBM Mingguan
        Schema::create('sch_acad_schedules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('sch_acad_years')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('sch_acad_semesters')->cascadeOnDelete();
            $table->foreignId('study_group_id')->constrained('sch_acad_study_groups')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('sch_acad_subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('sch_hr_staff')->cascadeOnDelete();

            $table->unsignedTinyInteger('day_of_week'); // 1=Senin, 2=Selasa, ..., 7=Minggu
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room_name', 50)->nullable(); // e.g. Lab RPL 1, Ruang 10
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['academic_year_id', 'semester_id', 'day_of_week']);
            $table->index(['study_group_id', 'day_of_week']);
            $table->index('teacher_id');
        });

        // Presensi Harian Siswa (H/I/S/A)
        Schema::create('sch_acad_attendances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained('sch_std_students')->cascadeOnDelete();
            $table->foreignId('study_group_id')->nullable()->constrained('sch_acad_study_groups')->nullOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('sch_acad_years')->nullOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained('sch_acad_semesters')->nullOnDelete();
            $table->date('date');
            $table->enum('status', ['H', 'I', 'S', 'A'])->default('H'); // Hadir, Izin, Sakit, Alpa
            $table->text('notes')->nullable();
            $table->string('attachment_path')->nullable(); // Foto surat sakit / izin
            $table->foreignUuid('recorded_by')->nullable()->constrained('srv_auth_users')->nullOnDelete();

            $table->timestamps();

            $table->unique(['student_id', 'date']);
            $table->index(['study_group_id', 'date', 'status']);
            $table->index('date');
        });

        // Jurnal Harian Mengajar Guru (KBM)
        Schema::create('sch_acad_teaching_journals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('sch_acad_years')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('sch_acad_semesters')->cascadeOnDelete();
            $table->foreignId('study_group_id')->constrained('sch_acad_study_groups')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('sch_acad_subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('sch_hr_staff')->cascadeOnDelete();
            $table->foreignId('schedule_id')->nullable()->constrained('sch_acad_schedules')->nullOnDelete();

            $table->date('date');
            $table->string('teaching_hour', 30); // e.g. "Jam 1 - 3"
            $table->string('topic', 200); // Materi Pelajaran / Topik
            $table->text('competency_description')->nullable(); // Capaian Kompetensi (CP/KD)
            $table->text('learning_activity')->nullable(); // Deskripsi Kegiatan Pembelajaran
            
            // Rekap kehadiran di jam pelajaran ini
            $table->unsignedSmallInteger('present_count')->default(0);
            $table->unsignedSmallInteger('sick_count')->default(0);
            $table->unsignedSmallInteger('permit_count')->default(0);
            $table->unsignedSmallInteger('absent_count')->default(0);

            $table->text('notes_reflection')->nullable(); // Refleksi & kendala kelas
            $table->string('evidence_photo_path')->nullable(); // Foto bukti KBM

            $table->timestamps();

            $table->index(['teacher_id', 'date']);
            $table->index(['study_group_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sch_acad_teaching_journals');
        Schema::dropIfExists('sch_acad_attendances');
        Schema::dropIfExists('sch_acad_schedules');
    }
};
