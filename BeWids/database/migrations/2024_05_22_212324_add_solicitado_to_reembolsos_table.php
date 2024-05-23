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
        Schema::table('reembolsos', function (Blueprint $table) {
            $table->boolean('solicitado')->default(false); // Añadir la columna con un valor por defecto
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('reembolsos', function (Blueprint $table) {
            $table->dropColumn('solicitado'); // Eliminar la columna si se revierte la migración
        });
    }
};
