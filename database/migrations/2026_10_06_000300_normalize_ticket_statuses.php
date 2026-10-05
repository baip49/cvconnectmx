<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('tickets')->where('status', 'resolved')->update(['status' => 'in_progress']);
    }

    public function down(): void
    {
        // Los estados se normalizaron a abierto/en progreso/cerrado y no se revierten.
    }
};
