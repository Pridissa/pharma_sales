<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code_barre')->unique()->nullable();
            $table->string('name');
            $table->string('dci')->nullable(); // Dénomination Commune Internationale / Principe actif
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->decimal('purchase_price', 10, 2)->default(0);
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock_alert')->default(10);
            $table->date('expiration_date')->nullable();
            $table->boolean('requires_prescription')->default(false);
            $table->string('dosage_unit')->nullable(); // ex: Boîte 16 comprimés, Flacon 150ml
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
