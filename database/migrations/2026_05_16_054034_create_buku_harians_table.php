<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buku_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('isi');
            $table->text('ai_reply')->nullable();
            $table->json('ai_saran')->nullable();
            $table->tinyInteger('label')->nullable();
            $table->string('risk_level', 20)->nullable();
            $table->float('confidence')->nullable();
            $table->boolean('is_analyzed')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buku_harian');
    }
};
