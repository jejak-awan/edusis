<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Staff / Pendidik & Tenaga Kependidikan (GTK)
        Schema::create('sch_hr_staff', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('srv_auth_users')->nullOnDelete();

            // Identitas Pokok GTK
            $table->string('nip', 30)->unique()->nullable();
            $table->string('nuptk', 20)->unique()->nullable();
            $table->string('nik', 16)->nullable();
            $table->string('full_name', 150);
            $table->string('title_prefix', 30)->nullable(); // e.g. Drs., Ir., Dr.
            $table->string('title_suffix', 30)->nullable(); // e.g. M.Pd., S.Kom., M.T.
            $table->enum('gender', ['L', 'P']);
            $table->string('place_of_birth', 100)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('religion', 30)->nullable();

            // Kepegawaian
            $table->enum('employment_status', ['PNS', 'PPPK', 'GTY', 'PTY', 'Honorer', 'GTT', 'PTT'])->default('PNS');
            $table->string('role_title', 100)->default('Guru Mata Pelajaran'); // Kepala Sekolah, Guru, Staf TU, dll.
            $table->string('last_education', 50)->nullable(); // S1, S2, D3, SMA
            $table->string('major', 100)->nullable(); // Pendidikan Komputer, Matematika, dll.
            $table->boolean('certification_status')->default(false); // Sertifikasi Pendidik
            $table->string('sk_number', 100)->nullable(); // SK Pengangkatan

            // Kontak & Alamat
            $table->string('phone', 25)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('avatar_path')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'is_active']);
            $table->index('full_name');
            $table->index('employment_status');
        });

        // Shift Kerja GTK
        Schema::create('sch_hr_shifts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->string('name', 50); // e.g. Shift Reguler Guru
            $table->time('start_time'); // 07:00
            $table->time('end_time'); // 15:30
            $table->unsignedSmallInteger('grace_period_minutes')->default(15); // Toleransi 15 menit
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Presensi Guru & Tendik (Check-in / Check-out & QR/GPS)
        Schema::create('sch_hr_attendances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('staff_id')->constrained('sch_hr_staff')->cascadeOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained('sch_hr_shifts')->nullOnDelete();
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->enum('status', ['hadir', 'terlambat', 'izin', 'sakit', 'alpa'])->default('alpa');
            $table->unsignedSmallInteger('late_minutes')->default(0);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('device_info')->nullable();
            $table->text('notes')->nullable();
            $table->string('attachment_path')->nullable(); // Surat dokter jika sakit
            $table->timestamps();

            $table->unique(['staff_id', 'date']);
            $table->index(['date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sch_hr_attendances');
        Schema::dropIfExists('sch_hr_shifts');
        Schema::dropIfExists('sch_hr_staff');
    }
};
