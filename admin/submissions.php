<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

require 'controllers/SubmissionsController.php';

function format_relative_time($datetime) {
    $time = strtotime($datetime);
    $diff = max(0, time() - $time); // Prevent negative diffs
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    if ($diff < 172800) return 'Yesterday';
    return date('M j', $time);
}

$queue = [];
foreach ($activity_plans as $plan) {
    if ($plan['status'] === 'Pending') {
        $plan['item_type'] = 'plan';
        $queue[] = $plan;
    }
}
foreach ($accomplishment_reports as $report) {
    if ($report['status'] === 'Pending') {
        $report['item_type'] = 'report';
        $queue[] = $report;
    }
}
usort($queue, function($a, $b) {
    return strtotime($b['submitted_date']) - strtotime($a['submitted_date']);
});

$active_id = $_GET['id'] ?? null;
$active_type = $_GET['type'] ?? null;
$active_item = null;

if ($active_id && $active_type) {
    foreach ($queue as $item) {
        if ($item['id'] == $active_id && $item['item_type'] === $active_type) {
            $active_item = $item;
            break;
        }
    }
} 
if (!$active_item && count($queue) > 0) {
    $active_item = $queue[0];
    $active_id = $active_item['id'];
    $active_type = $active_item['item_type'];
}

$extra_css = ['../assets/css/style.css'];
include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

