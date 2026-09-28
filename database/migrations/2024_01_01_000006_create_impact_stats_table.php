<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('impact_stats', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->unsignedInteger('total_items')->default(0);
            $table->decimal('total_value_idr', 15, 2)->default(0);
            $table->unsignedInteger('total_cities')->default(0);
            $table->unsignedInteger('total_donors')->default(0);
            $table->unsignedInteger('total_recipients')->default(0);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impact_stats');
    }
};
