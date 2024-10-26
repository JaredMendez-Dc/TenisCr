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
        Schema::create('tenis', function (Blueprint $table) {
            $table->id();
            $table->string('color', 50); 
            $table->string('talla', 10); 
            $table->decimal('costo', 8, 2); 
            $table->foreignId('marca_id')->constrained('marcas') 
                ->onUpdate('cascade')->onDelete('restrict'); 
            $table->string('categoria', 100); 
            $table->string('imagen',90);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenis');
    }
};
