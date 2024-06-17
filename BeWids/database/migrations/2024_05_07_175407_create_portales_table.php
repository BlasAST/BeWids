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
        Schema::create('portales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre',30);
            $table->string('fondo')->nullable()->default(null);
            $table->string('color_titulo')->default('#ffffff');
            $table->string('token_portal')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portales');
    }
};
