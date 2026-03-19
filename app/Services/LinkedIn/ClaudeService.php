<?php

namespace App\Services\LinkedIn;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClaudeService
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('linkedin.anthropic_key');
        $this->model  = config('linkedin.anthropic_model', 'claude-sonnet-4-20250514');
    }

    /**
     * Generate a swipe-style LinkedIn post from an angle.
     */
    public function generateSwipePost(string $angle): ?string
    {
        $system = <<<SYSTEM
You are a LinkedIn content specialist for Koloursyncc, a Mumbai-based brand design and development agency.
You write viral LinkedIn posts for FMCG, food, and packaging brands in India.
Your audience: food brand founders, marketing managers, packaging buyers.

WRITING RULES:
- Max 220 words
- Aggressive line breaks — max 2 sentences per paragraph, plenty of white space
- End with a punchy question or one-liner to drive comments
- Max 2 emojis per post
- Never use "partnership", "synergy", "delve", or "leverage"
- Statements as facts, not hedged opinions
- Indian market context throughout (rupees, kirana stores, India brands)
- Never name specific Koloursyncc clients

Choose the best format for the angle:
- Founder Story: hook → context → conflict → lesson
- Hot Take: bold claim → 2-3 support points → kicker line
- Numbered Lessons: hook → 3-5 specific numbered points
- Observation → Insight: what you noticed → what it means
- Mini Case Study: result in hook → brief story → transferable lesson

Output ONLY the post text. No preamble, no labels, no explanation.
SYSTEM;

        return $this->callClaude($system, "Write a LinkedIn post about this angle: {$angle}");
    }

    /**
     * Generate a lead magnet LinkedIn post.
     */
    public function generateLeadMagnetPost(string $angle, string $keyword, string $resourceName): ?string
    {
        $system = <<<SYSTEM
You are a LinkedIn content specialist for Koloursyncc, a Mumbai-based brand design and development agency.
You write viral LinkedIn LEAD MAGNET posts for FMCG, food, and packaging brands in India.

A lead magnet post teases a free resource, builds desire for it, then ends with the keyword trigger.

WRITING RULES:
- Max 180 words
- Aggressive line breaks — short paragraphs, maximum white space
- Tease the value of the resource without giving away the content
- Build genuine desire — the reader should feel they need this
- End EXACTLY with this line (replace KEYWORD with the actual keyword):
  Comment "KEYWORD" and I'll DM it to you. Free.
- Max 1 emoji
- Never use "partnership", "synergy", "delve"
- Indian market context throughout
- Never name specific clients

Output ONLY the post text. No preamble.
SYSTEM;

        $prompt = "Write a lead magnet LinkedIn post.\nAngle: {$angle}\nKeyword: {$keyword}\nResource name: {$resourceName}";

        return $this->callClaude($system, $prompt);
    }

    /**
     * Core API call to Claude.
     */
    private function callClaude(string $system, string $userMessage): ?string
    {
        try {
            $response = Http::withHeaders([
                'x-api-key'         => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
                'model'      => $this->model,
                'max_tokens' => 800,
                'system'     => $system,
                'messages'   => [
                    ['role' => 'user', 'content' => $userMessage],
                ],
            ]);

            if ($response->failed()) {
                Log::error('Claude API error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();
            return $data['content'][0]['text'] ?? null;

        } catch (\Exception $e) {
            Log::error('Claude API exception', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
