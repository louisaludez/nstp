<?php
// instructor/view_class.php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Instructor') {
    header("Location: ../login");
    exit;
}

$section_id = $_GET['section_id'] ?? null;
if (!$section_id) {
    header("Location: my_section.php");
    exit;
}

// Fetch section info
$stmtSec = $pdo->prepare("SELECT * FROM sections WHERE id = ? AND instructor_id = ?");
$stmtSec->execute([$section_id, $_SESSION['user_id']]);
$section = $stmtSec->fetch();

if (!$section) {
    echo "Section not found or access denied.";
    exit;
}

// Fetch students
$stmtStudents = $pdo->prepare("
    SELECT s.student_id, s.first_name, s.last_name, s.course 
    FROM students s 
    JOIN enrollments e ON s.student_id = e.student_id 
    WHERE e.section_id = ? 
    ORDER BY s.last_name ASC
");
$stmtStudents->execute([$section_id]);
$students = $stmtStudents->fetchAll();

$extra_css = ['../assets/css/style.css'];
include '../includes/header.php';
include '../includes/instructor_sidebar.php';
?>

<div class="flex-grow-1 p-4 p-lg-5 w-100" style="background-color: #F9FAFB; min-height: 100vh;">

    <?php include '../includes/topbar.php'; ?>
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
        <div>
            <a href="my_section.php" class="text-decoration-none text-muted small d-inline-flex align-items-center mb-2">
                <i class="bi bi-arrow-left me-1"></i> Back to My Classes
            </a>
            <h5 class="fw-bold mb-1" style="color: #111827; font-size: 1.15rem;"><?= htmlspecialchars($section['component'] . ' 1 · ' . $section['section_name']) ?></h5>
            <div class="text-muted" style="font-size: 0.85rem;">Class List</div>
        </div>
        <div class="d-flex gap-2">
            <a href="export_class.php?section_id=<?= $section['id'] ?>" class="btn btn-sm d-inline-flex align-items-center gap-2" style="background: #10B981; color: white; border-radius: 6px; font-weight: 500; font-size: 0.85rem; padding: 8px 14px; text-decoration: none; transition: background 0.2s;" onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10B981'">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export to Excel
            </a>
        </div>
    </div>

    <div class="dash-panel p-0" style="background: white; border: 1px solid #E5E7EB; border-radius: 12px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table mb-0 table-hover">
                <thead style="background-color: #F3F4F6;">
                    <tr>
                        <th style="font-size: 0.8rem; font-weight: 600; color: #4B5563; padding: 12px 24px; border-bottom: 1px solid #E5E7EB;">#</th>
                        <th style="font-size: 0.8rem; font-weight: 600; color: #4B5563; padding: 12px 24px; border-bottom: 1px solid #E5E7EB;">Student No.</th>
                        <th style="font-size: 0.8rem; font-weight: 600; color: #4B5563; padding: 12px 24px; border-bottom: 1px solid #E5E7EB;">Name</th>
                        <th style="font-size: 0.8rem; font-weight: 600; color: #4B5563; padding: 12px 24px; border-bottom: 1px solid #E5E7EB;">Program</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($students) > 0): ?>
                        <?php $idx = 1; foreach ($students as $student): ?>
                            <tr>
                                <td style="padding: 12px 24px; border-bottom: 1px solid #E5E7EB; color: #6B7280; font-size: 0.85rem;"><?= $idx++ ?></td>
                                <td style="padding: 12px 24px; border-bottom: 1px solid #E5E7EB; font-weight: 500; color: #111827; font-size: 0.85rem;"><?= htmlspecialchars($student['student_id']) ?></td>
                                <td style="padding: 12px 24px; border-bottom: 1px solid #E5E7EB; color: #374151; font-size: 0.85rem;">
                                    <?= htmlspecialchars($student['last_name'] . ', ' . $student['first_name']) ?>
                                </td>
                                <td style="padding: 12px 24px; border-bottom: 1px solid #E5E7EB; color: #6B7280; font-size: 0.85rem;"><?= htmlspecialchars($student['course']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted" style="font-size: 0.85rem;">No students enrolled in this section.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
