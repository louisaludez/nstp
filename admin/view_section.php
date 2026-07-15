<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

require 'controllers/ViewSectionController.php';

$extra_css = ['../assets/css/style.css'];
include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

<div class="flex-grow-1 p-4 p-lg-5 w-100">
    
    <?php include '../includes/topbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Students in Section: <?= htmlspecialchars($section['section_name']) ?></h4>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Manage and view enrolled students for this class section</p>
        </div>
        <div>
            <a href="manage_sections.php" class="btn btn-sm d-inline-flex align-items-center gap-2" style="background: white; border: 1px solid #E2E8F0; color: #1E293B; font-weight: 500; border-radius: 8px; padding: 8px 16px; text-decoration: none;">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                Back to Sections
            </a>
        </div>
    </div>

    <div style="background: white; border: 1px solid #F1F5F9; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); overflow: hidden;">
        <div class="p-4 border-bottom" style="border-color: #F8FAFC !important;">
            <h6 class="mb-0 fw-semibold" style="color: #1E293B;">Enrolled Students (<?= count($enrolled_students) ?>)</h6>
        </div>

        <div style="overflow-x: auto;">
            <table class="w-100" style="font-size: 0.875rem;">
                <thead style="border-bottom: 1px solid #F1F5F9; background: #FAFAF9;">
                    <tr style="text-align: left; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748B;">
                        <th style="padding: 16px 24px; font-weight: 600;">Student ID</th>
                        <th style="padding: 16px 24px; font-weight: 600;">Name</th>
                        <th style="padding: 16px 24px; font-weight: 600;">Gender</th>
                        <th style="padding: 16px 24px; font-weight: 600;">DOB / POB</th>
                        <th style="padding: 16px 24px; font-weight: 600;">Contact / Email</th>
                        <th style="padding: 16px 24px; font-weight: 600;">Residential Address</th>
                        <th style="padding: 16px 24px; font-weight: 600;">Course</th>
                        <th style="padding: 16px 24px; font-weight: 600;">NSTP Program</th>
                        <th style="padding: 16px 24px; font-weight: 600;">Final Grade</th>
                        <th style="padding: 16px 24px; font-weight: 600; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($enrolled_students) > 0): ?>
                        <?php foreach ($enrolled_students as $student): ?>
                            <?php
                                $prog = $student['component'] ?? $section['component'];
                                $progStyle = ($prog === 'CWTS') ? 'background: #F3E8FF; color: #9333EA;' : (($prog === 'LTS') ? 'background: #DCFCE7; color: #16A34A;' : 'background: #FFE4E6; color: #E11D48;');
                                
                                $grade = $student['grade_status'];
                                $gradeStyle = 'background: #F1F5F9; color: #475569;'; 
                                if ($grade === 'Passed') $gradeStyle = 'background: #DCFCE7; color: #16A34A;';
                                if ($grade === 'Failed') $gradeStyle = 'background: #FFE4E6; color: #E11D48;';
                                if ($grade === 'Pending') $gradeStyle = 'background: #FEF9C3; color: #CA8A04;';
                            ?>
                            <tr style="border-bottom: 1px solid #F8FAFC; transition: background 0.15s;" onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 16px 24px; color: #0F172A; font-weight: 500;"><?= htmlspecialchars($student['student_id']) ?></td>
                                <td style="padding: 16px 24px; color: #334155;"><?= htmlspecialchars($student['last_name'] . ', ' . $student['first_name']) ?></td>
                                <td style="padding: 16px 24px;">
                                    <?php if (!empty($student['sex'])): ?>
                                        <span style="font-size: 0.75rem; padding: 4px 10px; border-radius: 12px; background: #F1F5F9; color: #475569; font-weight: 500;"><?= htmlspecialchars($student['sex']) ?></span>
                                    <?php else: ?>
                                        <span style="font-size: 0.75rem; padding: 4px 10px; border-radius: 12px; background: #F1F5F9; color: #94A3B8; font-weight: 500;">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 16px 24px; color: #475569; font-size: 0.8rem;">
                                    <div style="font-weight: 500; color: #334155;"><?= !empty($student['date_of_birth']) ? htmlspecialchars($student['date_of_birth']) : 'N/A' ?></div>
                                    <div style="color: #94A3B8;"><?= !empty($student['place_of_birth']) ? htmlspecialchars($student['place_of_birth']) : 'N/A' ?></div>
                                </td>
                                <td style="padding: 16px 24px; color: #475569; font-size: 0.8rem;">
                                    <div style="font-weight: 500; color: #334155;"><?= !empty($student['contact_number']) ? htmlspecialchars($student['contact_number']) : 'N/A' ?></div>
                                    <div style="color: #94A3B8;"><?= !empty($student['email']) ? htmlspecialchars($student['email']) : 'N/A' ?></div>
                                </td>
                                <td style="padding: 16px 24px; color: #94A3B8; font-size: 0.85rem;">
                                    <?= !empty($student['complete_address']) ? htmlspecialchars($student['complete_address']) : 'N/A' ?>
                                </td>
                                <td style="padding: 16px 24px; color: #475569;">
                                    <?= !empty($student['course']) ? htmlspecialchars($student['course']) : 'N/A' ?>
                                </td>
                                <td style="padding: 16px 24px;">
                                    <span style="font-size: 0.75rem; padding: 4px 10px; border-radius: 12px; font-weight: 500; <?= $progStyle ?>"><?= htmlspecialchars($prog) ?></span>
                                </td>
                                <td style="padding: 16px 24px;">
                                    <span style="font-size: 0.75rem; padding: 4px 10px; border-radius: 12px; font-weight: 500; <?= $gradeStyle ?>"><?= htmlspecialchars($grade ?? 'N/A') ?></span>
                                </td>
                                <td style="padding: 16px 24px; text-align: center;">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm p-1 text-muted" title="Edit" onclick="openEditStudentModal(this)"
                                            data-id="<?= htmlspecialchars($student['student_id']) ?>"
                                            data-course="<?= htmlspecialchars($student['course']) ?>"
                                            data-name="<?= htmlspecialchars($student['last_name'] . ', ' . $student['first_name']) ?>"
                                            data-gender="<?= htmlspecialchars($student['sex']) ?>"
                                            data-dob="<?= htmlspecialchars($student['date_of_birth']) ?>"
                                            data-pob="<?= htmlspecialchars($student['place_of_birth'] ?? '') ?>"
                                            data-cell="<?= htmlspecialchars($student['contact_number']) ?>"
                                            data-address="<?= htmlspecialchars($student['complete_address']) ?>"
                                            data-email="<?= htmlspecialchars($student['email']) ?>"
                                            data-status="<?= htmlspecialchars($student['grade_status']) ?>"
                                            data-grade="<?= htmlspecialchars($student['final_grade']) ?>"
                                            style="border: none; background: transparent; cursor: pointer;">
                                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="#94A3B8" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" onmouseover="this.style.stroke='#4F46E5'" onmouseout="this.style.stroke='#94A3B8'"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                        </button>
                                        <button class="btn btn-sm p-1 text-muted" title="Delete" style="border: none; background: transparent; cursor: pointer;">
                                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="#94A3B8" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" onmouseover="this.style.stroke='#E11D48'" onmouseout="this.style.stroke='#94A3B8'"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="py-5 text-center text-muted" style="font-size: 0.875rem;">
                                No students enrolled in this section.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openEditStudentModal(btn) {
    document.getElementById('edit_student_id').value = btn.getAttribute('data-id');
    document.getElementById('edit_course').value = btn.getAttribute('data-course');
    document.getElementById('edit_full_name').value = btn.getAttribute('data-name');
    document.getElementById('edit_gender').value = btn.getAttribute('data-gender');
    document.getElementById('edit_dob').value = btn.getAttribute('data-dob');
    document.getElementById('edit_pob').value = btn.getAttribute('data-pob');
    document.getElementById('edit_cell').value = btn.getAttribute('data-cell');
    document.getElementById('edit_address').value = btn.getAttribute('data-address');
    document.getElementById('edit_email').value = btn.getAttribute('data-email');
    
    // Check if status is simple string or mapping
    let status = btn.getAttribute('data-status');
    document.getElementById('edit_status').value = status;
    document.getElementById('edit_final_grade').value = btn.getAttribute('data-grade');
    
    new bootstrap.Modal(document.getElementById('editStudentModal')).show();
}
</script>

