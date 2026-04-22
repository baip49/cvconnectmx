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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // tipo
            $table->enum('level', ['low', 'medium', 'high']); // nivel
            $table->string('status'); // estado
            $table->text('description'); // descripcion
            $table->foreignId('affected_user_id')->nullable()->constrained('users')->onDelete('set null'); // usuario_afectado_id
            $table->json('evidence')->nullable(); // evidencia_json
            $table->timestamp('detected_at'); // detectado_en
            $table->text('lessons_learned')->nullable(); // lecciones
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
