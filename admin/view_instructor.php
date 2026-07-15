<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

require 'controllers/ViewInstructorController.php';

$extra_css = ['../assets/css/style.css'];
include '../includes/header.php';
include '../includes/admin_sidebar.php';

$dept = $instructor['component'] ?? 'NSTP';
$dept_display = $dept === 'NSTP' ? 'CWTS/LTS' : $dept; // Just for display fallback

$status_bg = ($instructor['status'] === 'Active') ? '#ECFDF5' : '#FEF2F2';
$status_color = ($instructor['status'] === 'Active') ? '#10B981' : '#EF4444';
?>
<div class="flex-grow-1 p-4 p-lg-5 w-100 bg-light" style="min-height: 100vh;">
    <?php include '../includes/topbar.php'; ?>
    
    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #1E293B;">Instructor Profile: <?= htmlspecialchars($instructor['full_name']) ?></h4>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Detailed review of instructor assignments, handled sections, and active student enrollments</p>
        </div>
        <a href="manage_instructors.php" class="btn btn-sm d-inline-flex align-items-center gap-2 shadow-sm" style="background: white; color: #475569; border-radius: 8px; padding: 8px 16px; font-weight: 500; border: 1px solid #E2E8F0; text-decoration: none;">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 14 4 9 9 4"></polyline><path d="M20 20v-7a4 4 0 0 0-4-4H4"></path></svg>
            Back to Instructors
        </a>
    </div>

    <div class="row g-4">
        <!-- Left Column: Personnel Details -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-4" style="color: #475569;">Personnel Details</h6>
                    
                    <div class="text-center mb-4 pb-3 border-bottom border-light">
                        <div class="mx-auto mb-3" style="width: 80px; height: 80px; border-radius: 50%; background: #EEF2FF; color: #4F46E5; display: flex; align-items: center; justify-content: center;">
                            <svg viewBox="0 0 24 24" width="40" height="40" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </div>
                        <h5 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($instructor['full_name']) ?></h5>
                        <div class="d-inline-block px-3 py-1 mt-1" style="background: #EEF2FF; color: #4F46E5; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">
                            <?= htmlspecialchars($dept_display) ?> Instructor
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em; text-transform: uppercase;">Email Address</span>
                        <span class="fw-medium text-dark" style="font-size: 0.85rem;"><?= htmlspecialchars($instructor['email']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em; text-transform: uppercase;">Department</span>
                        <span class="fw-bold" style="font-size: 0.85rem; color: #7C3AED; background: #F3E8FF; padding: 2px 8px; border-radius: 12px;"><?= htmlspecialchars($dept) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em; text-transform: uppercase;">Phone Contact</span>
                        <span class="fw-bold text-dark" style="font-size: 0.85rem;"><?= !empty($instructor['contact_number']) ? htmlspecialchars($instructor['contact_number']) : 'N/A' ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em; text-transform: uppercase;">Status</span>
                        <span class="fw-bold" style="font-size: 0.75rem; padding: 2px 8px; border-radius: 12px; background: <?= $status_bg ?>; color: <?= $status_color ?>;">
                            <?= htmlspecialchars($instructor['status']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Sections & Students -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-body p-4 pb-0">
                    <h6 class="fw-bold mb-4" style="color: #475569;">Handled Class Sections</h6>
                </div>
                <div class="table-responsive px-4 pb-4">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr style="text-align: left; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748B;">
                                <th style="padding: 12px 16px; font-weight: 600; border-bottom: 1px solid #F1F5F9; border-top: none;">SECTION NAME</th>
                                <th style="padding: 12px 16px; font-weight: 600; border-bottom: 1px solid #F1F5F9; border-top: none;">COMPONENT</th>
                                <th style="padding: 12px 16px; font-weight: 600; border-bottom: 1px solid #F1F5F9; border-top: none;">SCHOOL YEAR</th>
                                <th style="padding: 12px 16px; font-weight: 600; border-bottom: 1px solid #F1F5F9; border-top: none;">ROOM</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($sections) > 0): ?>
                                <?php foreach ($sections as $sec): ?>
                                    <tr style="border-bottom: 1px solid #F8FAFC;">
                                        <td style="padding: 16px; color: #1E293B; font-weight: 500; font-size: 0.9rem; border: none;"><?= htmlspecialchars($sec['section_name']) ?></td>
                                        <td style="padding: 16px; border: none;">
                                            <span style="font-size: 0.75rem; padding: 4px 10px; border-radius: 12px; font-weight: 500; <?= ($sec['component']==='CWTS')?'background:#F3E8FF;color:#9333EA;':(($sec['component']==='LTS')?'background:#DCFCE7;color:#16A34A;':'background:#FFE4E6;color:#E11D48;') ?>"><?= htmlspecialchars($sec['component']) ?></span>
                                        </td>
                                        <td style="padding: 16px; color: #64748B; font-size: 0.85rem; border: none;"><?= htmlspecialchars($sec['school_year']) ?></td>
                                        <td style="padding: 16px; color: #64748B; font-size: 0.85rem; border: none;"><?= !empty($sec['room']) ? htmlspecialchars($sec['room']) : 'TBA' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted" style="font-size: 0.9rem; border: none;">No sections assigned to this instructor.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-4 pb-0">
                    <h6 class="fw-bold mb-4" style="color: #475569;">Handled Students (<?= count($students) ?>)</h6>
                </div>
                <div class="table-responsive px-4 pb-4">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr style="text-align: left; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748B;">
                                <th style="padding: 12px 16px; font-weight: 600; border-bottom: 1px solid #F1F5F9; border-top: none;">STUDENT ID</th>
                                <th style="padding: 12px 16px; font-weight: 600; border-bottom: 1px solid #F1F5F9; border-top: none;">NAME</th>
                                <th style="padding: 12px 16px; font-weight: 600; border-bottom: 1px solid #F1F5F9; border-top: none;">COURSE</th>
                                <th style="padding: 12px 16px; font-weight: 600; border-bottom: 1px solid #F1F5F9; border-top: none;">SECTION</th>
                                <th style="padding: 12px 16px; font-weight: 600; border-bottom: 1px solid #F1F5F9; border-top: none;">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($students) > 0): ?>
                                <?php foreach ($students as $stud): 
                                    $full_name = trim($stud['last_name'] . ', ' . $stud['first_name']);
                                    $gs = $stud['grade_status'];
                                    $gcolor = match($gs) {
                                        'Passed' => '#16A34A',
                                        'Failed', 'Dropped' => '#E11D48',
                                        default => '#F59E0B'
                                    };
                                ?>
                                    <tr style="border-bottom: 1px solid #F8FAFC;">
                                        <td style="padding: 16px; color: #64748B; font-size: 0.85rem; border: none;"><?= htmlspecialchars($stud['student_id']) ?></td>
                                        <td style="padding: 16px; color: #1E293B; font-weight: 500; font-size: 0.9rem; border: none;"><?= htmlspecialchars($full_name) ?></td>
                                        <td style="padding: 16px; color: #64748B; font-size: 0.85rem; border: none;"><?= htmlspecialchars($stud['course']) ?></td>
                                        <td style="padding: 16px; color: #475569; font-weight: 500; font-size: 0.85rem; border: none;"><?= htmlspecialchars($stud['section_name']) ?></td>
                                        <td style="padding: 16px; font-weight: 600; font-size: 0.85rem; color: <?= $gcolor ?>; border: none;"><?= htmlspecialchars($gs) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted" style="font-size: 0.9rem; border: none;">No students currently handled.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
