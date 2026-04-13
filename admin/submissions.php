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

// Handle Approve / Reject / Review Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve_plan'])) {
        $plan_id = $_POST['plan_id'];
        $pdo->prepare("UPDATE activity_plans SET status = 'Approved' WHERE id = ?")->execute([$plan_id]);
        
        $inst_stmt = $pdo->prepare("SELECT instructor_id, title FROM activity_plans WHERE id = ?");
        $inst_stmt->execute([$plan_id]);
        $plan = $inst_stmt->fetch();
        if ($plan) {
            $pdo->prepare("INSERT INTO notifications (user_id, message, link) VALUES (?, ?, ?)")
                ->execute([$plan['instructor_id'], "Your activity plan '{$plan['title']}' has been approved.", "activity_plans.php"]);
        }
        
        $message = "Activity Plan successfully approved.";
        $msgType = "success";
    } elseif (isset($_POST['reject_plan'])) {
        $plan_id = $_POST['plan_id'];
        $pdo->prepare("UPDATE activity_plans SET status = 'Rejected' WHERE id = ?")->execute([$plan_id]);
        
        $inst_stmt = $pdo->prepare("SELECT instructor_id, title FROM activity_plans WHERE id = ?");
        $inst_stmt->execute([$plan_id]);
        $plan = $inst_stmt->fetch();
        if ($plan) {
            $pdo->prepare("INSERT INTO notifications (user_id, message, link) VALUES (?, ?, ?)")
                ->execute([$plan['instructor_id'], "Your activity plan '{$plan['title']}' has been rejected.", "activity_plans.php"]);
        }
        
        $message = "Activity Plan has been rejected.";
        $msgType = "danger";
    } elseif (isset($_POST['review_report'])) {
        $report_id = $_POST['report_id'];
        $pdo->prepare("UPDATE accomplishment_reports SET status = 'Reviewed' WHERE id = ?")->execute([$report_id]);
        
        $inst_stmt = $pdo->prepare("SELECT instructor_id, title FROM accomplishment_reports WHERE id = ?");
        $inst_stmt->execute([$report_id]);
        $rep = $inst_stmt->fetch();
        if ($rep) {
            $pdo->prepare("INSERT INTO notifications (user_id, message, link) VALUES (?, ?, ?)")
                ->execute([$rep['instructor_id'], "Your accomplishment report '{$rep['title']}' has been reviewed.", "reports.php"]);
        }
        
        $message = "Accomplishment Report marked as reviewed.";
        $msgType = "success";
    }
}