<div class="flex-grow-1 p-4 p-lg-5 w-100">

    <?php include '../includes/topbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Approval Overview</h4>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Review and approve activity & accomplishment reports</p>
        </div>
        <button type="button" class="btn btn-sm d-inline-flex align-items-center gap-2" style="background: white; color: #1E293B; border: 1px solid #E2E8F0; border-radius: 8px; padding: 8px 16px; font-weight: 500;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Export Queue
        </button>
    </div>

    <?php if (isset($message) && $message): ?>
        <div class="alert alert-<?= $msgType ?? 'info' ?> alert-dismissible fade show rounded-3 mb-4">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Left Panel: Pending Queue -->
        <div class="col-lg-5 col-xl-4">
            <div class="dash-panel p-0" style="border-radius: 12px; overflow: hidden;">
                <div class="p-4 border-bottom">
                    <h6 class="fw-bold mb-1" style="color: var(--text-dark);">Pending Queue</h6>
                    <p class="text-muted small mb-0"><?= count($queue) ?> awaiting review</p>
                </div>
                
                <div class="queue-list d-flex flex-column" style="max-height: 600px; overflow-y: auto;">
                    <?php foreach ($queue as $item): ?>
                        <?php 
                        $isActive = ($active_item && $active_item['id'] == $item['id'] && $active_item['item_type'] == $item['item_type']); 
                        $itemUrl = "?id={$item['id']}&type={$item['item_type']}";
                        $timeStr = format_relative_time($item['submitted_date']);
                        $isRecent = str_contains($timeStr, 'h ago') || str_contains($timeStr, 'm ago') || $timeStr === 'Just now';
                        $iconBg = $item['item_type'] === 'plan' ? '#EFF6FF' : '#FFF7ED';
                        $iconColor = $item['item_type'] === 'plan' ? '#3B82F6' : '#F97316';
                        ?>
                        <a href="<?= $itemUrl ?>" class="p-3 border-bottom text-decoration-none d-flex gap-3 align-items-start transition-all <?= $isActive ? '' : 'bg-white hover-bg-light' ?>" 
                           style="<?= $isActive ? 'background: rgba(99,102,241,0.05); border-left: 3px solid var(--primary-accent);' : 'border-left: 3px solid transparent;' ?>">
                            
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: <?= $iconBg ?>; color: <?= $iconColor ?>; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="bi <?= $item['item_type'] === 'plan' ? 'bi-calendar-event' : 'bi-file-earmark-text' ?>"></i>
                            </div>
                            <div style="min-width: 0; flex: 1;">
                                <h6 class="mb-1 text-truncate" style="font-size: 0.9rem; color: var(--text-dark); font-weight: <?= $isActive ? '600' : '500' ?>; margin-top: 2px;">
                                    <?= htmlspecialchars($item['title']) ?>
                                </h6>
                                <p class="mb-0 text-truncate" style="font-size: 0.75rem; color: var(--text-muted);">
                                    <?= htmlspecialchars($item['instructor']) ?> &middot; <?= htmlspecialchars($item['section_name'] ?? 'N/A') ?>
                                </p>
                            </div>
                            <div class="text-end flex-shrink-0" style="min-width: 50px;">
                                <?php if ($isRecent): ?>
                                    <span style="font-size: 0.7rem; color: #EF4444; font-weight: 500; background: #FEE2E2; padding: 2px 6px; border-radius: 4px;"><?= $timeStr ?></span>
                                <?php else: ?>
                                    <span class="text-muted" style="font-size: 0.7rem;"><?= $timeStr ?></span>
                                <?php endif; ?>
                                <div class="mt-1"><i class="bi bi-chevron-right text-muted" style="font-size: 0.75rem;"></i></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                    
                    <?php if (empty($queue)): ?>
                        <div class="p-4 text-center text-muted" style="font-size: 0.85rem;">
                            No items currently awaiting review.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Panel: Detail View -->
        <div class="col-lg-7 col-xl-8">
            <?php if ($active_item): ?>
            <div class="dash-panel p-4 p-xl-5" style="border-radius: 12px;">
                
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge" style="background: <?= $active_item['item_type'] === 'plan' ? '#EFF6FF' : '#FFF7ED' ?>; color: <?= $active_item['item_type'] === 'plan' ? '#3B82F6' : '#F97316' ?>; border: 1px solid <?= $active_item['item_type'] === 'plan' ? '#BFDBFE' : '#FFEDD5' ?>; font-weight: 500;">
                            <?= $active_item['item_type'] === 'plan' ? 'Activity Plan' : 'Accomplishment Report' ?>
                        </span>
                    </div>
                    <h4 class="fw-bold mb-1" style="color: var(--text-dark);"><?= htmlspecialchars($active_item['title']) ?></h4>
                    <p class="text-muted" style="font-size: 0.9rem;"><?= htmlspecialchars($active_item['instructor']) ?> &middot; <?= htmlspecialchars($active_item['section_name'] ?? 'N/A') ?></p>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="p-3 rounded-3" style="background: var(--bg-light); border: 1px solid var(--border-color);">
                            <div class="text-muted mb-1" style="font-size: 0.75rem;">Submitted</div>
                            <div class="fw-medium" style="font-size: 0.9rem; color: var(--text-dark);"><?= format_relative_time($active_item['submitted_date']) ?></div>
                        </div>
                    </div>
                    
                    <?php if ($active_type === 'plan'): ?>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3" style="background: var(--bg-light); border: 1px solid var(--border-color);">
                            <div class="text-muted mb-1" style="font-size: 0.75rem;">Scheduled Date</div>
                            <div class="fw-medium" style="font-size: 0.9rem; color: var(--text-dark);">
                                <?= !empty($active_item['scheduled_date']) ? date('M j, Y', strtotime($active_item['scheduled_date'])) : 'TBD' ?>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3" style="background: var(--bg-light); border: 1px solid var(--border-color);">
                            <div class="text-muted mb-1" style="font-size: 0.75rem;">Beneficiaries</div>
                            <div class="fw-medium" style="font-size: 0.9rem; color: var(--text-dark);">
                                <?= htmlspecialchars($active_item['participants_count'] ?? '0') ?> community members
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="col-md-4">
                        <div class="p-3 rounded-3" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                            <div class="text-muted mb-1" style="font-size: 0.75rem;">Attachments</div>
                            <div class="fw-medium d-flex align-items-center gap-1" style="font-size: 0.9rem; color: #4F46E5;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                                <?= $active_item['files_attached'] > 0 ? $active_item['files_attached'] . ' file(s)' : 'None' ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-5">
                    <h6 class="text-muted mb-2" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <?= $active_type === 'plan' ? 'Activity Description' : 'Accomplishments' ?>
                    </h6>
                    <p style="font-size: 0.95rem; color: #4B5563; line-height: 1.6;">
                        <?php 
                            $content = $active_type === 'plan' ? ($active_item['description'] ?? '') : ($active_item['accomplishments'] ?? '');
                            echo nl2br(htmlspecialchars(empty($content) ? 'No description provided.' : $content));
                        ?>
                    </p>
                    
                    <?php if ($active_type === 'plan' && !empty($active_item['objectives'])): ?>
                        <h6 class="text-muted mt-4 mb-2" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Objectives</h6>
                        <p style="font-size: 0.95rem; color: #4B5563; line-height: 1.6;">
                            <?= nl2br(htmlspecialchars($active_item['objectives'])) ?>
                        </p>
                    <?php endif; ?>
                    
                    <?php if (!empty($active_item['location'])): ?>
                        <div class="mt-4 d-flex align-items-center gap-2 text-muted" style="font-size: 0.9rem;">
                            <i class="bi bi-geo-alt"></i> Location: <span class="fw-medium text-dark"><?= htmlspecialchars($active_item['location']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <form method="POST" class="d-flex gap-3 pt-4 border-top flex-wrap" style="border-color: var(--border-color) !important;">
                    <?php if (!empty($signature_path)): ?>
                        <div class="w-100 mb-2">
                            <span class="text-muted small d-block mb-1">Your signature to be attached:</span>
                            <img src="../<?= htmlspecialchars($signature_path) ?>" alt="Signature" style="max-height: 40px; border: 1px solid #E2E8F0; padding: 2px; border-radius: 4px;">
                        </div>
                    <?php endif; ?>
                    <?php if ($active_type === 'plan'): ?>
                        <input type="hidden" name="plan_id" value="<?= $active_item['id'] ?>">
                        <button type="submit" name="approve_plan" class="btn btn-sm d-inline-flex align-items-center gap-2" style="background: #059669; color: white; border: none; border-radius: 6px; padding: 8px 16px; font-weight: 500;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> Approve Plan
                        </button>
                        <button type="button" class="btn btn-sm d-inline-flex align-items-center gap-2" style="background: white; color: #64748B; border: 1px solid #E2E8F0; border-radius: 6px; padding: 8px 16px; font-weight: 500;" onclick="Swal.fire('Coming Soon', 'Requesting revisions will be supported in a future update.', 'info');">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> Request Revisions
                        </button>
                        <button type="button" class="btn btn-sm d-inline-flex align-items-center gap-2" style="background: white; color: #EF4444; border: 1px solid #FEE2E2; border-radius: 6px; padding: 8px 16px; font-weight: 500;" onclick="confirmReject(this)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> Reject
                        </button>
                        <!-- Hidden submit button for rejection -->
                        <input type="submit" name="reject_plan" id="real_reject_btn" class="d-none">
                    <?php else: ?>
                        <input type="hidden" name="report_id" value="<?= $active_item['id'] ?>">
                        <button type="submit" name="review_report" class="btn btn-sm d-inline-flex align-items-center gap-2" style="background: #059669; color: white; border: none; border-radius: 6px; padding: 8px 16px; font-weight: 500;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> Mark as Reviewed
                        </button>
                        <button type="button" class="btn btn-sm d-inline-flex align-items-center gap-2" style="background: white; color: #64748B; border: 1px solid #E2E8F0; border-radius: 6px; padding: 8px 16px; font-weight: 500;" onclick="Swal.fire('Coming Soon', 'Requesting revisions will be supported in a future update.', 'info');">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> Request Revisions
                        </button>
                    <?php endif; ?>
                </form>

            </div>
            <?php else: ?>
            <div class="dash-panel p-5 text-center d-flex flex-column align-items-center justify-content-center bg-white" style="border-radius: 12px; height: 100%; min-height: 400px; border: 1px dashed var(--border-color);">
                <div style="width: 64px; height: 64px; border-radius: 50%; background: #F8FAFC; display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                    <i class="bi bi-inbox text-muted" style="font-size: 1.5rem;"></i>
                </div>
                <h5 class="fw-bold mb-2" style="color: var(--text-dark);">All Caught Up!</h5>
                <p class="text-muted mb-0">There are no pending submissions to review at the moment.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<style>
.hover-bg-light:hover { background-color: #F9FAFB !important; cursor: pointer; }
.transition-all { transition: all 0.2s ease; }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function confirmReject(btn) {
    Swal.fire({
        title: 'Reject Plan?',
        text: "Are you sure you want to reject this plan? This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Yes, reject it'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('real_reject_btn').click();
        }
    });
}
</script>
</body>
</html>
