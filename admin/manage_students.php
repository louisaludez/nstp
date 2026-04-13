<?php
session_start();
require '../config/db.php';

// Security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

$message = '';
$msgType = '';

// Handle form submission to insert a new student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    $student_id = trim($_POST['student_id']);
    $full_name = trim($_POST['full_name']);
    $course = trim($_POST['course']);
    $year_level = $_POST['year_level'];
    $contact = trim($_POST['contact_number']);
    $email = trim($_POST['email']);

    // Auto-split the Full Name into first and last name for the database
    $name_parts = explode(' ', $full_name, 2);
    $first_name = $name_parts[0];
    $last_name = $name_parts[1] ?? ''; // If they only enter one name, avoid an error

    try {
        $stmt = $pdo->prepare("INSERT INTO students (student_id, first_name, last_name, course, year_level, contact_number, email) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$student_id, $first_name, $last_name, $course, $year_level, $contact, $email]);

        $message = "Student successfully enrolled.";
        $msgType = "success";
        logAction($pdo, 'Created Student', "Enrolled $first_name $last_name ($student_id) under $course");
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $message = "Error: A student with ID $student_id already exists.";
            $msgType = "danger";
        } else {
            $message = "Database Error: " . $e->getMessage();
            $msgType = "danger";
        }
    }
}

// Handle form submission to edit a student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_student'])) {
    $original_id = trim($_POST['original_student_id']);
    $student_id = trim($_POST['student_id']);
    $full_name = trim($_POST['full_name']);
    $course = trim($_POST['course']);
    $year_level = $_POST['year_level'];
    $contact = trim($_POST['contact_number']);
    $email = trim($_POST['email']);

    $name_parts = explode(' ', $full_name, 2);
    $first_name = $name_parts[0];
    $last_name = $name_parts[1] ?? '';

    try {
        $stmt = $pdo->prepare("UPDATE students SET student_id=?, first_name=?, last_name=?, course=?, year_level=?, contact_number=?, email=? WHERE student_id=?");
        $stmt->execute([$student_id, $first_name, $last_name, $course, $year_level, $contact, $email, $original_id]);

        $message = "Student successfully updated.";
        $msgType = "success";
        logAction($pdo, 'Updated Student', "Updated details for $first_name $last_name ($student_id)");
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $message = "Error: A student with ID $student_id already exists.";
            $msgType = "danger";
        } else {
            $message = "Database Error: " . $e->getMessage();
            $msgType = "danger";
        }
    }
}

// Handle form submission to delete a student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_student'])) {
    $student_id = trim($_POST['student_id']);

    try {
        $stmt = $pdo->prepare("DELETE FROM students WHERE student_id=?");
        $stmt->execute([$student_id]);

        $message = "Student successfully deleted.";
        $msgType = "success";
        logAction($pdo, 'Deleted Student', "Deleted student with ID ($student_id)");
    } catch (PDOException $e) {
        $message = "Database Error: " . $e->getMessage();
        $msgType = "danger";
    }
}

// Handle form submission to enroll an assigned student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll_student'])) {
    $student_id = trim($_POST['student_id']);
    $section_id = $_POST['section_id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO enrollments (student_id, section_id, status) VALUES (?, ?, 'Pending')");
        $stmt->execute([$student_id, $section_id]);

        $message = "Student successfully enrolled into section.";
        $msgType = "success";
        logAction($pdo, 'Enrolled Student', "Enrolled student ($student_id) into section ID $section_id");
    } catch (PDOException $e) {
        $message = "Database Error: " . $e->getMessage();
        $msgType = "danger";
    }
}

// Fetch all sections
$stmtSecs = $pdo->query("SELECT id, component, section_name FROM sections ORDER BY component, section_name");
$allSections = $stmtSecs->fetchAll();

