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
        Schema::create('cat_items', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // 'profile' o 'status'
            $table->unsignedTinyInteger('code'); // Valores 1, 2, etc.
            $table->string('description'); // Descripción
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_items');
    }
};
