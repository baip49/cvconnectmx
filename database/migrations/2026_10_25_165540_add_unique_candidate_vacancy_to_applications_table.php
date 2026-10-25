<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Keep the oldest application per candidate and vacancy, then forbid duplicates.
     */
    public function up(): void
    {
        // Plucked first: MySQL rejects a DELETE whose subquery reads the same table.
        $keepIds = DB::table('applications')
            ->selectRaw('MIN(id) as id')
            ->groupBy('candidate_id', 'vacancy_id')
            ->pluck('id');

        DB::table('applications')->whereNotIn('id', $keepIds)->delete();

        Schema::table('applications', function (Blueprint $table) {
            $table->unique(['candidate_id', 'vacancy_id']);
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropUnique(['candidate_id', 'vacancy_id']);
        });
    }
};
