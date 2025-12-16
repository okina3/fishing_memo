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
      Schema::create('memo_rods', function (Blueprint $table) {
         $table->foreignId('memo_id')
            ->constrained('memos')
            ->onUpdate('cascade')
            ->onDelete('cascade');
         $table->foreignId('rod_id')
            ->constrained('rods')
            ->onUpdate('cascade')
            ->onDelete('cascade');
         // 道糸
         $table->decimal('main_line', 4, 2)
            ->nullable()
            ->comment('号数');
         $table->primary(['memo_id', 'rod_id']);
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down(): void
   {
      Schema::dropIfExists('memo_rods');
   }
};
