<?php

namespace App\Console\Commands;

use App\Models\LinkedInCommentLead;
use App\Models\LinkedInPost;
use App\Models\LinkedInSetting;
use App\Services\LinkedIn\LinkedInService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PollCommentsAndSendDMs extends Command
{
    protected $signature   = 'linkedin:poll {--dry-run : Find leads but do not send DMs}';
    protected $description = 'Poll LinkedIn comments for keywords and send lead magnet DMs + follow-ups';

    public function handle(LinkedInService $linkedin): int
    {
        $token = LinkedInSetting::get('linkedin_token');

        if (!$token) {
            $this->error('No LinkedIn token. Visit /linkedin/dashboard to connect.');
            return self::FAILURE;
        }

        $this->pollForNewLeads($linkedin, $token);
        $this->sendPendingDMs($linkedin, $token);
        $this->sendPendingFollowUps($linkedin, $token);

        $this->info('Done.');
        return self::SUCCESS;
    }

    private function pollForNewLeads(LinkedInService $linkedin, string $token): void
    {
        $recentPosts = LinkedInPost::published()
            ->leadMagnets()
            ->whereNotNull('linkedin_post_id')
            ->where('published_at', '>=', now()->subDays(30))
            ->get();

        if ($recentPosts->isEmpty()) {
            $this->info('No recent lead magnet posts to poll.');
            return;
        }

        $this->info("Polling {$recentPosts->count()} lead magnet posts...");
        $newLeads = 0;

        foreach ($recentPosts as $post) {
            $comments = $linkedin->getPostComments($post->linkedin_post_id, $token);
            $matched  = $linkedin->extractKeywordComments($comments);

            foreach ($matched as $lead) {
                $exists = LinkedInCommentLead::where('comment_id', $lead['comment_id'])->exists();
                if ($exists) {
                    continue;
                }

                LinkedInCommentLead::create([
                    'linkedin_post_id' => $post->id,
                    'commenter_urn'    => $lead['commenter_urn'],
                    'comment_id'       => $lead['comment_id'],
                    'comment_text'     => $lead['comment_text'],
                    'keyword_matched'  => $lead['keyword'],
                    'dm_status'        => 'pending',
                    'followup_status'  => 'pending',
                ]);

                $newLeads++;
                $this->line("  ✓ New lead: {$lead['commenter_urn']} → keyword: {$lead['keyword']}");
            }
        }

        $this->info("Found {$newLeads} new leads.");
    }

    private function sendPendingDMs(LinkedInService $linkedin, string $token): void
    {
        $pending = LinkedInCommentLead::pendingDM()->get();
        $this->info("Sending {$pending->count()} pending DMs...");

        foreach ($pending as $lead) {
            $message = $linkedin->buildLeadMagnetDM(
                $lead->keyword_matched,
                $lead->commenter_name ?? ''
            );

            if ($this->option('dry-run')) {
                $this->line("  [DRY RUN] Would DM {$lead->commenter_urn}");
                continue;
            }

            $sent = $linkedin->sendDM($lead->commenter_urn, $message, $token);

            if ($sent) {
                $lead->update(['dm_status' => 'sent', 'dm_sent_at' => now()]);
                $this->line("  ✓ DM sent to {$lead->commenter_urn}");
                Log::info('linkedin:poll — DM sent', ['lead_id' => $lead->id]);
            } else {
                $lead->update(['dm_status' => 'failed', 'error_message' => 'LinkedIn API returned false']);
                $this->error("  ✗ DM failed for {$lead->commenter_urn}");
            }

            sleep(2);
        }
    }

    private function sendPendingFollowUps(LinkedInService $linkedin, string $token): void
    {
        $pending = LinkedInCommentLead::pendingFollowUp()->get();
        $this->info("Sending {$pending->count()} follow-up DMs...");

        foreach ($pending as $lead) {
            $message = $linkedin->buildFollowUpDM(
                $lead->keyword_matched,
                $lead->commenter_name ?? ''
            );

            if ($this->option('dry-run')) {
                $this->line("  [DRY RUN] Would follow-up DM {$lead->commenter_urn}");
                continue;
            }

            $sent = $linkedin->sendDM($lead->commenter_urn, $message, $token);

            if ($sent) {
                $lead->update(['followup_status' => 'sent', 'followup_sent_at' => now()]);
                $this->line("  ✓ Follow-up sent to {$lead->commenter_urn}");
            } else {
                $lead->update(['followup_status' => 'failed']);
                $this->error("  ✗ Follow-up failed for {$lead->commenter_urn}");
            }

            sleep(2);
        }
    }
}