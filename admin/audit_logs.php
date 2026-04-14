<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login");
    exit;
}

require 'controllers/AuditLogsController.php';

$extra_css = ['../assets/css/pages/admin/audit-logs.css'];
$extra_js = ['../assets/js/pages/admin/audit-logs.js'];
include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

<div class="flex-grow-1 p-5 d-flex flex-column align-items-start">

    <?php include '../includes/topbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-4 w-100">
        <div>
            <h3 class="fw-bold mb-1" style="color: #111827;">Audit Logs</h3>
            <p class="text-muted mb-0">Track all system changes and user activities</p>
        </div>
        <button type="button" class="btn btn-brand border-0">
            <i class="bi bi-download me-2"></i> Export Logs
        </button>
    </div>

    <div class="panel-container">
        <div class="d-flex justify-content-between align-items-center filter-bar" style="height: 50px;">
            <div class="d-flex align-items-center px-3">
                <i class="bi bi-search text-muted"></i>
                <input type="text" id="logSearch" class="form-control search-input ms-2" placeholder="Search logs...">
            </div>
            <div class="d-flex align-items-center px-3 border-start" style="height: 100%;">
                <i class="bi bi-funnel text-muted me-2"></i>
                <select class="filter-select border-0">
                    <option>All Actions</option>
                    <option>Created Student</option>
                    <option>Updated Grade</option>
                    <option>Assigned Instructor</option>
                </select>
            </div>
        </div>

        <div class="w-100">
            <?php if (count($logs) > 0): ?>
                <?php foreach ($logs as $log): ?>
                    <div class="log-card">
                        <i class="bi bi-shield shield-icon"></i>
                        <div>
                            <div class="d-flex align-items-center mb-2">
                                <span class="action-badge <?= getBadgeStyle($log['action_type']) ?>">
                                    <?= htmlspecialchars($log['action_type']) ?>
                                </span>
                                <span class="text-muted small fw-medium"><?= htmlspecialchars($log['user_name']) ?></span>
                            </div>
                            <p class="mb-1" style="color: #111827; font-size: 0.95rem;">
                                <?= htmlspecialchars($log['details']) ?>
                            </p>
                            <small class="text-muted" style="font-size: 0.8rem;">
                                <?= htmlspecialchars($log['created_at']) ?>
                            </small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-journal-x text-muted fs-1 mb-2"></i>
                    <p class="text-muted small">No audit logs found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
