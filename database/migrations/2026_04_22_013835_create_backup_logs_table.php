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
        Schema::create('backup_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // tipo
            $table->string('frequency'); // frecuencia
            $table->string('destination_path'); // ruta_destino
            $table->unsignedBigInteger('size_bytes'); // tamano_bytes
            $table->string('checksum_sha256'); // checksum_sha256
            $table->boolean('is_encrypted'); // cifrado
            $table->string('status'); // estado
            $table->boolean('restoration_tested')->default(false); // prueba_restauracion
            $table->integer('retention_days'); // retencion_dias
            $table->foreignId('executed_by')->nullable()->constrained('users'); // ejecutado_por
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_logs');
    }
};
