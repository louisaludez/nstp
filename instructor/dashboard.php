<?php
session_start();
require '../config/db.php';

// Security: Only allow Instructors
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Instructor') {
    header("Location: ../login");
    exit;
}

$instructor_id = $_SESSION['user_id'];
$instructor_name = $_SESSION['full_name'];

// Fetch ALL Instructor's Assigned Sections
$stmtAllSections = $pdo->prepare("SELECT * FROM sections WHERE instructor_id = ? ORDER BY component, section_name");
$stmtAllSections->execute([$instructor_id]);
$all_assigned_sections = $stmtAllSections->fetchAll();

// Determine which section is currently active
$current_section_id = $_GET['section_id'] ?? null;
if (!$current_section_id && count($all_assigned_sections) > 0) {
    $current_section_id = $all_assigned_sections[0]['id'];
}

$section = null;
if ($current_section_id) {
    foreach ($all_assigned_sections as $sec) {
        if ($sec['id'] == $current_section_id) {
            $section = $sec;
            break;
        }
    }
}

$student_count = 0;
$active_student_count = 0;
$attendance_rate = 100;

if ($section) {
    $stmtStu = $pdo->prepare("SELECT COUNT(s.student_id) FROM students s JOIN enrollments e ON s.student_id = e.student_id WHERE e.section_id = ?");
    $stmtStu->execute([$section['id']]);
    $student_count = $stmtStu->fetchColumn();

    $stmtAct = $pdo->prepare("SELECT COUNT(s.student_id) FROM students s JOIN enrollments e ON s.student_id = e.student_id WHERE e.section_id = ? AND s.enrollment_status = 'Active'");
    $stmtAct->execute([$section['id']]);
    $active_student_count = $stmtAct->fetchColumn();
    
    $stmtAtt = $pdo->prepare("SELECT COUNT(*) as tot, SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as pres FROM attendance WHERE section_id = ?");
    $stmtAtt->execute([$section['id']]);
    $attData = $stmtAtt->fetch();
    if ($attData['tot'] > 0) {
        $attendance_rate = round(($attData['pres'] / $attData['tot']) * 100);
    }
}

$stmtPlans = $pdo->prepare("SELECT * FROM activity_plans WHERE instructor_id = ? ORDER BY submitted_date DESC LIMIT 5");
$stmtPlans->execute([$instructor_id]);
$activity_plans = $stmtPlans->fetchAll();

$stmtReps = $pdo->prepare("SELECT * FROM accomplishment_reports WHERE instructor_id = ? ORDER BY submitted_date DESC LIMIT 5");
$stmtReps->execute([$instructor_id]);
$accomplishment_reports = $stmtReps->fetchAll();

$stmtSessions = $pdo->prepare("SELECT * FROM activities WHERE component = ? AND activity_date >= CURDATE() ORDER BY activity_date ASC LIMIT 5");
$stmtSessions->execute([$section['component'] ?? '']);
$upcoming_sessions = $stmtSessions->fetchAll();


include '../includes/header.php';
include '../includes/instructor_sidebar.php';
?>

