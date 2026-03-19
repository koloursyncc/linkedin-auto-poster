<?php

namespace App\Services\LinkedIn;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LinkedInService
{
    private string $personUrn;

    public function __construct()
    {
        $this->personUrn = config('linkedin.person_urn') || null;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // OAUTH
    // ──────────────────────────────────────────────────────────────────────────

    public function getAuthUrl(): string
    {
        $params = http_build_query([
            'response_type' => 'code',
            'client_id'     => config('linkedin.client_id'),
            'redirect_uri'  => config('linkedin.redirect_uri'),
            'scope'         => 'w_member_social openid profile',
            'state'         => csrf_token(),
        ]);

        return "https://www.linkedin.com/oauth/v2/authorization?{$params}";
    }

    public function exchangeCodeForToken(string $code): ?array
    {
        $response = Http::asForm()->post('https://www.linkedin.com/oauth/v2/accessToken', [
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'redirect_uri'  => config('linkedin.redirect_uri'),
            'client_id'     => config('linkedin.client_id'),
            'client_secret' => config('linkedin.client_secret'),
        ]);

        if ($response->failed()) {
            Log::error('LinkedIn token exchange failed', ['body' => $response->body()]);
            return null;
        }

        return $response->json();
    }

    public function getPersonUrn(string $token): ?string
    {
        $response = $this->apiGet('https://api.linkedin.com/v2/me', $token);
        return $response ? 'urn:li:person:' . $response['id'] : null;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // POSTING
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Publish a text post to LinkedIn. Returns the LinkedIn post ID or null.
     */
    public function publishPost(string $text, string $token): ?string
    {
        $payload = [
            'author'          => $this->personUrn,
            'lifecycleState'  => 'PUBLISHED',
            'specificContent' => [
                'com.linkedin.ugc.ShareContent' => [
                    'shareCommentary'    => ['text' => $text],
                    'shareMediaCategory' => 'NONE',
                ],
            ],
            'visibility' => [
                'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC',
            ],
        ];

        try {
            $response = Http::withHeaders([
                'Authorization'              => "Bearer {$token}",
                'Content-Type'               => 'application/json',
                'X-Restli-Protocol-Version'  => '2.0.0',
            ])->post('https://api.linkedin.com/v2/ugcPosts', $payload);

            if ($response->failed()) {
                Log::error('LinkedIn post failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            // LinkedIn returns the post ID in the X-RestLi-Id header
            $postId = $response->header('X-RestLi-Id')
                ?? ($response->json()['id'] ?? null);

            return $postId;
        } catch (\Exception $e) {
            Log::error('LinkedIn publishPost exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // COMMENT POLLING
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Fetch comments on a specific LinkedIn UGC post.
     * Returns array of comment objects or empty array.
     */
    public function getPostComments(string $linkedinPostId, string $token): array
    {
        // URL-encode the post ID for use as a query param
        $encoded = urlencode($linkedinPostId);
        $url = "https://api.linkedin.com/v2/socialActions/{$encoded}/comments";

        $response = $this->apiGet($url, $token, ['count' => 50, 'start' => 0]);

        if (!$response || !isset($response['elements'])) {
            return [];
        }

        return $response['elements'];
    }

    /**
     * Parse comments and check if any contain a lead magnet keyword.
     * Returns array of matched leads: [commenter_urn, comment_id, comment_text, keyword]
     */
    public function extractKeywordComments(array $comments): array
    {
        $keywords = array_keys(config('linkedin.resources', []));
        $leads    = [];

        foreach ($comments as $comment) {
            $text       = $comment['message']['text'] ?? '';
            $commentId  = $comment['id'] ?? null;
            $actorUrn   = $comment['actor'] ?? null;

            if (!$commentId || !$actorUrn) continue;

            foreach ($keywords as $keyword) {
                // Case-insensitive exact word match
                if (stripos($text, $keyword) !== false) {
                    $leads[] = [
                        'commenter_urn' => $actorUrn,
                        'comment_id'    => $commentId,
                        'comment_text'  => $text,
                        'keyword'       => strtoupper($keyword),
                    ];
                    break; // only match first keyword per comment
                }
            }
        }

        return $leads;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // DIRECT MESSAGES
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Send a LinkedIn DM to a person URN.
     * Uses the Messages API (requires w_member_social scope).
     */
    public function sendDM(string $recipientUrn, string $message, string $token): bool
    {
        $payload = [
            'recipients' => [
                ['person' => ['urn' => $recipientUrn]],
            ],
            'subject' => '',
            'body'    => $message,
        ];

        try {
            $response = Http::withHeaders([
                'Authorization'             => "Bearer {$token}",
                'Content-Type'              => 'application/json',
                'X-Restli-Protocol-Version' => '2.0.0',
            ])->post('https://api.linkedin.com/v2/messages', $payload);

            if ($response->failed()) {
                Log::error('LinkedIn DM failed', [
                    'recipient' => $recipientUrn,
                    'status'    => $response->status(),
                    'body'      => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('LinkedIn sendDM exception', ['message' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Build the initial DM message for a lead magnet keyword.
     */
    public function buildLeadMagnetDM(string $keyword, string $commenterName = ''): string
    {
        $resources = config('linkedin.resources', []);
        $link      = $resources[$keyword] ?? null;

        $greeting = $commenterName ? "Hey {$commenterName}!" : 'Hey!';

        $resourceNames = [
            'AUDIT'    => 'FMCG Packaging Audit Checklist',
            'KIRANA'   => 'Kirana Shelf Win Guide',
            'STORY'    => 'Food Brand Story Template',
            'SCORE'    => 'Brand Consistency Scorecard',
            'HOMEPAGE' => 'Food Brand Homepage Audit',
            'CONVERT'  => 'D2C Product Page Conversion Template',
            'CALENDAR' => '30-Day FMCG Instagram Content Calendar',
            'GRID'     => 'Instagram Grid Audit',
            'IDENTITY' => 'Brand Identity Starter Kit',
            'BRIEF'    => 'Logo Design Brief Template',
            'PITCH'    => 'FMCG Distributor Pitch Deck Template',
            'INVEST'   => 'Investor Pitch Narrative Guide',
            'COPY'     => 'Packaging Copy Formula Sheet',
            'VOICE'    => 'Brand Voice Style Guide Template',
            'ROI'      => 'Packaging Redesign ROI Calculator',
            'COMPETE'  => 'Competitor Brand Audit Template',
            'LAUNCH'   => 'Pre-Launch Website Checklist',
            'LI'       => 'LinkedIn Content Calendar for FMCG Agencies',
            'NAME'     => 'Brand Naming Framework',
            'SWIPE'    => 'Website Copy Swipe File',
        ];

        $name = $resourceNames[$keyword] ?? 'the resource';

        if (!$link) {
            return "{$greeting} Thanks for your comment — I'm preparing the {$name} and will send it shortly.";
        }

        return "{$greeting} Here's your {$name} 👇\n\n{$link}\n\nDM me if you have any questions — happy to give you a quick take on your specific situation.";
    }

    /**
     * Build the 24-hour follow-up DM.
     */
    public function buildFollowUpDM(string $keyword, string $commenterName = ''): string
    {
        $greeting = $commenterName ? "Hey {$commenterName}," : 'Hey,';

        $followUps = [
            'AUDIT'    => "Did you get a chance to run through the packaging audit checklist? Curious what your pack scored — DM me if anything flagged.",
            'KIRANA'   => "Did the kirana shelf guide help? If you're working on packaging for general trade, happy to share some specific pointers.",
            'STORY'    => "Did you get a chance to fill in the brand story template? Most founders are surprised by how much the 'villain' question clarifies their positioning.",
            'SCORE'    => "Did you run the brand consistency scorecard? Most brands score 6-8 on their first attempt — usually more inconsistency than expected.",
            'HOMEPAGE' => "Did you audit your homepage? The social-proof-above-the-fold point is the one most brands miss — let me know how it went.",
            'CONVERT'  => "Did the product page template help? The trust signal placement section tends to have the biggest immediate impact.",
            'CALENDAR' => "Did the content calendar work for your brand? Happy to tweak the topics for your specific product category.",
            'GRID'     => "Did you run the Instagram grid audit? The product-to-lifestyle image ratio is usually the quickest win.",
            'IDENTITY' => "Did the brand identity starter kit help clarify what you actually need vs what you thought you needed?",
            'BRIEF'    => "Did the logo brief template help? A good brief usually cuts revision rounds by half.",
            'PITCH'    => "Did you get a chance to look at the pitch deck template? Slide 4 (sell-out proof) is the one most founders build last but distributors look at first.",
            'INVEST'   => "Did the investor pitch guide help? The 'story not timeline' section tends to be the biggest mindset shift for founders.",
            'COPY'     => "Did the packaging copy formulas work for your product? The front-panel formula tends to have the biggest shelf impact.",
            'VOICE'    => "Did you fill in the brand voice guide? The '3 words we never say' section usually surprises founders most.",
            'ROI'      => "Did you run the ROI calculator? The numbers usually surprise founders — packaging ROI compounds in ways ad spend doesn't.",
            'COMPETE'  => "Did the competitor audit reveal any white space for your brand?",
            'LAUNCH'   => "Did the pre-launch checklist catch anything? The mobile checkout flow and WhatsApp contact points are the most commonly missed.",
            'LI'       => "Did the LinkedIn calendar help? Happy to customise the week-2 themes for your specific niche.",
            'NAME'     => "Did the naming framework help narrow things down? The trademark search step is the one most founders skip — and regret.",
            'SWIPE'    => "Did the copy swipe file help? The 'why us' section examples tend to be the most useful starting point.",
        ];

        $followUp = $followUps[$keyword] ?? "Did you get a chance to look at the resource I sent? Happy to answer any questions.";

        return "{$greeting}\n\n{$followUp}";
    }

    // ──────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────────────────────────────────

    private function apiGet(string $url, string $token, array $params = []): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
            ])->get($url, $params);

            if ($response->failed()) {
                Log::error('LinkedIn API GET failed', [
                    'url'    => $url,
                    'status' => $response->status(),
                ]);
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('LinkedIn apiGet exception', ['message' => $e->getMessage()]);
            return null;
        }
    }
}