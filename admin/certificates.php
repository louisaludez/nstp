<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

require 'controllers/CertificatesController.php';

$extra_css = ['../assets/css/style.css'];
include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

<div class="flex-grow-1 p-4 p-lg-5 w-100">

    <?php include '../includes/topbar.php'; ?>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Certificate Overview</h4>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Click a row to view the student list</p>
        </div>
        <button type="button" class="btn btn-sm d-inline-flex align-items-center gap-2" style="background: #ECFDF5; color: #10B981; border: 1px dashed #10B981; border-radius: 8px; padding: 8px 16px; font-weight: 500;" onclick="alert('Import coming soon.')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            Import XLSX File of Grades
        </button>
    </div>

    <!-- ══════════════════════════════════════
         MAIN 2-COLUMN LAYOUT
    ══════════════════════════════════════ -->
    <div class="row g-4">

        <!-- LEFT: Certificate Batches -->
        <div class="col-lg-7 col-xl-8">
            <div class="d-flex flex-column gap-3">

                <!-- Batch list -->
                <?php if (count($cert_batches) > 0):
                    foreach ($cert_batches as $idx => $batch):
                        $ready = ($batch['passed_count'] > 0 && $batch['issued_count'] < $batch['passed_count']);
                        $statusClass = $ready ? 'background: #ECFDF5; color: #10B981;' : 'background: #FFF7ED; color: #F59E0B;';
                        $statusLabel = $ready ? 'Ready' : 'Reviewing';
                        $title = htmlspecialchars($batch['component']) === 'ROTC' 
                            ? "ROTC Basic Course — " . htmlspecialchars($batch['section_name']) 
                            : htmlspecialchars($batch['component']) . " Completion";
                ?>
                <div class="dash-panel d-flex align-items-center justify-content-between p-3 px-4" style="border-radius: 12px; border: 1px solid #E2E8F0; background: white; cursor: pointer;">
                    <div class="d-flex align-items-center gap-3">
                        <!-- Award Icon -->
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: #6366F1; color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                        </div>

                        <!-- Info -->
                        <div>
                            <div style="font-size: 0.95rem; font-weight: 600; color: #0F172A; margin-bottom: 2px;">
                                <?= $title ?>
                            </div>
                            <div style="font-size: 0.75rem; color: #64748B;">
                                <?= (int)$batch['student_count'] ?> students
                                <?php if ($batch['passed_count'] > 0): ?>
                                    · Eligible <?= date('M j', strtotime('+17 days')) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Status + Action -->
                    <div class="d-flex align-items-center gap-3">
                        <span style="font-size: 0.7rem; font-weight: 600; padding: 4px 10px; border-radius: 6px; <?= $statusClass ?>">
                            <?= $statusLabel ?>
                        </span>
                        <button class="btn btn-sm" style="background: #0F172A; color: white; border-radius: 8px; font-weight: 500; padding: 6px 16px; font-size: 0.8rem;" onclick="alert('Generating certificate for <?= htmlspecialchars($batch['section_name']) ?>…')">
                            Generate
                        </button>
                    </div>
                </div>
                <?php endforeach; else: ?>

                <!-- Empty state -->
                <div class="dash-panel text-center py-5 px-4" style="border-radius: 12px; border: 1px solid #E2E8F0; background: white;">
                    <div style="width: 56px; height: 56px; border-radius: 50%; background: #EEF2FF;
                                display: flex; align-items: center; justify-content: center;
                                margin: 0 auto 14px; font-size: 1.4rem; color: #6366F1;">
                        <i class="bi bi-award"></i>
                    </div>
                    <p class="fw-semibold mb-1" style="color: var(--text-dark); font-size: 0.9rem;">
                        No certificate batches yet
                    </p>
                    <p class="text-muted small mb-0">
                        Batches appear here once sections have enrolled students.
                    </p>
                </div>

                <?php endif; ?>
            </div>
        </div>

        <!-- RIGHT: Recently Issued -->
        <div class="col-lg-5 col-xl-4">
            <div class="dash-panel p-0" style="overflow: hidden; border-radius: 12px; border: 1px solid #E2E8F0;">

                <div class="px-4 py-3 border-bottom" style="border-color: #E2E8F0 !important;">
                    <h6 class="fw-bold mb-0" style="font-size: 0.95rem; color: #111827;">Recently Issued</h6>
                </div>

                <div class="d-flex flex-column pt-2 pb-2">
                    <?php if (count($recent_certs) > 0):
                        foreach ($recent_certs as $idx => $cert):
                            $c = certComponentColor($cert['component']);
                            $relDate = relativeDate($cert['updated_at']);
                    ?>
                    <div class="d-flex align-items-center gap-3 px-4 py-3" style="transition: background-color 0.15s; cursor: pointer;" onmouseover="this.style.backgroundColor='#F8FAFC'" onmouseout="this.style.backgroundColor='transparent'">

                        <!-- Check icon -->
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: #F0FDF4; border: 1px solid #BBF7D0; color: #10B981; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>

                        <!-- Info -->
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 0.85rem; font-weight: 500; color: #0F172A; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px;">
                                <?= htmlspecialchars($cert['full_name']) ?>
                            </div>
                            <div style="font-size: 0.75rem; color: #64748B; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= htmlspecialchars($cert['component']) ?> · <?= htmlspecialchars($cert['serial_number'] ?? '2024-XXXXX') ?>
                            </div>
                        </div>

                        <!-- Date -->
                        <div style="font-size: 0.75rem; color: #64748B; flex-shrink: 0;">
                            <?= $relDate ?>
                        </div>
                    </div>
                    <?php endforeach; else: ?>

                <div class="text-center py-5 px-4">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--bg-light);
                                display: flex; align-items: center; justify-content: center;
                                margin: 0 auto 12px; color: var(--text-muted); font-size: 1.2rem;">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <p class="text-muted small mb-0">No certificates issued yet.</p>
                </div>

                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

</div>

<style>
/* ══════════════════════════════════════════
   CERTIFICATES PAGE — Figma-match styles
══════════════════════════════════════════ */

/* Certificate batch rows */
.cert-batch-row {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 18px 24px;
    transition: background-color 0.15s;
}
.cert-batch-row:hover { background-color: #F9FAFB; }

.cert-batch-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

.cert-batch-info { flex: 1; min-width: 0; }

.cert-batch-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--primary-accent);
    margin-bottom: 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.cert-batch-meta {
    font-size: 0.75rem;
    color: var(--text-muted);
}

.cert-batch-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
}

.cert-generate-btn {
    background: #111827;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 7px 16px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.15s;
    font-family: 'Inter', sans-serif;
}
.cert-generate-btn:hover { background: #1F2937; }

/* Recently issued rows */
.cert-issued-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 20px;
    transition: background-color 0.15s;
}
.cert-issued-row:hover { background-color: #F9FAFB; }

.cert-issued-icon { flex-shrink: 0; }

.cert-issued-info {
    flex: 1;
    min-width: 0;
}
.cert-issued-name {
    font-size: 0.83rem;
    font-weight: 600;
    color: var(--text-dark);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cert-issued-meta {
    font-size: 0.71rem;
    font-weight: 500;
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.cert-issued-date {
    font-size: 0.72rem;
    color: var(--text-muted);
    flex-shrink: 0;
    white-space: nowrap;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
