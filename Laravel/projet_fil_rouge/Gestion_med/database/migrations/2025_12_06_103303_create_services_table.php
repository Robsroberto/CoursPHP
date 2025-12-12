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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->decimal('prix', 8, 2)->default(0);
            $table->integer('duree');
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->unsignedBigInteger('medecin_id')->nullable();
            $table->timestamps();
            
            $table->foreign('medecin_id')->references('id')->on('users');
        });
    }
    
    /**
    * Reverse the migrations.
    */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
