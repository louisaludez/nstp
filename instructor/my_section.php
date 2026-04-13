<?php
session_start();
require '../config/db.php';

// Security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Instructor') {
    header("Location: ../login");
    exit;
}

$instructor_name = $_SESSION['full_name'];
$instructor_id = $_SESSION['user_id'];

// 1. Fetch ALL Instructor's Assigned Sections
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
    // Find the currently selected section in the array
    foreach ($all_assigned_sections as $sec) {
        if ($sec['id'] == $current_section_id) {
            $section = $sec;
            break;
        }
    }
}

$total_students = 0;
$active_students = 0;
$students = [];

if ($section) {
    // 2. Fetch the students assigned to this section
    $stmtStudents = $pdo->prepare("SELECT s.* FROM students s JOIN enrollments e ON s.student_id = e.student_id WHERE e.section_id = ? ORDER BY s.last_name ASC");
    $stmtStudents->execute([$section['id']]);
    $students = $stmtStudents->fetchAll();

    // 3. Calculate metrics
    $total_students = count($students);
    
    // Count active students (Assuming 'enrollment_status' is 'Active' by default)
    foreach ($students as $s) {
        $status = $s['enrollment_status'] ?? 'Active';
        if ($status === 'Active') {
            $active_students++;
        }
    }

    $attendance_rate = 100;
    $stmtAtt = $pdo->prepare("SELECT COUNT(*) as tot, SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as pres FROM attendance WHERE section_id = ?");
    $stmtAtt->execute([$section['id']]);
    $attData = $stmtAtt->fetch();
    if ($attData['tot'] > 0) {
        $attendance_rate = round(($attData['pres'] / $attData['tot']) * 100);
    }
}

include '../includes/header.php';
include '../includes/instructor_sidebar.php';
?>

<style>
    .panel-container { background-color: #fff; border: 1px solid #E5E7EB; border-radius: 12px; padding: 24px; margin-bottom: 24px; }
    
    /* Soft colored metric cards */
    .metric-card { border-radius: 12px; padding: 20px; height: 100%; display: flex; flex-direction: column; justify-content: center; }
    .metric-blue { background-color: #EFF6FF; border: 1px solid #BFDBFE; }
    .metric-blue i { color: #3B82F6; }
    .metric-blue h3, .metric-blue span { color: #1D4ED8; }
    
    .metric-green { background-color: #F0FDF4; border: 1px solid #BBF7D0; }
    .metric-green i { color: #10B981; }
    .metric-green h3, .metric-green span { color: #15803D; }
    
    .metric-purple { background-color: #FAF5FF; border: 1px solid #E9D5FF; }
    .metric-purple i { color: #A855F7; }
    .metric-purple h3, .metric-purple span { color: #7E22CE; }
    
    .metric-orange { background-color: #FFF7ED; border: 1px solid #FED7AA; }
    .metric-orange i { color: #F97316; }
    .metric-orange h3, .metric-orange span { color: #C2410C; }

    /* Custom Table Styling */
    .table-custom th { font-weight: 600; color: #6B7280; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #E5E7EB; padding-bottom: 12px; }
    .table-custom td { vertical-align: middle; padding: 16px 8px; font-size: 0.9rem; color: #374151; border-bottom: 1px solid #F3F4F6; }
    .badge-soft-success { background-color: #D1FAE5; color: #065F46; }
    .badge-soft-primary { background-color: #DBEAFE; color: #1E40AF; }
</style>

<div class="flex-grow-1 p-5">
    
    <?php include '../includes/topbar.php'; ?>
    <div class="d-flex justify-content-between align-items-lg-center flex-column flex-lg-row mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1" style="color: #111827;">Section Monitoring</h3>
            <p class="text-muted mb-0">Monitor your assigned NSTP section, students, and activities</p>
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

    <?php if (!$section): ?>
        <div class="alert alert-warning rounded-3 border-warning">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> You have not been assigned a section yet. Please contact the Coordinator.
        </div>
    <?php else: ?>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="metric-card metric-blue">
                    <i class="bi bi-people fs-4 mb-2"></i>
                    <h3 class="fw-bold mb-0"><?= $total_students ?></h3>
                    <span class="small fw-medium">Total Students</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-card metric-green">
                    <i class="bi bi-graph-up-arrow fs-4 mb-2"></i>
                    <h3 class="fw-bold mb-0"><?= $active_students ?></h3>
                    <span class="small fw-medium">Active Students</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-card metric-purple">
                    <i class="bi bi-calendar-check fs-4 mb-2"></i>
                    <h3 class="fw-bold mb-0"><?= isset($attendance_rate) ? $attendance_rate : '--' ?>%</h3>
                    <span class="small fw-medium">Avg. Attendance</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-card metric-orange">
                    <i class="bi bi-geo-alt fs-4 mb-2"></i>
                    <h3 class="fw-bold mb-0">TBA</h3>
                    <span class="small fw-medium">Assigned Room</span>
                </div>
            </div>
        </div>

        <div class="panel-container">
            <h5 class="fw-bold mb-1">Section Details</h5>
            <p class="text-muted small mb-4"><?= htmlspecialchars($section['component'] . ' - ' . $section['section_name']) ?></p>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-people text-muted me-3 fs-5"></i>
                        <div>
                            <small class="text-muted d-block">Component Type</small>
                            <strong class="text-dark"><?= htmlspecialchars($section['component']) ?></strong>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-calendar3 text-muted me-3 fs-5"></i>
                        <div>
                            <small class="text-muted d-block">Academic Year</small>
                            <strong class="text-dark">2025-2026</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Student List</h5>
                <div class="d-flex gap-2">
                    <div class="position-relative">
                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" class="form-control ps-5" placeholder="Search students..." style="width: 250px; border-radius: 8px;">
                    </div>
                </div>
            </div>
            <p class="text-muted small mb-4">Showing <?= $total_students ?> student(s) enrolled in your section.</p>

            <div class="table-responsive">
                <table class="table table-custom table-borderless w-100">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Year</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($students) > 0): ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td class="text-muted"><?= htmlspecialchars($student['student_id']) ?></td>
                                    <td><strong><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></strong></td>
                                    <td class="text-muted"><?= htmlspecialchars($student['course'] ?? 'N/A') ?></td>
                                    <td class="text-muted"><?= htmlspecialchars($student['year_level'] ?? 'N/A') ?></td>
                                    <td>
                                        <?php $status = $student['enrollment_status'] ?? 'Active'; ?>
                                        <span class="badge <?= $status === 'Active' ? 'badge-soft-success' : 'bg-secondary' ?> rounded-pill px-3">
                                            <?= htmlspecialchars($status) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No students are currently enrolled in this section.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php endif; ?>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>