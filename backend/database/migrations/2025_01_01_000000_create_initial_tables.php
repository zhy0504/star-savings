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
        // ============================================
        // Laravel System Tables
        // ============================================

        // Users table
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }

        // Cache table
        if (!Schema::hasTable('cache')) {
            Schema::create('cache', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->mediumText('value');
                $table->integer('expiration');
            });
        }

        if (!Schema::hasTable('cache_locks')) {
            Schema::create('cache_locks', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->string('owner');
                $table->integer('expiration');
            });
        }

        // Jobs table
        if (!Schema::hasTable('jobs')) {
            Schema::create('jobs', function (Blueprint $table) {
                $table->id();
                $table->string('queue')->index();
                $table->longText('payload');
                $table->unsignedTinyInteger('attempts');
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at');
                $table->unsignedInteger('created_at');
            });
        }

        if (!Schema::hasTable('job_batches')) {
            Schema::create('job_batches', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('name');
                $table->integer('total_jobs');
                $table->integer('pending_jobs');
                $table->integer('failed_jobs');
                $table->longText('failed_job_ids');
                $table->mediumText('options')->nullable();
                $table->integer('cancelled_at')->nullable();
                $table->integer('created_at');
                $table->integer('finished_at')->nullable();
            });
        }

        if (!Schema::hasTable('failed_jobs')) {
            Schema::create('failed_jobs', function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique();
                $table->text('connection');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
            });
        }

        // ============================================
        // Application Business Tables
        // ============================================

        // Children table
        if (!Schema::hasTable('children')) {
            Schema::create('children', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->date('birthday');
                $table->enum('gender', ['male', 'female']);
                $table->string('avatar')->nullable();
                $table->integer('star_count')->default(0); // Current star count (redundant field for performance)
                $table->timestamps();
            });
        }

        // Rewards table
        if (!Schema::hasTable('rewards')) {
            Schema::create('rewards', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('image')->nullable();
                $table->integer('star_cost'); // Stars required for this reward
                $table->boolean('is_redeemed')->default(false);
                $table->timestamp('redeemed_at')->nullable();
                $table->timestamps();
            });
        }

        // Reward-Children pivot table
        if (!Schema::hasTable('reward_children')) {
            Schema::create('reward_children', function (Blueprint $table) {
                $table->id();
                $table->foreignId('reward_id')->constrained()->onDelete('cascade');
                $table->foreignId('child_id')->constrained()->onDelete('cascade');
                $table->integer('deduction_amount')->nullable(); // Actual stars deducted when redeemed (null before redemption)
                $table->timestamps();

                // Unique constraint: each child can only be linked once to a reward
                $table->unique(['reward_id', 'child_id']);
            });
        }

        // Star records table
        if (!Schema::hasTable('star_records')) {
            Schema::create('star_records', function (Blueprint $table) {
                $table->id();
                $table->foreignId('child_id')->constrained()->onDelete('cascade');
                $table->integer('amount'); // Positive for add, negative for subtract/redeem
                $table->enum('type', ['add', 'subtract', 'redeem']);
                $table->foreignId('reward_id')->nullable()->constrained()->onDelete('set null'); // Only set when type is 'redeem'
                $table->string('reason')->nullable(); // Reason for this star change
                $table->timestamps();

                // Index for fast query by child and date
                $table->index(['child_id', 'created_at']);
            });
        }

        // Settings table
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique(); // Setting key (e.g., 'max_stars_per_add')
                $table->text('value'); // Setting value (stored as JSON or string)
                $table->string('type')->default('string'); // Data type: string, integer, boolean, json
                $table->text('description')->nullable(); // Human-readable description
                $table->timestamps();
            });
        }

        // ============================================
        // Insert Default Settings
        // ============================================

        // Only insert default settings if settings table is empty
        if (DB::table('settings')->count() === 0) {
            DB::table('settings')->insert([
                [
                    'key' => 'max_stars_per_add',
                    'value' => '100',
                    'type' => 'integer',
                    'description' => '每次加星星的最大数量',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'add_star_reasons',
                    'value' => json_encode([
                        ['emoji' => '😊', 'text' => '认真', 'amount' => 1],
                        ['emoji' => '🏃', 'text' => '主动', 'amount' => 1],
                        ['emoji' => '😴', 'text' => '按时', 'amount' => 1],
                        ['emoji' => '🤝', 'text' => '分享', 'amount' => 2],
                        ['emoji' => '🧹', 'text' => '做家务', 'amount' => 3],
                    ]),
                    'type' => 'json',
                    'description' => '加星星的理由标签',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'subtract_star_reasons',
                    'value' => json_encode([
                        ['emoji' => '😢', 'text' => '不听话', 'amount' => 1],
                        ['emoji' => '🎮', 'text' => '玩太久', 'amount' => 1],
                        ['emoji' => '😴', 'text' => '不按时', 'amount' => 1],
                        ['emoji' => '😤', 'text' => '发脾气', 'amount' => 2],
                    ]),
                    'type' => 'json',
                    'description' => '减星星的理由标签',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order (respecting foreign key constraints)
        Schema::dropIfExists('settings');
        Schema::dropIfExists('star_records');
        Schema::dropIfExists('reward_children');
        Schema::dropIfExists('rewards');
        Schema::dropIfExists('children');

        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
