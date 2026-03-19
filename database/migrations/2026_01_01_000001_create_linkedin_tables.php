<?php
// ============================================================
// MIGRATION 1: linkedin_posts
// Run: php artisan migrate
// ============================================================
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Posts table ──────────────────────────────────────
        Schema::create('linkedin_posts', function (Blueprint $table) {
            $table->id();
            $table->integer('rotation_index');               // which angle was used
            $table->enum('post_type', ['swipe', 'lead_magnet']);
            $table->string('angle', 500);                   // the seed angle
            $table->longText('generated_text');             // the full generated post
            $table->string('linkedin_post_id')->nullable(); // LinkedIn UGC post ID
            $table->enum('status', ['pending', 'published', 'failed'])->default('pending');
            $table->string('keyword')->nullable();          // lead magnet keyword if applicable
            $table->string('resource_name')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('scheduled_for');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('rotation_index');
            $table->index('linkedin_post_id');
        });

        // ── Comment leads table ──────────────────────────────
        Schema::create('linkedin_comment_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('linkedin_post_id')->constrained('linkedin_posts')->onDelete('cascade');
            $table->string('commenter_urn');                // urn:li:person:XXXX
            $table->string('commenter_name')->nullable();
            $table->string('comment_id');                   // LinkedIn comment ID
            $table->string('comment_text', 500);
            $table->string('keyword_matched')->nullable();  // which lead magnet keyword
            $table->enum('dm_status', ['pending', 'sent', 'failed', 'skipped'])->default('pending');
            $table->timestamp('dm_sent_at')->nullable();
            $table->enum('followup_status', ['pending', 'sent', 'skipped'])->default('pending');
            $table->timestamp('followup_sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->unique('comment_id');
            $table->index('dm_status');
            $table->index('followup_status');
            $table->index('commenter_urn');
        });

        // ── Settings / state table ───────────────────────────
        Schema::create('linkedin_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed initial rotation index
        DB::table('linkedin_settings')->insert([
            ['key' => 'rotation_index',    'value' => '0',  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'automation_active', 'value' => '1',  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'linkedin_token',    'value' => null, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'token_expires_at',  'value' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('linkedin_comment_leads');
        Schema::dropIfExists('linkedin_posts');
        Schema::dropIfExists('linkedin_settings');
    }
};
