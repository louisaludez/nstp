<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login");
    exit;
}

require 'controllers/ManageInstructorsController.php';

$extra_css = ['../assets/css/pages/admin/manage-instructors.css'];
include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

<div class="flex-grow-1 p-5 w-100" style="background-color: #F4F6F9;">
    
    <?php include '../includes/topbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-1">
        <div>
            <h3 class="fw-bold mb-0" style="color: #111827;">Instructor Assignment</h3>
            <p class="text-muted mb-4">Manage instructor assignments to sections</p>
        </div>
        <button type="button" class="btn-brand border-0" data-bs-toggle="modal" data-bs-target="#addInstructorModal">
            <i class="bi bi-person-plus me-1"></i> Add Instructor
        </button>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $msgType ?> alert-dismissible fade show rounded-3">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        
        <div class="col-xl-7 col-lg-6">
            <div class="panel-container">
                <h5 class="fw-bold mb-4" style="color: #111827;">Active Instructors</h5>
                <?php if (count($instructors) > 0): ?>
                    <?php foreach ($instructors as $inst): ?>
                        <div class="card-instructor d-flex justify-content-between" onclick="viewInstructorDetails(<?= $inst['id'] ?>, '<?= htmlspecialchars(addslashes($inst['full_name'])) ?>')">
                            <div>
                                <h6 class="fw-bold mb-1" style="color: #111827;">Prof. <?= htmlspecialchars($inst['full_name']) ?></h6>
                                <?php if ($inst['primary_component'] === 'CWTS'): ?>
                                    <span class="badge rounded-pill badge-cwts fw-medium">CWTS</span>
                                <?php elseif ($inst['primary_component'] === 'LTS'): ?>
                                    <span class="badge rounded-pill badge-lts fw-medium">LTS</span>
                                <?php elseif ($inst['primary_component'] === 'ROTC'): ?>
                                    <span class="badge rounded-pill badge-rotc fw-medium">ROTC</span>
                                <?php else: ?>
                                    <span class="badge rounded-pill fw-medium" style="background-color: #F3F4F6; color: #9CA3AF;">No Component</span>
                                <?php endif; ?>
                                <div>
                                    <?php 
                                    if ($inst['assigned_sections']) {
                                        $sections = explode(',', $inst['assigned_sections']);
                                        foreach ($sections as $sec) {
                                            echo "<span class='tag-section'>" . htmlspecialchars($sec) . "</span>";
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="text-end">
                                <h4 class="fw-bold mb-0" style="color: #111827;"><?= $inst['student_count'] ?? '0' ?></h4>
                                <small class="text-muted" style="font-size: 0.8rem;">students</small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted text-center py-4">No active instructors found.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-xl-5 col-lg-6">
            <div class="panel-container">
                <h5 class="fw-bold mb-4" style="color: #111827;">Unassigned Sections</h5>
                <?php if (count($unassigned_sections) > 0): ?>
                    <?php foreach ($unassigned_sections as $sec): ?>
                        <div class="card-unassigned-<?= $sec['component'] ?? 'CWTS' ?>">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="fw-bold mb-1" style="color: #111827;"><?= htmlspecialchars($sec['section_name']) ?></h6>
                                    <small class="text-muted"><?= $sec['enrolled_count'] ?? '0' ?> students enrolled</small>
                                </div>
                                <?php if ($sec['component'] === 'CWTS'): ?>
                                    <span class="badge rounded-pill badge-cwts fw-medium">CWTS</span>
                                <?php elseif ($sec['component'] === 'LTS'): ?>
                                    <span class="badge rounded-pill badge-lts fw-medium">LTS</span>
                                <?php elseif ($sec['component'] === 'ROTC'): ?>
                                    <span class="badge rounded-pill badge-rotc fw-medium">ROTC</span>
                                <?php endif; ?>
                            </div>
                            <button type="button" class="btn-brand w-100 mt-3" data-bs-toggle="modal" data-bs-target="#assignModal<?= $sec['id'] ?>">
                                Assign Instructor
                            </button>
                        </div>

                        <div class="modal fade" id="assignModal<?= $sec['id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                                    <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                                        <h5 class="modal-title fw-bold">Assign to <?= htmlspecialchars($sec['section_name']) ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="">
                                        <div class="modal-body p-4">
                                            <input type="hidden" name="section_id" value="<?= $sec['id'] ?>">
                                            <label class="form-label text-muted fw-medium small">Select Instructor</label>
                                            <select name="instructor_id" class="form-select modal-form-control" required>
                                                <option value="" disabled selected>Choose...</option>
                                                <?php foreach ($instructors as $inst): ?>
                                                    <option value="<?= $inst['id'] ?>">Prof. <?= htmlspecialchars($inst['full_name']) ?> (<?= htmlspecialchars($inst['primary_component']) ?>)</option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="modal-footer border-top-0 pb-4 pe-4 pt-0">
                                            <button type="button" class="btn btn-outline-cancel rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" name="assign_instructor" class="btn-brand rounded-3 px-4">Confirm Assignment</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-check-circle text-success fs-1 mb-2"></i>
                        <h6 class="fw-bold text-muted">All Assigned!</h6>
                        <p class="text-muted small">Every section currently has an instructor.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="instructorDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4 mt-1">
                <h5 class="modal-title fw-bold" style="color: #111827;">Prof. <span id="modalInstName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="modalInstBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addInstructorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px; max-width: 500px; margin: auto;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4 mt-1">
                <h4 class="modal-title fw-bold" style="color: #111827;">Add New Instructor</h4>
            </div>
            <form method="POST" action="">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium small mb-1">Full Name</label>
                        <input type="text" name="full_name" class="form-control modal-form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium small mb-1">Email</label>
                        <input type="email" name="email" class="form-control modal-form-control" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-medium small mb-1">Component</label>
                            <select name="component" class="form-select modal-form-control" required>
                                <option value="CWTS">CWTS</option>
                                <option value="LTS">LTS</option>
                                <option value="ROTC">ROTC</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-medium small mb-1">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control modal-form-control">
                        </div>
                    </div>
                    <div class="row g-3 mb-4 border-top pt-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-medium small mb-1">Password</label>
                            <input type="password" name="password" class="form-control modal-form-control" minlength="6" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-medium small mb-1">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control modal-form-control" minlength="6" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pe-4 pb-4">
                    <button type="button" class="btn btn-outline-cancel rounded-3 px-4 py-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_new_instructor" class="btn-brand rounded-3 px-4 py-2">Add Instructor</button>
                </div>
            </form>
        </div>
    </div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function viewInstructorDetails(instructorId, instructorName) {
    document.getElementById('modalInstName').innerText = instructorName;
    document.getElementById('modalInstBody').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted small">Loading details...</p></div>';
    
    var modal = new bootstrap.Modal(document.getElementById('instructorDetailsModal'));
    modal.show();

    fetch('ajax_get_instructor_students.php?instructor_id=' + instructorId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = '';
                if (data.sections.length === 0) {
                    html = '<div class="text-muted text-center py-4">No sections assigned to this instructor.</div>';
                } else {
                    data.sections.forEach(sec => {
                        let badgeClass = 'bg-secondary';
                        if (sec.component === 'CWTS') badgeClass = 'badge-cwts';
                        else if (sec.component === 'LTS') badgeClass = 'badge-lts';
                        else if (sec.component === 'ROTC') badgeClass = 'badge-rotc';
                        
                        html += `
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-2">
                                    <h6 class="fw-bold mb-0 me-2" style="color: #111827;">${sec.section_name}</h6>
                                    <span class="badge rounded-pill ${badgeClass} fw-medium">${sec.component}</span>
                                </div>
                                <div class="table-responsive border rounded-3">
                                    <table class="table table-hover mb-0" style="font-size: 0.9rem;">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="border-bottom-0 text-muted fw-medium py-2 px-3">Student ID</th>
                                                <th class="border-bottom-0 text-muted fw-medium py-2 px-3">Name</th>
                                                <th class="border-bottom-0 text-muted fw-medium py-2 px-3">Course</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;
                        if (sec.students.length === 0) {
                            html += `<tr><td colspan="3" class="text-center text-muted py-3">No students enrolled</td></tr>`;
                        } else {
                            sec.students.forEach(st => {
                                html += `
                                    <tr>
                                        <td class="text-secondary px-3 py-2">${st.student_id}</td>
                                        <td class="fw-medium text-dark px-3 py-2">${st.first_name} ${st.last_name}</td>
                                        <td class="text-secondary px-3 py-2">${st.course}</td>
                                    </tr>`;
                            });
                        }
                        html += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>`;
                    });
                }
                document.getElementById('modalInstBody').innerHTML = html;
            } else {
                document.getElementById('modalInstBody').innerHTML = '<div class="alert alert-danger text-center">Failed to load details.</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('modalInstBody').innerHTML = '<div class="alert alert-danger text-center">An error occurred while loading details.</div>';
        });
}
</script>
</body>
</html>
