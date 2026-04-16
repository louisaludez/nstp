<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login");
    exit;
}

require 'controllers/SubmissionsController.php';

$extra_css = ['../assets/css/pages/admin/submissions.css'];
$extra_js = ['../assets/js/pages/admin/submissions.js'];
include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

<div class="flex-grow-1 p-5 w-100" style="background-color: #F9FAFB;">

    <?php include '../includes/topbar.php'; ?>
    <div class="mb-4">
        <h3 class="fw-bold mb-1" style="color: #111827;">Instructor Submissions</h3>
        <p class="text-muted">Monitor and review activity plans and accomplishment reports from instructors</p>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $msgType ?> alert-dismissible fade show rounded-3">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="stat-card stat-blue d-flex flex-column justify-content-between"><i class="bi bi-file-earmark-text fs-4 mb-2"></i><div><h3 class="fw-bold mb-0"><?= $total_plans ?></h3><span class="small fw-medium">Total Activity Plans</span></div></div></div>
        <div class="col-md-3"><div class="stat-card stat-yellow d-flex flex-column justify-content-between"><i class="bi bi-clock-history fs-4 mb-2"></i><div><h3 class="fw-bold mb-0"><?= $pending_plans ?></h3><span class="small fw-medium">Pending Review</span></div></div></div>
        <div class="col-md-3"><div class="stat-card stat-green d-flex flex-column justify-content-between"><i class="bi bi-check-circle fs-4 mb-2"></i><div><h3 class="fw-bold mb-0"><?= $approved_plans ?></h3><span class="small fw-medium">Approved Plans</span></div></div></div>
        <div class="col-md-3"><div class="stat-card stat-purple d-flex flex-column justify-content-between"><i class="bi bi-journal-text fs-4 mb-2"></i><div><h3 class="fw-bold mb-0"><?= $total_reports ?></h3><span class="small fw-medium">Accomplishment Reports</span></div></div></div>
    </div>

    <div class="bg-white border rounded-4 p-4 shadow-sm">
        
        <ul class="nav nav-tabs nav-tabs-custom" id="submissionTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="plans-tab" data-bs-toggle="tab" data-bs-target="#plans" type="button" role="tab" aria-controls="plans" aria-selected="true">Activity Plans (<?= $total_plans ?>)</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="reports-tab" data-bs-toggle="tab" data-bs-target="#reports" type="button" role="tab" aria-controls="reports" aria-selected="false">Accomplishment Reports (<?= $total_reports ?>)</button>
            </li>
        </ul>

        <div class="tab-content" id="submissionTabsContent">
            
            <div class="tab-pane fade show active" id="plans" role="tabpanel">
                <div class="search-filter-bar">
                    <div class="search-wrapper"><i class="bi bi-search"></i><input type="text" placeholder="Search by title, instructor, or section..."></div>
                    <button class="filter-btn"><i class="bi bi-funnel me-2"></i> All Status</button>
                </div>

                <?php foreach ($activity_plans as $plan): ?>
                    <div class="submission-card">
                        <div class="d-flex align-items-center mb-1">
                            <h5 class="fw-bold mb-0 me-2 text-dark"><?= htmlspecialchars($plan['title']) ?></h5>
                            <?php if ($plan['status'] === 'Approved'): ?>
                                <span class="badge-status bg-approved">Approved</span>
                            <?php else: ?>
                                <span class="badge-status bg-pending">Pending</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-muted small mb-2"><?= htmlspecialchars($plan['description'] ?? '') ?></p>
                        <div class="d-flex align-items-center gap-3 text-dark small fw-medium mb-3">
                            <span><?= htmlspecialchars($plan['instructor']) ?></span>
                            <span class="text-muted">•</span>
                            <span class="text-muted"><?= htmlspecialchars($plan['section_name'] ?? 'N/A') ?></span>
                            <span class="text-muted">•</span>
                            <span class="text-muted"><?= $plan['files_attached'] ?> files attached</span>
                        </div>
                        <div class="row text-muted small mb-1">
                            <div class="col-md-4"><i class="bi bi-calendar3 me-1"></i> Scheduled: <?= $plan['scheduled_date'] ?></div>
                            <div class="col-md-4"><i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($plan['location'] ?? 'TBA') ?></div>
                            <div class="col-md-4 text-end">Submitted: <?= $plan['submitted_date'] ?></div>
                        </div>
                        <a class="obj-toggle" data-bs-toggle="collapse" href="#objCollapse<?= $plan['id'] ?>" role="button">
                            <i class="bi bi-caret-right-fill" style="font-size: 0.7rem;"></i> View Objectives
                        </a>
                        <div class="collapse mb-3" id="objCollapse<?= $plan['id'] ?>">
                            <div class="border-start border-primary border-2 ps-3 py-1 small text-muted">
                                <?= htmlspecialchars($plan['objectives'] ?? '') ?>
                            </div>
                        </div>
                        <?php if ($plan['status'] === 'Pending'): ?>
                            <div class="d-flex gap-2 mt-2">
                                <form method="POST" action="" class="d-inline">
                                    <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">
                                    <button type="submit" name="approve_plan" class="btn-approve"><i class="bi bi-check-circle me-1"></i> Approve</button>
                                </form>
                                <form method="POST" action="" class="d-inline">
                                    <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">
                                    <button type="submit" name="reject_plan" class="btn-reject"><i class="bi bi-x-circle me-1"></i> Reject</button>
                                </form>
                                <button type="button" class="btn-details"><i class="bi bi-eye me-1"></i> View Details</button>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="tab-pane fade" id="reports" role="tabpanel">
                <div class="search-filter-bar">
                    <div class="search-wrapper"><i class="bi bi-search"></i><input type="text" placeholder="Search by title, instructor, or section..."></div>
                    <button class="filter-btn"><i class="bi bi-funnel me-2"></i> All Status</button>
                </div>

                <?php foreach ($accomplishment_reports as $report): ?>
                    <div class="submission-card">
                        <div class="d-flex align-items-center mb-1">
                            <h5 class="fw-bold mb-0 me-2 text-dark"><?= htmlspecialchars($report['title']) ?></h5>
                            <?php if ($report['status'] === 'Reviewed'): ?>
                                <span class="badge-status bg-reviewed">Reviewed</span>
                            <?php else: ?>
                                <span class="badge-status bg-pending">Pending</span>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex align-items-center gap-3 text-dark small fw-medium mb-3">
                            <span><?= htmlspecialchars($report['instructor']) ?></span>
                            <span class="text-muted">•</span>
                            <span class="text-muted"><?= htmlspecialchars($report['section_name'] ?? 'N/A') ?></span>
                            <span class="text-muted">•</span>
                            <span class="text-muted"><?= $report['files_attached'] ?> files attached</span>
                        </div>
                        <div class="row text-muted small mb-3">
                            <div class="col-md-4"><i class="bi bi-calendar3 me-1"></i> Completed: <?= $report['completed_date'] ?></div>
                            <div class="col-md-4"><i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($report['location'] ?? 'TBA') ?></div>
                            <div class="col-md-4"><i class="bi bi-people me-1"></i> <?= $report['participants_count'] ?> participants</div>
                        </div>
                        <h6 class="fw-bold mb-0" style="font-size: 0.9rem; color: #374151;">Accomplishments:</h6>
                        <div class="accomplishment-block mb-3"><?= htmlspecialchars($report['accomplishments'] ?? '') ?></div>
                        <div class="text-muted small mb-3">Submitted: <?= $report['submitted_date'] ?></div>
                        <?php if ($report['status'] === 'Pending'): ?>
                            <div class="d-flex gap-2">
                                <form method="POST" action="" class="d-inline">
                                    <input type="hidden" name="report_id" value="<?= $report['id'] ?>">
                                    <button type="submit" name="review_report" class="btn-approve"><i class="bi bi-check2-circle me-1"></i> Mark as Reviewed</button>
                                </form>
                                <button type="button" class="btn-details"><i class="bi bi-eye me-1"></i> View Details</button>
                                <button type="button" class="btn-download"><i class="bi bi-download me-1"></i> Download Files</button>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/pages/submissions.js"></script>
</body>
</html>
