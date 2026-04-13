<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch total students and their component distribution
$stmt = $pdo->query("
    SELECT 
        COUNT(*) as total_students,
        SUM(CASE WHEN component = 'CWTS' THEN 1 ELSE 0 END) as cwts_count,
        SUM(CASE WHEN component = 'LTS' THEN 1 ELSE 0 END) as lts_count,
        SUM(CASE WHEN component = 'ROTC' THEN 1 ELSE 0 END) as rotc_count
    FROM students
");
$stats = $stmt->fetch();
$total_students = $stats['total_students'] ?: 0;
$cwts_count = $stats['cwts_count'] ?: 0;
$lts_count = $stats['lts_count'] ?: 0;
$rotc_count = $stats['rotc_count'] ?: 0;

$cwts_percent = $total_students > 0 ? round(($cwts_count / $total_students) * 100) : 0;
$lts_percent = $total_students > 0 ? round(($lts_count / $total_students) * 100) : 0;
$rotc_percent = $total_students > 0 ? round(($rotc_count / $total_students) * 100) : 0;

// Fetch additional metrics
$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'Instructor'");
$active_instructors = $stmt->fetchColumn() ?: 0;

$stmt = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE status = 'Passed'");
$passed_students = $stmt->fetchColumn() ?: 0;

$stmt = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE status = 'Failed'");
$failed_students = $stmt->fetchColumn() ?: 0;

$stmt = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE serial_number IS NOT NULL AND serial_number != ''");
$certificates_issued = $stmt->fetchColumn() ?: 0;

$stmt = $pdo->query("SELECT COUNT(*) FROM activities WHERE activity_date >= CURDATE()");
$upcoming_activities = $stmt->fetchColumn() ?: 0;

// Fetch recent activities
try {
    $recent_stmt = $pdo->query("SELECT * FROM audit_logs ORDER BY id DESC LIMIT 5");
    $recent_activities = $recent_stmt->fetchAll();
} catch (PDOException $e) {
    $recent_activities = []; // fallback if audit_logs table isn't accessible
}

function time_elapsed_string($datetime) {
    if (!$datetime) return 'just now';
    try {
        $now = new DateTime();
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
        
        if ($diff->y > 0) return $diff->y . ' yr' . ($diff->y > 1 ? 's' : '') . ' ago';
        if ($diff->m > 0) return $diff->m . ' mo' . ($diff->m > 1 ? 's' : '') . ' ago';
        if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
        if ($diff->h > 0) return $diff->h . ' hr' . ($diff->h > 1 ? 's' : '') . ' ago';
        if ($diff->i > 0) return $diff->i . ' min' . ($diff->i > 1 ? 's' : '') . ' ago';
        return 'just now';
    } catch (Exception $e) {
        return 'recently';
    }
}

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
                    <div class="progress">
                        <div class="progress-bar" style="width: <?= $cwts_percent ?>%"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-medium">LTS</span>
                        <span class="text-muted small"><?= $lts_count ?> students (<?= $lts_percent ?>%)</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?= $lts_percent ?>%"></div>
                    </div>
                </div>

                <div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-medium">ROTC</span>
                        <span class="text-muted small"><?= $rotc_count ?> students (<?= $rotc_percent ?>%)</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?= $rotc_percent ?>%"></div>
                    </div>
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