<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Instructor') {
    header("Location: ../login");
    exit;
}

require 'controllers/ReportsController.php';

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
                    <?php if (count($reports) > 0): ?>
                        <?php foreach ($reports as $idx => $report): 
                            // Build section label
                            $sec_label = ($report['component'] ?? '') 
                                ? htmlspecialchars($report['component']) . ' · ' . htmlspecialchars($report['section_name'] ?? '')
                                : 'All sections';
                            
                            // Determine badge styling based on status
                            $status = $report['status'];
                            switch ($status) {
                                case 'Draft':
                                    $badgeBg    = 'background: white; border: 1px solid #E5E7EB;';
                                    $badgeColor = 'color: #4B5563;';
                                    $badgeIcon  = 'bi-pencil';
                                    $badgeLabel = 'Draft';
                                    $date_label = $report['completed_date'] 
                                        ? 'Due ' . date('M j', strtotime($report['completed_date'])) 
                                        : 'Draft';
                                    break;
                                case 'Pending':
                                    $badgeBg    = 'background: #EEF2FF;';
                                    $badgeColor = 'color: #6366F1;';
                                    $badgeIcon  = 'bi-clock-history';
                                    $badgeLabel = 'Submitted';
                                    $date_label = 'Submitted ' . date('M j', strtotime($report['submitted_date']));
                                    break;
                                case 'Reviewed':
                                    $badgeBg    = 'background: #ECFDF5;';
                                    $badgeColor = 'color: #10B981;';
                                    $badgeIcon  = 'bi-check-circle';
                                    $badgeLabel = 'Approved';
                                    $date_label = 'Approved ' . date('M j', strtotime($report['submitted_date']));
                                    break;
                                case 'Revision':
                                    $badgeBg    = 'background: #FEF2F2;';
                                    $badgeColor = 'color: #EF4444;';
                                    $badgeIcon  = 'bi-exclamation-circle';
                                    $badgeLabel = 'Revisions';
                                    $date_label = 'Needs revisions';
                                    break;
                                default:
                                    $badgeBg    = 'background: #F3F4F6;';
                                    $badgeColor = 'color: #4B5563;';
                                    $badgeIcon  = 'bi-file-earmark';
                                    $badgeLabel = $status;
                                    $date_label = date('M j', strtotime($report['submitted_date']));
                            }
                            
                            // Last item should not have bottom border
                            $isLast = ($idx === count($reports) - 1);
                            $borderStyle = $isLast ? '' : 'border-bottom: 1px solid #F3F4F6 !important;';
                        ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 report-item" style="padding: 16px 24px; cursor: pointer; transition: background-color 0.2s; <?= $borderStyle ?>"
                             onmouseover="this.style.backgroundColor='#F9FAFB'" onmouseout="this.style.backgroundColor='transparent'"
                             data-bs-toggle="modal" data-bs-target="#reportDetailsModal"
                             data-id="<?= $report['id'] ?>"
                             data-title="<?= htmlspecialchars($report['title']) ?>"
                             data-badge-bg="<?= htmlspecialchars($badgeBg) ?>"
                             data-badge-color="<?= htmlspecialchars($badgeColor) ?>"
                             data-badge-icon="<?= htmlspecialchars($badgeIcon) ?>"
                             data-badge-label="<?= htmlspecialchars($badgeLabel) ?>"
                             data-section="<?= htmlspecialchars($report['component'] . ' 1 · Sec ' . substr($report['section_name'], -1)) ?>"
                             data-date="<?= $report['completed_date'] ? 'Due ' . date('M j', strtotime($report['completed_date'])) : '' ?>"
                             data-beneficiaries="<?= htmlspecialchars($report['participants_count']) ?>"
                             data-narrative="<?= htmlspecialchars($report['accomplishments']) ?>"
                             data-files="<?= htmlspecialchars($report['files_attached']) ?>">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width: 40px; height: 40px; border-radius: 8px; background: #F3F4F6; display: flex; align-items: center; justify-content: center; color: #9CA3AF;">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>
                                <div>
                                    <div style="font-size: 0.9rem; font-weight: 500; color: #374151; margin-bottom: 2px;"><?= htmlspecialchars($report['title']) ?></div>
                                    <div style="font-size: 0.75rem; color: #6B7280;"><?= $sec_label ?> · <?= $date_label ?></div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <span style="font-size: 0.7rem; padding: 4px 12px; border-radius: 20px; <?= $badgeBg ?> <?= $badgeColor ?> font-weight: 500; display: flex; align-items: center; gap: 4px;">
                                    <i class="bi <?= $badgeIcon ?>" style="font-size: 0.65rem;"></i> <?= $badgeLabel ?>
                                </span>
                                <i class="bi bi-chevron-right text-muted" style="font-size: 0.8rem;"></i>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="list-group-item border-0 text-center py-5">
                            <div style="color: #9CA3AF; margin-bottom: 8px;"><i class="bi bi-file-earmark-x" style="font-size: 1.5rem;"></i></div>
                            <div class="text-muted" style="font-size: 0.85rem;">No reports yet. Submit your first report using the form.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Panel: New Report Draft -->
        <div class="col-lg-4">
            <div style="background: white; border: 1px solid #E5E7EB; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column;">
                <div style="padding: 20px 24px; border-bottom: 1px solid #F3F4F6;">
                    <h6 class="fw-bold mb-0" style="color: #374151;">New Report Draft</h6>
                </div>
                
                <form method="POST" action="" enctype="multipart/form-data" class="needs-confirmation" data-confirm="Are you sure you want to submit this report?" style="padding: 24px;">
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #6B7280; margin-bottom: 6px;">Linked Activity</label>
                        <select name="activity_plan_id" class="form-select" style="border-radius: 8px; border-color: #E5E7EB; font-size: 0.85rem; padding: 10px 12px; box-shadow: none; color: #9CA3AF;" required>
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
                        <button type="submit" name="save_draft" class="btn btn-light" style="padding: 8px 16px; border-radius: 20px; font-size: 0.85rem; font-weight: 500; color: #374151; border: 1px solid #E5E7EB; background: white;">Save Draft</button>
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

