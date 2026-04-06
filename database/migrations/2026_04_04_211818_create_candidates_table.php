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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relación con la tabla de usuarios
            $table->string('phone', 20)->nullable(); // Teléfono
            $table->integer('age')->nullable(); // Edad
            $table->enum('sex', ['M', 'F'])->default('M'); // Sexo (Se castea como $candidate->sex->label() :"Masculino/Femenino" o $candidate->sex->value :"M/F")
            $table->string('address', 255)->nullable(); // Dirección
            $table->integer('scoring')->default(0); // Scoring (Puntaje de evaluación)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
