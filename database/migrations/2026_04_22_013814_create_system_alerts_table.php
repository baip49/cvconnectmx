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
        Schema::create('system_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // tipo
            $table->string('level'); // nivel
            $table->text('message'); // mensaje
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('is_resolved')->default(false); // resuelta
            $table->foreignId('reviewed_by')->nullable()->constrained('users'); // revisada_por
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_alerts');
    }
};
