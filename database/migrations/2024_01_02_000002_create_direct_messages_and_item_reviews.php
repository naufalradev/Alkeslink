<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Chat langsung antara donatur & penerima, terikat per pengajuan
        Schema::create('direct_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_request_id')->constrained('donation_requests')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // Ulasan & bintang dari penerima untuk item yang sudah diterima
        Schema::create('item_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('donation_request_id')->constrained('donation_requests')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->tinyInteger('rating');           // 1–5
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique('donation_request_id');  // satu ulasan per pengajuan
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_reviews');
        Schema::dropIfExists('direct_messages');
    }
};
