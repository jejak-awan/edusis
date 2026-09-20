<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pemilihan Ketua & Wakil Ketua OSIS (E-Voting)
        Schema::create('sch_std_osis_elections', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('sch_acad_years')->cascadeOnDelete();
            $table->string('title', 150); // e.g. "Pemilihan Ketua & Wakil Ketua OSIS Periode 2026/2027"
            $table->string('period', 30); // e.g. "2026/2027"
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->enum('status', ['draft', 'active', 'closed'])->default('draft');
            $table->timestamps();

            $table->index(['school_id', 'status']);
        });

        // 2. Pasangan Calon Kandidat OSIS
        Schema::create('sch_std_osis_candidates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('election_id')->constrained('sch_std_osis_elections')->cascadeOnDelete();
            $table->unsignedSmallInteger('candidate_number'); // Nomor Urut: 1, 2, 3
            $table->foreignId('chairman_student_id')->constrained('sch_std_students')->cascadeOnDelete();
            $table->foreignId('vice_chairman_student_id')->constrained('sch_std_students')->cascadeOnDelete();
            $table->text('vision');
            $table->text('mission');
            $table->string('photo_url', 255)->nullable();
            $table->unsignedInteger('vote_count')->default(0);
            $table->timestamps();

            $table->unique(['election_id', 'candidate_number']);
        });

        // 3. Rekam Suara Masuk (Bilik Suara Digital - Anti Double Voting)
        Schema::create('sch_std_osis_votes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('election_id')->constrained('sch_std_osis_elections')->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained('sch_std_osis_candidates')->cascadeOnDelete();
            $table->foreignId('voter_student_id')->constrained('sch_std_students')->cascadeOnDelete();
            $table->string('token_hash', 64)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('voted_at');
            $table->timestamps();

            // Kunci integritas pemilu: 1 siswa hanya boleh mencoblos 1 kali per pemilu
            $table->unique(['election_id', 'voter_student_id']);
        });

        // 4. Manajemen Kegiatan Ekstrakurikuler
        Schema::create('sch_std_extracurriculars', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->string('name', 100); // e.g. Pramuka, Paskibra, PMR, Futsal, IT Club
            $table->enum('category', ['wajib', 'pilihan'])->default('pilihan');
            $table->foreignId('advisor_staff_id')->nullable()->constrained('sch_hr_staff')->nullOnDelete();
            $table->string('schedule_day', 30)->nullable(); // e.g. "Jumat 15:00 - 17:00"
            $table->string('location', 100)->nullable(); // e.g. "Lapangan Utama"
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['school_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sch_std_extracurriculars');
        Schema::dropIfExists('sch_std_osis_votes');
        Schema::dropIfExists('sch_std_osis_candidates');
        Schema::dropIfExists('sch_std_osis_elections');
    }
};
