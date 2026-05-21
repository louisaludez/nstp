<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

require 'controllers/ManageInstructorsController.php';

$extra_css = ['../assets/css/style.css'];
include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

<div class="flex-grow-1 p-4 p-lg-5 w-100">
    
    <?php include '../includes/topbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Instructors & OIC List</h4>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Faculty directory and section load</p>
        </div>
        <button type="button" class="btn btn-sm d-inline-flex align-items-center gap-2" style="background: #6366F1; color: white; border-radius: 8px; padding: 8px 16px; font-weight: 500; border: none;" data-bs-toggle="modal" data-bs-target="#addInstructorModal">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            Add Personnel
        </button>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $msgType ?> alert-dismissible fade show rounded-3">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Instructor Grid -->
    <div class="row g-4">
        <?php if (count($instructors) > 0): ?>
            <?php foreach ($instructors as $inst): 
                $bg_color = '#4F46E5'; 
                
                $name_parts = explode(' ', trim($inst['full_name']));
                $initials = strtoupper(substr($name_parts[0], 0, 1));
                if (count($name_parts) > 1) {
                    $initials .= strtoupper(substr(end($name_parts), 0, 1));
                }

                $dept = $inst['primary_component'] ?? 'NSTP';
                $main_section = !empty($inst['assigned_sections']) ? $inst['assigned_sections'] : 'Unassigned';
                $status = $inst['status'] ?? 'Active';
            ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid #E2E8F0; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#94A3B8'" onmouseout="this.style.borderColor='#E2E8F0'" onclick="viewInstructorDetails(<?= $inst['id'] ?>, '<?= htmlspecialchars(addslashes($inst['full_name'])) ?>', '<?= addslashes($dept) ?>', '<?= addslashes($main_section) ?>', <?= $inst['student_count'] ?? 0 ?>, '<?= htmlspecialchars(addslashes($inst['email'])) ?>', '<?= $initials ?>', '<?= $bg_color ?>', '<?= $status ?>')">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div style="width: 42px; height: 42px; border-radius: 50%; background: <?= $bg_color ?>; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.9rem; flex-shrink: 0;">
                                <?= $initials ?>
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #1E293B; font-size: 0.95rem;"><?= htmlspecialchars($inst['full_name']) ?></div>
                                <div style="font-size: 0.75rem; color: #64748B; margin-top: 2px;"><?= $dept ?></div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-6">
                                <div style="font-size: 0.7rem; color: #94A3B8; margin-bottom: 4px;">Sections</div>
                                <div style="font-weight: 600; color: #1E293B; font-size: 0.85rem;"><?= $main_section ?></div>
                            </div>
                            <div class="col-6">
                                <div style="font-size: 0.7rem; color: #94A3B8; margin-bottom: 4px;">Students</div>
                                <div style="font-weight: 600; color: #1E293B; font-size: 0.85rem;"><?= $inst['student_count'] ?? 0 ?></div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 mt-auto" style="font-size: 0.75rem; color: #64748B;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            <?= htmlspecialchars($inst['email']) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="text-center py-5 dash-panel">
                    <i class="bi bi-people text-muted fs-1 mb-2"></i>
                    <h6 class="fw-bold text-dark">No Instructors Found</h6>
                    <p class="text-muted small mb-3">Add instructors to start assigning sections.</p>
                    <button type="button" class="btn-figma primary" data-bs-toggle="modal" data-bs-target="#addInstructorModal">
                        Invite Instructor
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- Instructor Details Modal -->
<div class="modal fade" id="instructorDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header border-bottom px-4 pt-4 pb-3">
                <h5 class="modal-title fw-bold" style="color: #111827;">Personnel Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div id="detailInitials" style="width: 56px; height: 56px; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1.2rem; flex-shrink: 0; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        CO
                    </div>
                    <div>
                        <div id="detailName" style="font-weight: 700; color: #111827; font-size: 1.1rem; margin-bottom: 2px;">Name</div>
                        <div id="detailDept" style="font-size: 0.85rem; color: #64748B; margin-bottom: 6px;">Dept</div>
                        <span id="detailStatus" style="font-size: 0.7rem; padding: 2px 8px; border-radius: 20px; background: #ECFDF5; color: #10B981; font-weight: 500;">Active</span>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 mb-4">
                    <div style="width: 40px; height: 40px; border-radius: 8px; background: #F8FAFC; display: flex; align-items: center; justify-content: center; color: #64748B;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div>
                        <div style="font-size: 0.7rem; font-weight: 600; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Assigned Sections</div>
                        <div id="detailSections" style="font-weight: 600; color: #1E293B; font-size: 0.95rem;">CWTS-1A</div>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 mb-4">
                    <div style="width: 40px; height: 40px; border-radius: 8px; background: #F8FAFC; display: flex; align-items: center; justify-content: center; color: #64748B;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
                    </div>
                    <div>
                        <div style="font-size: 0.7rem; font-weight: 600; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Total Handled Students</div>
                        <div id="detailStudents" style="font-weight: 600; color: #1E293B; font-size: 0.95rem;">158 Cadets / Students</div>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 mb-2">
                    <div style="width: 40px; height: 40px; border-radius: 8px; background: #F8FAFC; display: flex; align-items: center; justify-content: center; color: #64748B;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <div>
                        <div style="font-size: 0.7rem; font-weight: 600; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Official Email</div>
                        <div id="detailEmail" style="font-weight: 500; color: #4F46E5; font-size: 0.95rem;">class@aurora.edu</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top bg-light p-3 d-flex justify-content-between align-items-center" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                <form method="POST" action="" class="mb-0">
                    <input type="hidden" name="instructor_id" id="deleteInstId">
                    <button type="submit" name="delete_instructor" class="btn btn-sm d-flex align-items-center gap-2" style="background: #FFF1F2; color: #E11D48; border: 1px solid #FFE4E6; border-radius: 6px; padding: 6px 12px; font-weight: 500;" onclick="return confirm('Are you sure you want to delete this instructor? This action cannot be undone.');">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><path d="M18 6L6 18M6 6l12 12"/></svg>
                        Delete
                    </button>
                </form>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm" style="background: white; color: #1E293B; border: 1px solid #E2E8F0; border-radius: 6px; padding: 6px 16px; font-weight: 500;" onclick="openEditModal()">Edit</button>
                    <button type="button" class="btn btn-sm" style="background: #0F172A; color: white; border: none; border-radius: 6px; padding: 6px 16px; font-weight: 500;" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Personnel Modal -->
