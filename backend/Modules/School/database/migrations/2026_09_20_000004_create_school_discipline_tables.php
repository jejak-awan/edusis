<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kategori Poin Perilaku (Pelanggaran / Prestasi)
        Schema::create('sch_std_point_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100);
            $table->enum('type', ['pelanggaran', 'prestasi']);
            $table->integer('default_points'); // Negatif untuk pelanggaran (-5, -20), positif untuk prestasi (+10, +50)
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'is_active']);
        });

        // Transaksi Mutasi Kredit Poin Siswa (SISKA Engine)
        Schema::create('sch_std_credit_points', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained('sch_std_students')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('sch_std_point_categories')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('sch_acad_years')->nullOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained('sch_acad_semesters')->nullOnDelete();
            $table->integer('points'); // Nilai poin yang diberikan
            $table->text('description');
            $table->date('date');
            $table->foreignUuid('reporter_id')->nullable()->constrained('srv_auth_users')->nullOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'date']);
            $table->index('status');
        });

        // Rekap Pelanggaran & Pemicu Surat Peringatan (SP1, SP2, SP3)
        Schema::create('sch_std_violations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained('sch_std_students')->cascadeOnDelete();
            $table->foreignId('credit_point_id')->nullable()->constrained('sch_std_credit_points')->nullOnDelete();
            $table->date('violation_date');
            $table->text('sanction')->nullable(); // Bentuk sanksi/hukuman edukatif
            $table->enum('sp_level', ['none', 'sp1', 'sp2', 'sp3'])->default('none');
            $table->string('sp_letter_number', 50)->nullable();
            $table->date('sp_issued_at')->nullable();
            $table->text('follow_up_notes')->nullable();
            $table->boolean('is_resolved')->default(false);
            $table->timestamps();

            $table->index(['student_id', 'sp_level']);
            $table->index('is_resolved');
        });

        // Prestasi & Penghargaan Siswa
        Schema::create('sch_std_achievements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained('sch_std_students')->cascadeOnDelete();
            $table->foreignId('credit_point_id')->nullable()->constrained('sch_std_credit_points')->nullOnDelete();
            $table->string('title', 150);
            $table->enum('level', ['sekolah', 'kecamatan', 'kabupaten', 'provinsi', 'nasional', 'internasional'])->default('sekolah');
            $table->string('rank', 50)->nullable(); // e.g. Juara 1, Medali Emas
            $table->enum('type', ['akademik', 'non_akademik'])->default('akademik');
            $table->string('event_name', 150);
            $table->string('organizer', 150)->nullable();
            $table->date('event_date');
            $table->string('certificate_file')->nullable();
            $table->text('reward_description')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'level']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sch_std_achievements');
        Schema::dropIfExists('sch_std_violations');
        Schema::dropIfExists('sch_std_credit_points');
        Schema::dropIfExists('sch_std_point_categories');
    }
};
