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
            <h4 class="fw-bold mb-1" style="color: #111827;">Instructor Management</h4>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Manage faculty profiles, department assignments, and contact details</p>
        </div>
        <button type="button" class="btn btn-sm d-inline-flex align-items-center gap-2" style="background: #6366F1; color: white; border-radius: 8px; padding: 8px 16px; font-weight: 500; border: none;" data-bs-toggle="modal" data-bs-target="#addInstructorModal">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="19" y1="8" x2="19" y2="14"></line><line x1="22" y1="11" x2="16" y2="11"></line></svg>
            Add Instructor
        </button>
    </div>



    <!-- Controls -->
    <div class="mb-4">
        <div class="position-relative" style="width: 400px; max-width: 100%;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="position-absolute text-muted" style="width: 16px; height: 16px; left: 14px; top: 50%; transform: translateY(-50%);"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="instructorSearch" class="form-control" placeholder="Search instructors..." style="padding-left: 40px; border-radius: 8px; border: 1px solid #E2E8F0; box-shadow: 0 1px 2px rgba(0,0,0,0.05); font-size: 0.9rem;">
        </div>
    </div>

    <!-- Instructor Table -->
    <div style="background: white; border-radius: 12px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div class="p-4 border-bottom" style="border-color: #F8FAFC !important;">
            <h6 class="mb-0 fw-semibold" style="color: #1E293B;">Registered Instructors</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle" style="color: #1E293B;">
                <thead style="background: #FAFAF9; border-bottom: 1px solid #F1F5F9;">
                    <tr style="text-align: left; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748B;">
                        <th style="padding: 16px 24px; font-weight: 600; border: none;">INSTRUCTOR NAME</th>
                        <th style="padding: 16px 24px; font-weight: 600; border: none;">DEPARTMENT</th>
                        <th style="padding: 16px 24px; font-weight: 600; border: none;">EMAIL</th>
                        <th style="padding: 16px 24px; font-weight: 600; border: none;">NO. OF SECTIONS</th>
                        <th style="padding: 16px 24px; font-weight: 600; border: none;">STATUS</th>
                        <th style="padding: 16px 24px; font-weight: 600; border: none; text-align: center;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($instructors) > 0): ?>
                    <?php foreach ($instructors as $inst): 
                        $dept = $inst['primary_component'] ?? 'NSTP';
                        $main_section = !empty($inst['assigned_sections']) ? $inst['assigned_sections'] : '-';
                        $status = $inst['status'] ?? 'Active';
                        
                        $status_bg = ($status === 'Active') ? '#DCFCE7' : '#F1F5F9';
                        $status_text = ($status === 'Active') ? '#16A34A' : '#64748B';
                    ?>
                        <tr style="border-bottom: 1px solid #F8FAFC; transition: background 0.15s; cursor: pointer;" onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'" onclick="window.location.href='view_instructor.php?id=<?= $inst['id'] ?>'">
                            <td style="padding: 16px 24px; color: #1E293B; font-weight: 500;"><?= htmlspecialchars($inst['full_name']) ?></td>
                            <td style="padding: 16px 24px;">
                                <span style="font-size: 0.75rem; padding: 4px 10px; border-radius: 12px; font-weight: 500; <?= ($dept==='CWTS')?'background:#F3E8FF;color:#9333EA;':(($dept==='LTS')?'background:#DCFCE7;color:#16A34A;':'background:#FFE4E6;color:#E11D48;') ?>"><?= htmlspecialchars($dept) ?></span>
                            </td>
                            <td style="padding: 16px 24px; color: #64748B;"><?= htmlspecialchars($inst['email']) ?></td>
                            <td style="padding: 16px 24px; color: #475569; font-weight: 500;"><?= substr_count($main_section, ',') + (!empty($main_section) && $main_section !== '-' ? 1 : 0) ?></td>
                            <td style="padding: 16px 24px;">
                                <span style="font-size: 0.75rem; padding: 4px 10px; border-radius: 12px; font-weight: 500; background: <?= $status_bg ?>; color: <?= $status_text ?>;">
                                    <?= htmlspecialchars($status) ?>
                                </span>
                            </td>
                            <td style="padding: 16px 24px; text-align: center;">
                                <button class="btn btn-sm p-1 text-muted" title="Edit" onclick="event.stopPropagation(); openEditInstructorModal(this)"
                                    data-id="<?= $inst['id'] ?>"
                                    data-name="<?= htmlspecialchars($inst['full_name']) ?>"
                                    data-dept="<?= htmlspecialchars($dept) ?>"
                                    data-status="<?= htmlspecialchars($status) ?>"
                                    style="border: none; background: transparent; cursor: pointer;">
                                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="#94A3B8" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" onmouseover="this.style.stroke='#4F46E5'" onmouseout="this.style.stroke='#94A3B8'"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div style="width: 48px; height: 48px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; color: #94A3B8;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">No Instructors Found</h6>
                            <p class="text-muted small mb-3">Add instructors to start assigning sections.</p>
                            <button type="button" class="btn btn-sm" style="background: #6366F1; color: white; border-radius: 8px; padding: 8px 16px; font-weight: 500;" data-bs-toggle="modal" data-bs-target="#addInstructorModal">
                                Add Personnel
                            </button>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </div>
    </div>

