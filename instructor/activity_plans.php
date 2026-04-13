<?php
session_start();
require '../config/db.php';

// Security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Instructor') {
    header("Location: ../login");
    exit;
}

$instructor_name = $_SESSION['full_name'];
$instructor_id = $_SESSION['user_id'];

$message = '';
$msgType = '';

// Get instructor's primary section
$stmtSec = $pdo->prepare("SELECT id FROM sections WHERE instructor_id = ? LIMIT 1");
$stmtSec->execute([$instructor_id]);
$section_id = $stmtSec->fetchColumn();
$section_id = $section_id ? $section_id : null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_plan'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $objectives = $_POST['objectives'];
    $scheduled_date = $_POST['scheduled_date'];
    $scheduled_time = $_POST['scheduled_time'] ?? null;
    $location = $_POST['location'];
    
    // File handling
    $files_attached = 0;
    if (isset($_FILES['supporting_files']) && !empty($_FILES['supporting_files']['name'][0])) {
        $files_attached = count($_FILES['supporting_files']['name']);
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO activity_plans (instructor_id, section_id, title, description, location, scheduled_date, scheduled_time, objectives, files_attached, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
        $stmt->execute([$instructor_id, $section_id, $title, $description, $location, $scheduled_date, $scheduled_time, $objectives, $files_attached]);
        header("Location: activity_plans.php?msg=submitted");
        exit;
    } catch(PDOException $e) {
        $message = "Error submitting plan: " . $e->getMessage();
        $msgType = "danger";
    }
}

// Handle messages
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'submitted') {
        $message = "Activity plan submitted successfully and is pending approval.";
        $msgType = "success";
    } elseif ($_GET['msg'] === 'deleted') {
        $message = "Activity plan permanently deleted.";
        $msgType = "success";
    }
}

// Handle deletion
if (isset($_GET['delete'])) {
    $del_id = $_GET['delete'];
    $pdo->prepare("DELETE FROM activity_plans WHERE id = ? AND instructor_id = ?")->execute([$del_id, $instructor_id]);
    header("Location: activity_plans.php?msg=deleted");
    exit;
}

// Fetch all plans for this instructor
$stmtPlans = $pdo->prepare("SELECT * FROM activity_plans WHERE instructor_id = ? ORDER BY submitted_date DESC");
$stmtPlans->execute([$instructor_id]);
$plans = $stmtPlans->fetchAll();

include '../includes/header.php';
include '../includes/instructor_sidebar.php';
?>

