<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Clean up Data Model Studio menu entries from sys_console_menus
        if (Schema::hasTable('sys_console_menus')) {
            DB::table('sys_console_menus')
                ->where('route_name', 'model-index')
                ->orWhere('group_slug', 'studio')
                ->delete();
        }

        // 2. Drop legacy Data Model Studio dynamic record and content type tables
        Schema::dropIfExists('sys_dynamic_records');
        Schema::dropIfExists('sys_content_types');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('sys_content_types', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('fields')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('sys_dynamic_records', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('content_type_id')->index();
            $table->json('data')->nullable();
            $table->timestamps();

            $table->foreign('content_type_id')
                ->references('id')
                ->on('sys_content_types')
                ->cascadeOnDelete();
        });
    }
};
