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
            $table->text('platform')->change();
        });

        // Migrate existing data
        $contents = \DB::table('contents')->get();
        foreach ($contents as $content) {
            // Check if it's already a JSON array or a single string
            $platform = $content->platform;
            if ($platform && $platform[0] !== '[' && $platform[0] !== '{') {
                \DB::table('contents')
                    ->where('id', $content->id)
                    ->update(['platform' => json_encode([$platform])]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->enum('platform', ['Instagram', 'TikTok', 'Facebook'])->change();
        });
    }
};
