<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('donation_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();
            $table->text('reason');
            $table->string('support_doc_url')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'handover', 'completed'])
                  ->default('pending');
            $table->text('donor_note')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('handover_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_requests');
    }
};
