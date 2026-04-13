<?php
session_start();
require '../config/db.php';

// Security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Instructor') {
    header("Location: ../login");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $contact = trim($_POST['contact_number']);
        
        $upd = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, contact_number = ? WHERE id = ?");
        if($upd->execute([$full_name, $email, $contact, $user_id])) {
            $_SESSION['full_name'] = $full_name; // update session
            $message = "Profile details updated successfully!";
            $msgType = "success";
        } else {
            $message = "Failed to update profile.";
            $msgType = "danger";
        }
    }
    
    if (isset($_POST['change_password'])) {
        $stmtMe = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmtMe->execute([$user_id]);
        $me = $stmtMe->fetch();

        $current_pw = $_POST['current_password'];
        $new_pw = $_POST['new_password'];
        $confirm_pw = $_POST['confirm_password'];
        
        if (password_verify($current_pw, $me['password'])) {
            if ($new_pw === $confirm_pw) {
                if (strlen($new_pw) >= 6) {
                    $hash = password_hash($new_pw, PASSWORD_DEFAULT);
                    $upd = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $upd->execute([$hash, $user_id]);
                    $message = "Password successfully changed!";
                    $msgType = "success";
                } else {
                    $message = "New password must be at least 6 characters long.";
                    $msgType = "warning";
                }
            } else {
                $message = "New passwords do not match.";
                $msgType = "warning";
            }
        } else {
            $message = "Incorrect current password.";
            $msgType = "danger";
        }
    }
}

// Fetch Latest User Info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

include '../includes/header.php';
include '../includes/instructor_sidebar.php';
?>

<style>
    .panel-container {
        background-color: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        padding: 30px;
    }
    .form-control {
        border-radius: 8px;
        padding: 10px 15px;
        font-size: 0.95rem;
    }
    .form-control:focus {
        border-color: #004D40;
        box-shadow: 0 0 0 3px rgba(0, 77, 64, 0.1);
    }
    .btn-brand {
        background-color: #004D40;
        color: white;
        font-weight: 500;
        border-radius: 8px;
        padding: 10px 20px;
    }
    .btn-brand:hover {
        background-color: #00332A;
        color: white;
    }
    input:read-only {
        background-color: #F9FAFB !important;
        cursor: not-allowed;
    }
</style>

<div class="flex-grow-1 p-5">
    <?php include '../includes/topbar.php'; ?>
    
    <div class="mb-4">
        <h3 class="fw-bold mb-1" style="color: #111827;">Profile Settings</h3>
        <p class="text-muted mb-0">Manage your account details and password.</p>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $msgType ?> alert-dismissible fade show rounded-3 mb-4">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="panel-container h-100">
                <h5 class="fw-bold mb-4">Profile Information</h5>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium small mb-1">Full Name</label>
                        <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium small mb-1">Email Address</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium small mb-1">Contact Number</label>
                        <input type="text" name="contact_number" class="form-control" value="<?= htmlspecialchars($user['contact_number'] ?? '') ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-dark fw-medium small mb-1">Role / Component</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($user['role'] . ($user['component'] ? ' (' . $user['component'] . ')' : '')) ?>" readonly>
                        <small class="text-muted">Role assignments can only be changed by system administrators.</small>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-brand w-100">Save Changes</button>
                </form>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="panel-container h-100">
                <h5 class="fw-bold mb-4">Security Settings</h5>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium small mb-1">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required placeholder="Enter your current password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium small mb-1">New Password</label>
                        <input type="password" name="new_password" class="form-control" required placeholder="Enter new password">
                        <small class="text-muted">Must be at least 6 characters.</small>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-dark fw-medium small mb-1">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" required placeholder="Re-type new password">
                    </div>
                    <button type="submit" name="change_password" class="btn btn-dark w-100" style="font-weight: 500; border-radius: 8px; padding: 10px 20px;">Update Password</button>
                </form>
            </div>
        </div>
    </div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
