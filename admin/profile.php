<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login");
    exit;
}

require 'controllers/ProfileController.php';

$extra_css = ['../assets/css/pages/admin/profile.css'];
include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

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
                <form method="POST" action="" enctype="multipart/form-data">
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
                        <label class="form-label text-dark fw-medium small mb-1">Role</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($user['role']) ?>" readonly>
                        <small class="text-muted">Role assignments can only be changed by system administrators.</small>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-dark fw-medium small mb-1">Electronic Signature</label>
                        <?php if (!empty($user['signature_path'])): ?>
                            <div class="mb-2">
                                <img src="../<?= htmlspecialchars($user['signature_path']) ?>" alt="Signature" style="max-height: 60px; border: 1px solid #ccc; padding: 4px; border-radius: 4px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="signature" class="form-control" accept="image/png, image/jpeg">
                        <small class="text-muted">Upload a transparent PNG for best results.</small>
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
