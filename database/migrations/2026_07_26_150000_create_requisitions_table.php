<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->string('product_name');
            $table->integer('requested_quantity')->default(1);
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('en_attente'); // 'en_attente', 'approvisionne'
            $table->string('type')->default('demande_client'); // 'demande_client', 'seuil_alerte', 'approche_alerte'
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisitions');
    }
};
