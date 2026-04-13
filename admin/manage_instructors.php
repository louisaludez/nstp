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

// --- 1. Handle Assigning an Instructor to an Unassigned Section ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_instructor'])) {
    $section_id = $_POST['section_id'];
    $instructor_id = $_POST['instructor_id'];
    
    try {
        $stmt = $pdo->prepare("UPDATE sections SET instructor_id = ? WHERE id = ?");
        $stmt->execute([$instructor_id, $section_id]);
        
        $instQuery = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
        $instQuery->execute([$instructor_id]);
        $instName = $instQuery->fetchColumn();
        
        $secQuery = $pdo->prepare("SELECT section_name FROM sections WHERE id = ?");
        $secQuery->execute([$section_id]);
        $secName = $secQuery->fetchColumn();

        $message = "Prof. $instName successfully assigned to $secName.";
        $msgType = "success";
        
        logAction($pdo, 'Assigned Instructor', "Assigned Prof. $instName to section $secName"); 
        
    } catch (PDOException $e) {
        $message = "Database Error: " . $e->getMessage();
        $msgType = "danger";
    }
}

// --- 2. Handle Adding a Brand New Instructor ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_new_instructor'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $component = $_POST['component'];
    $contact_number = trim($_POST['contact_number']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate that passwords match before touching the database
    if ($password !== $confirm_password) {
        $message = "Error: Passwords do not match.";
        $msgType = "danger";
    } elseif (strlen($password) < 6) {
        $message = "Error: Password must be at least 6 characters long.";
        $msgType = "danger";
    } else {
        // Hash the user-provided password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role, component, contact_number) VALUES (?, ?, ?, 'Instructor', ?, ?)");
            $stmt->execute([$full_name, $email, $hashedPassword, $component, $contact_number]);
            
            $message = "Instructor Prof. $full_name successfully added.";
            $msgType = "success";
            
            logAction($pdo, 'Created Instructor', "Created account for Prof. $full_name ($component)");
            
        } catch (PDOException $e) {
            $message = ($e->getCode() == 23000) ? "Error: Email already exists." : "Error: " . $e->getMessage();
            $msgType = "danger";
        }
    }
}

// --- 3. Fetch Data for the UI ---
$stmtInstructors = $pdo->query("
    SELECT u.id, u.full_name, u.email, u.component AS primary_component, u.contact_number,
           GROUP_CONCAT(DISTINCT s.section_name SEPARATOR ',') as assigned_sections,
           (SELECT COUNT(e.student_id) FROM enrollments e JOIN sections sec ON e.section_id = sec.id WHERE sec.instructor_id = u.id) as student_count
    FROM users u 
    LEFT JOIN sections s ON u.id = s.instructor_id 
    WHERE u.role = 'Instructor' 
    GROUP BY u.id
    ORDER BY u.full_name ASC
");
$instructors = $stmtInstructors->fetchAll();

$stmtUnassigned = $pdo->query("
    SELECT s.*, 
           (SELECT COUNT(student_id) FROM enrollments WHERE section_id = s.id) as enrolled_count
    FROM sections s 
    WHERE instructor_id IS NULL 
    ORDER BY component, section_name
");
$unassigned_sections = $stmtUnassigned->fetchAll();

include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

<style>
    /* Figma Match Styles */
    .btn-brand {
        background-color: var(--primary-active, #4A46D6);
        color: white;
        font-weight: 500;
        border-radius: 8px;
        padding: 8px 16px;
        border: none;
    }
    .btn-brand:hover { background-color: var(--primary-bg, #2B2866); color: white; }
    
    .btn-outline-cancel {
        border: 1px solid #D1D5DB;
        color: #111827;
        font-weight: 500;
        background-color: white;
        border-radius: 8px;
        padding: 8px 16px;
    }
    .btn-outline-cancel:hover { background-color: #F3F4F6; }
    
    .panel-container {
        background-color: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 16px;
        padding: 24px;
        height: 100%;
    }
    
    .card-instructor {
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 16px;
        transition: 0.2s;
    }
    .card-instructor:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    
    .card-unassigned-CWTS {
        border: 1px solid #FED7AA; 
        background-color: #FFF7ED; 
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 16px;
    }
    .card-unassigned-LTS {
        border: 1px solid #BBF7D0; 
        background-color: #F0FDF4; 
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 16px;
    }
    .card-unassigned-ROTC {
        border: 1px solid #FECACA; 
        background-color: #FEF2F2; 
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 16px;
    }
    
    .badge-cwts { background-color: #E0E7FF; color: #4338CA; padding: 4px 10px; font-size: 0.75rem; }
    .badge-lts { background-color: #D1FAE5; color: #059669; padding: 4px 10px; font-size: 0.75rem; }
    .badge-rotc { background-color: #FEE2E2; color: #DC2626; padding: 4px 10px; font-size: 0.75rem; }
    
    .tag-section {
        background-color: #F3F4F6;
        color: #4B5563;
        border-radius: 6px;
        padding: 4px 12px;
        font-size: 0.8rem;
        font-weight: 500;
        margin-right: 6px;
        display: inline-block;
        margin-top: 12px;
    }
    
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
                        <div class="card-instructor d-flex justify-content-between">
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
                                <h4 class="fw-bold mb-0" style="color: #111827;">
                                    <?= $inst['student_count'] ?? '0' ?>
                                </h4>
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
</body>
</html>