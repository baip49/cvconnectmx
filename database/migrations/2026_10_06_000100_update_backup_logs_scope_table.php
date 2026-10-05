<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('backup_logs')->where('type', 'database')->update(['type' => 'full']);

        Schema::table('backup_logs', function (Blueprint $table) {
            $table->string('scope')->default('database')->after('type');
            $table->foreignId('schedule_id')->nullable()->after('executed_by')->constrained('backup_schedules')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('backup_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('schedule_id');
            $table->dropColumn('scope');
        });
    }
};
