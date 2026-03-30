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
        Schema::table('contents', function (Blueprint $table) {
            $table->integer('bs_year')->nullable()->after('date');
            $table->integer('bs_month')->nullable()->after('bs_year');
            $table->integer('bs_day')->nullable()->after('bs_month');
        });

        Schema::table('boosts', function (Blueprint $table) {
            $table->integer('bs_year')->nullable()->after('date');
            $table->integer('bs_month')->nullable()->after('bs_year');
            $table->integer('bs_day')->nullable()->after('bs_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn(['bs_year', 'bs_month', 'bs_day']);
        });

        Schema::table('boosts', function (Blueprint $table) {
            $table->dropColumn(['bs_year', 'bs_month', 'bs_day']);
        });
    }
};
