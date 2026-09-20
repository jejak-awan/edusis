<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sch_ins_schools', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('type', 20)->default('swasta'); // negeri, swasta
            $table->string('npsn', 10)->unique()->nullable();
            $table->string('nss', 30)->nullable();
            $table->string('accreditation', 10)->nullable(); // A, B, C, Unggul
            $table->string('curriculum', 50)->default('Kurikulum Merdeka');

            // Location & Address
            $table->text('address')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('dusun', 100)->nullable();
            $table->string('desa_kelurahan', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('kabupaten_kota', 100)->nullable();
            $table->string('provinsi', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Contacts
            $table->string('phone', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('website', 150)->nullable();

            // Profile
            $table->string('principal_name', 150)->nullable();
            $table->string('principal_nip', 30)->nullable();
            $table->text('vision')->nullable();
            $table->text('mission')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('sch_ins_levels', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->string('name', 50); // e.g. Kelas 10 / X
            $table->unsignedSmallInteger('grade_level'); // 10, 11, 12
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sch_ins_levels');
        Schema::dropIfExists('sch_ins_schools');
    }
};
