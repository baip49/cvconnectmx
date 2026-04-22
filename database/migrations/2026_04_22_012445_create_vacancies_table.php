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
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->text('requirements');
            $table->string('work_model'); // modelo_trabajo
            $table->decimal('min_salary', 10, 2)->nullable(); // salario_minimo
            $table->decimal('max_salary', 10, 2)->nullable(); // salario_maximo
            $table->boolean('show_salary')->default(true); // mostrar_salario
            $table->string('status'); // estado
            $table->timestamp('published_at')->nullable(); // publicado_en
            $table->timestamp('expires_at')->nullable(); // expira_en
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacancies');
    }
};