// Fetch Activity Plans
$stmt_plans = $pdo->query("
    SELECT ap.*, u.full_name as instructor, s.section_name 
    FROM activity_plans ap
    JOIN users u ON ap.instructor_id = u.id
    LEFT JOIN sections s ON ap.section_id = s.id
    ORDER BY ap.submitted_date DESC
");
$activity_plans = $stmt_plans->fetchAll();

// Fetch Accomplishment Reports
$stmt_reps = $pdo->query("
    SELECT ar.*, u.full_name as instructor, s.section_name 
    FROM accomplishment_reports ar
    JOIN users u ON ar.instructor_id = u.id
    LEFT JOIN sections s ON ar.section_id = s.id
    ORDER BY ar.submitted_date DESC
");
$accomplishment_reports = $stmt_reps->fetchAll();

$total_plans = count($activity_plans);
$pending_plans = count(array_filter($activity_plans, function($p) { return $p['status'] === 'Pending'; }));
$approved_plans = count(array_filter($activity_plans, function($p) { return $p['status'] === 'Approved'; }));
$total_reports = count($accomplishment_reports);


include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

<style>
    /* Metric Cards */
    .stat-card { border-radius: 12px; padding: 24px; height: 100%; background-color: #fff; }
    .stat-blue { border: 1px solid #BFDBFE; background-color: #EFF6FF; }
    .stat-blue i, .stat-blue h3, .stat-blue span { color: #1D4ED8; }
    
    .stat-yellow { border: 1px solid #FDE68A; background-color: #FEF3C7; }
    .stat-yellow i, .stat-yellow h3, .stat-yellow span { color: #D97706; }
    
    .stat-green { border: 1px solid #BBF7D0; background-color: #F0FDF4; }
    .stat-green i, .stat-green h3, .stat-green span { color: #15803D; }
    
    .stat-purple { border: 1px solid #E9D5FF; background-color: #FAF5FF; }
    .stat-purple i, .stat-purple h3, .stat-purple span { color: #7E22CE; }

    /* Custom Nav Tabs */
    .nav-tabs-custom { border-bottom: 1px solid #E5E7EB; margin-bottom: 24px; }
    .nav-tabs-custom .nav-link { border: none; color: #6B7280; font-weight: 600; padding: 12px 24px; background: transparent; }
    .nav-tabs-custom .nav-link:hover { color: #111827; }
    .nav-tabs-custom .nav-link.active { color: #2563EB; border-bottom: 2px solid #2563EB; }

    /* Search Bar */
    .search-filter-bar { display: flex; gap: 16px; margin-bottom: 24px; }
    .search-wrapper { position: relative; flex-grow: 1; }
    .search-wrapper i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #9CA3AF; }
    .search-wrapper input { width: 100%; padding: 10px 16px 10px 40px; border: 1px solid #D1D5DB; border-radius: 8px; font-size: 0.95rem; }
    .filter-btn { border: 1px solid #D1D5DB; background-color: white; border-radius: 8px; padding: 8px 16px; color: #374151; font-weight: 500; }

    /* Submission Cards */
    .submission-card { border: 1px solid #E5E7EB; border-radius: 12px; padding: 24px; background-color: #fff; margin-bottom: 16px; }
    
    .badge-status { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .bg-approved, .bg-reviewed { background-color: #D1FAE5; color: #065F46; }
    .bg-pending { background-color: #FEF3C7; color: #92400E; }

    /* Action Buttons */
    .btn-approve { background-color: #10B981; color: white; border-radius: 8px; font-weight: 500; padding: 6px 20px; border: none; }
    .btn-approve:hover { background-color: #059669; }
    
    .btn-reject { background-color: #EF4444; color: white; border-radius: 8px; font-weight: 500; padding: 6px 20px; border: none; }
    .btn-reject:hover { background-color: #DC2626; }
    
    .btn-details { background-color: #F3F4F6; color: #374151; border-radius: 8px; font-weight: 500; padding: 6px 20px; border: none; }
    .btn-details:hover { background-color: #E5E7EB; }

    .btn-download { background-color: #E0E7FF; color: #4338CA; border-radius: 8px; font-weight: 500; padding: 6px 20px; border: none; }
    .btn-download:hover { background-color: #C7D2FE; }

    /* Misc */
    .obj-toggle { color: #2563EB; font-size: 0.85rem; font-weight: 500; text-decoration: none; cursor: pointer; display: inline-block; margin-top: 8px; margin-bottom: 12px;}
    .obj-toggle:hover { color: #1D4ED8; }
    
    .accomplishment-block {
        border-left: 2px solid #6EE7B7; /* Light green left border from Figma */
        padding-left: 12px;
        margin-top: 6px;
        color: #4B5563;
        font-size: 0.9rem;
    }
</style>

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
        <div class="col-md-3">
            <div class="stat-card stat-blue d-flex flex-column justify-content-between">
                <i class="bi bi-file-earmark-text fs-4 mb-2"></i>
                <div>
                    <h3 class="fw-bold mb-0"><?= $total_plans ?></h3>
                    <span class="small fw-medium">Total Activity Plans</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-yellow d-flex flex-column justify-content-between">
                <i class="bi bi-clock-history fs-4 mb-2"></i>
                <div>
                    <h3 class="fw-bold mb-0"><?= $pending_plans ?></h3>
                    <span class="small fw-medium">Pending Review</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-green d-flex flex-column justify-content-between">
                <i class="bi bi-check-circle fs-4 mb-2"></i>
                <div>
                    <h3 class="fw-bold mb-0"><?= $approved_plans ?></h3>
                    <span class="small fw-medium">Approved Plans</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-purple d-flex flex-column justify-content-between">
                <i class="bi bi-journal-text fs-4 mb-2"></i>
                <div>
                    <h3 class="fw-bold mb-0"><?= $total_reports ?></h3>
                    <span class="small fw-medium">Accomplishment Reports</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white border rounded-4 p-4 shadow-sm">
        
        <ul class="nav nav-tabs nav-tabs-custom" id="submissionTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="plans-tab" data-bs-target="#plans" type="button" role="tab">Activity Plans (<?= $total_plans ?>)</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="reports-tab" data-bs-target="#reports" type="button" role="tab">Accomplishment Reports (<?= $total_reports ?>)</button>
            </li>
        </ul>

        <div class="tab-content" id="submissionTabsContent">
            
            <div class="tab-pane fade show active" id="plans" role="tabpanel">
                
                <div class="search-filter-bar">
                    <div class="search-wrapper">
                        <i class="bi bi-search"></i>
                        <input type="text" placeholder="Search by title, instructor, or section...">
                    </div>
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
                    <div class="search-wrapper">
                        <i class="bi bi-search"></i>
                        <input type="text" placeholder="Search by title, instructor, or section...">
                    </div>
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
                        <div class="accomplishment-block mb-3">
                            <?= htmlspecialchars($report['accomplishments'] ?? '') ?>
                        </div>

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
<script>
    // Tab initialization workaround for Bootstrap 5 JSON Parse issue
    document.querySelectorAll('#submissionTabs button').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            var tab = new bootstrap.Tab(this);
            tab.show();
        });
    });

    // Simple toggle icon rotation for "View Objectives"
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
</script>
</body>
</html>