<?php

namespace App\Http\Controllers;

use App\Models\LinkedInCommentLead;
use App\Models\LinkedInPost;
use App\Models\LinkedInSetting;
use App\Services\LinkedIn\LinkedInService;
use Illuminate\Http\Request;

// ============================================================
// OAuth Controller — handles LinkedIn connect flow
// ============================================================
class LinkedInAuthController extends Controller
{
    public function connect(LinkedInService $service)
    {
        return redirect($service->getAuthUrl());
    }

    public function callback(Request $request, LinkedInService $service)
    {
        $code = $request->get('code');

        if (!$code) {
            return redirect('/linkedin/dashboard')
                ->with('error', 'LinkedIn authorisation cancelled or failed.');
        }

        $tokenData = $service->exchangeCodeForToken($code);

        if (!$tokenData || !isset($tokenData['access_token'])) {
            return redirect('/linkedin/dashboard')
                ->with('error', 'Failed to get access token from LinkedIn.');
        }

        $token     = $tokenData['access_token'];
        $expiresIn = $tokenData['expires_in'] ?? 5184000; // 60 days default

        // Get person URN
        $personUrn = $service->getPersonUrn($token);

        // Save to settings
        LinkedInSetting::set('linkedin_token',   $token);
        LinkedInSetting::set('token_expires_at', now()->addSeconds($expiresIn)->toISOString());

        if ($personUrn) {
            LinkedInSetting::set('linkedin_person_urn', $personUrn);
            // Also update config at runtime
            config(['linkedin.person_urn' => $personUrn]);
        }

        return redirect('/linkedin/dashboard')
            ->with('success', 'LinkedIn connected successfully! Token valid for 60 days.');
    }

    public function disconnect()
    {
        LinkedInSetting::set('linkedin_token',    null);
        LinkedInSetting::set('token_expires_at',  null);

        return redirect('/linkedin/dashboard')
            ->with('success', 'LinkedIn disconnected.');
    }
}