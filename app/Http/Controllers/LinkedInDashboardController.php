<?php

namespace App\Http\Controllers;

use App\Models\LinkedInCommentLead;
use App\Models\LinkedInPost;
use App\Models\LinkedInSetting;
use App\Services\LinkedIn\PostAnglesLibrary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class LinkedInDashboardController extends Controller
{
    public function index()
    {
        $token            = LinkedInSetting::get('linkedin_token');
        $expiresAt        = LinkedInSetting::get('token_expires_at');
        $isActive         = LinkedInSetting::get('automation_active', '1') === '1';
        $leadMagnetsOn    = LinkedInSetting::get('lead_magnets_active', '0') === '1';
        $rotIndex         = (int) LinkedInSetting::get('rotation_index', 0);

        $recentPosts = LinkedInPost::latest()->take(20)->get();

        $totalLeads       = LinkedInCommentLead::count();
        $pendingDMs       = LinkedInCommentLead::where('dm_status', 'pending')->count();
        $sentDMs          = LinkedInCommentLead::where('dm_status', 'sent')->count();
        $pendingFollowUps = LinkedInCommentLead::where('followup_status', 'pending')
            ->where('dm_status', 'sent')->count();

        $leads = LinkedInCommentLead::with('post')->latest()->take(30)->get();

        $tokenExpiringSoon = $expiresAt && now()->diffInDays($expiresAt) <= 7;

        return view('linkedin.dashboard', compact(
            'token',
            'expiresAt',
            'isActive',
            'leadMagnetsOn',
            'rotIndex',
            'recentPosts',
            'totalLeads',
            'pendingDMs',
            'sentDMs',
            'pendingFollowUps',
            'leads',
            'tokenExpiringSoon'
        ));
    }

    public function toggleAutomation()
    {
        $current = LinkedInSetting::get('automation_active', '1');
        LinkedInSetting::set('automation_active', $current === '1' ? '0' : '1');

        return back()->with('success', 'Automation ' . ($current === '1' ? 'paused' : 'resumed') . '.');
    }

    public function toggleLeadMagnets()
    {
        $current = LinkedInSetting::get('lead_magnets_active', '0');
        $next    = $current === '1' ? '0' : '1';
        LinkedInSetting::set('lead_magnets_active', $next);

        return back()->with('success', 'Lead magnet posts ' . ($next === '1' ? 'enabled' : 'disabled') . '.');
    }

    public function resetIndex(Request $request)
    {
        $request->validate(['index' => 'required|integer|min:0']);
        LinkedInSetting::set('rotation_index', $request->index);

        return back()->with('success', "Rotation index set to {$request->index}.");
    }

    public function previewPost()
    {
        $index     = (int) LinkedInSetting::get('rotation_index', 0);
        $angleData = PostAnglesLibrary::getAngleForIndex($index);

        return response()->json([
            'index'    => $index,
            'type'     => $angleData['post_type'],
            'angle'    => $angleData['angle'],
            'keyword'  => $angleData['keyword'] ?? null,
            'resource' => $angleData['resource'] ?? null,
        ]);
    }

    public function publishNow()
    {
        Artisan::call('linkedin:post');
        $output = trim(Artisan::output());

        return back()->with('success', 'Post triggered. ' . $output);
    }

    public function retryDM(LinkedInCommentLead $lead)
    {
        $lead->update(['dm_status' => 'pending', 'error_message' => null]);

        return back()->with('success', 'DM queued for retry.');
    }
}