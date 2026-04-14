<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Instructor') {
    header("Location: ../login");
    exit;
}

require 'controllers/AttendanceController.php';

$extra_css = ['../assets/css/pages/instructor/attendance.css'];
$extra_js = ['../assets/js/pages/instructor/attendance.js'];
include '../includes/header.php';
include '../includes/instructor_sidebar.php';
?>

<div class="flex-grow-1 p-5">
    
    <?php include '../includes/topbar.php'; ?>
    <div class="mb-4">
        <h3 class="fw-bold mb-1" style="color: #111827;">Attendance Tracking</h3>
        <p class="text-muted mb-0">Mark student attendance for each session</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3"><div class="stat-card"><small class="text-muted d-block mb-2">Present</small><h2 class="fw-bold mb-0 text-present" id="count-present">0</h2></div></div>
        <div class="col-md-3"><div class="stat-card"><small class="text-muted d-block mb-2">Absent</small><h2 class="fw-bold mb-0 text-absent" id="count-absent">0</h2></div></div>
        <div class="col-md-3"><div class="stat-card"><small class="text-muted d-block mb-2">Late</small><h2 class="fw-bold mb-0 text-late" id="count-late">0</h2></div></div>
        <div class="col-md-3"><div class="stat-card"><small class="text-muted d-block mb-2">Total</small><h2 class="fw-bold mb-0" id="count-total"><?= count($students) ?></h2></div></div>
    </div>

    <div class="panel-container">
        <form method="POST" action="">
            <div class="row g-4 mb-4 pb-4 border-bottom">
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-dark">Section</label>
                    <select class="form-select" name="section" onchange="window.location.href='?section='+this.value">
                        <?php foreach($sections as $sec): ?>
                            <option value="<?= $sec['id'] ?>" <?= $sec['id'] == $active_section_id ? 'selected' : '' ?>><?= htmlspecialchars($sec['section_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-dark">Date</label>
                    <input type="date" class="form-control" name="date" value="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <div class="list-group list-group-flush mb-4">
                <?php foreach ($students as $index => $student): ?>
                    <div class="list-group-item px-0 py-3 border-0 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <strong class="d-block text-dark"><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></strong>
                            <small class="text-muted"><?= htmlspecialchars($student['student_id']) ?></small>
                        </div>
                        <div class="attendance-group">
                            <input type="radio" class="btn-check status-radio" name="status[<?= $student['student_id'] ?>]" id="present_<?= $index ?>" value="present" autocomplete="off" checked>
                            <label class="attendance-btn btn-present" for="present_<?= $index ?>">Present</label>

                            <input type="radio" class="btn-check status-radio" name="status[<?= $student['student_id'] ?>]" id="late_<?= $index ?>" value="late" autocomplete="off">
                            <label class="attendance-btn btn-late" for="late_<?= $index ?>">Late</label>

                            <input type="radio" class="btn-check status-radio" name="status[<?= $student['student_id'] ?>]" id="absent_<?= $index ?>" value="absent" autocomplete="off">
                            <label class="attendance-btn btn-absent" for="absent_<?= $index ?>">Absent</label>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="btn btn-teal w-100">
                <i class="bi bi-save me-2"></i> Save Attendance
            </button>
        </form>
    </div>

</div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/pages/instructor-attendance.js"></script>
</body>
</html>
