<?php
/**
 * ROTC First Class Officer — One-Time Setup Script
 * Run once via browser: http://localhost:8000/rotc_setup.php
 * Delete this file after running it.
 */
require 'config/db.php';

$steps   = [];
$errors  = [];

// ── Step 1: Alter users.role enum to include 'ROTC' ──────────────────────────
try {
    $pdo->exec("ALTER TABLE `users`
        MODIFY COLUMN `role` ENUM('Admin','Instructor','ROTC') NOT NULL");
    $steps[] = "✅ Step 1: <code>users.role</code> enum updated to include <strong>ROTC</strong>.";
} catch (PDOException $e) {
    if (str_contains($e->getMessage(), "Duplicate column") || str_contains($e->getMessage(), "already exists")) {
        $steps[] = "ℹ️  Step 1: Enum already includes ROTC — skipped.";
    } else {
        $errors[] = "❌ Step 1 failed: " . htmlspecialchars($e->getMessage());
    }
}

// ── Step 2: Insert ROTC First Class Officer account ───────────────────────────
$rotc_name    = 'ROTC First Class Officer';
$rotc_email   = 'rotc@dnsc.edu.ph';
$rotc_password = 'rotc1234'; // Change this after first login!
$rotc_hash    = password_hash($rotc_password, PASSWORD_BCRYPT);

try {
    // Check if account already exists
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$rotc_email]);
    if ($check->fetch()) {
        $steps[] = "ℹ️  Step 2: ROTC account <strong>{$rotc_email}</strong> already exists — skipped.";
    } else {
        $insert = $pdo->prepare(
            "INSERT INTO users (full_name, email, password, role, component, contact_number)
             VALUES (?, ?, ?, 'ROTC', 'ROTC', NULL)"
        );
        $insert->execute([$rotc_name, $rotc_email, $rotc_hash]);
        $steps[] = "✅ Step 2: ROTC officer account created.
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;📧 Email: <code>{$rotc_email}</code>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;🔑 Password: <code>{$rotc_password}</code>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;<em style='color:#b45309;'>⚠️ Change the password after first login!</em>";
    }
} catch (PDOException $e) {
    $errors[] = "❌ Step 2 failed: " . htmlspecialchars($e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ROTC Setup — NSTP Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #F8F9FB;
        }
        .card {
            background: #fff;
            border-radius: 16px;
            padding: 40px 48px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .badge-rotc {
            display: inline-flex; align-items: center; gap: 8px;
            background: #374151; color: #fff;
            font-size: 0.7rem; font-weight: 700; letter-spacing: 0.08em;
            padding: 4px 12px; border-radius: 20px; margin-bottom: 20px;
        }
        h1 { font-size: 1.4rem; font-weight: 700; color: #111827; margin-bottom: 6px; }
        p.sub { font-size: 0.85rem; color: #6B7280; margin-bottom: 28px; }

        .step { margin-bottom: 16px; font-size: 0.9rem; line-height: 1.6; color: #374151; }
        .step code { background: #F3F4F6; padding: 2px 6px; border-radius: 4px; font-size: 0.85rem; }

        .error { color: #DC2626; font-size: 0.9rem; margin-bottom: 12px; }

        .divider { height: 1px; background: #E5E7EB; margin: 24px 0; }

        .footer { font-size: 0.8rem; color: #9CA3AF; text-align: center; }
        .footer a { color: #6366F1; text-decoration: none; }
        .footer a:hover { text-decoration: underline; }

        .warning-box {
            background: #FEF3C7; border: 1px solid #FCD34D; border-radius: 10px;
            padding: 14px 18px; margin-top: 20px;
            font-size: 0.82rem; color: #92400E; line-height: 1.6;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="badge-rotc">🛡 ROTC SETUP</div>
    <h1>ROTC First Class Officer — Account Setup</h1>
    <p class="sub">One-time migration script for the NSTP &amp; Cadet Portal.</p>

    <?php foreach ($steps as $step): ?>
        <div class="step"><?= $step ?></div>
    <?php endforeach; ?>

    <?php foreach ($errors as $err): ?>
        <div class="error"><?= $err ?></div>
    <?php endforeach; ?>

    <?php if (empty($errors)): ?>
        <div class="warning-box">
            <strong>⚠️ Security Reminder:</strong> Delete or disable <code>rotc_setup.php</code> after confirming the account works.
            This file should not be accessible in production.
        </div>
    <?php endif; ?>

    <div class="divider"></div>
    <div class="footer">
        <a href="login.php">← Back to Login</a>
        &nbsp;·&nbsp;
        <a href="login.php">Test ROTC Login</a>
    </div>
</div>
</body>
</html>