<style>
    .panel-container { 
        background-color: #fff; 
        border: 1px solid #E5E7EB; 
        border-radius: 12px; 
        padding: 24px; 
        margin-bottom: 24px; 
    }
    
    /* Form Styling */
    .form-label {
        font-size: 0.85rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 6px;
    }
    .form-control {
        border-radius: 8px;
        border: 1px solid #D1D5DB;
        padding: 10px 14px;
        font-size: 0.95rem;
    }
    .form-control:focus {
        border-color: #00695C;
        box-shadow: 0 0 0 3px rgba(0, 105, 92, 0.1);
    }

    /* File Upload Zone */
    .upload-zone {
        border: 2px dashed #D1D5DB;
        border-radius: 8px;
        padding: 32px;
        text-align: center;
        background-color: #F9FAFB;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .upload-zone:hover { background-color: #F3F4F6; }
    .upload-icon { font-size: 1.5rem; color: #6B7280; margin-bottom: 8px; }

    /* Buttons */
    .btn-brand {
        background-color: #0D6EFD; /* Matching the blue in the screenshot */
        color: white;
        font-weight: 500;
        border-radius: 8px;
        padding: 8px 20px;
        border: none;
    }
    .btn-brand:hover { background-color: #0b5ed7; color: white; }
    
    .btn-cancel-light {
        background-color: #F3F4F6;
        color: #4B5563;
        font-weight: 500;
        border-radius: 8px;
        padding: 8px 20px;
        border: none;
    }
    .btn-cancel-light:hover { background-color: #E5E7EB; }

    /* Submitted Plan Cards */
    .plan-card {
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 20px;
        background-color: #fff;
        margin-bottom: 16px;
    }
    .badge-soft-primary { background-color: #DBEAFE; color: #1E40AF; padding: 4px 10px; font-weight: 500; }
    .badge-soft-success { background-color: #D1FAE5; color: #065F46; padding: 4px 10px; font-weight: 500; }
    .badge-soft-danger { background-color: #FEE2E2; color: #991B1B; padding: 4px 10px; font-weight: 500; }
    .action-icon { cursor: pointer; font-size: 1.1rem; transition: 0.2s; color: #6B7280;}
    .action-icon.edit { color: #3B82F6; }
    .action-icon.delete { color: #EF4444; }
    .action-icon:hover { opacity: 0.7; }
    
    .file-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background-color: #F3F4F6;
        border: 1px solid #E5E7EB;
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 0.8rem;
        color: #374151;
        margin-right: 8px;
        margin-top: 12px;
    }

    /* Objectives Toggle */
    .obj-toggle { color: #3B82F6; font-size: 0.85rem; font-weight: 500; cursor: pointer; text-decoration: none; display: inline-block; margin-top: 12px; }
    .obj-toggle:hover { color: #2563EB; }
    .obj-content { border-left: 2px solid #BFDBFE; padding-left: 12px; margin-top: 8px; font-size: 0.9rem; color: #4B5563; }
</style>

<div class="flex-grow-1 p-5 w-100" style="background-color: #F9FAFB;">
    
    <?php include '../includes/topbar.php'; ?>
    
    <!-- Header Area -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: #111827;">Activity Plan Submission</h3>
            <p class="text-muted mb-0">Submit and manage planned NSTP activities for your section</p>
        </div>
        <button type="button" class="btn btn-brand" id="toggleFormBtn">+ New Activity Plan</button>
    </div>

    <!-- Alerts -->
    <?php if ($message): ?>
        <div class="alert alert-<?= $msgType ?> alert-dismissible fade show rounded-3">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Form Panel -->
    <div class="panel-container" id="activityFormContainer" style="display: none;">
        <h5 class="fw-bold mb-4">Create New Activity Plan</h5>
        <form method="POST" action="" enctype="multipart/form-data">
            
            <div class="mb-4">
                <label class="form-label">Activity Title *</label>
                <input type="text" name="title" class="form-control" placeholder="e.g., Community Clean-up Drive" required>
            </div>
            
            <div class="mb-4">
                <label class="form-label">Description *</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Describe the activity in detail..." required></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label">Objectives *</label>
                <textarea name="objectives" class="form-control" rows="2" placeholder="List the objectives and expected outcomes..." required></textarea>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Scheduled Date *</label>
                    <input type="date" name="scheduled_date" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Scheduled Time *</label>
                    <input type="time" name="scheduled_time" class="form-control" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Location *</label>
                <input type="text" name="location" class="form-control" placeholder="e.g., Sunset Bay Beach" required>
            </div>
            
            <div class="mb-4">
                <label class="form-label">Supporting Files</label>
                <div class="upload-zone" onclick="document.getElementById('fileUpload').click()">
                    <i class="bi bi-upload upload-icon d-block"></i>
                    <small class="text-muted d-block mb-2">Upload PDF, Word documents, or images</small>
                    <span class="btn btn-sm btn-light border fw-medium">Choose Files</span>
                    <input type="file" name="supporting_files[]" id="fileUpload" class="d-none" multiple>
                    <div id="fileList" class="mt-2 small text-primary fw-medium"></div>
                </div>
            </div>
            
            <div class="d-flex gap-2 mt-4">
                <button type="submit" name="submit_plan" class="btn btn-brand">Submit Plan</button>
                <button type="button" class="btn btn-cancel-light" id="cancelFormBtnBottom">Cancel</button>
            </div>
        </form>
    </div>

    <!-- Submitted Plans -->
    <div class="panel-container">
        <h5 class="fw-bold mb-4">Submitted Activity Plans</h5>
        
        <?php if (count($plans) > 0): ?>
            <?php foreach ($plans as $plan): ?>
                <div class="plan-card">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="fw-bold mb-0" style="font-size: 1.05rem;"><?= htmlspecialchars($plan['title']) ?></h6>
                            <?php 
                                $badgeClass = 'badge-soft-primary';
                                if ($plan['status'] === 'Approved') $badgeClass = 'badge-soft-success';
                                if ($plan['status'] === 'Rejected') $badgeClass = 'badge-soft-danger';
                            ?>
                            <span class="badge <?= $badgeClass ?> rounded-pill"><?= htmlspecialchars($plan['status']) ?></span>
                        </div>
                        <div class="d-flex gap-3">
                            <?php if ($plan['status'] === 'Pending'): ?>
                                <i class="bi bi-pencil action-icon edit" title="Edit"></i>
                                <a href="?delete=<?= $plan['id'] ?>" onclick="return confirm('Are you sure you want to delete this activity plan?');"><i class="bi bi-trash action-icon delete" title="Delete"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <p class="text-muted small mb-3"><?= htmlspecialchars($plan['description']) ?></p>
                    
                    <div class="row text-muted small mb-1">
                        <div class="col-md-4 d-flex align-items-center">
                            <i class="bi bi-calendar3 me-2"></i> 
                            <?= date('n/j/Y', strtotime($plan['scheduled_date'])) ?> 
                            <?= $plan['scheduled_time'] ? 'at ' . date('H:i', strtotime($plan['scheduled_time'])) : '' ?>
                        </div>
                        <div class="col-md-4 d-flex align-items-center">
                            <i class="bi bi-geo-alt me-2"></i> <?= htmlspecialchars($plan['location'] ?? 'TBA') ?>
                        </div>
                        <div class="col-md-4 text-end">
                            Submitted: <?= date('n/j/Y', strtotime($plan['submitted_date'])) ?>
                        </div>
                    </div>
                    
                    <?php if ($plan['files_attached'] > 0): ?>
                    <div>
                        <span class="file-pill"><i class="bi bi-file-earmark-pdf"></i> <?= $plan['files_attached'] ?> document(s)</span>
                    </div>
                    <?php endif; ?>
                    
                    <div>
                        <a class="obj-toggle" data-bs-toggle="collapse" href="#collapseObj<?= $plan['id'] ?>" role="button" aria-expanded="false" aria-controls="collapseObj<?= $plan['id'] ?>">
                            <i class="bi bi-caret-right-fill" style="font-size: 0.7rem;"></i> View Objectives
                        </a>
                        <div class="collapse" id="collapseObj<?= $plan['id'] ?>">
                            <div class="obj-content">
                                <?= nl2br(htmlspecialchars($plan['objectives'])) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">No activity plans submitted yet.</p>
        <?php endif; ?>
    </div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggleFormBtn');
    const cancelBtnBottom = document.getElementById('cancelFormBtnBottom');
    const formContainer = document.getElementById('activityFormContainer');
    const fileUpload = document.getElementById('fileUpload');
    const fileList = document.getElementById('fileList');
    
    let isFormVisible = false;

    function toggleForm() {
        isFormVisible = !isFormVisible;
        if (isFormVisible) {
            formContainer.style.display = 'block';
            toggleBtn.innerHTML = 'Cancel';
        } else {
            formContainer.style.display = 'none';
            toggleBtn.innerHTML = '+ New Activity Plan';
        }
    }

    toggleBtn.addEventListener('click', toggleForm);
    cancelBtnBottom.addEventListener('click', toggleForm);
    
    // File input name logic
    fileUpload.addEventListener('change', function() {
        if(this.files.length > 0) {
            fileList.textContent = this.files.length + " file(s) selected";
        } else {
            fileList.textContent = '';
        }
    });

    // Objectives caret rotation toggle
    document.querySelectorAll('.obj-toggle').forEach(toggle => {
        toggle.addEventListener('click', function() {
            let icon = this.querySelector('i');
            if(this.classList.contains('collapsed')) {
                icon.classList.remove('bi-caret-down-fill');
                icon.classList.add('bi-caret-right-fill');
            } else {
                icon.classList.remove('bi-caret-right-fill');
                icon.classList.add('bi-caret-down-fill');
            }
        });
    });
});
</script>
</body>
</html>