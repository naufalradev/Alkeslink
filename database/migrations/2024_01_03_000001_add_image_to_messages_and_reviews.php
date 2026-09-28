<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Chat messages dapat menyertakan satu gambar
        Schema::table('direct_messages', function (Blueprint $table) {
            $table->string('image_url')->nullable()->after('body');
        });

        // Review dapat menyertakan hingga 3 gambar (tabel terpisah)
        Schema::create('review_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_review_id')->constrained('item_reviews')->cascadeOnDelete();
            $table->string('image_url');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_images');
        Schema::table('direct_messages', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });
    }
};
