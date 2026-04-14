<?php
session_start();
require '../config/db.php';

// Security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login");
    exit;
}

require_once __DIR__ . '/controllers/ManageStudentsController.php';

$title = "Student Management";
$extra_css = ['../assets/css/pages/admin/manage-students.css'];
$extra_js = ['../assets/js/pages/admin/manage-students.js'];

ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-1">
    <h3 class="fw-bold mb-0" style="color: #111827;">Student Management</h3>
    <button type="button" class="btn btn-brand border-0" data-bs-toggle="modal" data-bs-target="#addStudentModal">
        <i class="bi bi-plus-lg me-1"></i> Add Student
    </button>
</div>
<p class="text-muted mb-4">Manage student enrollment and information</p>

<?php if ($message): ?>
    <div class="alert alert-<?= $msgType ?> alert-dismissible fade show rounded-3">
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="search-bar-container p-2 mb-4 d-flex align-items-center">
    <i class="bi bi-search ms-3 text-muted"></i>
    <input type="text" class="form-control search-input ms-2" placeholder="Search by name or student ID...">
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-borderless align-middle mb-0">
            <thead style="background-color: #F9FAFB; border-bottom: 1px solid #E5E7EB;">
                <tr>
                    <th class="px-4 py-3 text-muted" style="font-weight: 600; font-size: 0.85rem;">Student ID</th>
                    <th class="px-4 py-3 text-muted" style="font-weight: 600; font-size: 0.85rem;">Name</th>
                    <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem;">Course</th>
                    <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem;">Year</th>
                    <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem;">Component</th>
                    <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem;">Status</th>
                    <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem;">Contact</th>
                    <th class="px-4 py-3 text-muted" style="font-weight: 600; font-size: 0.85rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($students) > 0): ?>
                    <?php foreach ($students as $row): ?>
                        <tr style="border-bottom: 1px solid #F3F4F6;">
                            <td class="px-4 py-3" style="color: #4B5563;"><?= htmlspecialchars($row['student_id']) ?></td>
                            <td class="px-4 py-3 fw-medium" style="color: #111827;">
                                <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                            <td class="py-3" style="color: #4B5563;"><?= htmlspecialchars($row['course'] ?? 'N/A') ?></td>
                            <td class="py-3" style="color: #4B5563;"><?= htmlspecialchars($row['year_level'] ?? 'N/A') ?>
                            </td>
                            <td class="py-3">
                                <?php if ($row['component'] === 'CWTS'): ?>
                                    <span class="badge rounded-pill badge-cwts px-3 py-2 fw-medium">CWTS<?= $row['section_name'] ? ' - '.htmlspecialchars($row['section_name']) : '' ?></span>
                                <?php elseif ($row['component'] === 'LTS'): ?>
                                    <span class="badge rounded-pill badge-lts px-3 py-2 fw-medium">LTS<?= $row['section_name'] ? ' - '.htmlspecialchars($row['section_name']) : '' ?></span>
                                <?php elseif ($row['component'] === 'ROTC'): ?>
                                    <span class="badge rounded-pill badge-rotc px-3 py-2 fw-medium">ROTC<?= $row['section_name'] ? ' - '.htmlspecialchars($row['section_name']) : '' ?></span>
                                <?php else: ?>
                                    <span class="badge rounded-pill px-3 py-2 fw-medium"
                                        style="background-color: #F3F4F6; color: #6B7280;">Unassigned</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3"><span
                                    class="badge rounded-pill badge-active px-3 py-2 fw-medium"><?= htmlspecialchars($row['enrollment_status'] ?? 'Active') ?></span>
                            </td>
                            <td class="py-3" style="color: #4B5563;">
                                <?= htmlspecialchars($row['contact_number'] ?? 'N/A') ?></td>
                            <td class="px-4 py-3 fs-5">
                                <?php if ($row['component'] && empty($row['section_name'])): ?>
                                    <i class="bi bi-box-arrow-in-right action-icon me-2 text-primary enroll-btn" title="Enroll in Section"
                                       data-bs-toggle="modal" data-bs-target="#enrollStudentModal"
                                       data-id="<?= htmlspecialchars($row['student_id'] ?? '') ?>"
                                       data-component="<?= htmlspecialchars($row['component'] ?? '') ?>"
                                       data-name="<?= htmlspecialchars(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')) ?>"
                                    ></i>
                                <?php endif; ?>
                                <i class="bi bi-eye action-icon me-2 view-btn"
                                   data-bs-toggle="modal" data-bs-target="#viewStudentModal"
                                   data-id="<?= htmlspecialchars($row['student_id'] ?? '') ?>"
                                   data-name="<?= htmlspecialchars(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')) ?>"
                                   data-course="<?= htmlspecialchars($row['course'] ?? '') ?>"
                                   data-year="<?= htmlspecialchars($row['year_level'] ?? '') ?>"
                                   data-component="<?= htmlspecialchars($row['component'] ?? 'Unassigned') ?>"
                                   data-status="<?= htmlspecialchars($row['enrollment_status'] ?? 'Active') ?>"
                                   data-contact="<?= htmlspecialchars($row['contact_number'] ?? '') ?>"
                                   data-email="<?= htmlspecialchars($row['email'] ?? '') ?>"
                                ></i>
                                <i class="bi bi-pencil-square action-icon me-2 edit-btn"
                                   data-bs-toggle="modal" data-bs-target="#editStudentModal"
                                   data-id="<?= htmlspecialchars($row['student_id'] ?? '') ?>"
                                   data-fname="<?= htmlspecialchars($row['first_name'] ?? '') ?>"
                                   data-lname="<?= htmlspecialchars($row['last_name'] ?? '') ?>"
                                   data-course="<?= htmlspecialchars($row['course'] ?? '') ?>"
                                   data-year="<?= htmlspecialchars($row['year_level'] ?? '') ?>"
                                   data-contact="<?= htmlspecialchars($row['contact_number'] ?? '') ?>"
                                   data-email="<?= htmlspecialchars($row['email'] ?? '') ?>"
                                ></i>
                                <i class="bi bi-trash action-icon delete delete-btn"
                                   data-bs-toggle="modal" data-bs-target="#deleteStudentModal"
                                   data-id="<?= htmlspecialchars($row['student_id'] ?? '') ?>"
                                   data-name="<?= htmlspecialchars(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')) ?>"
                                ></i>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">No student records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4 mt-1">
                <h4 class="modal-title fw-bold" style="color: #111827;">Add New Student</h4>
            </div>
            <form method="POST" action="">
                <div class="modal-body p-4">
                    <div class="row g-4 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-medium small mb-1">Full Name</label>
                            <input type="text" name="full_name" class="form-control modal-form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-medium small mb-1">Student ID</label>
                            <input type="text" name="student_id" class="form-control modal-form-control" required>
                        </div>
                    </div>
                    <div class="row g-4 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-medium small mb-1">Course</label>
                            <input type="text" name="course" class="form-control modal-form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-medium small mb-1">Year Level</label>
                            <select name="year_level" class="form-select modal-form-control" required>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-4 mb-2">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-medium small mb-1">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control modal-form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-medium small mb-1">Email</label>
                            <input type="email" name="email" class="form-control modal-form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pe-4 pb-4">
                    <button type="button" class="btn btn-outline-cancel rounded-3 px-4 py-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_student" class="btn btn-brand rounded-3 px-4 py-2">Add Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Student Modal -->
