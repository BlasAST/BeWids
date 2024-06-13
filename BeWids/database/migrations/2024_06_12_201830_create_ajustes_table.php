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
        Schema::create('ajustes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_portal');
            $table->boolean('crear_participante')->default(true);
            $table->boolean('crear_invitacion')->default(true);
            $table->boolean('aniadir_gasto')->default(false);
            $table->boolean('aniadir_lista')->default(false);
            $table->boolean('aniadir_cal')->default(false);
            $table->boolean('crear_encuesta')->default(false);
            $table->boolean('modif_cal')->default(false);
            $table->boolean('pers_portal')->default(true);

            $table->timestamps();

            $table->foreign('id_portal')->references('id')->on('portales')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ajustes');
    }
};
