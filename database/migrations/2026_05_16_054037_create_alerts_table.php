<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('sumber', ['buku_harian', 'self_check', 'forum_post']);
            $table->unsignedBigInteger('sumber_id');
            $table->tinyInteger('label');
            $table->string('risk_level', 20);
            $table->float('confidence');
            $table->json('kata_kunci')->nullable();
            $table->string('cuplikan_teks', 255);
            $table->boolean('is_handled')->default(0);
            $table->foreignId('handled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('handled_at')->nullable();
            $table->boolean('identity_opened')->default(0);
            $table->foreignId('opened_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('opened_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
