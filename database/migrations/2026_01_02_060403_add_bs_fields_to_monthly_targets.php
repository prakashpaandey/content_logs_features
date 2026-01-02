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
        Schema::table('monthly_targets', function (Blueprint $table) {
            $table->integer('bs_month')->nullable()->after('client_id');
            $table->integer('bs_year')->nullable()->after('bs_month');
        });

        // Backfill existing targets
        $targets = DB::table('monthly_targets')->get();
        foreach ($targets as $target) {
            if ($target->month) {
                $m = date('n', strtotime($target->month));
                $y = date('Y', strtotime($target->month));
                
                // Representative Mapping Logic
                if ($m >= 4) {
                    $bsMonth = $m - 3;
                    $bsYear = $y + 57;
                } else {
                    $bsMonth = $m + 9;
                    $bsYear = $y + 56;
                }
                
                DB::table('monthly_targets')->where('id', $target->id)->update([
                    'bs_month' => $bsMonth,
                    'bs_year' => $bsYear
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_targets', function (Blueprint $table) {
            $table->dropColumn(['bs_month', 'bs_year']);
        });
    }
};