// Fetch all students AND their sections
$stmt = $pdo->query("
    SELECT st.*, s.section_name 
    FROM students st 
    LEFT JOIN enrollments e ON st.student_id = e.student_id 
    LEFT JOIN sections s ON e.section_id = s.id 
    ORDER BY st.created_at DESC
");
$students = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

<style>
    /* Button & Input Styling to match Figma */
    .btn-brand {
        background-color: var(--primary-active, #4A46D6);
        color: white;
        font-weight: 500;
        border-radius: 8px;
        padding: 8px 16px;
    }

    .btn-brand:hover {
        background-color: var(--primary-bg, #2B2866);
        color: white;
    }

    .btn-outline-cancel {
        border: 1px solid #D1D5DB;
        color: #111827;
        font-weight: 500;
        background-color: white;
    }

    .btn-outline-cancel:hover {
        background-color: #F3F4F6;
    }

    .search-bar-container {
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        background-color: #fff;
    }

    .search-input {
        border: none;
        box-shadow: none;
    }

    .search-input:focus {
        outline: none;
        box-shadow: none;
    }

    /* Custom Pill Badges */
    .badge-cwts {
        background-color: #E0E7FF;
        color: #4338CA;
    }

    .badge-lts {
        background-color: #D1FAE5;
        color: #059669;
    }

    .badge-rotc {
        background-color: #FEE2E2;
        color: #DC2626;
    }

    .badge-active {
        background-color: #DCFCE7;
        color: #16A34A;
    }

    .action-icon {
        cursor: pointer;
        color: #6B7280;
        transition: 0.2s;
    }

    .action-icon:hover {
        color: var(--primary-active);
    }

    .action-icon.delete:hover {
        color: #DC2626;
    }

    /* Form Inputs */
    .modal-form-control {
        border-radius: 8px;
        border: 1px solid #D1D5DB;
        padding: 10px 14px;
        font-size: 0.95rem;
    }

    .modal-form-control:focus {
        border-color: var(--primary-active, #4A46D6);
        box-shadow: 0 0 0 3px rgba(74, 70, 214, 0.1);
        outline: none;
    }
</style>

<div class="flex-grow-1 p-5 w-100">

    <?php include '../includes/topbar.php'; ?>

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
                    <button type="button" class="btn btn-outline-cancel rounded-3 px-4 py-2"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_student" class="btn btn-brand rounded-3 px-4 py-2">Add
                        Student</button>
                </div>
            </form>

        </div>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // View functionality
    document.querySelectorAll('.view-btn').forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('view_student_id').textContent = this.getAttribute('data-id');
            document.getElementById('view_name').textContent = this.getAttribute('data-name');
            document.getElementById('view_course_year').textContent = this.getAttribute('data-course') + ' - ' + this.getAttribute('data-year');
            document.getElementById('view_component').textContent = this.getAttribute('data-component');
            document.getElementById('view_status').textContent = this.getAttribute('data-status');
            document.getElementById('view_contact').textContent = this.getAttribute('data-contact') || 'N/A';
            document.getElementById('view_email').textContent = this.getAttribute('data-email') || 'N/A';
        });
    });

    const allSecs = <?= json_encode($allSections) ?>;
    document.querySelectorAll('.enroll-btn').forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('enroll_student_id').value = this.getAttribute('data-id');
            document.getElementById('enroll_student_name').textContent = this.getAttribute('data-name');
            
            let comp = this.getAttribute('data-component');
            let select = document.getElementById('enroll_section_id');
            select.innerHTML = '<option value="" disabled selected>Select a section...</option>';
            allSecs.forEach(s => {
                if(s.component === comp) {
                    select.innerHTML += `<option value="${s.id}">${s.section_name}</option>`;
                }
            });
        });
    });

    // Edit functionality
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('edit_original_student_id').value = this.getAttribute('data-id');
            document.getElementById('edit_student_id').value = this.getAttribute('data-id');
            document.getElementById('edit_full_name').value = this.getAttribute('data-fname') + ' ' + (this.getAttribute('data-lname') || '');
            document.getElementById('edit_course').value = this.getAttribute('data-course');
            document.getElementById('edit_year_level').value = this.getAttribute('data-year');
            document.getElementById('edit_contact').value = this.getAttribute('data-contact');
            document.getElementById('edit_email').value = this.getAttribute('data-email');
        });
    });

    // Delete functionality
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('delete_student_id').value = this.getAttribute('data-id');
            document.getElementById('delete_student_name').textContent = this.getAttribute('data-name');
        });
    });
});
</script>
</body>
</html>