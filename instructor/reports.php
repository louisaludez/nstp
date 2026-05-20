<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Instructor') {
    header("Location: ../login");
    exit;
}

$instructor_name = $_SESSION['full_name'];
$instructor_id = $_SESSION['user_id'];

// Fetch activity plans to populate the Linked Activity dropdown
$stmtPlans = $pdo->prepare("SELECT id, title FROM activity_plans WHERE instructor_id = ? AND status = 'Approved' ORDER BY title ASC");
$stmtPlans->execute([$instructor_id]);
$approved_plans = $stmtPlans->fetchAll();

$extra_css = ['../assets/css/style.css'];
include '../includes/header.php';
include '../includes/instructor_sidebar.php';
?>

<div class="flex-grow-1 p-4 p-lg-5 w-100" style="background-color: #F9FAFB; min-height: 100vh;">
    
    <?php include '../includes/topbar.php'; ?>
    
    <!-- Header -->
    <div class="mb-4 mt-2">
        <h5 class="fw-bold mb-1" style="color: #111827; font-size: 1.15rem;">Accomplishment Reports</h5>
        <div class="text-muted" style="font-size: 0.85rem;">Document and submit completed activities</div>
    </div>

    <div class="row g-4">
        <!-- Left Panel: All Reports -->
        <div class="col-lg-8">
            <div style="background: white; border: 1px solid #E5E7EB; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); height: 100%;">
                <div style="padding: 20px 24px; border-bottom: 1px solid #F3F4F6;">
                    <h6 class="fw-bold mb-0" style="color: #374151;">All Reports</h6>
                </div>
                
                <div class="list-group list-group-flush">
                    <!-- Item 1: Draft -->
                    <div class="list-group-item d-flex justify-content-between align-items-center border-0" style="padding: 16px 24px; border-bottom: 1px solid #F3F4F6 !important;">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 40px; height: 40px; border-radius: 8px; background: #F3F4F6; display: flex; align-items: center; justify-content: center; color: #9CA3AF;">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div>
                                <div style="font-size: 0.9rem; font-weight: 500; color: #374151; margin-bottom: 2px;">Tree-Planting Drive Report</div>
                                <div style="font-size: 0.75rem; color: #6B7280;">CWTS 1 · Sec A · Due May 15</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span style="font-size: 0.7rem; padding: 4px 12px; border-radius: 20px; background: white; border: 1px solid #E5E7EB; color: #4B5563; font-weight: 500; display: flex; align-items: center; gap: 4px;">
                                <i class="bi bi-pencil" style="font-size: 0.65rem;"></i> Draft
                            </span>
                            <i class="bi bi-chevron-right text-muted" style="font-size: 0.8rem;"></i>
                        </div>
                    </div>

                    <!-- Item 2: Submitted -->
                    <div class="list-group-item d-flex justify-content-between align-items-center border-0" style="padding: 16px 24px; border-bottom: 1px solid #F3F4F6 !important;">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 40px; height: 40px; border-radius: 8px; background: #F3F4F6; display: flex; align-items: center; justify-content: center; color: #9CA3AF;">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div>
                                <div style="font-size: 0.9rem; font-weight: 500; color: #374151; margin-bottom: 2px;">Adult Literacy Session #4</div>
                                <div style="font-size: 0.75rem; color: #6B7280;">LTS 2 · Sec A · Submitted May 9</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span style="font-size: 0.7rem; padding: 4px 12px; border-radius: 20px; background: #EEF2FF; color: #6366F1; font-weight: 500; display: flex; align-items: center; gap: 4px;">
                                <i class="bi bi-clock-history" style="font-size: 0.65rem;"></i> Submitted
                            </span>
                            <i class="bi bi-chevron-right text-muted" style="font-size: 0.8rem;"></i>
                        </div>
                    </div>

                    <!-- Item 3: Approved -->
                    <div class="list-group-item d-flex justify-content-between align-items-center border-0" style="padding: 16px 24px; border-bottom: 1px solid #F3F4F6 !important;">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 40px; height: 40px; border-radius: 8px; background: #F3F4F6; display: flex; align-items: center; justify-content: center; color: #9CA3AF;">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div>
                                <div style="font-size: 0.9rem; font-weight: 500; color: #374151; margin-bottom: 2px;">Barangay Clean-Up Plan</div>
                                <div style="font-size: 0.75rem; color: #6B7280;">CWTS 1 · Sec C · Approved May 6</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span style="font-size: 0.7rem; padding: 4px 12px; border-radius: 20px; background: #ECFDF5; color: #10B981; font-weight: 500; display: flex; align-items: center; gap: 4px;">
                                <i class="bi bi-check-circle" style="font-size: 0.65rem;"></i> Approved
                            </span>
                            <i class="bi bi-chevron-right text-muted" style="font-size: 0.8rem;"></i>
                        </div>
                    </div>

                    <!-- Item 4: Revisions -->
                    <div class="list-group-item d-flex justify-content-between align-items-center border-0" style="padding: 16px 24px; border-bottom: 1px solid #F3F4F6 !important;">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 40px; height: 40px; border-radius: 8px; background: #F3F4F6; display: flex; align-items: center; justify-content: center; color: #9CA3AF;">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div>
                                <div style="font-size: 0.9rem; font-weight: 500; color: #374151; margin-bottom: 2px;">Reading Buddies Kick-off</div>
                                <div style="font-size: 0.75rem; color: #6B7280;">LTS 2 · Sec B · Needs revisions</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span style="font-size: 0.7rem; padding: 4px 12px; border-radius: 20px; background: #FEF2F2; color: #EF4444; font-weight: 500; display: flex; align-items: center; gap: 4px;">
                                <i class="bi bi-exclamation-circle" style="font-size: 0.65rem;"></i> Revisions
                            </span>
                            <i class="bi bi-chevron-right text-muted" style="font-size: 0.8rem;"></i>
                        </div>
                    </div>

                    <!-- Item 5: Draft -->
                    <div class="list-group-item d-flex justify-content-between align-items-center border-0" style="padding: 16px 24px;">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 40px; height: 40px; border-radius: 8px; background: #F3F4F6; display: flex; align-items: center; justify-content: center; color: #9CA3AF;">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div>
                                <div style="font-size: 0.9rem; font-weight: 500; color: #374151; margin-bottom: 2px;">Mid-semester Accomplishment</div>
                                <div style="font-size: 0.75rem; color: #6B7280;">All sections · Due May 22</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span style="font-size: 0.7rem; padding: 4px 12px; border-radius: 20px; background: white; border: 1px solid #E5E7EB; color: #4B5563; font-weight: 500; display: flex; align-items: center; gap: 4px;">
                                <i class="bi bi-pencil" style="font-size: 0.65rem;"></i> Draft
                            </span>
                            <i class="bi bi-chevron-right text-muted" style="font-size: 0.8rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: New Report Draft -->
        <div class="col-lg-4">
            <div style="background: white; border: 1px solid #E5E7EB; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column;">
                <div style="padding: 20px 24px; border-bottom: 1px solid #F3F4F6;">
                    <h6 class="fw-bold mb-0" style="color: #374151;">New Report Draft</h6>
                </div>
                
                <form method="POST" action="" style="padding: 24px;">
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #6B7280; margin-bottom: 6px;">Linked Activity</label>
                        <select name="activity_plan_id" class="form-select" style="border-radius: 8px; border-color: #E5E7EB; font-size: 0.85rem; padding: 10px 12px; box-shadow: none; color: #9CA3AF;">
                            <option value="" disabled selected>Select or type an activity...</option>
                            <?php if(count($approved_plans) > 0): ?>
                                <?php foreach($approved_plans as $ap): ?>
                                    <option value="<?= $ap['id'] ?>" style="color: #111827;"><?= htmlspecialchars($ap['title']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #6B7280; margin-bottom: 6px;">Beneficiaries</label>
                        <input type="number" name="beneficiaries" class="form-control" value="42" style="border-radius: 8px; border-color: #E5E7EB; font-size: 0.85rem; padding: 10px 12px; box-shadow: none;">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #6B7280; margin-bottom: 6px;">Narrative</label>
                        <textarea name="narrative" class="form-control" rows="4" placeholder="Describe the activity, outputs, and impact..." style="border-radius: 8px; border-color: #E5E7EB; font-size: 0.85rem; padding: 10px 12px; box-shadow: none;"></textarea>
                    </div>
                    
                    <!-- Upload Dropzone -->
                    <div class="mb-4 text-center" style="border: 1px dashed #A7F3D0; border-radius: 8px; padding: 20px; background: #F0FDF4; cursor: pointer; transition: all 0.2s;" onclick="document.getElementById('reportFiles').click()">
                        <i class="bi bi-upload" style="color: #10B981; font-size: 1.15rem;"></i>
                        <p class="mb-0 mt-2" style="font-size: 0.75rem; color: #374151; font-weight: 500;">Drop photos, attendance sheet, Accomplishment Reports</p>
                        <input type="file" id="reportFiles" class="d-none" multiple>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light" style="padding: 8px 16px; border-radius: 20px; font-size: 0.85rem; font-weight: 500; color: #374151; border: 1px solid #E5E7EB; background: white;">Save Draft</button>
                        <button type="submit" name="submit_report" class="btn btn-success d-flex align-items-center gap-2" style="padding: 8px 16px; border-radius: 20px; font-size: 0.85rem; font-weight: 500; background: #059669; border: none;">
                            <i class="bi bi-send"></i> Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
