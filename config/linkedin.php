<?php

return [

    /*
    |--------------------------------------------------------------------------
    | LinkedIn OAuth Credentials
    |--------------------------------------------------------------------------
    | From your LinkedIn Developer App → Auth tab
    */
    'client_id'     => env('LINKEDIN_CLIENT_ID'),
    'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
    'redirect_uri'  => env('LINKEDIN_REDIRECT_URI', '/linkedin/callback'),

    /*
    |--------------------------------------------------------------------------
    | Your LinkedIn Person URN
    |--------------------------------------------------------------------------
    | Found at: GET https://api.linkedin.com/v2/me after OAuth
    | Format: urn:li:person:XXXXXXXX
    */
    'person_urn'    => env('LINKEDIN_PERSON_URN'),

    /*
    |--------------------------------------------------------------------------
    | Anthropic (Claude) API
    |--------------------------------------------------------------------------
    */
    'anthropic_key'   => env('ANTHROPIC_API_KEY'),
    'anthropic_model' => env('ANTHROPIC_MODEL', 'claude-sonnet-4-20250514'),

    /*
    |--------------------------------------------------------------------------
    | Posting Schedule (IST = UTC+5:30)
    |--------------------------------------------------------------------------
    | morning_utc : 03:30 UTC = 09:00 IST
    | evening_utc : 12:30 UTC = 18:00 IST
    */
    'schedule' => [
        'morning_utc' => '03:30',
        'evening_utc' => '12:30',
    ],

    /*
    |--------------------------------------------------------------------------
    | Lead Magnet Rotation
    |--------------------------------------------------------------------------
    | Every Nth post will be a lead magnet. Default: every 3rd post.
    */
    'lead_magnet_every' => 3,

    /*
    |--------------------------------------------------------------------------
    | Comment Polling Interval (minutes)
    |--------------------------------------------------------------------------
    | How often to poll LinkedIn for new comments on recent posts
    */
    'poll_interval_minutes' => 15,

    /*
    |--------------------------------------------------------------------------
    | Follow-up DM delay (hours)
    |--------------------------------------------------------------------------
    */
    'followup_delay_hours' => 24,

    /*
    |--------------------------------------------------------------------------
    | DM Resources
    |--------------------------------------------------------------------------
    | keyword => Google Drive / hosted URL of the resource PDF/doc
    */
    'resources' => [
        'AUDIT'    => env('RESOURCE_AUDIT',    'https://drive.google.com/file/d/YOUR_ID/view'),
        'KIRANA'   => env('RESOURCE_KIRANA',   'https://drive.google.com/file/d/YOUR_ID/view'),
        'STORY'    => env('RESOURCE_STORY',    'https://drive.google.com/file/d/YOUR_ID/view'),
        'SCORE'    => env('RESOURCE_SCORE',    'https://drive.google.com/file/d/YOUR_ID/view'),
        'HOMEPAGE' => env('RESOURCE_HOMEPAGE', 'https://drive.google.com/file/d/YOUR_ID/view'),
        'CONVERT'  => env('RESOURCE_CONVERT',  'https://drive.google.com/file/d/YOUR_ID/view'),
        'CALENDAR' => env('RESOURCE_CALENDAR', 'https://drive.google.com/file/d/YOUR_ID/view'),
        'GRID'     => env('RESOURCE_GRID',     'https://drive.google.com/file/d/YOUR_ID/view'),
        'IDENTITY' => env('RESOURCE_IDENTITY', 'https://drive.google.com/file/d/YOUR_ID/view'),
        'BRIEF'    => env('RESOURCE_BRIEF',    'https://drive.google.com/file/d/YOUR_ID/view'),
        'PITCH'    => env('RESOURCE_PITCH',    'https://drive.google.com/file/d/YOUR_ID/view'),
        'INVEST'   => env('RESOURCE_INVEST',   'https://drive.google.com/file/d/YOUR_ID/view'),
        'COPY'     => env('RESOURCE_COPY',     'https://drive.google.com/file/d/YOUR_ID/view'),
        'VOICE'    => env('RESOURCE_VOICE',    'https://drive.google.com/file/d/YOUR_ID/view'),
        'ROI'      => env('RESOURCE_ROI',      'https://drive.google.com/file/d/YOUR_ID/view'),
        'COMPETE'  => env('RESOURCE_COMPETE',  'https://drive.google.com/file/d/YOUR_ID/view'),
        'LAUNCH'   => env('RESOURCE_LAUNCH',   'https://drive.google.com/file/d/YOUR_ID/view'),
        'LI'       => env('RESOURCE_LI',       'https://drive.google.com/file/d/YOUR_ID/view'),
        'NAME'     => env('RESOURCE_NAME',     'https://drive.google.com/file/d/YOUR_ID/view'),
        'SWIPE'    => env('RESOURCE_SWIPE',    'https://drive.google.com/file/d/YOUR_ID/view'),
    ],

];