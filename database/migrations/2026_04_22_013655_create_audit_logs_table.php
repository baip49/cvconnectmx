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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // accion
            $table->string('entity_type')->nullable(); // entidad (Polimórfica)
            $table->unsignedBigInteger('entity_id')->nullable(); // entidad_id
            $table->json('old_data')->nullable(); // datos_anteriores JSON
            $table->json('new_data')->nullable(); // datos_nuevos JSON
            $table->ipAddress('ip_address')->nullable(); // ip_origen
            $table->string('result')->nullable(); // resultado
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
