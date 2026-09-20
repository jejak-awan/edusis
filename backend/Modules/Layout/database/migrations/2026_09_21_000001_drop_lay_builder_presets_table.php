<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('lay_builder_presets');
    }

    public function down(): void
    {
        Schema::create('lay_builder_presets', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable()->index();
            $table->string('type', 50)->index();
            $table->string('name');
            $table->json('settings');
            $table->boolean('is_system')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('srv_auth_users')->nullOnDelete();
        });
    }
};
