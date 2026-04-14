<?php
session_start();
require '../config/db.php';

// Security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Instructor') {
    header("Location: ../login");
    exit;
}

require_once __DIR__ . '/controllers/StudentsController.php';

$title = "My Students";
$extra_css = ['../assets/css/pages/instructor/students.css'];
$extra_js = ['../assets/js/pages/instructor/students.js'];

ob_start();
?>

<div class="mb-4">
    <h3 class="fw-bold mb-1" style="color: #111827;">My Students</h3>
    <p class="text-muted mb-0">View and manage your assigned students</p>
</div>

<div class="panel-container">
    
    <div class="search-bar-container">
        <div class="search-input-wrapper">
            <i class="bi bi-search"></i>
            <input type="text" id="searchInput" class="search-input" placeholder="Search by name or student ID...">
        </div>
        <select id="sectionFilter" class="filter-select">
            <option value="All">All Sections</option>
            <?php foreach($sections as $sec): ?>
                <option value="<?= htmlspecialchars($sec['id']) ?>"><?= htmlspecialchars($sec['section_name']) ?></option>
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
            <tbody id="studentsTableBody">
                <?php if (count($students) === 0): ?>
                    <tr id="emptyStateRow">
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No students found.
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($students as $student): ?>
                    <tr class="student-row" data-section-id="<?= htmlspecialchars($student['section_id'] ?? '') ?>">
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
                            <a href="#" class="action-icon view-student-btn" 
                               data-id="<?= htmlspecialchars($student['student_id']) ?>"
                               data-name="<?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>"
                               data-course="<?= htmlspecialchars($student['course'] ?? '') ?>"
                               data-section="<?= htmlspecialchars($student['section'] ?? '') ?>"
                               data-attendance="<?= htmlspecialchars($student['attendance']) ?>"
                               data-grade="<?= htmlspecialchars($student['grade']) ?>"
                               data-status="<?= htmlspecialchars($student['status']) ?>"
                               data-bs-toggle="modal" data-bs-target="#viewStudentModal">
                               <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<!-- View Student Modal -->
<div class="modal fade" id="viewStudentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold" style="color: #111827;">Student Overview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-3 pb-4 px-4">
        <div class="d-flex align-items-center mb-4">
            <div class="rounded-circle bg-light d-flex justify-content-center align-items-center me-3" style="width: 60px; height: 60px;">
                <i class="bi bi-person fs-1 text-muted"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-0" id="modalStudentName">--</h4>
                <div class="text-muted small" id="modalStudentId">--</div>
            </div>
        </div>
        
        <div class="row g-3 mb-4">
            <div class="col-6">
                <div class="p-3 bg-light rounded-3">
                    <div class="text-muted small mb-1">Course</div>
                    <div class="fw-bold text-dark" id="modalStudentCourse" style="font-size: 0.9rem;">--</div>
                </div>
            </div>
            <div class="col-6">
                <div class="p-3 bg-light rounded-3">
                    <div class="text-muted small mb-1">Section</div>
                    <div class="fw-bold text-dark" id="modalStudentSection" style="font-size: 0.9rem;">--</div>
                </div>
            </div>
            <div class="col-6">
                <div class="p-3 bg-light rounded-3">
                    <div class="text-muted small mb-1">Attendance</div>
                    <div class="fw-bold text-dark" id="modalStudentAttendance" style="font-size: 0.9rem;">--</div>
                </div>
            </div>
            <div class="col-6">
                <div class="p-3 bg-light rounded-3">
                    <div class="text-muted small mb-1">Grade Final</div>
                    <div class="fw-bold text-dark" id="modalStudentGrade" style="font-size: 0.9rem;">--</div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center p-3 rounded-3" style="background-color: #F9FAFB; border: 1px dashed #D1D5DB;">
            <span class="text-muted fw-medium small">Current Status</span>
            <span class="badge-status" id="modalStudentStatus">--</span>
        </div>
      </div>
      <div class="modal-footer border-top-0 pt-0">
        <button type="button" class="btn btn-light w-100 fw-medium" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
require '../includes/layout.php';
