<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LinkedIn Panel — Koloursyncc</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
  :root {
    --green: #28A745;
    --green-light: #EAFCED;
    --navy: #140F33;
    --cream: #f8f6f0;
    --ink: #1a1a1a;
    --muted: #6b7280;
    --border: #e5e5e5;
  }

  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: 'Syne', sans-serif;
    background: var(--navy);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .login-box {
    background: #fff;
    border-radius: 16px;
    padding: 48px 40px;
    width: 100%;
    max-width: 380px;
    box-shadow: 0 24px 80px rgba(0,0,0,0.3);
  }

  .login-logo {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.25em;
    text-transform: uppercase;
    color: var(--green);
    margin-bottom: 24px;
  }

  .login-title {
    font-family: 'Syne', sans-serif;
    font-size: 22px;
    font-weight: 800;
    color: var(--navy);
    margin-bottom: 6px;
  }

  .login-sub {
    font-size: 13px;
    color: var(--muted);
    margin-bottom: 32px;
  }

  .form-group {
    margin-bottom: 16px;
  }

  .form-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--muted);
    margin-bottom: 8px;
  }

  .form-input {
    width: 100%;
    padding: 12px 14px;
    border: 1.5px solid var(--border);
    border-radius: 8px;
    font-family: 'DM Mono', monospace;
    font-size: 14px;
    color: var(--ink);
    outline: none;
    transition: border-color 0.15s;
    background: #fff;
  }

  .form-input:focus {
    border-color: var(--green);
  }

  .btn-login {
    width: 100%;
    background: var(--navy);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 13px;
    font-family: 'Syne', sans-serif;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    letter-spacing: 0.03em;
    transition: background 0.15s;
    margin-top: 8px;
  }

  .btn-login:hover {
    background: #1e1654;
  }

  .alert-error {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
    border-radius: 7px;
    padding: 10px 14px;
    font-size: 13px;
    margin-bottom: 18px;
  }
</style>
</head>
<body>

<div class="login-box">
  <div class="login-logo">Koloursyncc</div>
  <div class="login-title">LinkedIn Panel</div>
  <div class="login-sub">Enter your password to continue.</div>

  @if(session('error'))
    <div class="alert-error">{{ session('error') }}</div>
  @endif

  <form method="POST" action="{{ route('linkedin.login.post') }}">
    @csrf
    <div class="form-group">
      <label class="form-label">Password</label>
      <input
        type="password"
        name="password"
        class="form-input"
        autofocus
        autocomplete="current-password"
        placeholder="••••••••"
      >
    </div>
    <button type="submit" class="btn-login">Enter →</button>
  </form>
</div>

</body>
</html>
