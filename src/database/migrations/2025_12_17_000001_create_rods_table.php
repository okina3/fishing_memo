<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up(): void
   {
      Schema::create('rods', function (Blueprint $table) {
         $table->id();
         $table->string('name', 30);
         $table->foreignId('user_id')
            ->constrained()
            ->cascadeOnDelete();
         $table->timestamps();

         $table->unique(['user_id', 'name']);
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down(): void
   {
      Schema::dropIfExists('rods');
   }
};
