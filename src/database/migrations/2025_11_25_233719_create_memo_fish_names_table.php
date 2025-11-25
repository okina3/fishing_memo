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
        Schema::create('memo_fish_names', function (Blueprint $table) {
            $table->foreignId('memo_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('fish_name_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->unsignedInteger('count')
                ->default(0)
                ->comment('匹数');
            $table->unsignedSmallInteger('length')
                ->nullable()
                ->comment('cm');
            $table->timestamps();
            $table->primary(['memo_id', 'fish_name_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memo_fish_names');
    }
};
