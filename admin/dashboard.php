<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login");
    exit;
}

require 'controllers/DashboardController.php';

$extra_css = ['../assets/css/pages/admin-dashboard.css'];
include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

<div class="flex-grow-1 p-5 w-100">
    
    <?php include '../includes/topbar.php'; ?>
    <div class="mb-4">
        <h3 class="fw-bold mb-1">Coordinator Dashboard</h3>
        <p class="text-muted">Welcome back! Here's what's happening with NSTP today.</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Total Students</small>
                        <h3 class="fw-bold mb-0 mt-1"><?= number_format($total_students) ?></h3>
                    </div>
                    <div class="icon-circle" style="background-color: var(--icon-blue);">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Active Instructors</small>
                        <h3 class="fw-bold mb-0 mt-1"><?= number_format($active_instructors) ?></h3>
                    </div>
                    <div class="icon-circle" style="background-color: var(--icon-green);">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Passed Students</small>
                        <h3 class="fw-bold mb-0 mt-1"><?= number_format($passed_students) ?></h3>
                    </div>
                    <div class="icon-circle" style="background-color: var(--icon-green);">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Failed Students</small>
                        <h3 class="fw-bold mb-0 mt-1"><?= number_format($failed_students) ?></h3>
                    </div>
                    <div class="icon-circle" style="background-color: var(--icon-red);">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Certificates Issued</small>
                        <h3 class="fw-bold mb-0 mt-1"><?= number_format($certificates_issued) ?></h3>
                    </div>
                    <div class="icon-circle" style="background-color: var(--icon-purple);">
                        <i class="bi bi-award-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Upcoming Activities</small>
                        <h3 class="fw-bold mb-0 mt-1"><?= number_format($upcoming_activities) ?></h3>
                    </div>
                    <div class="icon-circle" style="background-color: var(--icon-orange);">
                        <i class="bi bi-calendar-event-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card p-4 h-100">
                <h6 class="fw-bold mb-4">Component Distribution</h6>
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-medium">CWTS</span>
                        <span class="text-muted small"><?= $cwts_count ?> students (<?= $cwts_percent ?>%)</span>
                    </div>
                    <div class="progress"><div class="progress-bar" style="width: <?= $cwts_percent ?>%"></div></div>
                </div>
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-medium">LTS</span>
                        <span class="text-muted small"><?= $lts_count ?> students (<?= $lts_percent ?>%)</span>
                    </div>
                    <div class="progress"><div class="progress-bar" style="width: <?= $lts_percent ?>%"></div></div>
                </div>
                <div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-medium">ROTC</span>
                        <span class="text-muted small"><?= $rotc_count ?> students (<?= $rotc_percent ?>%)</span>
                    </div>
                    <div class="progress"><div class="progress-bar" style="width: <?= $rotc_percent ?>%"></div></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-4 h-100">
                <h6 class="fw-bold mb-4">Recent Activities</h6>
                <?php if (count($recent_activities) > 0): ?>
                    <?php foreach ($recent_activities as $activity): ?>
                        <div class="d-flex mb-4">
                            <?php
                                $color = 'primary';
                                if (stripos($activity['action_type'], 'Delete') !== false) $color = 'danger';
                                elseif (stripos($activity['action_type'], 'Update') !== false) $color = 'warning';
                                elseif (stripos($activity['action_type'], 'Create') !== false) $color = 'success';
                            ?>
                            <i class="bi bi-circle-fill text-<?= $color ?> mt-1 me-3" style="font-size: 0.5rem;"></i>
                            <div>
                                <p class="mb-0 fw-medium"><?= htmlspecialchars($activity['action_type']) ?></p>
                                <small class="text-muted d-block"><?= htmlspecialchars($activity['details']) ?></small>
                                <small class="text-muted" style="font-size: 0.75rem;">
                                    <?= time_elapsed_string($activity['created_at'] ?? null) ?> by <?= htmlspecialchars($activity['user_name']) ?>
                                </small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted small text-center mt-3">No recent activities.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