<style>
    .panel-container {
        background-color: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
    }

    .stat-card {
        background-color: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .icon-box {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
    }

    /* Branding Colors */
    .bg-blue-brand {
        background-color: #3B82F6;
    }

    .bg-green-brand {
        background-color: #10B981;
    }

    .bg-purple-brand {
        background-color: #A855F7;
    }

    .bg-orange-brand {
        background-color: #F97316;
    }

    .text-teal-brand {
        color: #00695C;
    }

    .quick-action-btn {
        border-radius: 8px;
        border: 1px solid #E5E7EB;
        background: white;
        color: #3B82F6;
        padding: 6px 16px;
        font-weight: 500;
        text-decoration: none;
        font-size: 0.9rem;
        margin-right: 10px;
    }

    .quick-action-btn:hover {
        background-color: #F9FAFB;
    }
</style>

<div class="flex-grow-1 p-5">

    <?php include '../includes/topbar.php'; ?>
    <div class="d-flex justify-content-between align-items-lg-center flex-column flex-lg-row mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1">Instructor Dashboard</h3>
            <p class="text-muted mb-0">Welcome back, Prof. <?= htmlspecialchars($instructor_name) ?>! Here's your comprehensive overview.</p>
        </div>
        
        <?php if (!empty($all_assigned_sections) && count($all_assigned_sections) > 0): ?>
            <div>
                <form method="GET" action="" class="d-flex align-items-center">
                    <label class="text-muted small fw-medium me-2 text-nowrap">Active Section:</label>
                    <select name="section_id" class="form-select border-0 shadow-sm" style="font-weight: 500; min-width: 180px; background-color: white;" onchange="this.form.submit()">
                        <?php foreach ($all_assigned_sections as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= $s['id'] == $current_section_id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['component'] . ' - ' . $s['section_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div><small class="text-muted d-block">My Students</small>
                    <h2 class="fw-bold mb-0"><?= $student_count ?></h2>
                </div>
                <div class="icon-box bg-blue-brand"><i class="bi bi-people"></i></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div><small class="text-muted d-block">Attendance Rate</small>
                    <h2 class="fw-bold mb-0"><?= $attendance_rate ?>%</h2>
                </div>
                <div class="icon-box bg-green-brand"><i class="bi bi-check2-square"></i></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div><small class="text-muted d-block">Activity Plans</small>
                    <h2 class="fw-bold mb-0"><?= count($activity_plans) ?></h2>
                </div>
                <div class="icon-box bg-purple-brand"><i class="bi bi-file-earmark-text"></i></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div><small class="text-muted d-block">Reports Submitted</small>
                    <h2 class="fw-bold mb-0"><?= count($accomplishment_reports) ?></h2>
                </div>
                <div class="icon-box bg-orange-brand"><i class="bi bi-calendar-event"></i></div>
            </div>
        </div>
    </div>

    <div class="panel-container">
        <h5 class="fw-bold mb-4">My Section Details</h5>
        <div class="row">
            <div class="col-md-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-people text-primary me-2 fs-5"></i>
                    <div><small
                            class="text-muted d-block">Section</small><strong><?= $section ? htmlspecialchars($section['section_name']) : 'Not Assigned' ?></strong>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle text-success me-2 fs-5"></i>
                    <div><small class="text-muted d-block">Students</small><strong><?= $student_count ?> (<?= $active_student_count ?> active)</strong></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-geo-alt text-purple me-2 fs-5"></i>
                    <div><small class="text-muted d-block">Room Assignment</small><strong>Room 301</strong></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-clock text-orange me-2 fs-5"></i>
                    <div><small class="text-muted d-block">Schedule</small><strong>Thursdays, 8:00 AM - 10:00
                            AM</strong></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="panel-container">
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="fw-bold mb-0">Activity Plans</h5>
                    <a href="#" class="text-primary text-decoration-none small">View All</a>
                </div>
                <div class="list-group list-group-flush">
                    <?php if (count($activity_plans) > 0): ?>
                        <?php foreach($activity_plans as $ap): ?>
                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center border-0 mb-2">
                                <div><strong><?= htmlspecialchars($ap['title']) ?></strong><br><small class="text-muted">Deadline: <?= htmlspecialchars($ap['scheduled_date'] ?: 'None') ?></small></div>
                                <span class="badge bg-<?= $ap['status'] === 'Approved' ? 'success' : ($ap['status'] === 'Rejected' ? 'danger' : 'primary') ?> bg-opacity-10 text-<?= $ap['status'] === 'Approved' ? 'success' : ($ap['status'] === 'Rejected' ? 'danger' : 'primary') ?> rounded-pill px-3"><?= htmlspecialchars($ap['status']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-muted small py-2">No activity plans submitted.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel-container">
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="fw-bold mb-0">Accomplishment Reports</h5>
                    <a href="#" class="text-primary text-decoration-none small">View All</a>
                </div>
                <div class="list-group list-group-flush">
                    <?php if (count($accomplishment_reports) > 0): ?>
                        <?php foreach($accomplishment_reports as $ar): ?>
                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center border-0 mb-2">
                                <div><strong><?= htmlspecialchars($ar['title']) ?></strong><br><small class="text-muted">Submitted: <?= htmlspecialchars($ar['completed_date'] ?: 'Unknown') ?></small></div>
                                <span class="badge bg-<?= $ar['status'] === 'Reviewed' ? 'success' : 'primary' ?> bg-opacity-10 text-<?= $ar['status'] === 'Reviewed' ? 'success' : 'primary' ?> rounded-pill px-3"><?= htmlspecialchars($ar['status']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-muted small py-2">No accomplishment reports submitted.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="panel-container">
                <h5 class="fw-bold mb-4">Upcoming Sessions</h5>
                <div class="list-group list-group-flush">
                    <?php if (count($upcoming_sessions) > 0): ?>
                        <?php foreach($upcoming_sessions as $sess): ?>
                            <div class="list-group-item px-0 border-0 mb-3 pb-3 border-bottom">
                                <h6 class="fw-bold mb-2"><?= htmlspecialchars($sess['title']) ?></h6>
                                <div class="d-flex gap-3 text-muted" style="font-size: 0.85rem;">
                                    <span><i class="bi bi-calendar3 me-1"></i> <?= htmlspecialchars($sess['activity_date']) ?></span>
                                    <span><i class="bi bi-clock me-1"></i> <?= htmlspecialchars($sess['activity_time'] ?? 'TBA') ?></span>
                                    <span><i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($sess['location']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-muted small py-2">No upcoming sessions.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="panel-container">
                <h5 class="fw-bold mb-4">Announcements</h5>
                <div class="list-group list-group-flush">
                    <?php 
                    $stmtAnnounce = $pdo->query("SELECT * FROM notifications WHERE user_id IS NULL AND (role = 'Instructor' OR role IS NULL) ORDER BY created_at DESC LIMIT 3");
                    $announcements = $stmtAnnounce->fetchAll();
                    if (count($announcements) > 0):
                        foreach ($announcements as $ann):
                    ?>
                        <div class="list-group-item px-0 border-0 mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-info-circle text-primary me-2 mt-1"></i>
                                <div>
                                    <h6 class="fw-bold mb-1"><?= htmlspecialchars($ann['message']) ?></h6>
                                    <small class="text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($ann['created_at']) ?></small>
                                </div>
                            </div>
                        </div>
                    <?php 
                        endforeach;
                    else: ?>
                        <div class="text-muted small py-2">No recent announcements.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-container" style="background-color: #F0F7FF; border: 1px solid #CFE2FF;">
        <div class="d-flex align-items-center mb-3">
            <i class="bi bi-info-circle text-primary me-2"></i>
            <h6 class="fw-bold mb-0 text-primary">Quick Actions</h6>
        </div>
        <div class="d-flex">
            <a href="#" class="quick-action-btn">Submit Activity Plan</a>
            <a href="#" class="quick-action-btn">Report Accomplishment</a>
            <a href="my_section" class="quick-action-btn">View Section</a>
        </div>
    </div>


</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>