<!-- Report Details Modal -->
<div class="modal fade" id="reportDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" style="color: #111827; font-size: 1.1rem;">Report Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.8rem;"></button>
            </div>
            
            <div class="modal-body px-4 py-4">
                <!-- Title and Status -->
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <h4 class="fw-bold mb-0" id="modalReportTitle" style="color: #111827; font-size: 1.25rem;">Report Title</h4>
                    <span id="modalReportStatus" style="font-size: 0.75rem; padding: 4px 12px; border-radius: 20px; font-weight: 500; display: flex; align-items: center; gap: 4px;">
                        <i class="bi" id="modalReportStatusIcon" style="font-size: 0.7rem;"></i> <span id="modalReportStatusText">Draft</span>
                    </span>
                </div>
                
                <!-- Subtitle -->
                <div class="text-muted mb-4" id="modalReportSubtitle" style="font-size: 0.9rem;">
                    CWTS 1 &middot; Sec A &middot; Due May 15
                </div>
                
                <!-- Beneficiaries Card -->
                <div style="background: #F9FAFB; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; width: fit-content; min-width: 150px;">
                    <div style="font-size: 0.7rem; font-weight: 600; color: #6B7280; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 4px;">BENEFICIARIES</div>
                    <div style="font-size: 1.25rem; color: #111827;" id="modalReportBeneficiaries">0</div>
                </div>
                
                <!-- Narrative Section -->
                <div class="mb-4">
                    <h6 class="fw-medium mb-2" style="color: #111827; font-size: 0.95rem;">Narrative</h6>
                    <p class="text-muted" id="modalReportNarrative" style="font-size: 0.9rem; line-height: 1.5;"></p>
                </div>
                
                <!-- Attachments Section -->
                <div>
                    <h6 class="fw-medium mb-2" style="color: #111827; font-size: 0.95rem;">Attachments</h6>
                    <div style="border: 1px solid #E5E7EB; border-radius: 8px; padding: 16px; display: flex; align-items: center; justify-content: space-between;">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 40px; height: 40px; border-radius: 8px; background: #EEF2FF; color: #4F46E5; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                                <i class="bi bi-file-earmark-check"></i>
                            </div>
                            <div>
                                <div style="font-weight: 500; color: #111827; font-size: 0.9rem;" id="modalReportFileName">attendance.pdf</div>
                                <div style="color: #6B7280; font-size: 0.75rem;">Document file</div>
                            </div>
                        </div>
                        <a href="#" class="text-decoration-none" style="color: #4F46E5; font-size: 0.85rem; font-weight: 500;">Download</a>
                    </div>
                </div>
                
            </div>
            
            <!-- Footer Actions -->
            <div class="modal-footer border-top-0 pt-0 pb-4 px-4 d-flex justify-content-center gap-4" style="background: #F9FAFB; padding-top: 16px !important; border-radius: 0 0 12px 12px;">
                <button type="button" class="btn" style="background: #4F46E5; color: white; border-radius: 8px; padding: 8px 24px; font-weight: 500; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-pencil"></i> Edit Report
                </button>
                <button type="button" class="btn btn-link text-decoration-none" style="color: #EF4444; font-weight: 500; font-size: 0.9rem; display: flex; align-items: center; gap: 8px; padding: 8px 16px;">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const reportModal = document.getElementById('reportDetailsModal');
    if (reportModal) {
        reportModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            
            // Extract info from data-* attributes
            const title = button.getAttribute('data-title');
            const statusLabel = button.getAttribute('data-badge-label');
            const statusBg = button.getAttribute('data-badge-bg');
            const statusColor = button.getAttribute('data-badge-color');
            const statusIcon = button.getAttribute('data-badge-icon');
            const section = button.getAttribute('data-section');
            const date = button.getAttribute('data-date');
            const beneficiaries = button.getAttribute('data-beneficiaries');
            const narrative = button.getAttribute('data-narrative');
            const filesCount = parseInt(button.getAttribute('data-files') || 0);
            
            // Update the modal's content
            document.getElementById('modalReportTitle').textContent = title;
            
            const statusBadge = document.getElementById('modalReportStatus');
            statusBadge.style.cssText = `font-size: 0.75rem; padding: 4px 12px; border-radius: 20px; font-weight: 500; display: flex; align-items: center; gap: 4px; ${statusBg} ${statusColor}`;
            document.getElementById('modalReportStatusText').textContent = statusLabel;
            document.getElementById('modalReportStatusIcon').className = `bi ${statusIcon}`;
            
            document.getElementById('modalReportSubtitle').innerHTML = `${section} &middot; ${date}`;
            document.getElementById('modalReportBeneficiaries').textContent = beneficiaries;
            document.getElementById('modalReportNarrative').textContent = narrative || 'No narrative provided.';
            
            const fileNameElem = document.getElementById('modalReportFileName');
            if (filesCount > 0) {
                fileNameElem.textContent = `${filesCount} file(s) attached`;
            } else {
                fileNameElem.textContent = 'No attachments';
            }
        });
    }
});
</script>
</body>
</html>
