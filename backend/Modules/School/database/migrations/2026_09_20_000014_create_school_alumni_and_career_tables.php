<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Angkatan Kelulusan (Graduation Batches)
        Schema::create('sch_graduation_batches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('sch_acad_years')->nullOnDelete();
            $table->string('batch_year', 4); // e.g. 2026
            $table->string('batch_name', 100); // e.g. Angkatan 2025/2026
            $table->date('graduation_date');
            $table->string('decree_number', 100)->nullable(); // Nomor SK Kelulusan
            $table->unsignedInteger('total_students')->default(0);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'batch_year']);
        });

        // 2. Direktori Alumni & Tracer Study
        Schema::create('sch_alumnis', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('sch_std_students')->cascadeOnDelete();
            $table->foreignId('graduation_batch_id')->nullable()->constrained('sch_graduation_batches')->nullOnDelete();
            $table->string('graduation_year', 4); // e.g. 2026
            $table->string('skl_number', 100)->nullable(); // Nomor Surat Keterangan Lulus
            $table->string('certificate_number', 100)->nullable(); // Nomor Ijazah
            $table->decimal('average_score', 5, 2)->nullable();
            $table->enum('career_status', [
                'kuliah',
                'kerja',
                'wirausaha',
                'mencari_kerja',
                'lainnya',
            ])->default('mencari_kerja');
            $table->string('company_or_campus', 150)->nullable();
            $table->string('current_position', 150)->nullable();
            $table->string('salary_range', 50)->nullable();
            $table->string('contact_phone', 25)->nullable();
            $table->string('contact_email', 100)->nullable();
            $table->string('linkedin_url', 255)->nullable();
            $table->string('portfolio_url', 255)->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_public')->default(true);
            $table->timestamps();

            $table->unique(['school_id', 'student_id']);
            $table->index(['school_id', 'graduation_year', 'career_status']);
        });

        // 3. Bursa Kerja Khusus (BKK / Career Vacancies)
        Schema::create('sch_career_vacancies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('sch_acad_departments')->nullOnDelete();
            $table->string('company_name', 150);
            $table->string('company_logo_url', 255)->nullable();
            $table->string('job_title', 150);
            $table->enum('job_type', [
                'fulltime',
                'parttime',
                'internship',
                'freelance',
                'contract',
            ])->default('fulltime');
            $table->enum('workplace_type', [
                'on_site',
                'remote',
                'hybrid',
            ])->default('on_site');
            $table->string('location', 150)->nullable();
            $table->string('salary_range', 100)->nullable();
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->text('benefits')->nullable();
            $table->date('application_deadline')->nullable();
            $table->string('contact_email', 100)->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->string('external_url', 255)->nullable();
            $table->enum('status', ['draft', 'published', 'closed'])->default('published');
            $table->timestamps();

            $table->index(['school_id', 'status', 'job_type']);
        });

        // 4. Lamaran Pekerjaan (Job Applications)
        Schema::create('sch_career_applications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('vacancy_id')->constrained('sch_career_vacancies')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('sch_std_students')->nullOnDelete();
            $table->foreignId('alumni_id')->nullable()->constrained('sch_alumnis')->nullOnDelete();
            $table->string('applicant_name', 150);
            $table->string('applicant_email', 100);
            $table->string('applicant_phone', 30);
            $table->string('resume_url', 255)->nullable();
            $table->string('portfolio_url', 255)->nullable();
            $table->text('cover_letter')->nullable();
            $table->enum('status', [
                'submitted',
                'reviewed',
                'shortlisted',
                'interview',
                'accepted',
                'rejected',
            ])->default('submitted');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['vacancy_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sch_career_applications');
        Schema::dropIfExists('sch_career_vacancies');
        Schema::dropIfExists('sch_alumnis');
        Schema::dropIfExists('sch_graduation_batches');
    }
};