</div>

<!-- Edit Instructor Modal -->
<div class="modal fade" id="editInstructorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; background: #FAFAFA;">
            <div class="modal-header border-bottom pb-3 pt-4 px-4 bg-white" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <div>
                    <h5 class="modal-title fw-bold" style="color: #1E293B;">Edit Instructor</h5>
                    <div style="font-size: 0.85rem; color: #64748B;">Modify instructor configuration</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 0.75rem;"></button>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="instructor_id" id="editInstId">
                <div class="modal-body p-4 bg-white">
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: 0.75rem; color: #64748B; letter-spacing: 0.5px; text-transform: uppercase;">INSTRUCTOR NAME</label>
                        <input type="text" name="full_name" id="editFullName" class="form-control" style="border-radius:8px; border-color: #E2E8F0; padding: 10px 14px;" required>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size: 0.75rem; color: #64748B; letter-spacing: 0.5px; text-transform: uppercase;">DEPARTMENT / SCOPE <span class="text-danger">*</span></label>
                            <select name="component" id="editDept" class="form-select" style="border-radius:8px; border-color: #E2E8F0; padding: 10px 14px;" required>
                                <option value="CWTS">CWTS — Civic Welfare</option>
                                <option value="LTS">LTS — Literacy Training</option>
                                <option value="ROTC">ROTC — Reserve Officers</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size: 0.75rem; color: #64748B; letter-spacing: 0.5px; text-transform: uppercase;">STATUS <span class="text-danger">*</span></label>
                            <select name="status" id="editStatus" class="form-select" style="border-radius:8px; border-color: #E2E8F0; padding: 10px 14px;" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold" style="font-size: 0.75rem; color: #64748B; letter-spacing: 0.5px; text-transform: uppercase;">ASSIGN NEW SECTION</label>
                        <select name="assign_section_id" class="form-select" style="border-radius:8px; border-color: #E2E8F0; padding: 10px 14px;">
                            <option value="">— Select a section (optional) —</option>
                            <?php if (!empty($unassigned_sections)): ?>
                                <?php foreach($unassigned_sections as $sec): ?>
                                    <option value="<?= $sec['id'] ?>"><?= htmlspecialchars($sec['section_name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top p-3" style="background: #FAFAFA; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; border-color: #F1F5F9 !important;">
                    <button type="button" class="btn px-4" data-bs-dismiss="modal" style="background: white; border: 1px solid #E2E8F0; color: #1E293B; font-weight: 500; border-radius: 8px;">Cancel</button>
                    <button type="submit" name="edit_instructor" class="btn px-4 d-inline-flex align-items-center gap-2" style="background: #4F46E5; color: white; font-weight: 500; border-radius: 8px;">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Instructor Modal -->
<div class="modal fade" id="addInstructorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 550px;">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 px-4 pt-4 pb-2">
                <div>
                    <h5 class="modal-title fw-bold" style="color: #1E293B; font-size: 1.25rem;">Add Instructor</h5>
                    <p class="text-muted small mb-0 mt-1" style="color: #64748B;">Create a new faculty account</p>
                </div>
                <button type="button" class="btn-close align-self-start mt-1" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body px-4 pb-4 pt-3">
                    <div class="mb-4">
                        <label class="form-label fw-bold" style="color: #64748B; font-size: 0.75rem; letter-spacing: 0.05em; text-transform: uppercase;">SELECT REGISTERED FACULTY <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select" style="border-radius:8px; border: 1px solid #E2E8F0; color: #1E293B; padding: 10px 14px;" required>
                            <option value="">— Choose a registered instructor account —</option>
                            <?php
                            try {
                                $potentials_stmt = $pdo->query("SELECT id, full_name FROM users WHERE role != 'Admin'");
                                $potentials = $potentials_stmt->fetchAll();
                                foreach($potentials as $p) {
                                    echo '<option value="' . $p['id'] . '">' . htmlspecialchars($p['full_name']) . '</option>';
                                }
                            } catch(Exception $e) {}
                            ?>
                        </select>
                        <div class="form-text mt-2" style="font-size: 0.75rem; color: #94A3B8;">Only user accounts created by the system administrator can be configured here.</div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="color: #64748B; font-size: 0.75rem; letter-spacing: 0.05em; text-transform: uppercase;">DEPARTMENT / SCOPE <span class="text-danger">*</span></label>
                            <select name="component" class="form-select" style="border-radius:8px; border: 1px solid #E2E8F0; color: #1E293B; padding: 10px 14px;" required>
                                <option value="CWTS">CWTS — Civic Welfare</option>
                                <option value="LTS">LTS — Literacy Training</option>
                                <option value="ROTC">ROTC — Military Training</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="color: #64748B; font-size: 0.75rem; letter-spacing: 0.05em; text-transform: uppercase;">ASSIGN SECTION</label>
                            <select name="section_id" class="form-select" style="border-radius:8px; border: 1px solid #E2E8F0; color: #1E293B; padding: 10px 14px;">
                                <option value="">— Select a section (optional) —</option>
                                <?php
                                if (isset($unassigned_sections)) {
                                    foreach($unassigned_sections as $sec) {
                                        echo '<option value="' . $sec['id'] . '">' . htmlspecialchars($sec['section_name']) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pe-4 pb-4 mt-2" style="background: white; border-bottom-left-radius: 16px; border-bottom-right-radius: 16px; justify-content: flex-end; gap: 8px;">
                    <button type="button" class="btn btn-sm" style="background: white; color: #1E293B; border: 1px solid #E2E8F0; border-radius: 8px; padding: 10px 20px; font-weight: 500;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="configure_instructor" class="btn btn-sm d-inline-flex align-items-center gap-2" style="background: #4F46E5; color: white; border: none; border-radius: 8px; padding: 10px 20px; font-weight: 500; transition: background 0.2s;" onmouseover="this.style.background='#4338CA'" onmouseout="this.style.background='#4F46E5'">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Save Instructor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openEditInstructorModal(btn) {
    document.getElementById('editInstId').value = btn.getAttribute('data-id');
    document.getElementById('editFullName').value = btn.getAttribute('data-name');
    
    // Check if component mapped exactly or if it needs logic
    let dept = btn.getAttribute('data-dept');
    document.getElementById('editDept').value = dept;
    document.getElementById('editStatus').value = btn.getAttribute('data-status');
    
    new bootstrap.Modal(document.getElementById('editInstructorModal')).show();
}
</script>
</body>
</html>