<!-- Edit Student Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 550px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; background: #FAFAFA;">
            <div class="modal-header border-bottom pb-3 pt-4 px-4 bg-white" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <div>
                    <h5 class="modal-title fw-bold" style="color: #1E293B;">Edit Student Record</h5>
                    <div style="font-size: 0.85rem; color: #64748B;">Update credentials and status within section</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 0.75rem;"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body p-4 bg-white">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size: 0.75rem; color: #64748B; letter-spacing: 0.5px;">STUDENT ID <span class="text-danger">*</span></label>
                            <input type="text" name="student_id" id="edit_student_id" class="form-control" style="border-radius:8px; border-color: #E2E8F0; padding: 10px 14px;" required readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size: 0.75rem; color: #64748B; letter-spacing: 0.5px;">COURSE / PROGRAM <span class="text-danger">*</span></label>
                            <input type="text" name="course" id="edit_course" class="form-control" style="border-radius:8px; border-color: #E2E8F0; padding: 10px 14px;" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: 0.75rem; color: #64748B; letter-spacing: 0.5px;">FULL NAME (LAST, FIRST) <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" id="edit_full_name" class="form-control" style="border-radius:8px; border-color: #E2E8F0; padding: 10px 14px;" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size: 0.75rem; color: #64748B; letter-spacing: 0.5px;">GENDER</label>
                            <select name="gender" id="edit_gender" class="form-select" style="border-radius:8px; border-color: #E2E8F0; padding: 10px 14px;">
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size: 0.75rem; color: #64748B; letter-spacing: 0.5px;">DATE OF BIRTH</label>
                            <input type="date" name="date_of_birth" id="edit_dob" class="form-control" style="border-radius:8px; border-color: #E2E8F0; padding: 10px 14px;">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size: 0.75rem; color: #64748B; letter-spacing: 0.5px;">PLACE OF BIRTH</label>
                            <input type="text" name="place_of_birth" id="edit_pob" class="form-control" style="border-radius:8px; border-color: #E2E8F0; padding: 10px 14px;" placeholder="e.g. Tagum City">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size: 0.75rem; color: #64748B; letter-spacing: 0.5px;">CELL NUMBER</label>
                            <input type="text" name="cell_number" id="edit_cell" class="form-control" style="border-radius:8px; border-color: #E2E8F0; padding: 10px 14px;" placeholder="e.g. 09123456789">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: 0.75rem; color: #64748B; letter-spacing: 0.5px;">RESIDENTIAL ADDRESS</label>
                        <textarea name="residential_address" id="edit_address" class="form-control" style="border-radius:8px; border-color: #E2E8F0; padding: 10px 14px;" rows="2" placeholder="e.g. Apokon, Tagum City"></textarea>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="font-size: 0.75rem; color: #64748B; letter-spacing: 0.5px;">EMAIL</label>
                            <input type="email" name="email" id="edit_email" class="form-control" style="border-radius:8px; border-color: #E2E8F0; padding: 10px 14px;" placeholder="e.g. juan.delacruz@d...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="font-size: 0.75rem; color: #64748B; letter-spacing: 0.5px;">STATUS <span class="text-danger">*</span></label>
                            <select name="status" id="edit_status" class="form-select" style="border-radius:8px; border-color: #E2E8F0; padding: 10px 14px;" required>
                                <option value="Pending">Pending</option>
                                <option value="Passed">Passed (Completed)</option>
                                <option value="Failed">Failed</option>
                                <option value="Dropped">Dropped</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="font-size: 0.75rem; color: #64748B; letter-spacing: 0.5px;">FINAL GRADE</label>
                            <input type="text" name="final_grade" id="edit_final_grade" class="form-control" style="border-radius:8px; border-color: #E2E8F0; padding: 10px 14px;" placeholder="e.g. 1.5">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3" style="background: #FAFAFA; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; border-color: #F1F5F9 !important;">
                    <button type="button" class="btn px-4" data-bs-dismiss="modal" style="background: white; border: 1px solid #E2E8F0; color: #1E293B; font-weight: 500; border-radius: 8px;">Cancel</button>
                    <button type="submit" name="edit_student" class="btn px-4 d-inline-flex align-items-center gap-2" style="background: #4F46E5; color: white; font-weight: 500; border-radius: 8px;">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M9 12l2 2 4-4"></path></svg>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
