<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['low_stock', 'expired', 'expiring_soon']);
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamp('alert_date')->useCurrent();
            $table->timestamps();
            
            $table->index(['is_read', 'alert_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_alerts');
    }
};