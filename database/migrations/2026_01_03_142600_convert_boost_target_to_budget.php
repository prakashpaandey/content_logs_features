<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add new boost_budget column (nullable initially)
        Schema::table('monthly_targets', function (Blueprint $table) {
            $table->decimal('target_boost_budget', 10, 2)->nullable()->after('target_reels');
        });

        // Step 3: Drop old target_boosts column
        Schema::table('monthly_targets', function (Blueprint $table) {
            $table->dropColumn('target_boosts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate old column
        Schema::table('monthly_targets', function (Blueprint $table) {
            $table->integer('target_boosts')->default(0)->after('target_reels');
        });

        // Drop new column
        Schema::table('monthly_targets', function (Blueprint $table) {
            $table->dropColumn('target_boost_budget');
        });
    }
};
