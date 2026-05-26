<?php
session_start();
require 'config/db.php';

// If already logged in, redirect based on current session
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    $role = $_SESSION['role'];
    if ($role === 'Admin')       $redirect = 'admin/dashboard.php';
    elseif ($role === 'Instructor') $redirect = 'instructor/dashboard.php';
    elseif ($role === 'ROTC')    $redirect = 'rotc/dashboard.php';
    elseif ($role === 'System Admin') $redirect = 'sysadmin/accounts.php';
    else                         $redirect = 'login.php';
    header("Location: $redirect");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_unset();
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];

        if ($_SESSION['role'] === 'Admin') {
            header("Location: admin/dashboard.php");
        } elseif ($_SESSION['role'] === 'Instructor') {
            header("Location: instructor/dashboard.php");
        } elseif ($_SESSION['role'] === 'ROTC') {
            header("Location: rotc/dashboard.php");
        } elseif ($_SESSION['role'] === 'System Admin') {
            header("Location: sysadmin/accounts.php");
        } else {
            $error = "Unauthorized role. Please contact the administrator.";
        }
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — NSTP & Cadet Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #F8FAFC;
            padding: 40px 20px;
            position: relative;
            z-index: 0;
        }

        body::before {
            content: '';
            position: fixed;
            top: -20px;
            left: -20px;
            right: -20px;
            bottom: -20px;
            background: url('nstpmangementsystem-main/dnsc_bg2.png') no-repeat center center / cover;
            filter: blur(3px);
            z-index: -1;
        }

        /* ── Login Card ── */
        .login-card {
            display: flex;
            width: 100%;
            max-width: 1024px;
            min-height: 460px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        /* ── Left Panel ── */
        .login-left {
            flex: 1;
            background: linear-gradient(to bottom right, #0F172A, #1E293B);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }
        .login-left::before {
            content: '';
            position: absolute;
            width: 224px; height: 224px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            top: -40px; right: -40px;
        }
        .login-left::after {
            content: '';
            position: absolute;
            width: 288px; height: 288px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            bottom: 0px; left: -40px;
        }
        .login-left .brand-icon {
            width: 44px; height: 44px;
            background: rgba(99,102,241,0.25);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
            margin-bottom: 28px;
            position: relative; z-index: 1;
        }
        .login-left .uni-label {
            font-size: 11px;
            font-weight: 400;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            color: #94A3B8;
            margin-bottom: 8px;
            position: relative; z-index: 1;
        }
        .login-left h1 {
            font-size: 1.875rem;
            font-weight: 400;
            line-height: 1.25;
            margin-bottom: 32px;
            position: relative; z-index: 1;
        }
        .login-left .tagline {
            display: none;
        }
        .feature-list {
            list-style: none; padding: 0;
            display: flex; flex-direction: column; gap: 12px;
            position: relative; z-index: 1;
        }
        .feature-list li {
            display: flex; align-items: center; gap: 10px;
            font-size: 0.85rem; color: rgba(255,255,255,0.75);
        }
        .feature-list li .dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #818CF8;
            flex-shrink: 0;
        }

        /* ── Right Panel ── */
        .login-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px;
            background: #fff;
        }
        .login-right .sign-in-label {
            font-size: 0.75rem;
            font-weight: 400;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #94A3B8;
            margin-bottom: 4px;
        }
        .login-right h2 {
            font-size: 1.5rem;
            font-weight: 400;
            color: #0F172A;
            margin-bottom: 4px;
        }
        .login-right .subtitle {
            font-size: 0.875rem;
            color: #64748B;
            margin-bottom: 24px;
        }

        /* ── Form ── */
        .form-group { margin-bottom: 16px; }
        .form-group label {
            display: block; font-size: 0.75rem; font-weight: 400; color: #64748B; margin-bottom: 4px;
        }
        .input-wrap {
            display: flex; align-items: center; gap: 10px;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 10px 12px;
            transition: all 0.15s;
        }
        .input-wrap:focus-within {
            border-color: #A5B4FC;
            box-shadow: 0 0 0 4px rgba(238,242,255,1);
        }
        .input-wrap i { color: #9CA3AF; font-size: 1rem; }
        .input-wrap input {
            border: none; outline: none; flex: 1;
            font-size: 0.875rem; font-family: 'Inter', sans-serif; color: #111827;
            background: transparent;
        }
        .input-wrap input::placeholder { color: #94A3B8; }

        .btn-signin {
            width: 100%;
            padding: 12px 16px;
            border-radius: 8px;
            border: none;
            background: #0F172A;
            color: #fff;
            font-size: 0.875rem;
            font-weight: 400;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: all 0.15s;
            margin-top: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .btn-signin:hover { background: #1E293B; }

        .help-link {
            text-align: center;
            margin-top: 18px;
            font-size: 0.78rem;
            color: #6B7280;
        }
        .help-link a { color: #6366F1; text-decoration: none; }
        .help-link a:hover { text-decoration: underline; }

        .alert-error {
            background: #FEE2E2; color: #991B1B;
            padding: 10px 14px; border-radius: 8px;
            font-size: 0.82rem; margin-bottom: 14px;
        }

        /* ── Responsive ── */

        /* Tablet: hide left panel, shrink card */
        @media (max-width: 768px) {
            body { padding: 24px 16px; align-items: flex-start; }
            .login-card {
                flex-direction: column;
                max-width: 480px;
                margin: 0 auto;
            }
            .login-left {
                padding: 32px 36px 28px;
                min-height: unset;
            }
            .login-left h1 { font-size: 1.6rem; }
            .login-left .tagline { margin-bottom: 24px; }
            .feature-list { display: none; }
            .login-right {
                width: 100%;
                padding: 36px;
            }
        }

        /* Mobile: compact */
        @media (max-width: 480px) {
            body { padding: 16px; }
            .login-card { border-radius: 16px; }
            .login-left { padding: 28px 24px 22px; }
            .login-left .brand-icon { width: 38px; height: 38px; font-size: 1rem; margin-bottom: 20px; }
            .login-left h1 { font-size: 1.4rem; }
            .login-right { padding: 28px 24px; }
            .login-right h2 { font-size: 1.25rem; }
        }
    </style>
</head>
<body>

    <div class="login-card">

        <!-- Left Panel -->
        <div class="login-left">
            <img src="assets/images/DSNC.png" alt="DNSC Logo" style="width:64px;height:64px;object-fit:contain;margin-bottom:24px;position:relative;z-index:1;">
            <div class="uni-label">Davao Del Norte State College</div>
            <h1>NSTP Management System</h1>
            <!-- Role hint cards (matching reference design) -->
            <div style="position:relative;z-index:1;margin-top:32px;">
                <div style="font-size:10px;font-weight:400;letter-spacing:0.1em;text-transform:uppercase;color:#64748B;margin-bottom:8px;">Accounts</div>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <div style="display:flex;align-items:center;gap:12px;padding:8px 12px;border-radius:8px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);">
                        <div style="width:28px;height:28px;border-radius:6px;background:linear-gradient(to bottom right, #4F46E5, #3B82F6);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-mortarboard-fill" style="font-size:0.75rem;color:#fff;"></i>
                        </div>
                        <span style="font-size:0.75rem;color:rgba(255,255,255,0.8);">NSTP Coordinator</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px;padding:8px 12px;border-radius:8px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);">
                        <div style="width:28px;height:28px;border-radius:6px;background:linear-gradient(to bottom right, #10B981, #14B8A6);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-book-half" style="font-size:0.75rem;color:#fff;"></i>
                        </div>
                        <span style="font-size:0.75rem;color:rgba(255,255,255,0.8);">CWTS/LTS Instructor</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px;padding:8px 12px;border-radius:8px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);">
                        <div style="width:28px;height:28px;border-radius:6px;background:linear-gradient(to bottom right, #1E293B, #0F172A);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-shield-fill" style="font-size:0.75rem;color:#fff;"></i>
                        </div>
                        <span style="font-size:0.75rem;color:rgba(255,255,255,0.8);">ROTC 1st Class Officer</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="login-right">
            <div class="sign-in-label">SIGN IN</div>
            <h2>Welcome back</h2>
            <p class="subtitle">Enter your credentials to continue.</p>

            <?php if ($error): ?>
                <div class="alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Email</label>
                    <div class="input-wrap">
                        <i class="bi bi-envelope"></i>
                        <input type="email" name="email" placeholder="e.g. coordinator@dnsc.edu.ph" required autofocus>
                    </div>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrap">
                        <i class="bi bi-lock"></i>
                        <input type="password" name="password" placeholder="Enter your password" required>
                    </div>
                </div>
                <button type="submit" class="btn-signin">
                    <i class="bi bi-arrow-right"></i> Sign In
                </button>
            </form>

            <div class="help-link">
                Trouble signing in? <a href="#">Contact the NSTP/ROTC office.</a>
            </div>
        </div>

    </div><!-- /.login-card -->

</body>
</html>