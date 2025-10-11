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
        Schema::create('solicitudes', function (Blueprint $table) {
        $table->id();
        $table->string('codigo_solicitud', 20)->unique();
        $table->string('evento', 200);
        $table->date('fecha_evento');
        $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
        $table->text('observaciones')->nullable();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('aprobado_por')->nullable()->constrained('users')->onDelete('set null');
        $table->timestamp('fecha_aprobacion')->nullable();
        $table->timestamps();
    });

    // Tabla para los items de cada solicitud
    Schema::create('solicitud_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('solicitud_id')->constrained('solicitudes')->onDelete('cascade');
        $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
        $table->decimal('cantidad_solicitada', 10, 2);
        $table->decimal('cantidad_disponible', 10, 2);
        $table->boolean('stock_suficiente')->default(true);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('solicitud_items');
    Schema::dropIfExists('solicitudes');
    }
};
