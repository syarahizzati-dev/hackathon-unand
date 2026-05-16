<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('self_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->tinyInteger('jawaban_1');
            $table->tinyInteger('jawaban_2');
            $table->tinyInteger('jawaban_3');
            $table->tinyInteger('jawaban_4');
            $table->tinyInteger('jawaban_5');
            $table->tinyInteger('skor_total');
            $table->text('teks_gabung');
            $table->tinyInteger('label');
            $table->string('risk_level', 20);
            $table->float('confidence');
            $table->timestamps();
        });
        
        // Constraints handled at application layer due to DB engine functional index limitations
    }

    public function down(): void
    {
        Schema::dropIfExists('self_checks');
    }
};
