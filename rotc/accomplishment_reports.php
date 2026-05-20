<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ROTC') {
    header("Location: ../login.php");
    exit;
}

$extra_css = ['../assets/css/style.css'];
include '../includes/header.php';
include '../includes/rotc_sidebar.php';

$reports = [
    [
        'title'    => 'Q1 Tactical Drill Accomplishment',
        'subtitle' => 'Due May 18',
        'status'   => 'Draft'
    ],
    [
        'title'    => 'Civil-Military Operations Report',
        'subtitle' => 'Submitted May 8',
        'status'   => 'Under Review'
    ],
    [
        'title'    => 'Community Outreach — Brgy. San Roque',
        'subtitle' => 'Approved May 4',
        'status'   => 'Approved'
    ],
    [
        'title'    => 'Monthly Strength Report — May',
        'subtitle' => 'Revisions requested',
        'status'   => 'Revisions'
    ],
];

function renderReportStatus($status) {
    if ($status === 'Approved') {
        return '<span style="background:#ECFDF5; color:#10B981; font-size:0.7rem; font-weight:500; padding:4px 10px; border-radius:20px; display:inline-flex; align-items:center; gap:4px;"><i class="bi bi-check-circle" style="font-size:0.65rem;"></i> Approved</span>';
    } else if ($status === 'Under Review') {
        return '<span style="background:#EEF2FF; color:#6366F1; font-size:0.7rem; font-weight:500; padding:4px 10px; border-radius:20px; display:inline-flex; align-items:center; gap:4px;"><i class="bi bi-send" style="font-size:0.65rem;"></i> Under Review</span>';
    } else if ($status === 'Draft') {
        return '<span style="background:#F8FAFC; color:#64748B; font-size:0.7rem; font-weight:500; padding:4px 10px; border-radius:20px; display:inline-flex; align-items:center; gap:4px; border:1px solid #E2E8F0;"><i class="bi bi-pencil" style="font-size:0.65rem;"></i> Draft</span>';
    } else if ($status === 'Revisions') {
        return '<span style="background:#FEF2F2; color:#EF4444; font-size:0.7rem; font-weight:500; padding:4px 10px; border-radius:20px; display:inline-flex; align-items:center; gap:4px;"><i class="bi bi-exclamation-circle" style="font-size:0.65rem;"></i> Revisions</span>';
    }
    return $status;
}
?>
<div class="flex-grow-1 p-4 p-lg-5 w-100" style="background:#F8FAFC;min-height:100vh;">
    <?php include '../includes/topbar.php'; ?>
    
    <div class="mb-4 mt-2">
        <h5 class="fw-bold mb-1" style="color:#0F172A;font-size:1.15rem;">Report Submission Overview</h5>
        <div class="text-muted" style="font-size:0.85rem;">Document and submit completed ROTC activities</div>
    </div>

    <div class="row g-4">
        <!-- Left Column: All Reports -->
        <div class="col-lg-8">
            <div class="card" style="border: 1px solid #E2E8F0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); padding: 0; background: white;">
                <div style="padding: 20px 24px; border-bottom: 1px solid #F1F5F9;">
                    <h6 class="mb-0" style="color:#0F172A; font-weight:600; font-size:0.95rem;">All Reports</h6>
                </div>
                <div>
                    <?php foreach ($reports as $index => $r): ?>
                    <div style="padding: 20px 24px; border-bottom: 1px solid #F8FAFC; display: flex; justify-content: space-between; align-items: center; cursor: pointer; transition: background 0.15s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'" data-bs-toggle="modal" data-bs-target="#reportDetailsModal">
                        
                        <!-- Left Side: Icon & Titles -->
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 42px; height: 42px; border-radius: 10px; background: #F1F5F9; display: flex; align-items: center; justify-content: center; color: #64748B;">
                                <i class="bi bi-file-text" style="font-size: 1.1rem;"></i>
                            </div>
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 500; color: #0F172A; margin-bottom: 2px;"><?= htmlspecialchars($r['title']) ?></div>
                                <div style="font-size: 0.75rem; color: #64748B;"><?= htmlspecialchars($r['subtitle']) ?></div>
                            </div>
                        </div>

                        <!-- Right Side: Badge & Chevron -->
                        <div class="d-flex align-items-center gap-3">
                            <?= renderReportStatus($r['status']) ?>
                            <i class="bi bi-chevron-right" style="color: #CBD5E1; font-size: 0.85rem;"></i>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Right Column: New Report Draft -->
        <div class="col-lg-4">
            <div class="card" style="border: 1px solid #E2E8F0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); padding: 0;">
                <div class="card-header bg-white" style="border-bottom: 1px solid #F1F5F9; padding: 20px 24px; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                    <h6 class="mb-0" style="color:#0F172A; font-weight:600; font-size:0.95rem;">New Report Draft</h6>
                </div>
                <div class="card-body" style="padding: 24px;">
                    
                    <div class="mb-3">
                        <label class="form-label" style="font-size:0.75rem; color:#64748B; font-weight:500;">Linked Activity</label>
                        <input type="text" class="form-control shadow-none" placeholder="Select or type an activity..." style="border-radius:8px; border:1px solid #E2E8F0; font-size:0.85rem; padding:10px 14px; color:#0F172A;">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" style="font-size:0.75rem; color:#64748B; font-weight:500;">Cadets Involved</label>
                        <input type="text" class="form-control shadow-none" placeholder="42" style="border-radius:8px; border:1px solid #E2E8F0; font-size:0.85rem; padding:10px 14px; color:#0F172A;">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label" style="font-size:0.75rem; color:#64748B; font-weight:500;">Narrative</label>
                        <textarea class="form-control shadow-none" placeholder="Describe the activity, outputs, and impact..." rows="4" style="border-radius:8px; border:1px solid #E2E8F0; font-size:0.85rem; padding:10px 14px; color:#475569;"></textarea>
                    </div>
                    
                    <!-- Attachments Dropzone -->
                    <div class="mb-4" style="border: 1px dashed #CBD5E1; border-radius: 8px; padding: 20px; text-align: center; cursor: pointer; background: white;">
                        <i class="bi bi-upload" style="color: #94A3B8; font-size: 1.1rem; margin-bottom: 6px; display: block;"></i>
                        <div style="font-size: 0.75rem; color: #64748B; font-weight: 400;">Drop photos, attendance sheet, Accomplishment Reports</div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button class="btn" style="background:#fff; border: 1px solid #E2E8F0; border-radius:8px; color:#475569; font-weight: 500; font-size:0.85rem; padding: 10px 20px;">
                            Save Draft
                        </button>
                        <button class="btn d-flex align-items-center justify-content-center gap-2 flex-grow-1" style="background:#0F172A; border: none; border-radius:8px; color:white; font-weight: 500; font-size:0.85rem; padding: 10px 24px;">
                            <i class="bi bi-send" style="font-size: 0.85rem;"></i> Submit
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Report Details Modal -->
<div class="modal fade" id="reportDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <div class="modal-content" style="border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
            
            <!-- Header -->
            <div class="modal-header border-0" style="background: #0F172A; padding: 20px 24px;">
                <h5 class="modal-title fw-bold" style="color: white; font-size: 1.1rem;">Report Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.8rem;"></button>
            </div>
            
            <!-- Body -->
            <div class="modal-body" style="padding: 24px;">
                <!-- Top Section -->
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h4 class="fw-bold mb-1" style="color: #0F172A; font-size: 1.25rem;">Community Outreach — Brgy. San Roque</h4>
                        <div style="color: #64748B; font-size: 0.85rem;">Approved May 4</div>
                    </div>
                    <span style="background:#ECFDF5; color:#10B981; font-size:0.75rem; font-weight:500; padding:4px 12px; border-radius:20px;">Approved</span>
                </div>
                
                <!-- Middle Section -->
                <div class="mb-4">
                    <div style="font-weight: 600; color: #0F172A; font-size: 0.9rem; margin-bottom: 8px;">Narrative</div>
                    <div style="color: #475569; font-size: 0.85rem; line-height: 1.5;">Succesful community interaction and medical mission.</div>
                </div>
                
                <!-- Attachments Section -->
                <div>
                    <div style="font-weight: 600; color: #0F172A; font-size: 0.9rem; margin-bottom: 8px;">Attached Documents</div>
                    <div style="border: 1px solid #E2E8F0; border-radius: 8px; padding: 16px; display: flex; align-items: center; justify-content: space-between;">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 40px; height: 40px; border-radius: 8px; border: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: center; color: #94A3B8; font-size: 1.2rem;">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #0F172A; font-size: 0.85rem;">Community_Outreach_Summary.docx</div>
                                <div style="color: #64748B; font-size: 0.75rem;">Accomplishment Report · 3.1 MB</div>
                            </div>
                        </div>
                        <i class="bi bi-download" style="color: #CBD5E1; font-size: 1.2rem; cursor: pointer;"></i>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="modal-footer border-0" style="background: #F8FAFC; padding: 16px 24px; display: flex; justify-content: flex-end; border-top: 1px solid #F1F5F9;">
                <button type="button" class="btn" style="color: #EF4444; font-weight: 600; font-size: 0.85rem; padding: 8px 16px; display: flex; align-items: center; gap: 6px; border: none; background: transparent;">
                    <i class="bi bi-trash3"></i> Delete
                </button>
            </div>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
