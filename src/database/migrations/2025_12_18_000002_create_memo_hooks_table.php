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
      Schema::create('memo_hooks', function (Blueprint $table) {
         $table->foreignId('memo_id')
            ->constrained('memos')
            ->onUpdate('cascade')
            ->onDelete('cascade');
         $table->foreignId('hook_id')
            ->constrained('hooks')
            ->onUpdate('cascade')
            ->onDelete('cascade');
         // ハリス号数
         $table->decimal('leader_size', 4, 1)
            ->nullable()
            ->comment('号数');
         // ハリス上長(cm)
         $table->integer('leader_upper_cm')
            ->nullable()
            ->comment('cm');
         // ハリス下長(cm)
         $table->integer('leader_lower_cm')
            ->nullable()
            ->comment('cm');
         $table->primary(['memo_id', 'hook_id']);
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down(): void
   {
      Schema::dropIfExists('memo_hooks');
   }
};
