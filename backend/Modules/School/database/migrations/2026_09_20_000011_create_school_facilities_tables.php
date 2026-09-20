<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ruangan & Fasilitas Bangunan
        Schema::create('sch_fac_rooms', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->string('code', 30); // e.g. "LAB-RPL-01", "R-X-PPLG1"
            $table->string('name', 100);
            $table->enum('type', [
                'classroom',
                'laboratory',
                'workshop',
                'library',
                'hall',
                'office',
                'other',
            ])->default('classroom');
            $table->unsignedSmallInteger('capacity')->default(36);
            $table->unsignedTinyInteger('floor')->default(1);
            $table->enum('condition', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'type']);
            $table->unique(['school_id', 'code']);
        });

        // 2. Aset & Barang Inventaris Sarpras
        Schema::create('sch_fac_assets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('sch_fac_rooms')->nullOnDelete();
            $table->string('code', 50); // Barcode / Kode Aset e.g. "AST-PC-001"
            $table->string('name', 150);
            $table->enum('category', [
                'elektronik',
                'mebeler',
                'alat_praktik',
                'kendaraan',
                'buku',
                'lainnya',
            ])->default('elektronik');
            $table->string('brand_model', 100)->nullable();
            $table->year('acquisition_year')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->enum('condition', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');
            $table->string('source_of_fund', 50)->default('BOS');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'category']);
            $table->unique(['school_id', 'code']);
        });

        // 3. Tiket Laporan Kerusakan & Perbaikan Fasilitas
        Schema::create('sch_fac_maintenance_tickets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('sch_fac_rooms')->cascadeOnDelete();
            $table->foreignId('asset_id')->nullable()->constrained('sch_fac_assets')->nullOnDelete();
            $table->foreignId('reporter_staff_id')->constrained('sch_hr_staff')->cascadeOnDelete();

            $table->string('ticket_number', 50)->unique(); // e.g. "TKT/2026/0001"
            $table->text('issue_description');
            $table->enum('priority', ['rendah', 'sedang', 'tinggi', 'darurat'])->default('sedang');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'cancelled'])->default('open');
            $table->decimal('cost', 12, 2)->default(0);
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'status', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sch_fac_maintenance_tickets');
        Schema::dropIfExists('sch_fac_assets');
        Schema::dropIfExists('sch_fac_rooms');
    }
};
