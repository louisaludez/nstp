<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ROTC') {
    header("Location: ../login.php"); exit;
}
$extra_css = ['../assets/css/style.css'];
include '../includes/header.php';
include '../includes/rotc_sidebar.php';
?>
<div class="flex-grow-1 p-4 p-lg-5 w-100" style="background:#F9FAFB;min-height:100vh;">
    <?php include '../includes/topbar.php'; ?>
    <div class="mb-4">
        <h2 class="fw-bold mb-0" style="color:#111827;">Activity Plans</h2>
        <p class="text-muted" style="font-size:0.88rem;">Design and submit ROTC training activity plans</p>
    </div>
    <div class="d-flex flex-column align-items-center justify-content-center py-5 text-center"
         style="background:#fff;border:1px dashed #E5E7EB;border-radius:16px;min-height:300px;">
        <div style="width:60px;height:60px;border-radius:50%;background:#F1F5F9;display:flex;align-items:center;justify-content:center;font-size:1.6rem;color:#9CA3AF;margin-bottom:16px;">
            <i class="bi bi-journal-text"></i>
        </div>
        <h6 style="color:#374151;font-weight:600;margin-bottom:4px;">Activity Plans Module</h6>
        <p class="text-muted small">This page is coming soon. Activity plan creation will appear here.</p>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
