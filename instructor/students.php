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

// Get instructor's sections
$stmtSec = $pdo->prepare("SELECT * FROM sections WHERE instructor_id = ?");
$stmtSec->execute([$instructor_id]);
$sections = $stmtSec->fetchAll();

$students = [];
if (count($sections) > 0) {
    $secIds = array_column($sections, 'id');
    $placeholders = implode(',', array_fill(0, count($secIds), '?'));
    $stmtStu = $pdo->prepare("
        SELECT s.*, sec.section_name as section
        FROM students s
        JOIN enrollments e ON s.student_id = e.student_id
        LEFT JOIN sections sec ON e.section_id = sec.id
        WHERE e.section_id IN ($placeholders)
        ORDER BY s.last_name ASC
    ");
    $stmtStu->execute($secIds);
    $students = $stmtStu->fetchAll();
    
    foreach ($students as &$stu) {
        $stmtAtt = $pdo->prepare("SELECT COUNT(*) as tot, SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as pres FROM attendance WHERE student_id = ?");
        $stmtAtt->execute([$stu['student_id']]);
        $attData = $stmtAtt->fetch();
        $stu['attendance'] = ($attData['tot'] > 0) ? round(($attData['pres'] / $attData['tot']) * 100) : 100;
        
        $stmtGrade = $pdo->prepare("SELECT final_grade, status FROM enrollments WHERE student_id = ?");
        $stmtGrade->execute([$stu['student_id']]);
        $enrollment = $stmtGrade->fetch();
        $stu['grade'] = isset($enrollment['final_grade']) ? $enrollment['final_grade'] : 'N/A';
        $stu['status'] = $enrollment['status'] ?? 'Active';
    }
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
    
    /* Search and Filter */
    .search-bar-container {
        display: flex;
        gap: 16px;
        margin-bottom: 24px;
    }
    .search-input-wrapper {
        position: relative;
        flex-grow: 1;
    }
    .search-input-wrapper i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #9CA3AF;
    }
    .search-input {
        width: 100%;
        padding: 10px 10px 10px 40px;
        border: 1px solid #D1D5DB;
        border-radius: 8px;
        font-size: 0.95rem;
    }
    .search-input:focus { outline: none; border-color: #00695C; box-shadow: 0 0 0 3px rgba(0, 105, 92, 0.1); }
    
    .filter-select {
        padding: 10px 16px;
        border: 1px solid #D1D5DB;
        border-radius: 8px;
        background-color: white;
        color: #374151;
        font-size: 0.95rem;
        min-width: 140px;
    }

    /* Custom Table Styling */
    .table-custom th {
        font-weight: 700;
        color: #111827;
        font-size: 0.85rem;
        border-bottom: 1px solid #E5E7EB;
        padding-bottom: 16px;
    }
    .table-custom td {
        vertical-align: middle;
        padding: 16px 8px;
        font-size: 0.9rem;
        color: #374151;
        border-bottom: 1px solid #F3F4F6;
    }

    /* Badges */
    .badge-section { 
        background-color: #CCFBF1; /* Light Teal */
        color: #0F766E; 
        font-weight: 500; 
        padding: 6px 12px; 
        border-radius: 20px; 
        font-size: 0.75rem; 
    }
    .badge-status { 
        background-color: #D1FAE5; /* Light Green */
        color: #065F46; 
        font-weight: 500; 
        padding: 6px 12px; 
        border-radius: 20px; 
        font-size: 0.75rem; 
    }

    /* Custom Progress Bar for Attendance */
    .progress-container {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .progress-track {
        width: 45px;
        height: 6px;
        background-color: #E5E7EB;
        border-radius: 4px;
        overflow: hidden;
    }
    .progress-fill { height: 100%; border-radius: 4px; }
    .bg-teal-brand { background-color: #10B981; }
    .bg-yellow-brand { background-color: #F59E0B; }

    /* Actions */
    .action-icon {
        color: #4B5563;
        font-size: 1.1rem;
        cursor: pointer;
        transition: color 0.2s;
    }
    .action-icon:hover { color: #111827; }
</style>

<div class="flex-grow-1 p-5">
    
    <?php include '../includes/topbar.php'; ?>
    <div class="mb-4">
        <h3 class="fw-bold mb-1" style="color: #111827;">My Students</h3>
        <p class="text-muted mb-0">View and manage your assigned students</p>
    </div>

    <div class="panel-container">
        
        <div class="search-bar-container">
            <div class="search-input-wrapper">
                <i class="bi bi-search"></i>
                <input type="text" class="search-input" placeholder="Search by name or student ID...">
            </div>
            <select class="filter-select">
                <option>All Sections</option>
                <?php foreach($sections as $sec): ?>
                    <option value="<?= $sec['id'] ?>"><?= htmlspecialchars($sec['section_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="table-responsive">
            <table class="table table-custom table-borderless mb-0 w-100">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Section</th>
                        <th>Attendance</th>
                        <th>Grade</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td class="text-muted"><?= htmlspecialchars($student['student_id']) ?></td>
                            <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                            <td class="text-muted"><?= htmlspecialchars($student['course'] ?? '') ?></td>
                            <td>
                                <span class="badge-section"><?= htmlspecialchars($student['section'] ?? '') ?></span>
                            </td>
                            <td>
                                <div class="progress-container">
                                    <div class="progress-track">
                                        <?php 
                                            // Make it yellow if under 90%, otherwise green
                                            $fillColor = ($student['attendance'] < 90) ? 'bg-yellow-brand' : 'bg-teal-brand';
                                        ?>
                                        <div class="progress-fill <?= $fillColor ?>" style="width: <?= $student['attendance'] ?>%;"></div>
                                    </div>
                                    <span class="small fw-medium"><?= $student['attendance'] ?>%</span>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($student['grade']) ?></td>
                            <td>
                                <span class="badge-status"><?= htmlspecialchars($student['status']) ?></span>
                            </td>
                            <td>
                                <a href="#" class="action-icon"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

</div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>