<div class="modal fade" id="viewStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4 mt-1">
                <h4 class="modal-title fw-bold" style="color: #111827;">Student Details</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex flex-column gap-3">
                    <div><small class="text-muted">Student ID</small><div class="fw-medium text-dark" id="view_student_id"></div></div>
                    <div><small class="text-muted">Full Name</small><div class="fw-medium text-dark" id="view_name"></div></div>
                    <div><small class="text-muted">Course & Year</small><div class="fw-medium text-dark" id="view_course_year"></div></div>
                    <div><small class="text-muted">NSTP Component</small><div class="fw-medium text-dark" id="view_component"></div></div>
                    <div><small class="text-muted">Enrollment Status</small><div class="fw-medium text-dark" id="view_status"></div></div>
                    <div><small class="text-muted">Contact Number</small><div class="fw-medium text-dark" id="view_contact"></div></div>
                    <div><small class="text-muted">Email Address</small><div class="fw-medium text-dark" id="view_email"></div></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Student Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4 mt-1">
                <h4 class="modal-title fw-bold" style="color: #111827;">Edit Student</h4>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="original_student_id" id="edit_original_student_id">
                <div class="modal-body p-4">
                    <div class="row g-4 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-medium small mb-1">Full Name</label>
                            <input type="text" name="full_name" id="edit_full_name" class="form-control modal-form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-medium small mb-1">Student ID</label>
                            <input type="text" name="student_id" id="edit_student_id" class="form-control modal-form-control" required>
                        </div>
                    </div>
                    <div class="row g-4 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-medium small mb-1">Course</label>
                            <input type="text" name="course" id="edit_course" class="form-control modal-form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-medium small mb-1">Year Level</label>
                            <select name="year_level" id="edit_year_level" class="form-select modal-form-control" required>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-4 mb-2">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-medium small mb-1">Contact Number</label>
                            <input type="text" name="contact_number" id="edit_contact" class="form-control modal-form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-medium small mb-1">Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control modal-form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pe-4 pb-4">
                    <button type="button" class="btn btn-outline-cancel rounded-3 px-4 py-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="edit_student" class="btn btn-brand rounded-3 px-4 py-2">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Student Modal -->
<div class="modal fade" id="deleteStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4 mt-1">
                <h4 class="modal-title fw-bold" style="color: #DC2626;">Confirm Deletion</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body p-4">
                    <input type="hidden" name="student_id" id="delete_student_id">
                    <p class="text-dark mb-0">Are you sure you want to delete student <strong id="delete_student_name"></strong>? This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-top-0 pt-0 pe-4 pb-4">
                    <button type="button" class="btn btn-outline-cancel rounded-3 px-4 py-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_student" class="btn btn-danger rounded-3 px-4 py-2">Delete Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Enroll Student Modal -->
<div class="modal fade" id="enrollStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4 mt-1">
                <h4 class="modal-title fw-bold" style="color: #4A46D6;">Section Enrollment</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body p-4">
                    <input type="hidden" name="student_id" id="enroll_student_id">
                    <p class="text-dark mb-4">Enroll student <strong id="enroll_student_name"></strong> into a section matching their component.</p>
                    
                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium small mb-1">Select Section</label>
                        <select name="section_id" id="enroll_section_id" class="form-select modal-form-control" required>
                            <!-- JavaScript populates this dynamically -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pe-4 pb-4">
                    <button type="button" class="btn btn-outline-cancel rounded-3 px-4 py-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="enroll_student" class="btn btn-brand rounded-3 px-4 py-2">Confirm Enrollment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    window.allSectionsData = <?= json_encode($allSections) ?>;
</script>

<?php
$content = ob_get_clean();
require '../includes/layout.php';
