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
        Schema::create('mis_eventos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_portal');
            $table->boolean('aniadido')->default(false);
            $table->string('fecha_cal')->default(null);
            $table->string('hora_inicio')->default(null);
            $table->string('hora_fin')->default(null);
            $table->string('titulo')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('inicio')->nullable();
            $table->string('fin')->nullable();
            $table->string('horario')->nullable();
            $table->string('horas')->nullable();
            $table->string('dias')->nullable();
            $table->text('precio')->nullable(); // Precio con dos decimales
            $table->string('calle')->nullable();
            $table->string('cp', 5)->nullable(); // Código postal puede ser más corto
            $table->string('localidad')->nullable();
            $table->string('lugar')->nullable();
            $table->string('conex')->nullable();
            $table->decimal('latitud', 10, 7)->nullable(); // Latitud con precisión
            $table->decimal('longitud', 10, 7)->nullable(); // Longitud con precisión
            $table->string('edad')->nullable();
            $table -> string('categoria')->nullable();
            $table -> string('url')->nullable();
            $table->string('api');
            


            $table->timestamps();

            $table->foreign('id_portal')->references('id')->on('portales')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mis_eventos');
    }
};
