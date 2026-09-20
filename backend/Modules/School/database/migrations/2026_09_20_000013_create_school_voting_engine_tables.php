<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Agenda Pemilihan & Polling Generik (OSIS, MPK, Ekskul, Guru Favorit, Polling Siswa)
        Schema::create('sch_vote_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('sch_acad_years')->nullOnDelete();

            $table->string('title', 150);
            $table->enum('type', [
                'osis',
                'mpk',
                'ekskul',
                'guru_favorit',
                'polling',
                'komite',
            ])->default('osis');
            $table->text('description')->nullable();
            $table->enum('target_audience', [
                'all_students',
                'specific_classes',
                'teachers_only',
                'parents',
                'all',
            ])->default('all_students');
            $table->enum('voting_mode', [
                'token_kiosk',
                'online_login',
                'hybrid',
            ])->default('hybrid');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->enum('status', ['draft', 'active', 'closed'])->default('active');
            $table->boolean('allow_abstain')->default(false);
            $table->timestamps();

            $table->index(['school_id', 'status', 'type']);
        });

        // 2. Pasangan Calon / Kandidat / Opsi Pemilihan
        Schema::create('sch_vote_candidates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('event_id')->constrained('sch_vote_events')->cascadeOnDelete();
            $table->unsignedSmallInteger('candidate_number');
            $table->string('title', 150);
            $table->foreignId('leader_student_id')->nullable()->constrained('sch_std_students')->nullOnDelete();
            $table->foreignId('vice_student_id')->nullable()->constrained('sch_std_students')->nullOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('sch_hr_staff')->nullOnDelete();
            $table->text('vision')->nullable();
            $table->text('mission')->nullable();
            $table->string('photo_url')->nullable();
            $table->unsignedInteger('vote_count')->default(0);
            $table->timestamps();

            $table->unique(['event_id', 'candidate_number']);
        });

        // 3. Daftar Pemilih Tetap (DPT) & Generator Token Bilik Suara Sekali Pakai (ala ja-evoting)
        Schema::create('sch_vote_voters', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('event_id')->constrained('sch_vote_events')->cascadeOnDelete();
            $table->enum('voter_type', ['student', 'staff', 'parent', 'general'])->default('student');
            $table->foreignId('student_id')->nullable()->constrained('sch_std_students')->nullOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('sch_hr_staff')->nullOnDelete();
            $table->string('voter_name', 150);
            $table->string('voter_identifier', 50)->nullable(); // NISN / NIP / NIK
            $table->string('token', 16);
            $table->boolean('has_voted')->default(false);
            $table->timestamp('voted_at')->nullable();
            $table->timestamp('token_generated_at')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'token']);
            $table->index(['event_id', 'has_voted']);
            $table->index(['event_id', 'student_id']);
        });

        // 4. Kotak Suara Sah Terpisah (Secret Ballot / Luber Jurdil)
        Schema::create('sch_vote_ballots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('event_id')->constrained('sch_vote_events')->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained('sch_vote_candidates')->cascadeOnDelete();
            $table->enum('vote_mode', ['kiosk', 'online'])->default('kiosk');
            $table->string('token_hash', 64)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('device_info', 255)->nullable();
            $table->timestamp('voted_at');
            $table->timestamps();

            $table->index(['event_id', 'candidate_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sch_vote_ballots');
        Schema::dropIfExists('sch_vote_voters');
        Schema::dropIfExists('sch_vote_candidates');
        Schema::dropIfExists('sch_vote_events');
    }
};
