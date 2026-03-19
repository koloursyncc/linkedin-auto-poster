<?php

namespace App\Console\Commands;

use App\Models\LinkedInPost;
use App\Models\LinkedInSetting;
use App\Services\LinkedIn\ClaudeService;
use App\Services\LinkedIn\LinkedInService;
use App\Services\LinkedIn\PostAnglesLibrary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateAndPublishPost extends Command
{
    protected $signature   = 'linkedin:post {--dry-run : Generate post text but do not publish}';
    protected $description = 'Generate and publish a LinkedIn post using the rotation engine';

    public function handle(ClaudeService $claude, LinkedInService $linkedin): int
    {
        if (LinkedInSetting::get('automation_active', '1') !== '1') {
            $this->info('Automation is paused. Skipping.');
            return self::SUCCESS;
        }

        $token     = LinkedInSetting::get('linkedin_token');
        $expiresAt = LinkedInSetting::get('token_expires_at');

        if (!$token) {
            $this->error('No LinkedIn token. Visit /linkedin/dashboard to connect.');
            return self::FAILURE;
        }

        if ($expiresAt && now()->isAfter($expiresAt)) {
            $this->error('LinkedIn token expired. Visit /linkedin/dashboard to re-connect.');
            return self::FAILURE;
        }

        $index     = (int) LinkedInSetting::get('rotation_index', 0);
        $angleData = PostAnglesLibrary::getAngleForIndex($index);

        $this->info("Run #{$index} | Type: {$angleData['post_type']} | Angle: " . substr($angleData['angle'], 0, 70) . '...');

        if ($angleData['post_type'] === 'lead_magnet') {
            $postText = $claude->generateLeadMagnetPost(
                $angleData['angle'],
                $angleData['keyword'],
                $angleData['resource']
            );
        } else {
            $postText = $claude->generateSwipePost($angleData['angle']);
        }

        if (!$postText) {
            $this->error('Claude API failed to generate post text.');
            $this->savePost($index, $angleData, '', 'failed', 'Claude API returned null');
            return self::FAILURE;
        }

        $this->line("\n--- Generated Post ---\n{$postText}\n---\n");

        if ($this->option('dry-run')) {
            $this->info('[DRY RUN] Post not published.');
            return self::SUCCESS;
        }

        $linkedinPostId = $linkedin->publishPost($postText, $token);

        if (!$linkedinPostId) {
            $this->error('LinkedIn API failed to publish post.');
            $this->savePost($index, $angleData, $postText, 'failed', 'LinkedIn API returned null');
            return self::FAILURE;
        }

        $this->savePost($index, $angleData, $postText, 'published', null, $linkedinPostId);

        LinkedInSetting::set('rotation_index', $index + 1);

        $this->info("✓ Published! Post ID: {$linkedinPostId}");
        Log::info('linkedin:post published', ['index' => $index, 'linkedin_post_id' => $linkedinPostId]);

        return self::SUCCESS;
    }

    private function savePost(
        int $index,
        array $angleData,
        string $postText,
        string $status,
        ?string $error = null,
        ?string $linkedinPostId = null
    ): void {
        LinkedInPost::create([
            'rotation_index'   => $index,
            'post_type'        => $angleData['post_type'],
            'angle'            => $angleData['angle'],
            'generated_text'   => $postText,
            'linkedin_post_id' => $linkedinPostId,
            'status'           => $status,
            'keyword'          => $angleData['keyword'] ?? null,
            'resource_name'    => $angleData['resource'] ?? null,
            'error_message'    => $error,
            'scheduled_for'    => now(),
            'published_at'     => $status === 'published' ? now() : null,
        ]);
    }
}
