<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('category')->nullable()->after('action')->index();
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('low')->after('category');
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->text('details')->nullable()->after('user_agent');
        });

        Schema::table('login_attempts', function (Blueprint $table) {
            $table->string('user_agent')->nullable()->after('ip_address');
            $table->string('location')->nullable()->after('user_agent');
        });

        Schema::table('system_alerts', function (Blueprint $table) {
            $table->timestamp('resolved_at')->nullable()->after('is_resolved');
            $table->text('resolution_notes')->nullable()->after('resolved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn(['category', 'severity', 'user_agent', 'details']);
        });

        Schema::table('login_attempts', function (Blueprint $table) {
            $table->dropColumn(['user_agent', 'location']);
        });

        Schema::table('system_alerts', function (Blueprint $table) {
            $table->dropColumn(['resolved_at', 'resolution_notes']);
        });
    }
};
