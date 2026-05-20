<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

require 'controllers/SubmissionsController.php';

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

    <?php if ($message): ?>
        <div class="alert alert-<?= $msgType ?> alert-dismissible fade show rounded-3 mb-4">
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
                    <p class="text-muted small mb-0">4 awaiting review</p>
                </div>
                
                <div class="queue-list d-flex flex-column">
                    <!-- Active Item -->
                    <a href="#" class="p-3 border-bottom text-decoration-none d-flex gap-3 align-items-start" style="background: rgba(99,102,241,0.05); border-left: 3px solid var(--primary-accent);">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: #FFF7ED; color: #F97316; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <div style="min-width: 0; flex: 1;">
                            <h6 class="mb-1 text-truncate" style="font-size: 0.9rem; color: var(--text-dark); font-weight: 600; margin-top: 2px;">Tree-Planting Drive Report</h6>
                            <p class="mb-0 text-truncate" style="font-size: 0.75rem; color: var(--text-muted);">Prof. Tan · CWTS 1-A</p>
                        </div>
                        <div class="text-end flex-shrink-0" style="min-width: 50px;">
                            <span style="font-size: 0.7rem; color: #EF4444; font-weight: 500; background: #FEE2E2; padding: 2px 6px; border-radius: 4px;">2h ago</span>
                            <div class="mt-1"><i class="bi bi-chevron-right text-muted" style="font-size: 0.75rem;"></i></div>
                        </div>
                    </a>

                    <!-- Queue Item 2 -->
                    <a href="#" class="p-3 border-bottom text-decoration-none bg-white transition-all hover-bg-light d-flex gap-3 align-items-start">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: #FFF7ED; color: #F97316; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <div style="min-width: 0; flex: 1;">
                            <h6 class="mb-1 text-truncate" style="font-size: 0.9rem; color: var(--text-dark); font-weight: 500; margin-top: 2px;">Adult Literacy Session #4</h6>
                            <p class="mb-0 text-truncate" style="font-size: 0.75rem; color: var(--text-muted);">Prof. Santos · LTS 2-A</p>
                        </div>
                        <div class="text-end flex-shrink-0" style="min-width: 50px;">
                            <span class="text-muted" style="font-size: 0.7rem;">Yesterday</span>
                            <div class="mt-1"><i class="bi bi-chevron-right text-muted" style="font-size: 0.75rem;"></i></div>
                        </div>
                    </a>

                    <!-- Queue Item 3 -->
                    <a href="#" class="p-3 border-bottom text-decoration-none bg-white transition-all hover-bg-light d-flex gap-3 align-items-start">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: #FFF7ED; color: #F97316; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <div style="min-width: 0; flex: 1;">
                            <h6 class="mb-1 text-truncate" style="font-size: 0.9rem; color: var(--text-dark); font-weight: 500; margin-top: 2px;">Barangay Clean-Up Plan</h6>
                            <p class="mb-0 text-truncate" style="font-size: 0.75rem; color: var(--text-muted);">Prof. Cruz · CWTS 1-C</p>
                        </div>
                        <div class="text-end flex-shrink-0" style="min-width: 50px;">
                            <span class="text-muted" style="font-size: 0.7rem;">May 9</span>
                            <div class="mt-1"><i class="bi bi-chevron-right text-muted" style="font-size: 0.75rem;"></i></div>
                        </div>
                    </a>

                    <!-- Queue Item 4 -->
                    <a href="#" class="p-3 border-bottom text-decoration-none bg-white transition-all hover-bg-light d-flex gap-3 align-items-start">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: #FFF7ED; color: #F97316; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <div style="min-width: 0; flex: 1;">
                            <h6 class="mb-1 text-truncate" style="font-size: 0.9rem; color: var(--text-dark); font-weight: 500; margin-top: 2px;">Reading Buddies Kick-off</h6>
                            <p class="mb-0 text-truncate" style="font-size: 0.75rem; color: var(--text-muted);">Prof. Garcia · LTS 2-B</p>
                        </div>
                        <div class="text-end flex-shrink-0" style="min-width: 50px;">
                            <span class="text-muted" style="font-size: 0.7rem;">May 8</span>
                            <div class="mt-1"><i class="bi bi-chevron-right text-muted" style="font-size: 0.75rem;"></i></div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Panel: Detail View -->
        <div class="col-lg-7 col-xl-8">
            <div class="dash-panel p-4 p-xl-5" style="border-radius: 12px;">
                
                <div class="mb-4">
                    <h4 class="fw-bold mb-1" style="color: var(--text-dark);">Tree-Planting Drive Report</h4>
                    <p class="text-muted" style="font-size: 0.9rem;">Prof. Tan · CWTS 1-A</p>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="p-3 rounded-3" style="background: var(--bg-light); border: 1px solid var(--border-color);">
                            <div class="text-muted mb-1" style="font-size: 0.75rem;">Submitted</div>
                            <div class="fw-medium" style="font-size: 0.9rem; color: var(--text-dark);">2h ago</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3" style="background: var(--bg-light); border: 1px solid var(--border-color);">
                            <div class="text-muted mb-1" style="font-size: 0.75rem;">Beneficiaries</div>
                            <div class="fw-medium" style="font-size: 0.9rem; color: var(--text-dark);">42 community members</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                            <div class="text-muted mb-1" style="font-size: 0.75rem;">Attachments</div>
                            <div class="fw-medium d-flex align-items-center gap-1" style="font-size: 0.9rem; color: #4F46E5;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                                6 photos · 1 PDF
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-5">
                    <p style="font-size: 0.95rem; color: #4B5563; line-height: 1.6;">
                        Activity executed on schedule at Barangay San Roque, with 28 cadets and 14 community volunteers participating. Total of 120 saplings planted across the riverside zone. All required documentation, attendance sheet, and beneficiary feedback forms attached.
                    </p>
                </div>

                <div class="d-flex gap-3 pt-4 border-top" style="border-color: var(--border-color) !important;">
                    <button class="btn btn-sm d-inline-flex align-items-center gap-2" style="background: #059669; color: white; border: none; border-radius: 6px; padding: 8px 16px; font-weight: 500;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> Approve
                    </button>
                    <button class="btn btn-sm d-inline-flex align-items-center gap-2" style="background: white; color: #64748B; border: 1px solid #E2E8F0; border-radius: 6px; padding: 8px 16px; font-weight: 500;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> Request Revisions
                    </button>
                    <button class="btn btn-sm d-inline-flex align-items-center gap-2" style="background: white; color: #EF4444; border: 1px solid #FEE2E2; border-radius: 6px; padding: 8px 16px; font-weight: 500;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> Reject
                    </button>
                </div>

            </div>
        </div>
    </div>

</div>

<style>
.hover-bg-light:hover { background-color: #F9FAFB !important; cursor: pointer; }
.transition-all { transition: all 0.2s ease; }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
