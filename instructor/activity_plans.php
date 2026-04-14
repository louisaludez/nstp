<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Instructor') {
    header("Location: ../login");
    exit;
}

require 'controllers/ActivityPlansController.php';

$extra_css = ['../assets/css/pages/instructor/activity-plans.css'];
$extra_js = ['../assets/js/pages/instructor/activity-plans.js'];
include '../includes/header.php';
include '../includes/instructor_sidebar.php';
?>

<div class="flex-grow-1 p-5 w-100" style="background-color: #F9FAFB;">
    
    <?php include '../includes/topbar.php'; ?>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: #111827;">Activity Plan Submission</h3>
            <p class="text-muted mb-0">Submit and manage planned NSTP activities for your section</p>
        </div>
        <button type="button" class="btn btn-brand" id="toggleFormBtn">+ New Activity Plan</button>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $msgType ?> alert-dismissible fade show rounded-3">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="panel-container" id="activityFormContainer" style="display: none;">
        <h5 class="fw-bold mb-4">Create New Activity Plan</h5>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="mb-4"><label class="form-label">Activity Title *</label><input type="text" name="title" class="form-control" placeholder="e.g., Community Clean-up Drive" required></div>
            <div class="mb-4"><label class="form-label">Description *</label><textarea name="description" class="form-control" rows="3" placeholder="Describe the activity in detail..." required></textarea></div>
            <div class="mb-4"><label class="form-label">Objectives *</label><textarea name="objectives" class="form-control" rows="2" placeholder="List the objectives and expected outcomes..." required></textarea></div>
            <div class="row g-4 mb-4">
                <div class="col-md-6"><label class="form-label">Scheduled Date *</label><input type="date" name="scheduled_date" class="form-control" required></div>
                <div class="col-md-6"><label class="form-label">Scheduled Time *</label><input type="time" name="scheduled_time" class="form-control" required></div>
            </div>
            <div class="mb-4"><label class="form-label">Location *</label><input type="text" name="location" class="form-control" placeholder="e.g., Sunset Bay Beach" required></div>
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
                        <div class="col-md-4 d-flex align-items-center"><i class="bi bi-calendar3 me-2"></i> <?= date('n/j/Y', strtotime($plan['scheduled_date'])) ?> <?= $plan['scheduled_time'] ? 'at ' . date('H:i', strtotime($plan['scheduled_time'])) : '' ?></div>
                        <div class="col-md-4 d-flex align-items-center"><i class="bi bi-geo-alt me-2"></i> <?= htmlspecialchars($plan['location'] ?? 'TBA') ?></div>
                        <div class="col-md-4 text-end">Submitted: <?= date('n/j/Y', strtotime($plan['submitted_date'])) ?></div>
                    </div>
                    <?php if ($plan['files_attached'] > 0): ?>
                    <div><span class="file-pill"><i class="bi bi-file-earmark-pdf"></i> <?= $plan['files_attached'] ?> document(s)</span></div>
                    <?php endif; ?>
                    <div>
                        <a class="obj-toggle" data-bs-toggle="collapse" href="#collapseObj<?= $plan['id'] ?>" role="button" aria-expanded="false">
                            <i class="bi bi-caret-right-fill" style="font-size: 0.7rem;"></i> View Objectives
                        </a>
                        <div class="collapse" id="collapseObj<?= $plan['id'] ?>">
                            <div class="obj-content"><?= nl2br(htmlspecialchars($plan['objectives'])) ?></div>
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
<script src="../assets/js/pages/instructor-activity-plans.js"></script>
</body>
</html>
