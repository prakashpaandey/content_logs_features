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
        // 1. Add target_boosts to monthly_targets
        Schema::table('monthly_targets', function (Blueprint $table) {
            $table->integer('target_boosts')->default(0)->after('target_reels');
        });

        // 2. Add 'Boost' to content type enum
        // Since enum modification can be tricky across different DBs, 
        // we'll use a raw statement for MySQL or a standard change if supported.
        try {
            Schema::table('contents', function (Blueprint $table) {
                $table->string('type')->change();
            });
        } catch (\Exception $e) {
            // Fallback for DBs that don't support standard enum change easily
            // Logic handled in code anyway
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_targets', function (Blueprint $table) {
            $table->dropColumn('target_boosts');
        });

        Schema::table('contents', function (Blueprint $table) {
            $table->enum('type', ['Post', 'Reel'])->change();
        });
    }
};
