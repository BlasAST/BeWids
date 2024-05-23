<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->string('receptor')->nullable(); // Añadir la columna 'receptor'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->dropColumn('receptor'); // Revertir los cambios, eliminar la columna 'receptor'
        });
    }
};
