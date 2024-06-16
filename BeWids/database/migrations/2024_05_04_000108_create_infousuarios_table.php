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
        Schema::create('infousuarios', function (Blueprint $table) {
            $table->id();
            $table->string('id_user')->nullable();
            $table->string('nombre');
            $table->date('fecha_nacimiento')->nullable();
            $table->string('descripcion')->nullable();
            $table->string('numero_contacto')->nullable();
            $table->string('provincia')->nullable();
            $table->string('foto_perfil');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infousuarios');
    }
};
