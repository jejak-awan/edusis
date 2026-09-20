<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Administrasi Persuratan & Disposisi TU
        Schema::create('sch_adm_letters', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->enum('type', ['incoming', 'outgoing'])->default('incoming');
            $table->string('letter_number', 100)->unique();
            $table->string('reference_number', 100)->nullable(); // Nomor asal surat untuk surat masuk
            $table->enum('category', [
                'invitation',
                'assignment',
                'decree',
                'certificate',
                'circular',
                'general',
            ])->default('general');

            $table->string('sender', 200); // Pengirim instansi / pihak
            $table->string('recipient', 200); // Penerima surat
            $table->string('subject', 255); // Perihal
            $table->date('letter_date'); // Tanggal tercantum pada surat
            $table->date('received_or_sent_date'); // Tanggal masuk/keluar di TU
            $table->enum('status', ['draft', 'registered', 'dispatched', 'archived'])->default('registered');

            // Lembar Disposisi Kepala Sekolah / Pimpinan
            $table->string('disposition_to', 200)->nullable();
            $table->text('disposition_instruction')->nullable();
            $table->enum('disposition_status', ['pending', 'processed', 'completed'])->default('pending');

            $table->string('attachment_path', 255)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'type', 'letter_date']);
            $table->index('category');
            $table->index('disposition_status');
        });

        // 2. PPDB Online (Penerimaan Peserta Didik Baru)
        Schema::create('sch_adm_ppdb_registrations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('sch_acad_years')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('sch_acad_departments')->cascadeOnDelete();
            $table->foreignId('second_choice_department_id')->nullable()->constrained('sch_acad_departments')->nullOnDelete();

            $table->string('registration_number', 50)->unique(); // e.g. "PPDB/2026/0001"
            $table->enum('track', [
                'zonasi',
                'afirmasi',
                'prestasi_akademik',
                'prestasi_non_akademik',
                'perpindahan_ortu',
            ])->default('zonasi');

            // Biodata Calon Siswa
            $table->string('full_name', 150);
            $table->string('nisn', 20)->nullable();
            $table->string('nik', 20)->nullable();
            $table->enum('gender', ['L', 'P'])->default('L');
            $table->string('place_of_birth', 100)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('religion', 30)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();

            // Biodata Orang Tua / Wali
            $table->string('parent_name', 150)->nullable();
            $table->string('parent_phone', 30)->nullable();
            $table->string('parent_job', 100)->nullable();
            $table->string('parent_income', 50)->nullable();

            // Akademik Asal Sekolah
            $table->string('previous_school', 150)->nullable();
            $table->decimal('average_report_score', 5, 2)->default(0);

            // Berkas & Verifikasi Panitia
            $table->enum('status', ['submitted', 'verified', 'accepted', 'rejected'])->default('submitted');
            $table->json('document_paths')->nullable(); // {"kk": "...", "akta": "...", "ijazah": "...", "prestasi": "..."}
            $table->foreignId('verified_by')->nullable()->constrained('sch_hr_staff')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'academic_year_id', 'status']);
            $table->index('track');
            $table->index('department_id');
        });

        // 3. Buku Tamu Resepsionis / Pos Keamanan
        Schema::create('sch_adm_guest_books', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->string('visitor_name', 150);
            $table->string('institution', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->enum('identity_type', ['KTP', 'SIM', 'Passport', 'Lainnya'])->default('KTP');
            $table->string('identity_number', 50)->nullable();

            $table->string('purpose', 255); // Maksud & keperluan kunjungan
            $table->string('target_person', 150); // Menemui siapa / bagian apa

            $table->timestamp('check_in_at');
            $table->timestamp('check_out_at')->nullable();
            $table->string('badge_number', 50)->nullable(); // Nomor ID Card Tamu e.g. "TAMU-08"
            $table->enum('status', ['checked_in', 'checked_out'])->default('checked_in');

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'status', 'check_in_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sch_adm_guest_books');
        Schema::dropIfExists('sch_adm_ppdb_registrations');
        Schema::dropIfExists('sch_adm_letters');
    }
};
