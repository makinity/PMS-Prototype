<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_name')->nullable();
            $table->string('actor_role', 50)->nullable();
            $table->string('action_key', 100);
            $table->string('module_key', 120);
            $table->string('target_type', 120)->nullable();
            $table->string('target_id', 120)->nullable();
            $table->string('route_name', 150)->nullable();
            $table->string('http_method', 12);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('status', 20);
            $table->string('summary', 255);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['created_at']);
            $table->index(['status', 'created_at']);
            $table->index(['module_key', 'created_at']);
            $table->index(['action_key', 'created_at']);
            $table->index(['actor_user_id', 'created_at']);
            $table->index(['actor_role', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
