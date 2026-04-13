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

// Handle student assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_component'])) {
    $student_id = $_POST['student_id'];
    $component = $_POST['component']; // 'CWTS', 'LTS', or 'ROTC'
    
    try {
        $stmt = $pdo->prepare("UPDATE students SET component = ? WHERE student_id = ?");
        $stmt->execute([$component, $student_id]);
        
        $message = "Student successfully assigned to $component.";
        $msgType = "success";
    } catch (PDOException $e) {
        $message = "Database Error: " . $e->getMessage();
        $msgType = "danger";
    }
}

// Handle Section Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_section'])) {
    $section_name = trim($_POST['section_name']);
    $component = $_POST['component'];
    $school_year = trim($_POST['school_year']);
    $semester = $_POST['semester'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO sections (component, section_name, school_year, semester) VALUES (?, ?, ?, ?)");
        $stmt->execute([$component, $section_name, $school_year, $semester]);
        $message = "Section $section_name ($component) successfully created!";
        $msgType = "success";
    } catch (PDOException $e) {
        $message = "Database Error: " . $e->getMessage();
        $msgType = "danger";
    }
}

// 1. Fetch counts for the top stat cards
$stmtCounts = $pdo->query("SELECT component, COUNT(*) as total FROM students WHERE component IS NOT NULL GROUP BY component");
$counts = $stmtCounts->fetchAll(PDO::FETCH_KEY_PAIR); 
// Defaults to 0 if no students are assigned yet
$cwts_count = $counts['CWTS'] ?? 0;
$lts_count  = $counts['LTS'] ?? 0;
$rotc_count = $counts['ROTC'] ?? 0;

// Program component capacities (Current operational limits)
$cwts_cap = 800;
$lts_cap = 400;
$rotc_cap = 400;

// 2. Fetch all Unassigned Students
$stmtUnassigned = $pdo->query("SELECT * FROM students WHERE component IS NULL ORDER BY created_at DESC");
$unassigned_students = $stmtUnassigned->fetchAll();

include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

<style>
    /* Component Card Styling */
    .component-card {
        border-radius: 12px;
        border: 1px solid #E5E7EB;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    
    .icon-circle-lg {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.25rem;
    }
    
    /* Branding Colors based on Figma */
    .bg-blue-brand { background-color: #2563EB; }
    .bg-green-brand { background-color: #16A34A; }
    .bg-red-brand { background-color: #DC2626; }
    
    .text-blue-brand { color: #2563EB; }
    .text-green-brand { color: #16A34A; }
    .text-red-brand { color: #DC2626; }

    /* Thin Progress Bars */
    .progress-thin {
        height: 6px;
        border-radius: 10px;
        background-color: #F3F4F6;
        margin-top: 8px;
        margin-bottom: 16px;
    }
    
    /* Unassigned Student List Styling */
    .unassigned-container {
        border-radius: 12px;
        border: 1px solid #E5E7EB;
        background-color: #fff;
        padding: 24px;
    }
    
    .student-row {
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 16px 20px;
        margin-bottom: 12px;
        transition: 0.2s;
    }
    .student-row:hover {
        border-color: #D1D5DB;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    
    .btn-assign {
        font-size: 0.85rem;
        font-weight: 500;
        padding: 8px 16px;
        border-radius: 6px;
        color: white;
        border: none;
        transition: opacity 0.2s;
    }
    .btn-assign:hover {
        opacity: 0.9;
    }
</style>

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
                    <div class="icon-circle-lg bg-blue-brand me-3">C</div>
                    <div>
                        <h5 class="fw-bold mb-0">CWTS</h5>
                        <small class="text-muted">Civic Welfare Training Service</small>
                    </div>
                </div>
                <div class="d-flex justify-content-between text-muted small fw-medium">
                    <span>Students</span>
                    <span style="color: #111827;"><?= $cwts_count ?> / <?= $cwts_cap ?></span>
                </div>
                <div class="progress-thin">
                    <div class="progress-bar bg-blue-brand" style="width: <?= ($cwts_count / $cwts_cap) * 100 ?>%"></div>
                </div>
                <div class="d-flex justify-content-between text-muted small fw-medium">
                    <span>Sections</span>
                    <span style="color: #111827;">12</span> 
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="component-card p-4 h-100">
                <div class="d-flex align-items-center mb-4">
                    <div class="icon-circle-lg bg-green-brand me-3">L</div>
                    <div>
                        <h5 class="fw-bold mb-0">LTS</h5>
                        <small class="text-muted">Literacy Training Service</small>
                    </div>
                </div>
                <div class="d-flex justify-content-between text-muted small fw-medium">
                    <span>Students</span>
                    <span style="color: #111827;"><?= $lts_count ?> / <?= $lts_cap ?></span>
                </div>
                <div class="progress-thin">
                    <div class="progress-bar bg-green-brand" style="width: <?= ($lts_count / $lts_cap) * 100 ?>%"></div>
                </div>
                <div class="d-flex justify-content-between text-muted small fw-medium">
                    <span>Sections</span>
                    <span style="color: #111827;">8</span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="component-card p-4 h-100">
                <div class="d-flex align-items-center mb-4">
                    <div class="icon-circle-lg bg-red-brand me-3">R</div>
                    <div>
                        <h5 class="fw-bold mb-0">ROTC</h5>
                        <small class="text-muted">Reserve Officers' Training Corps</small>
                    </div>
                </div>
                <div class="d-flex justify-content-between text-muted small fw-medium">
                    <span>Students</span>
                    <span style="color: #111827;"><?= $rotc_count ?> / <?= $rotc_cap ?></span>
                </div>
                <div class="progress-thin">
                    <div class="progress-bar bg-red-brand" style="width: <?= ($rotc_count / $rotc_cap) * 100 ?>%"></div>
                </div>
                <div class="d-flex justify-content-between text-muted small fw-medium">
                    <span>Sections</span>
                    <span style="color: #111827;">6</span>
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
                        
                        <button type="submit" name="component" value="CWTS" class="btn-assign bg-blue-brand">
                            Assign to CWTS
                        </button>
                        <button type="submit" name="component" value="LTS" class="btn-assign bg-green-brand">
                            Assign to LTS
                        </button>
                        <button type="submit" name="component" value="ROTC" class="btn-assign bg-red-brand">
                            Assign to ROTC
                        </button>
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