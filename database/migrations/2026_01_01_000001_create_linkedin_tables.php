<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('linkedin_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        DB::table('linkedin_settings')->insert([
            ['key' => 'rotation_index',       'value' => '0',  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'automation_active',    'value' => '1',  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'lead_magnets_active',  'value' => '0',  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'linkedin_token',       'value' => null, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'token_expires_at',     'value' => null, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'linkedin_person_urn',  'value' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::create('linkedin_posts', function (Blueprint $table) {
            $table->id();
            $table->integer('rotation_index');
            $table->enum('post_type', ['swipe', 'lead_magnet']);
            $table->string('angle', 500);
            $table->longText('generated_text');
            $table->string('linkedin_post_id')->nullable();
            $table->enum('status', ['pending', 'published', 'failed'])->default('pending');
            $table->string('keyword')->nullable();
            $table->string('resource_name')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('scheduled_for');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('linkedin_post_id');
        });

        Schema::create('linkedin_comment_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('linkedin_post_id')->constrained('linkedin_posts')->onDelete('cascade');
            $table->string('commenter_urn');
            $table->string('commenter_name')->nullable();
            $table->string('comment_id')->unique();
            $table->string('comment_text', 500);
            $table->string('keyword_matched')->nullable();
            $table->enum('dm_status', ['pending', 'sent', 'failed', 'skipped'])->default('pending');
            $table->timestamp('dm_sent_at')->nullable();
            $table->enum('followup_status', ['pending', 'sent', 'failed', 'skipped'])->default('pending');
            $table->timestamp('followup_sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index('dm_status');
            $table->index('followup_status');
            $table->index('commenter_urn');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('linkedin_comment_leads');
        Schema::dropIfExists('linkedin_posts');
        Schema::dropIfExists('linkedin_settings');
    }
};