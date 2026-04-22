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
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('professional_title')->nullable(); // titulo_profesional
            $table->text('summary')->nullable(); // resumen
            $table->string('city')->nullable(); // ciudad
            $table->decimal('expected_salary', 10, 2)->nullable(); // salario_esperado
            $table->text('phone_encrypted')->nullable(); // telefono_encriptado
            $table->text('ssn_encrypted')->nullable(); // ssn_encriptado
            $table->text('tax_id_encrypted')->nullable(); // id_impuesto_encriptado
            $table->boolean('is_public_profile')->default(false); // perfil_publico
            $table->string('cv_url')->nullable(); // url_cv
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
