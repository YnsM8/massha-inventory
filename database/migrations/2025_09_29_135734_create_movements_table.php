<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['incoming', 'outgoing']);
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('reason', [
                'purchase', 'event', 'production', 'waste', 'expiry', 
                'adjustment', 'return', 'transfer'
            ])->nullable();
            $table->string('reference', 100)->nullable();
            $table->string('batch_number', 50)->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('movement_date')->useCurrent();
            $table->timestamps();
            
            $table->index(['type', 'movement_date']);
            $table->index(['item_id', 'movement_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('movements');
    }
};