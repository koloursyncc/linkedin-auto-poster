<?php

namespace App\Services\LinkedIn;

use App\Models\LinkedInSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LinkedInService
{
    private string $personUrn;

    public function __construct()
    {
        // DB setting takes priority — written on first OAuth connect.
        // Falls back to .env if someone set it manually.
        $this->personUrn = LinkedInSetting::get('linkedin_person_urn')
            ?? config('linkedin.person_urn', '');
    }

    // ── OAUTH ─────────────────────────────────────────────────────────────────

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
            Log::error('LinkedInService: token exchange failed', ['body' => $response->body()]);
            return null;
        }

        return $response->json();
    }

    public function getPersonUrn(string $token): ?string
    {
        // /v2/me requires special Marketing API access.
        // /v2/userinfo works with standard openid + profile scope.
        // Returns 'sub' which is the person ID.
        $response = $this->apiGet('https://api.linkedin.com/v2/userinfo', $token);
        $sub      = $response['sub'] ?? null;

        return $sub ? 'urn:li:person:' . $sub : null;
    }

    // ── POSTING ───────────────────────────────────────────────────────────────

    public function publishPost(string $text, string $token): ?string
    {
        if (empty($this->personUrn)) {
            Log::error('LinkedInService: personUrn is empty — re-connect LinkedIn at /linkedin/dashboard');
            return null;
        }

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
                'Authorization'             => "Bearer {$token}",
                'Content-Type'              => 'application/json',
                'X-Restli-Protocol-Version' => '2.0.0',
            ])->post('https://api.linkedin.com/v2/ugcPosts', $payload);

            if ($response->failed()) {
                Log::error('LinkedInService: publishPost failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            return $response->header('X-RestLi-Id')
                ?? ($response->json()['id'] ?? null);

        } catch (\Exception $e) {
            Log::error('LinkedInService: publishPost exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    // ── COMMENT POLLING ───────────────────────────────────────────────────────

    public function getPostComments(string $linkedinPostId, string $token): array
    {
        $encoded  = urlencode($linkedinPostId);
        $url      = "https://api.linkedin.com/v2/socialActions/{$encoded}/comments";
        $response = $this->apiGet($url, $token, ['count' => 50, 'start' => 0]);

        return $response['elements'] ?? [];
    }

    public function extractKeywordComments(array $comments): array
    {
        $keywords = array_keys(config('linkedin.resources', []));
        $leads    = [];

        foreach ($comments as $comment) {
            $text      = $comment['message']['text'] ?? '';
            $commentId = $comment['id'] ?? null;
            $actorUrn  = $comment['actor'] ?? null;

            if (!$commentId || !$actorUrn) {
                continue;
            }

            foreach ($keywords as $keyword) {
                if (stripos($text, $keyword) !== false) {
                    $leads[] = [
                        'commenter_urn' => $actorUrn,
                        'comment_id'    => $commentId,
                        'comment_text'  => $text,
                        'keyword'       => strtoupper($keyword),
                    ];
                    break;
                }
            }
        }

        return $leads;
    }

    // ── DIRECT MESSAGES ───────────────────────────────────────────────────────

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
                Log::error('LinkedInService: sendDM failed', [
                    'recipient' => $recipientUrn,
                    'status'    => $response->status(),
                    'body'      => $response->body(),
                ]);
                return false;
            }

            return true;

        } catch (\Exception $e) {
            Log::error('LinkedInService: sendDM exception', ['message' => $e->getMessage()]);
            return false;
        }
    }

    public function buildLeadMagnetDM(string $keyword, string $commenterName = ''): string
    {
        $resources = config('linkedin.resources', []);
        $link      = $resources[$keyword] ?? null;
        $greeting  = $commenterName ? "Hey {$commenterName}!" : 'Hey!';

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

    public function buildFollowUpDM(string $keyword, string $commenterName = ''): string
    {
        $greeting  = $commenterName ? "Hey {$commenterName}," : 'Hey,';

        $followUps = [
            'AUDIT'    => "Did you get a chance to run through the packaging audit checklist? Curious what your pack scored.",
            'KIRANA'   => "Did the kirana shelf guide help? Happy to share some specific pointers if you're working on general trade packaging.",
            'STORY'    => "Did you get a chance to fill in the brand story template? The 'villain' question is usually the most clarifying one.",
            'SCORE'    => "Did you run the brand consistency scorecard? Most brands score 6–8 on the first attempt.",
            'HOMEPAGE' => "Did you audit your homepage? The social-proof-above-the-fold point is the one most brands miss.",
            'CONVERT'  => "Did the product page template help? The trust signal placement section tends to have the biggest immediate impact.",
            'CALENDAR' => "Did the content calendar work for your brand? Happy to tweak the topics for your specific product category.",
            'GRID'     => "Did you run the Instagram grid audit? The product-to-lifestyle image ratio is usually the quickest win.",
            'IDENTITY' => "Did the brand identity starter kit help clarify what you actually need?",
            'BRIEF'    => "Did the logo brief template help? A good brief usually cuts revision rounds by half.",
            'PITCH'    => "Did you get a chance to look at the pitch deck template? Slide 4 (sell-out proof) is the one distributors look at first.",
            'INVEST'   => "Did the investor pitch guide help? The 'story not timeline' section tends to be the biggest mindset shift.",
            'COPY'     => "Did the packaging copy formulas work for your product? The front-panel formula tends to have the biggest shelf impact.",
            'VOICE'    => "Did you fill in the brand voice guide? The 'words we never say' section usually surprises founders most.",
            'ROI'      => "Did you run the ROI calculator? Packaging ROI compounds in ways ad spend doesn't.",
            'COMPETE'  => "Did the competitor audit reveal any white space for your brand?",
            'LAUNCH'   => "Did the pre-launch checklist catch anything? The WhatsApp contact point is the most commonly missed.",
            'LI'       => "Did the LinkedIn calendar help? Happy to customise the themes for your specific niche.",
            'NAME'     => "Did the naming framework help narrow things down? The trademark search step is the one most founders skip.",
            'SWIPE'    => "Did the copy swipe file help? The 'why us' section examples tend to be the most useful starting point.",
        ];

        $followUp = $followUps[$keyword] ?? "Did you get a chance to look at the resource I sent? Happy to answer any questions.";

        return "{$greeting}\n\n{$followUp}";
    }

    // ── PRIVATE HELPERS ───────────────────────────────────────────────────────

    private function apiGet(string $url, string $token, array $params = []): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
            ])->get($url, $params);

            if ($response->failed()) {
                Log::error('LinkedInService: GET failed', [
                    'url'    => $url,
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error('LinkedInService: apiGet exception', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
