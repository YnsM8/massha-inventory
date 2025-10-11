<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->enum('category', ['Carnes', 'Verduras', 'Lácteos', 'Especias', 'Bebidas', 'Otros']);
            $table->decimal('current_stock', 10, 2)->default(0);
            $table->decimal('min_stock', 10, 2);
            $table->enum('unit', ['kg', 'g', 'l', 'ml', 'unid', 'paq']);
            $table->enum('status', ['normal', 'low', 'expired'])->default('normal');
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->foreignId('default_supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index(['category', 'status']);
            $table->index(['current_stock', 'min_stock']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('items');
    }
};