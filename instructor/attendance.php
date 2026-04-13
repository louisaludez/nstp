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

// Handle saving attendance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $section_id = $_POST['section'];
    $date = $_POST['date'];
    $statuses = $_POST['status']; // array of student_id => status

    foreach ($statuses as $sid => $status) {
        $statusMatch = ['present' => 'Present', 'late' => 'Late', 'absent' => 'Absent'];
        $dbStatus = $statusMatch[$status] ?? 'Present';
        
        $stmtCheck = $pdo->prepare("SELECT id FROM attendance WHERE student_id = ? AND attendance_date = ?");
        $stmtCheck->execute([$sid, $date]);
        if ($stmtCheck->fetch()) {
            $pdo->prepare("UPDATE attendance SET status = ? WHERE student_id = ? AND attendance_date = ?")->execute([$dbStatus, $sid, $date]);
        } else {
            $pdo->prepare("INSERT INTO attendance (student_id, section_id, attendance_date, status) VALUES (?, ?, ?, ?)")->execute([$sid, $section_id, $date, $dbStatus]);
        }
    }
    header("Location: attendance.php?msg=saved");
    exit;
}

// Get sections
$stmtSec = $pdo->prepare("SELECT * FROM sections WHERE instructor_id = ?");
$stmtSec->execute([$instructor_id]);
$sections = $stmtSec->fetchAll();

$students = [];
$active_section_id = null;
if (count($sections) > 0) {
    $active_section_id = $_GET['section'] ?? $sections[0]['id'];
    
    $stmtStu = $pdo->prepare("SELECT s.* FROM students s JOIN enrollments e ON s.student_id = e.student_id WHERE e.section_id = ? ORDER BY s.last_name ASC");
    $stmtStu->execute([$active_section_id]);
    $students = $stmtStu->fetchAll();
}

include '../includes/header.php';
include '../includes/instructor_sidebar.php';
?>

<style>
    .panel-container { 
        background-color: #fff; 
        border: 1px solid #E5E7EB; 
        border-radius: 12px; 
        padding: 24px; 
    }
    
    /* Stat Cards */
    .stat-card {
        background-color: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        padding: 20px;
    }
    
    /* Custom Radio Toggle Buttons */
    .attendance-group {
        display: flex;
        gap: 8px;
        background-color: #F9FAFB;
        padding: 4px;
        border-radius: 8px;
        border: 1px solid #E5E7EB;
    }
    
    .attendance-btn {
        padding: 6px 16px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 500;
        color: #4B5563;
        background-color: transparent;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .attendance-btn:hover { background-color: #E5E7EB; }

    /* Checked States */
    .btn-check:checked + .btn-present { background-color: #10B981; color: white; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2); }
    .btn-check:checked + .btn-late { background-color: #F59E0B; color: white; box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2); }
    .btn-check:checked + .btn-absent { background-color: #EF4444; color: white; box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2); }

    /* Text Colors for Stats */
    .text-present { color: #10B981; }
    .text-absent { color: #EF4444; }
    .text-late { color: #F59E0B; }
    
    /* Save Button */
    .btn-teal {
        background-color: #00695C;
        color: white;
        font-weight: 600;
        border-radius: 8px;
        padding: 12px;
        transition: background-color 0.2s;
    }
    .btn-teal:hover { background-color: #004D40; color: white; }
</style>

<div class="flex-grow-1 p-5">
    
    <?php include '../includes/topbar.php'; ?>
    <div class="mb-4">
        <h3 class="fw-bold mb-1" style="color: #111827;">Attendance Tracking</h3>
        <p class="text-muted mb-0">Mark student attendance for each session</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <small class="text-muted d-block mb-2">Present</small>
                <h2 class="fw-bold mb-0 text-present" id="count-present">4</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <small class="text-muted d-block mb-2">Absent</small>
                <h2 class="fw-bold mb-0 text-absent" id="count-absent">0</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <small class="text-muted d-block mb-2">Late</small>
                <h2 class="fw-bold mb-0 text-late" id="count-late">0</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <small class="text-muted d-block mb-2">Total</small>
                <h2 class="fw-bold mb-0" id="count-total">4</h2>
            </div>
        </div>
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
                    <input type="date" class="form-control" name="date" value="2026-08-04">
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

</div> <script>
document.addEventListener('DOMContentLoaded', function() {
    const radios = document.querySelectorAll('.status-radio');
    
    function updateCounts() {
        let present = 0, late = 0, absent = 0;
        
        radios.forEach(radio => {
            if (radio.checked) {
                if (radio.value === 'present') present++;
                if (radio.value === 'late') late++;
                if (radio.value === 'absent') absent++;
            }
        });
        
        document.getElementById('count-present').innerText = present;
        document.getElementById('count-late').innerText = late;
        document.getElementById('count-absent').innerText = absent;
    }

    // Attach event listeners to all radio buttons
    radios.forEach(radio => {
        radio.addEventListener('change', updateCounts);
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 