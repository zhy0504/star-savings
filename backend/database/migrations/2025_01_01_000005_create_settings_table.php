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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // Setting key (e.g., 'max_stars_per_add')
            $table->text('value'); // Setting value (stored as JSON or string)
            $table->string('type')->default('string'); // Data type: string, integer, boolean, json
            $table->text('description')->nullable(); // Human-readable description
            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            [
                'key' => 'max_stars_per_add',
                'value' => '100',
                'type' => 'integer',
                'description' => '每次加星星的最大数量',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
