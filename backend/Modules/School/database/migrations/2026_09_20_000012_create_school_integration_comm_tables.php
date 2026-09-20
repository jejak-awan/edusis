<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Konfigurasi Jembatan KataMPI CBT Integration
        Schema::create('sch_int_katampi_configs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->string('cbt_url', 255)->default('http://localhost:8787');
            $table->string('api_key', 128)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_sync_at')->nullable();
            $table->json('sync_stats')->nullable(); // {"students_synced": 150, "classes_synced": 6, "grades_ingested": 450}
            $table->timestamps();

            $table->unique('school_id');
        });

        // 2. Log Notifikasi Pesan WhatsApp Gateway
        Schema::create('sch_comm_wa_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('sch_ins_schools')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('sch_std_students')->nullOnDelete();
            $table->string('recipient_phone', 30);
            $table->string('recipient_name', 150)->nullable();
            $table->enum('message_type', [
                'attendance_alert',
                'discipline_sp',
                'achievement',
                'announcement',
                'general',
            ])->default('general');
            $table->text('content');
            $table->enum('status', ['queued', 'sent', 'delivered', 'failed'])->default('sent');
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'status', 'created_at']);
            $table->index('recipient_phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sch_comm_wa_logs');
        Schema::dropIfExists('sch_int_katampi_configs');
    }
};
