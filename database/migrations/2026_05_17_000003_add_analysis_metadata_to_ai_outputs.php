<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buku_harian', function (Blueprint $table) {
            $table->json('analysis_metadata')->nullable()->after('confidence');
        });

        Schema::table('alerts', function (Blueprint $table) {
            $table->json('analysis_metadata')->nullable()->after('admin_steps');
        });
    }

    public function down(): void
    {
        Schema::table('buku_harian', function (Blueprint $table) {
            $table->dropColumn('analysis_metadata');
        });

        Schema::table('alerts', function (Blueprint $table) {
            $table->dropColumn('analysis_metadata');
        });
    }
};
