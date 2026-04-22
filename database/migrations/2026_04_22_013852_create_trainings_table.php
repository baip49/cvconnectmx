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
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // titulo
            $table->string('type'); // tipo
            $table->foreignId('target_role_id')->nullable()->constrained('roles')->onDelete('set null'); // rol_objetivo FK roles
            $table->integer('validity_days'); // vigencia_dias
            $table->boolean('is_active')->default(true); // activa
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
