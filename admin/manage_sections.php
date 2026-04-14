<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login");
    exit;
}

require 'controllers/ManageSectionsController.php';

$extra_css = ['../assets/css/pages/admin/manage-sections.css'];
include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

<div class="flex-grow-1 p-5 w-100">
    
    <?php include '../includes/topbar.php'; ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: #111827;">NSTP Component Assignment</h3>
            <p class="text-muted mb-0">Assign students to components and structure sections</p>
        </div>
        <button type="button" class="btn bg-blue-brand text-white fw-medium border-0" data-bs-toggle="modal" data-bs-target="#createSectionModal">
            <i class="bi bi-plus-lg me-1"></i> Create Section
        </button>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $msgType ?> alert-dismissible fade show rounded-3">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="component-card p-4 h-100">
                <div class="d-flex align-items-center mb-4">
                    <img src="../assets/images/dnsc_logo.png" alt="CWTS Logo" style="width: 55px; height: 55px; object-fit: contain;" class="me-3 drop-shadow-sm rounded-circle">
                    <div>
                        <h5 class="fw-bold mb-0">CWTS</h5>
                        <small class="text-muted">Civic Welfare Training Service</small>
                    </div>
                </div>
                <div class="d-flex justify-content-between text-muted small fw-medium">
                    <span>Students</span>
                    <span style="color: #111827;"><?= $cwts_count ?> / <?= $cwts_cap ?></span>
                </div>
                <div class="progress-thin"><div class="progress-bar bg-blue-brand" style="width: <?= ($cwts_count / $cwts_cap) * 100 ?>%"></div></div>
                <div class="d-flex justify-content-between text-muted small fw-medium">
                    <span>Sections</span><span style="color: #111827;">12</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="component-card p-4 h-100">
                <div class="d-flex align-items-center mb-4">
                    <img src="../assets/images/dnsc_logo.png" alt="LTS Logo" style="width: 55px; height: 55px; object-fit: contain;" class="me-3 drop-shadow-sm rounded-circle">
                    <div>
                        <h5 class="fw-bold mb-0">LTS</h5>
                        <small class="text-muted">Literacy Training Service</small>
                    </div>
                </div>
                <div class="d-flex justify-content-between text-muted small fw-medium">
                    <span>Students</span>
                    <span style="color: #111827;"><?= $lts_count ?> / <?= $lts_cap ?></span>
                </div>
                <div class="progress-thin"><div class="progress-bar bg-green-brand" style="width: <?= ($lts_count / $lts_cap) * 100 ?>%"></div></div>
                <div class="d-flex justify-content-between text-muted small fw-medium">
                    <span>Sections</span><span style="color: #111827;">8</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="component-card p-4 h-100">
                <div class="d-flex align-items-center mb-4">
                    <img src="../assets/images/rotc_logo.png" alt="ROTC Logo" style="width: 55px; height: 55px; object-fit: contain;" class="me-3 drop-shadow-sm rounded-circle">
                    <div>
                        <h5 class="fw-bold mb-0">ROTC</h5>
                        <small class="text-muted">Reserve Officers' Training Corps</small>
                    </div>
                </div>
                <div class="d-flex justify-content-between text-muted small fw-medium">
                    <span>Students</span>
                    <span style="color: #111827;"><?= $rotc_count ?> / <?= $rotc_cap ?></span>
                </div>
                <div class="progress-thin"><div class="progress-bar bg-red-brand" style="width: <?= ($rotc_count / $rotc_cap) * 100 ?>%"></div></div>
                <div class="d-flex justify-content-between text-muted small fw-medium">
                    <span>Sections</span><span style="color: #111827;">6</span>
                </div>
            </div>
        </div>
    </div>

    <div class="unassigned-container">
        <h5 class="fw-bold mb-1" style="color: #111827;">Unassigned Students</h5>
        <p class="text-muted small mb-4">Students waiting for component assignment</p>

        <?php if (count($unassigned_students) > 0): ?>
            <?php foreach ($unassigned_students as $student): ?>
                <div class="student-row d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-1" style="color: #111827;">
                            <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>
                        </h6>
                        <small class="text-muted">
                            <?= htmlspecialchars($student['student_id']) ?> - 
                            <?= htmlspecialchars($student['course'] ?? 'N/A') ?> Year <?= htmlspecialchars($student['year_level'] ?? 'N/A') ?>
                        </small>
                    </div>
                    <form method="POST" action="" class="d-flex gap-2 mb-0">
                        <input type="hidden" name="student_id" value="<?= htmlspecialchars($student['student_id']) ?>">
                        <button type="submit" name="component" value="CWTS" class="btn-assign bg-blue-brand">Assign to CWTS</button>
                        <button type="submit" name="component" value="LTS" class="btn-assign bg-green-brand">Assign to LTS</button>
                        <button type="submit" name="component" value="ROTC" class="btn-assign bg-red-brand">Assign to ROTC</button>
                        <input type="hidden" name="assign_component" value="1">
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-check-circle text-success fs-1 mb-2"></i>
                <h6 class="fw-bold text-muted">All Caught Up!</h6>
                <p class="text-muted small">There are currently no unassigned students.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Create Section Modal -->
<div class="modal fade" id="createSectionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Create New Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium small">Section Name</label>
                        <input type="text" name="section_name" class="form-control" placeholder="e.g., CWTS-A" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium small">Component</label>
                        <select name="component" class="form-select" required>
                            <option value="CWTS">CWTS</option>
                            <option value="LTS">LTS</option>
                            <option value="ROTC">ROTC</option>
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-medium small">School Year</label>
                            <input type="text" name="school_year" class="form-control" value="2026-2027" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-medium small">Semester</label>
                            <select name="semester" class="form-select" required>
                                <option value="1st">1st Semester</option>
                                <option value="2nd">2nd Semester</option>
                                <option value="Summer">Summer</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 pe-4 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="create_section" class="btn bg-blue-brand text-white rounded-3 px-4">Create Section</button>
                </div>
            </form>
        </div>
    </div>
</div>

</div>  

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
