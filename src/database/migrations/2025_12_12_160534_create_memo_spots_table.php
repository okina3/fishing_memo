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
        Schema::create('memo_spots', function (Blueprint $table) {
            $table->foreignId('memo_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('spot_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            // 流れの有無
            $table->string('river_flow', 20)
                ->nullable();
            // 濁度
            $table->string('turbidity')
                ->nullable();
            // 水位
            $table->decimal('water_level', 4, 1)
                ->nullable();
            // 水温
            $table->integer('water_temp')
                ->nullable();
            $table->timestamps();
            $table->primary(['memo_id', 'spot_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memo_spots');
    }
};
