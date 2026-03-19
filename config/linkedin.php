<?php

return [

    'client_id'     => env('LINKEDIN_CLIENT_ID'),
    'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
    'redirect_uri'  => env('LINKEDIN_REDIRECT_URI', '/linkedin/callback'),
    'person_urn'    => env('LINKEDIN_PERSON_URN'),

    // Dashboard password — set LINKEDIN_PANEL_PASSWORD in .env
    'panel_password' => env('LINKEDIN_PANEL_PASSWORD'),

    'anthropic_key'   => env('ANTHROPIC_API_KEY'),
    'anthropic_model' => env('ANTHROPIC_MODEL', 'claude-sonnet-4-20250514'),

    'schedule' => [
        'morning_utc' => '03:30', // 09:00 IST
        'evening_utc' => '12:30', // 18:00 IST
    ],

    'lead_magnet_every'    => 3,
    'poll_interval_minutes' => 15,
    'followup_delay_hours' => 24,

    'resources' => [
        'AUDIT'    => env('RESOURCE_AUDIT',    ''),
        'KIRANA'   => env('RESOURCE_KIRANA',   ''),
        'STORY'    => env('RESOURCE_STORY',    ''),
        'SCORE'    => env('RESOURCE_SCORE',    ''),
        'HOMEPAGE' => env('RESOURCE_HOMEPAGE', ''),
        'CONVERT'  => env('RESOURCE_CONVERT',  ''),
        'CALENDAR' => env('RESOURCE_CALENDAR', ''),
        'GRID'     => env('RESOURCE_GRID',     ''),
        'IDENTITY' => env('RESOURCE_IDENTITY', ''),
        'BRIEF'    => env('RESOURCE_BRIEF',    ''),
        'PITCH'    => env('RESOURCE_PITCH',    ''),
        'INVEST'   => env('RESOURCE_INVEST',   ''),
        'COPY'     => env('RESOURCE_COPY',     ''),
        'VOICE'    => env('RESOURCE_VOICE',    ''),
        'ROI'      => env('RESOURCE_ROI',      ''),
        'COMPETE'  => env('RESOURCE_COMPETE',  ''),
        'LAUNCH'   => env('RESOURCE_LAUNCH',   ''),
        'LI'       => env('RESOURCE_LI',       ''),
        'NAME'     => env('RESOURCE_NAME',     ''),
        'SWIPE'    => env('RESOURCE_SWIPE',    ''),
    ],

];