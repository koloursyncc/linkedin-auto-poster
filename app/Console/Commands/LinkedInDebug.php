<?php

namespace App\Console\Commands;

use App\Models\LinkedInSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class LinkedInDebug extends Command
{
    protected $signature   = 'linkedin:debug';
    protected $description = 'Diagnose LinkedIn connection and configuration issues';

    public function handle(): int
    {
        $this->info('');
        $this->info('══════════════════════════════════════');
        $this->info('  Koloursyncc — LinkedIn Debug Report');
        $this->info('══════════════════════════════════════');
        $this->info('');

        $this->checkConfig();
        $this->checkDatabase();
        $this->checkTokenExpiry();
        $this->fetchAndSavePersonUrn();
        $this->checkPostPayload();

        $this->info('══════════════════════════════════════');
        $this->info('');

        return self::SUCCESS;
    }

    private function checkConfig(): void
    {
        $this->info('[ 1 ] .env / Config values');

        $items = [
            'LINKEDIN_CLIENT_ID'     => config('linkedin.client_id'),
            'LINKEDIN_CLIENT_SECRET' => config('linkedin.client_secret'),
            'LINKEDIN_REDIRECT_URI'  => config('linkedin.redirect_uri'),
            'LINKEDIN_PERSON_URN'    => config('linkedin.person_urn'),
            'ANTHROPIC_API_KEY'      => config('linkedin.anthropic_key'),
        ];

        foreach ($items as $key => $value) {
            if ($value) {
                $masked = in_array($key, ['LINKEDIN_CLIENT_SECRET', 'ANTHROPIC_API_KEY'])
                    ? substr($value, 0, 8) . '...' . substr($value, -4)
                    : $value;
                $this->line("  <fg=green>✓</> {$key} = {$masked}");
            } else {
                $this->line("  <fg=red>✗</> {$key} = (empty)");
            }
        }

        $this->info('');
    }

    private function checkDatabase(): void
    {
        $this->info('[ 2 ] Database (linkedin_settings table)');

        $token       = LinkedInSetting::get('linkedin_token');
        $expiresAt   = LinkedInSetting::get('token_expires_at');
        $personUrn   = LinkedInSetting::get('linkedin_person_urn');
        $isActive    = LinkedInSetting::get('automation_active', '1');
        $rotIndex    = LinkedInSetting::get('rotation_index', '0');

        $this->line('  linkedin_token:       ' . ($token ? substr($token, 0, 12) . '...(set)' : '<fg=red>(empty)</>'));
        $this->line('  token_expires_at:     ' . ($expiresAt ?? '<fg=red>(empty)</>'));
        $this->line('  linkedin_person_urn:  ' . ($personUrn ?? '<fg=red>(empty) ← THIS CAUSES THE 422 ERROR</>'));
        $this->line('  automation_active:    ' . $isActive);
        $this->line('  rotation_index:       ' . $rotIndex);

        if (!$token) {
            $this->warn('  → No token. Go to /linkedin/dashboard and click Connect LinkedIn.');
        }

        $this->info('');
    }

    private function checkTokenExpiry(): void
    {
        $this->info('[ 3 ] Token expiry');

        $token     = LinkedInSetting::get('linkedin_token');
        $expiresAt = LinkedInSetting::get('token_expires_at');

        if (!$token) {
            $this->line('  <fg=yellow>Skipped — no token.</>');
            $this->info('');
            return;
        }

        if ($expiresAt) {
            $expiry   = \Carbon\Carbon::parse($expiresAt);
            $daysLeft = (int) now()->diffInDays($expiry, false);

            if ($daysLeft <= 0) {
                $this->line('  <fg=red>✗ Token EXPIRED. Re-connect at /linkedin/dashboard.</>');
            } elseif ($daysLeft <= 7) {
                $this->line("  <fg=yellow>⚠ Token expires in {$daysLeft} days. Re-connect soon.</>");
            } else {
                $this->line("  <fg=green>✓ Token valid for {$daysLeft} more days.</>");
            }
        } else {
            $this->line('  <fg=yellow>No expiry date stored.</>');
        }

        $this->info('');
    }

    private function fetchAndSavePersonUrn(): void
    {
        $this->info('[ 4 ] Fetch Person URN from LinkedIn API');

        $token = LinkedInSetting::get('linkedin_token');

        if (!$token) {
            $this->line('  <fg=yellow>Skipped — no token.</>');
            $this->info('');
            return;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
            ])->get('https://api.linkedin.com/v2/userinfo');

            if ($response->successful()) {
                $data = $response->json();
                $sub  = $data['sub'] ?? null;

                if ($sub) {
                    $urn = 'urn:li:person:' . $sub;
                    $this->line("  <fg=green>✓ Person URN: {$urn}</>");

                    $existing = LinkedInSetting::get('linkedin_person_urn');
                    if (!$existing) {
                        LinkedInSetting::set('linkedin_person_urn', $urn);
                        $this->line('  <fg=green>✓ Auto-saved to linkedin_settings table.</> Try posting now.');
                    } else {
                        $this->line('  <fg=green>✓ Already saved in DB.</>');
                    }

                    if (!config('linkedin.person_urn')) {
                        $this->warn("  → Also add to .env: LINKEDIN_PERSON_URN={$urn}");
                    }
                } else {
                    $this->line('  <fg=red>✗ No sub field in response: ' . json_encode($data) . '</>');
                }
            } else {
                $this->line('  <fg=red>✗ API error ' . $response->status() . ': ' . $response->body() . '</>');
            }
        } catch (\Exception $e) {
            $this->line('  <fg=red>✗ Exception: ' . $e->getMessage() . '</>');
        }

        $this->info('');
    }

    private function checkPostPayload(): void
    {
        $this->info('[ 5 ] Post payload check');

        $resolvedUrn = LinkedInSetting::get('linkedin_person_urn')
            ?? config('linkedin.person_urn', '');

        if ($resolvedUrn) {
            $this->line("  <fg=green>✓ author URN resolved: {$resolvedUrn}</>");
            $this->line('  Ready to post. Run: php artisan linkedin:post --dry-run');
        } else {
            $this->line('  <fg=red>✗ author URN still empty — post will fail with 422.</>');
            $this->line('  Fix: ensure token is valid then re-run php artisan linkedin:debug');
        }

        $this->info('');
    }
}
