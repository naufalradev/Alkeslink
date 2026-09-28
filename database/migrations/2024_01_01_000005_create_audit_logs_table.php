<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type', 50); // 'item' or 'donation_request'
            $table->unsignedBigInteger('entity_id');
            $table->foreignId('actor_id')->constrained('users')->cascadeOnDelete();
            $table->string('action', 100);
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            // No updated_at — audit logs are immutable
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