<div class="modal fade" id="editPersonnelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header border-bottom px-4 pt-4 pb-3">
                <h5 class="modal-title fw-bold" style="color: #111827;">Edit Personnel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="instructor_id" id="editInstId">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold small" style="font-size: 0.7rem; letter-spacing: 0.05em;">FULL NAME</label>
                        <input type="text" name="full_name" id="editFullName" class="form-control" style="border-radius:8px; border: 1px solid #E2E8F0; color: #1E293B; font-weight: 500;" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold small" style="font-size: 0.7rem; letter-spacing: 0.05em;">COMPONENT</label>
                        <select name="component" id="editDept" class="form-select" style="border-radius:8px; border: 1px solid #E2E8F0; color: #1E293B; font-weight: 500;" required>
                            <option value="CWTS">CWTS</option>
                            <option value="LTS">LTS</option>
                            <option value="ROTC">ROTC</option>
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label text-muted fw-bold small" style="font-size: 0.7rem; letter-spacing: 0.05em;">SECTIONS LOAD</label>
                            <input type="text" id="editSections" class="form-control bg-light" style="border-radius:8px; border: 1px solid #E2E8F0; color: #1E293B; font-weight: 500;" readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted fw-bold small" style="font-size: 0.7rem; letter-spacing: 0.05em;">STUDENTS COUNT</label>
                            <input type="text" id="editStudents" class="form-control bg-light" style="border-radius:8px; border: 1px solid #E2E8F0; color: #1E293B; font-weight: 500;" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold small" style="font-size: 0.7rem; letter-spacing: 0.05em;">EMAIL ADDRESS</label>
                        <input type="email" name="email" id="editEmail" class="form-control" style="border-radius:8px; border: 1px solid #E2E8F0; color: #1E293B; font-weight: 500;" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label text-muted fw-bold small" style="font-size: 0.7rem; letter-spacing: 0.05em;">STATUS</label>
                        <select name="status" id="editStatus" class="form-select" style="border-radius:8px; border: 1px solid #E2E8F0; color: #1E293B; font-weight: 500;">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light p-3 d-flex justify-content-end gap-2" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                    <button type="button" class="btn btn-sm" style="background: white; color: #1E293B; border: 1px solid #E2E8F0; border-radius: 6px; padding: 8px 16px; font-weight: 500;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="edit_instructor" class="btn btn-sm" style="background: #6366F1; color: white; border: none; border-radius: 6px; padding: 8px 16px; font-weight: 500;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Instructor Modal -->
