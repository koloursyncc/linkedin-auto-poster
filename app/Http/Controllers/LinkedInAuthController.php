<?php

namespace App\Http\Controllers;

use App\Models\LinkedInSetting;
use App\Services\LinkedIn\LinkedInService;
use Illuminate\Http\Request;

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
            return redirect()->route('linkedin.dashboard')
                ->with('error', 'LinkedIn authorisation cancelled or failed.');
        }

        $tokenData = $service->exchangeCodeForToken($code);

        if (!$tokenData || !isset($tokenData['access_token'])) {
            return redirect()->route('linkedin.dashboard')
                ->with('error', 'Failed to get access token from LinkedIn.');
        }

        $token     = $tokenData['access_token'];
        $expiresIn = $tokenData['expires_in'] ?? 5184000; // 60 days

        // Save token first
        LinkedInSetting::set('linkedin_token',    $token);
        LinkedInSetting::set('token_expires_at',  now()->addSeconds($expiresIn)->toISOString());

        // Fetch and save person URN using /v2/userinfo
        $personUrn = $service->getPersonUrn($token);

        if ($personUrn) {
            LinkedInSetting::set('linkedin_person_urn', $personUrn);
        }

        return redirect()->route('linkedin.dashboard')
            ->with('success', 'LinkedIn connected! Token valid for 60 days.' . ($personUrn ? " URN: {$personUrn}" : ' (Run linkedin:debug if posting fails.)'));
    }

    public function disconnect()
    {
        LinkedInSetting::set('linkedin_token',       null);
        LinkedInSetting::set('token_expires_at',     null);
        LinkedInSetting::set('linkedin_person_urn',  null);

        return redirect()->route('linkedin.dashboard')
            ->with('success', 'LinkedIn disconnected.');
    }
}
