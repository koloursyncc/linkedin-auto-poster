<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LinkedIn Automation — Koloursyncc</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
  :root {
    --green: #28A745; --green-light: #EAFCED;
    --navy: #140F33;  --navy-mid: #1e1654;
    --cream: #f8f6f0; --ink: #1a1a1a;
    --muted: #6b7280; --border: #e5e5e5;
    --red: #dc3545;   --orange: #fd7e14;
  }
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family:'Syne',sans-serif; background:var(--cream); color:var(--ink); }

  /* LAYOUT */
  .page { max-width:1100px; margin:0 auto; padding:32px 24px; }

  /* HEADER */
  .page-header {
    background:var(--navy); border-radius:12px; padding:24px 28px;
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom:24px;
  }
  .page-header h1 { font-size:20px; font-weight:700; color:#fff; letter-spacing:0.01em; }
  .page-header-sub { font-size:12px; color:rgba(255,255,255,0.4); margin-top:2px; }

  /* ALERTS */
  .alert { border-radius:8px; padding:12px 16px; font-size:13px; margin-bottom:20px; }
  .alert-success { background:#d4edda; border:1px solid #a8d5b5; color:#155724; }
  .alert-error   { background:#f8d7da; border:1px solid #f5c6cb; color:#721c24; }
  .alert-warning { background:#fff3cd; border:1px solid #ffeeba; color:#856404; }

  /* STATUS BAR */
  .status-bar {
    display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr));
    gap:12px; margin-bottom:24px;
  }
  .stat-card {
    background:#fff; border:1.5px solid var(--border);
    border-radius:10px; padding:16px 18px;
  }
  .stat-num  { font-family:'DM Mono',monospace; font-size:26px; font-weight:500; color:var(--navy); }
  .stat-label { font-size:11px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:var(--muted); margin-top:2px; }
  .stat-card.green { border-color:var(--green); background:var(--green-light); }
  .stat-card.green .stat-num { color:var(--green); }
  .stat-card.orange { border-color:var(--orange); background:#fff8f0; }
  .stat-card.orange .stat-num { color:var(--orange); }

  /* GRID */
  .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px; }
  .grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; margin-bottom:24px; }
  @media(max-width:700px) { .grid-2,.grid-3 { grid-template-columns:1fr; } }

  /* CARD */
  .card {
    background:#fff; border:1.5px solid var(--border);
    border-radius:12px; overflow:hidden; margin-bottom:20px;
  }
  .card-header {
    padding:14px 20px; border-bottom:1px solid var(--border);
    display:flex; align-items:center; justify-content:space-between;
  }
  .card-title { font-size:13px; font-weight:700; color:var(--navy); letter-spacing:0.04em; text-transform:uppercase; }
  .card-body  { padding:20px; }

  /* CONNECTION STATUS */
  .conn-status { display:flex; align-items:center; gap:10px; }
  .conn-dot    { width:10px; height:10px; border-radius:50%; }
  .conn-dot.green  { background:var(--green); box-shadow:0 0 0 3px rgba(40,167,69,0.2); }
  .conn-dot.red    { background:var(--red);   box-shadow:0 0 0 3px rgba(220,53,69,0.2); }
  .conn-dot.orange { background:var(--orange);box-shadow:0 0 0 3px rgba(253,126,20,0.2); }
  .conn-text  { font-size:13px; font-weight:600; color:var(--ink); }
  .conn-sub   { font-size:11px; color:var(--muted); }

  /* BUTTONS */
  .btn {
    display:inline-flex; align-items:center; gap:6px;
    padding:9px 18px; border-radius:7px; border:none;
    font-family:'Syne',sans-serif; font-size:12px; font-weight:700;
    cursor:pointer; letter-spacing:0.04em; text-decoration:none;
    transition:all 0.15s;
  }
  .btn-primary   { background:var(--navy); color:#fff; }
  .btn-primary:hover { background:var(--navy-mid); }
  .btn-green     { background:var(--green); color:#fff; }
  .btn-green:hover { background:#1e8c38; }
  .btn-outline   { background:transparent; color:var(--navy); border:1.5px solid var(--border); }
  .btn-outline:hover { border-color:var(--navy); }
  .btn-danger    { background:var(--red); color:#fff; }
  .btn-warning   { background:var(--orange); color:#fff; }
  .btn-sm        { padding:6px 12px; font-size:11px; }

  /* TOGGLE */
  .toggle-wrap { display:flex; align-items:center; gap:12px; }
  .toggle {
    position:relative; width:44px; height:24px;
    background:var(--border); border-radius:100px; cursor:pointer;
    border:none; transition:background 0.2s;
  }
  .toggle.on { background:var(--green); }
  .toggle::after {
    content:''; position:absolute; top:3px; left:3px;
    width:18px; height:18px; border-radius:50%; background:#fff;
    transition:left 0.2s;
  }
  .toggle.on::after { left:23px; }
  .toggle-label { font-size:13px; font-weight:600; color:var(--ink); }

  /* TABLE */
  .data-table { width:100%; border-collapse:collapse; font-size:12.5px; }
  .data-table th {
    background:var(--navy); color:#fff; padding:10px 14px;
    text-align:left; font-size:10px; font-weight:700;
    letter-spacing:0.1em; text-transform:uppercase;
  }
  .data-table td {
    padding:10px 14px; border-bottom:1px solid var(--border);
    vertical-align:top; color:var(--ink);
  }
  .data-table tr:last-child td { border-bottom:none; }
  .data-table tr:hover td { background:#fafafa; }

  /* BADGES */
  .badge {
    display:inline-block; padding:2px 8px; border-radius:100px;
    font-size:10px; font-weight:700; letter-spacing:0.06em; text-transform:uppercase;
  }
  .badge-published { background:#d4edda; color:#155724; }
  .badge-failed    { background:#f8d7da; color:#721c24; }
  .badge-pending   { background:#fff3cd; color:#856404; }
  .badge-swipe     { background:var(--green-light); color:#1a5a2a; }
  .badge-magnet    { background:#fff3cd; color:#92640a; }
  .badge-sent      { background:#d4edda; color:#155724; }
  .badge-skipped   { background:#f3f4f6; color:var(--muted); }

  /* PREVIEW BOX */
  .preview-box {
    background:var(--navy); border-radius:10px; padding:20px;
    font-size:13px; color:rgba(255,255,255,0.8);
    font-family:'DM Mono',monospace; line-height:1.6;
    white-space:pre-wrap; min-height:60px;
  }

  /* FORM INLINE */
  .form-inline { display:flex; gap:10px; align-items:center; }
  .form-input  {
    padding:8px 12px; border:1.5px solid var(--border); border-radius:7px;
    font-family:'Syne',sans-serif; font-size:13px; color:var(--ink);
    background:#fff; outline:none; width:100px;
  }
  .form-input:focus { border-color:var(--green); }
</style>
</head>
<body>
<div class="page">

  {{-- HEADER --}}
  <div class="page-header">
    <div>
      <h1>LinkedIn Automation</h1>
      <div class="page-header-sub">Koloursyncc — Internal Dashboard</div>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
      <span style="font-family:'DM Mono',monospace;font-size:11px;color:rgba(255,255,255,0.4);">Rotation: #{{ $rotIndex }}</span>
      <form action="{{ route('linkedin.publish-now') }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit" class="btn btn-green btn-sm">▶ Post Now</button>
      </form>
    </div>
  </div>

  {{-- ALERTS --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
  @endif
  @if($tokenExpiringSoon)
    <div class="alert alert-warning">⚠️ LinkedIn token expiring soon. <a href="{{ route('linkedin.connect') }}">Re-authorise now →</a></div>
  @endif

  {{-- STAT CARDS --}}
  <div class="status-bar">
    <div class="stat-card {{ $token ? 'green' : '' }}">
      <div class="stat-num">{{ $token ? 'Live' : 'Off' }}</div>
      <div class="stat-label">LinkedIn Status</div>
    </div>
    <div class="stat-card {{ $isActive ? 'green' : '' }}">
      <div class="stat-num">{{ $isActive ? 'ON' : 'OFF' }}</div>
      <div class="stat-label">Auto-posting</div>
    </div>
    <div class="stat-card">
      <div class="stat-num">{{ $rotIndex }}</div>
      <div class="stat-label">Current Index</div>
    </div>
    <div class="stat-card">
      <div class="stat-num">{{ $totalLeads }}</div>
      <div class="stat-label">Total Leads</div>
    </div>
    <div class="stat-card {{ $pendingDMs > 0 ? 'orange' : '' }}">
      <div class="stat-num">{{ $pendingDMs }}</div>
      <div class="stat-label">Pending DMs</div>
    </div>
    <div class="stat-card green">
      <div class="stat-num">{{ $sentDMs }}</div>
      <div class="stat-label">DMs Sent</div>
    </div>
  </div>

  <div class="grid-2">

    {{-- CONNECTION & CONTROLS --}}
    <div class="card">
      <div class="card-header"><span class="card-title">LinkedIn Connection</span></div>
      <div class="card-body">

        @if($token)
          <div class="conn-status" style="margin-bottom:16px;">
            <div class="conn-dot {{ $tokenExpiringSoon ? 'orange' : 'green' }}"></div>
            <div>
              <div class="conn-text">Connected</div>
              <div class="conn-sub">
                Expires: {{ $expiresAt ? \Carbon\Carbon::parse($expiresAt)->format('d M Y') : 'Unknown' }}
                @if($tokenExpiringSoon) <strong style="color:var(--orange);">(Soon!)</strong> @endif
              </div>
            </div>
          </div>
          <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('linkedin.connect') }}" class="btn btn-outline btn-sm">↺ Re-authorise</a>
            <form action="{{ route('linkedin.disconnect') }}" method="POST" style="display:inline;">
              @csrf
              <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Disconnect LinkedIn?')">Disconnect</button>
            </form>
          </div>
        @else
          <div class="conn-status" style="margin-bottom:16px;">
            <div class="conn-dot red"></div>
            <div>
              <div class="conn-text">Not Connected</div>
              <div class="conn-sub">Authorise LinkedIn to start posting</div>
            </div>
          </div>
          <a href="{{ route('linkedin.connect') }}" class="btn btn-green">Connect LinkedIn →</a>
        @endif

      </div>
    </div>

    {{-- AUTOMATION CONTROLS --}}
    <div class="card">
      <div class="card-header"><span class="card-title">Automation Controls</span></div>
      <div class="card-body">

        <form action="{{ route('linkedin.toggle') }}" method="POST" style="margin-bottom:20px;">
          @csrf
          <div class="toggle-wrap">
            <button type="submit" class="toggle {{ $isActive ? 'on' : '' }}"></button>
            <span class="toggle-label">Auto-posting is {{ $isActive ? 'ON' : 'OFF' }}</span>
          </div>
          <div style="font-size:11px;color:var(--muted);margin-top:6px;">
            Posts at 9:00 AM and 6:00 PM IST daily
          </div>
        </form>

        <div style="border-top:1px solid var(--border);padding-top:16px;">
          <div style="font-size:11px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);margin-bottom:10px;">Set Rotation Index</div>
          <form action="{{ route('linkedin.reset-index') }}" method="POST">
            @csrf
            <div class="form-inline">
              <input type="number" name="index" value="{{ $rotIndex }}" min="0" class="form-input">
              <button type="submit" class="btn btn-outline btn-sm">Update</button>
            </div>
          </form>
        </div>

      </div>
    </div>

  </div>

  {{-- NEXT POST PREVIEW --}}
  <div class="card">
    <div class="card-header">
      <span class="card-title">Next Post Preview</span>
      <button onclick="loadPreview()" class="btn btn-outline btn-sm">↺ Refresh</button>
    </div>
    <div class="card-body">
      <div id="preview-loading" style="font-size:13px;color:var(--muted);">Click refresh to preview next post angle...</div>
      <div id="preview-content" style="display:none;">
        <div style="display:flex;gap:10px;margin-bottom:12px;flex-wrap:wrap;">
          <span id="preview-index" class="badge badge-swipe"></span>
          <span id="preview-type" class="badge"></span>
          <span id="preview-keyword" class="badge badge-magnet" style="display:none;"></span>
        </div>
        <div class="preview-box" id="preview-angle"></div>
      </div>
    </div>
  </div>

  {{-- RECENT POSTS --}}
  <div class="card">
    <div class="card-header"><span class="card-title">Recent Posts</span></div>
    <div class="card-body" style="padding:0;">
      <table class="data-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Type</th>
            <th>Post Preview</th>
            <th>Status</th>
            <th>Published</th>
            <th>Leads</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentPosts as $post)
          <tr>
            <td style="font-family:'DM Mono',monospace;color:var(--muted);">{{ $post->rotation_index }}</td>
            <td>
              <span class="badge {{ $post->post_type === 'lead_magnet' ? 'badge-magnet' : 'badge-swipe' }}">
                {{ $post->post_type === 'lead_magnet' ? '🧲 ' . $post->keyword : 'Swipe' }}
              </span>
            </td>
            <td style="max-width:320px;">
              <div style="font-size:12px;color:var(--ink);line-height:1.4;">
                {{ Str::limit($post->generated_text, 100) }}
              </div>
            </td>
            <td><span class="badge badge-{{ $post->status }}">{{ $post->status }}</span></td>
            <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);white-space:nowrap;">
              {{ $post->published_at?->format('d M, H:i') ?? '—' }}
            </td>
            <td style="font-family:'DM Mono',monospace;font-weight:600;color:var(--green);">
              {{ $post->commentLeads->count() }}
            </td>
          </tr>
          @empty
          <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:24px;">No posts yet. Hit "Post Now" to generate your first post.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- LEADS TABLE --}}
  <div class="card">
    <div class="card-header"><span class="card-title">Comment Leads & DM Status</span></div>
    <div class="card-body" style="padding:0;">
      <table class="data-table">
        <thead>
          <tr>
            <th>Person</th>
            <th>Keyword</th>
            <th>Comment</th>
            <th>DM Status</th>
            <th>Follow-up</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($leads as $lead)
          <tr>
            <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
              {{ $lead->commenter_name ?? Str::limit($lead->commenter_urn, 20) }}
            </td>
            <td><span class="badge badge-magnet">{{ $lead->keyword_matched }}</span></td>
            <td style="font-size:12px;max-width:200px;">{{ Str::limit($lead->comment_text, 60) }}</td>
            <td>
              <span class="badge badge-{{ $lead->dm_status }}">{{ $lead->dm_status }}</span>
              @if($lead->dm_sent_at)
                <div style="font-size:10px;color:var(--muted);margin-top:2px;">{{ $lead->dm_sent_at->format('d M H:i') }}</div>
              @endif
            </td>
            <td><span class="badge badge-{{ $lead->followup_status }}">{{ $lead->followup_status }}</span></td>
            <td>
              @if($lead->dm_status !== 'sent')
              <form action="{{ route('linkedin.retry-dm', $lead) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline btn-sm">Retry DM</button>
              </form>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:24px;">No leads yet. Lead magnet posts will populate this table when people comment.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

<script>
async function loadPreview() {
  document.getElementById('preview-loading').style.display = 'block';
  document.getElementById('preview-content').style.display = 'none';

  try {
    const res  = await fetch('{{ route("linkedin.preview") }}');
    const data = await res.json();

    document.getElementById('preview-index').textContent = 'Index #' + data.index;
    document.getElementById('preview-angle').textContent = data.angle;

    const typeEl = document.getElementById('preview-type');
    typeEl.textContent = data.type === 'lead_magnet' ? '🧲 Lead Magnet' : '📋 Swipe Post';
    typeEl.className   = 'badge ' + (data.type === 'lead_magnet' ? 'badge-magnet' : 'badge-swipe');

    const kwEl = document.getElementById('preview-keyword');
    if (data.keyword) {
      kwEl.textContent   = 'Keyword: ' + data.keyword;
      kwEl.style.display = 'inline-block';
    } else {
      kwEl.style.display = 'none';
    }

    document.getElementById('preview-loading').style.display = 'none';
    document.getElementById('preview-content').style.display = 'block';
  } catch(e) {
    document.getElementById('preview-loading').textContent = 'Error loading preview.';
  }
}
</script>
</body>
</html>
