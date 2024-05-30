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
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();

            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('inicio')->nullable();
            $table->string('fin')->nullable();
            $table->string('horario')->nullable();
            $table->string('horas')->nullable();
            $table->string('dias')->nullable();
            $table->string('precio', 8, 2)->nullable(); // Precio con dos decimales
            $table->string('calle')->nullable();
            $table->unsignedBigInteger('cp', 5)->nullable(); // Código postal puede ser más corto
            $table->string('localidad')->nullable();
            $table->string('lugar')->nullable();
            $table->string('conex')->nullable();
            $table->decimal('latitud', 10, 7)->nullable(); // Latitud con precisión
            $table->decimal('longitud', 10, 7)->nullable(); // Longitud con precisión
            $table->string('edad')->nullable();
            $table->string('api'); // Identificador de la API de origen

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
