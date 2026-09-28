<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('donation_requests', function (Blueprint $table) {
            $table->boolean('donor_confirmed')->default(false)->after('completed_at');
            $table->boolean('recipient_confirmed')->default(false)->after('donor_confirmed');
            $table->boolean('admin_confirmed')->default(false)->after('recipient_confirmed');
            $table->timestamp('donor_confirmed_at')->nullable()->after('admin_confirmed');
            $table->timestamp('recipient_confirmed_at')->nullable()->after('donor_confirmed_at');
            $table->timestamp('admin_confirmed_at')->nullable()->after('recipient_confirmed_at');
        });
    }

    public function down(): void
    {
        Schema::table('donation_requests', function (Blueprint $table) {
            $table->dropColumn([
                'donor_confirmed', 'recipient_confirmed', 'admin_confirmed',
                'donor_confirmed_at', 'recipient_confirmed_at', 'admin_confirmed_at',
            ]);
        });
    }
};
