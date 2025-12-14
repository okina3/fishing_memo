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
        Schema::create('memos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            // 釣行日時
            $table->date('fishing_date');
            $table->time('start_time');
            $table->time('end_time');
            // 天候・気温・風向き
            $table->string('weather', 15);
            $table->integer('air_temp')->nullable();
            $table->string('wind_dir', 2)->nullable();
            // 備考
            $table->text('content');
            //ソフトデリート
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memos');
    }
};