<div class="modal fade" id="addInstructorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header border-bottom px-4 pt-4 pb-3">
                <div>
                    <h5 class="modal-title fw-bold" style="color: #1E293B; font-size: 1.1rem;">Add Instructor</h5>
                    <p class="text-muted small mb-0 mt-1" style="color: #64748B;">Add a faculty member to the NSTP program</p>
                </div>
                <button type="button" class="btn-close align-self-start mt-1" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <!-- Default password fields to maintain backend compatibility if needed -->
                <input type="hidden" name="password" value="tempPassword123!">
                <input type="hidden" name="confirm_password" value="tempPassword123!">
                
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label" style="color: #64748B; font-size: 0.85rem; font-weight: 400;">Full Name</label>
                        <input type="text" name="full_name" class="form-control" placeholder="e.g. Prof. Juan Santos" style="border-radius:8px; border: 1px solid #E2E8F0; color: #1E293B; font-weight: 400; padding: 10px 14px;" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label" style="color: #64748B; font-size: 0.85rem; font-weight: 400;">Component</label>
                            <select name="component" class="form-select" style="border-radius:8px; border: 1px solid #E2E8F0; color: #1E293B; font-weight: 400; padding: 10px 14px;" required>
                                <option value="CWTS">CWTS</option>
                                <option value="LTS">LTS</option>
                                <option value="ROTC">ROTC</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="color: #64748B; font-size: 0.85rem; font-weight: 400;">University Email</label>
                            <input type="email" name="email" class="form-control" placeholder="e.g. j.santos@aurora.edu" style="border-radius:8px; border: 1px solid #E2E8F0; color: #1E293B; font-weight: 400; padding: 10px 14px;" required>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" style="color: #64748B; font-size: 0.85rem; font-weight: 400;">Section Name</label>
                        <input type="text" name="section_name" class="form-control" placeholder="e.g. BSCS-2A" style="border-radius:8px; border: 1px solid #E2E8F0; color: #1E293B; font-weight: 400; padding: 10px 14px;" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pe-4 pb-4 mt-2">
                    <button type="button" class="btn btn-sm" style="background: white; color: #1E293B; border: 1px solid #E2E8F0; border-radius: 6px; padding: 8px 16px; font-weight: 500;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_new_instructor" class="btn btn-sm d-inline-flex align-items-center gap-2" style="background: #6366F1; color: white; border: none; border-radius: 6px; padding: 8px 16px; font-weight: 500;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                        Add Personnel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
let currentInstructor = {};

function viewInstructorDetails(id, name, dept, section, students, email, initials, bgColor, status) {
    // Save for edit modal
    currentInstructor = { id, name, dept, section, students, email, status };

    // Populate Details Modal
    document.getElementById('detailInitials').innerText = initials;
    document.getElementById('detailInitials').style.backgroundColor = bgColor;
    document.getElementById('detailName').innerText = name;
    document.getElementById('detailDept').innerText = dept;
    document.getElementById('detailSections').innerText = section || 'None';
    document.getElementById('detailStudents').innerText = `${students} Cadets / Students`;
    document.getElementById('detailEmail').innerText = email;
    
    let statusEl = document.getElementById('detailStatus');
    statusEl.innerText = status;
    if (status === 'Active') {
        statusEl.style.background = '#ECFDF5';
        statusEl.style.color = '#10B981';
    } else {
        statusEl.style.background = '#FEF2F2';
        statusEl.style.color = '#EF4444';
    }

    // Set delete form ID
    document.getElementById('deleteInstId').value = id;

    // Show Details Modal
    var detailsModal = new bootstrap.Modal(document.getElementById('instructorDetailsModal'));
    detailsModal.show();
}

function openEditModal() {
    // Hide details modal
    var detailsModalEl = document.getElementById('instructorDetailsModal');
    var detailsModal = bootstrap.Modal.getInstance(detailsModalEl);
    if (detailsModal) detailsModal.hide();

    // Populate Edit Modal
    document.getElementById('editInstId').value = currentInstructor.id;
    document.getElementById('editFullName').value = currentInstructor.name;
    document.getElementById('editDept').value = currentInstructor.dept;
    document.getElementById('editSections').value = currentInstructor.section || 'None';
    document.getElementById('editStudents').value = currentInstructor.students;
    document.getElementById('editEmail').value = currentInstructor.email;
    document.getElementById('editStatus').value = currentInstructor.status || 'Active';

    // Show Edit Modal
    setTimeout(() => {
        var editModal = new bootstrap.Modal(document.getElementById('editPersonnelModal'));
        editModal.show();
    }, 400); // Wait for the first modal to hide completely to prevent backdrop issues
}
</script>
</body>
</html>
