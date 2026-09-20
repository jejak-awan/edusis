<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Students (Buku Induk Siswa Dapodik-Compliant)
        Schema::create('sch_std_students', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->foreignId('school_level_id')->nullable()->constrained('sch_ins_levels')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('sch_acad_departments')->nullOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('srv_auth_users')->nullOnDelete();

            // Identitas Pokok Dapodik
            $table->string('nisn', 10)->unique()->nullable();
            $table->string('nis', 20)->nullable();
            $table->string('nik', 16)->nullable();
            $table->string('full_name', 150);
            $table->enum('gender', ['L', 'P']);
            $table->string('place_of_birth', 100)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('religion', 30)->nullable();

            // Alamat Domisili
            $table->text('address')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('dusun', 100)->nullable();
            $table->string('desa_kelurahan', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('kabupaten_kota', 100)->nullable();
            $table->string('provinsi', 100)->nullable();
            $table->string('postal_code', 10)->nullable();

            // Kontak
            $table->string('phone', 25)->nullable();
            $table->string('email', 100)->nullable();

            // Status Kesiswaan
            $table->string('entry_year', 10)->nullable(); // e.g. 2026
            $table->enum('status', ['active', 'graduated', 'transferred', 'dropped_out'])->default('active');
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'status']);
            $table->index('nisn');
            $table->index('nik');
            $table->index('full_name');
        });

        // Add foreign key constraint to study group members now that students table exists
        Schema::table('sch_acad_study_group_members', function (Blueprint $table): void {
            $table->foreign('student_id')->references('id')->on('sch_std_students')->cascadeOnDelete();
        });

        // Student Parents & Guardians (Data Orang Tua / Wali & Kesejahteraan)
        Schema::create('sch_std_parents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained('sch_std_students')->cascadeOnDelete();

            // Data Ayah
            $table->string('father_name', 100)->nullable();
            $table->string('father_nik', 16)->nullable();
            $table->year('father_birth_year')->nullable();
            $table->string('father_education', 50)->nullable();
            $table->string('father_occupation', 100)->nullable();
            $table->string('father_income', 50)->nullable();
            $table->string('father_phone', 25)->nullable();

            // Data Ibu
            $table->string('mother_name', 100)->nullable();
            $table->string('mother_nik', 16)->nullable();
            $table->year('mother_birth_year')->nullable();
            $table->string('mother_education', 50)->nullable();
            $table->string('mother_occupation', 100)->nullable();
            $table->string('mother_income', 50)->nullable();
            $table->string('mother_phone', 25)->nullable();

            // Data Wali
            $table->string('guardian_name', 100)->nullable();
            $table->string('guardian_nik', 16)->nullable();
            $table->year('guardian_birth_year')->nullable();
            $table->string('guardian_education', 50)->nullable();
            $table->string('guardian_occupation', 100)->nullable();
            $table->string('guardian_income', 50)->nullable();
            $table->string('guardian_phone', 25)->nullable();
            $table->string('guardian_relation', 50)->nullable();

            // Kesejahteraan Sosial & Bantuan
            $table->string('economic_status', 30)->nullable(); // mampu, kurang_mampu, tidak_mampu
            $table->boolean('is_pkh')->default(false);
            $table->boolean('is_kip')->default(false);
            $table->string('kip_number', 50)->nullable();
            $table->string('family_card_number', 20)->nullable(); // No Kartu Keluarga
            $table->string('emergency_phone', 25)->nullable();
            $table->string('emergency_contact_name', 100)->nullable();

            $table->timestamps();

            $table->index('father_nik');
            $table->index('mother_nik');
        });
    }

    public function down(): void
    {
        Schema::table('sch_acad_study_group_members', function (Blueprint $table): void {
            $table->dropForeign(['student_id']);
        });

        Schema::dropIfExists('sch_std_parents');
        Schema::dropIfExists('sch_std_students');
    }
};
