<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Instructor') {
    header("Location: ../login");
    exit;
}

$instructor_name = $_SESSION['full_name'];

$extra_css = ['../assets/css/pages/instructor/reports.css'];
$extra_js = ['../assets/js/pages/instructor/reports.js'];
include '../includes/header.php';
include '../includes/instructor_sidebar.php';
?>

<div class="flex-grow-1 p-5">
    
    <?php include '../includes/topbar.php'; ?>

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: #111827;">Accomplishment Reporting</h3>
            <p class="text-muted mb-0">Report completed NSTP activities and document their execution</p>
        </div>
        <button id="toggleFormBtn" class="btn btn-green-brand">
            <i class="bi bi-plus-lg me-1"></i> New Report
        </button>
    </div>

    <div id="reportFormContainer" class="panel-container" style="display: none;">
        <h5 class="fw-bold mb-4">Create Accomplishment Report</h5>
        <form action="" method="POST">
            <div class="mb-3"><label class="form-label">Activity Title *</label><input type="text" class="form-control" placeholder="e.g., Community Clean-up Drive"></div>
            <div class="row mb-3">
                <div class="col-md-6"><label class="form-label">Date Completed *</label><input type="date" class="form-control"></div>
                <div class="col-md-6"><label class="form-label">Location *</label><input type="text" class="form-control" placeholder="e.g., Sunset Bay Beach"></div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6"><label class="form-label">Number of Participants *</label><input type="number" class="form-control" placeholder="e.g., 45"></div>
                <div class="col-md-6"><label class="form-label">Participant Details *</label><input type="text" class="form-control" placeholder="e.g., 45 CWTS students from Section A"></div>
            </div>
            <div class="mb-3"><label class="form-label">Accomplishments &amp; Outcomes *</label><textarea class="form-control" rows="3" placeholder="Describe what was accomplished and the impact of the activity..."></textarea></div>
            <div class="mb-3"><label class="form-label">Challenges Encountered</label><textarea class="form-control" rows="2" placeholder="Describe any challenges or issues encountered during the activity (optional)..."></textarea></div>
            <div class="mb-4">
                <label class="form-label">Supporting Evidence (Photos, Documents) *</label>
                <div class="upload-zone" onclick="document.getElementById('fileUpload').click()">
                    <i class="bi bi-upload upload-icon d-block"></i>
                    <small class="text-muted d-block mb-3">Upload photos, attendance sheets, and other supporting documents</small>
                    <span class="btn btn-sm btn-light border fw-medium px-4">Choose Files</span>
                    <input type="file" id="fileUpload" class="d-none" multiple>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="button" class="btn btn-green-brand">Submit Report</button>
                <button type="button" class="btn btn-cancel-light" id="cancelFormBtnBottom">Cancel</button>
            </div>
        </form>
    </div>

    <div class="panel-container">
        <h5 class="fw-bold mb-4">Submitted Accomplishment Reports</h5>
        <div class="report-card">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="fw-bold mb-0" style="color: #111827;">Community Clean-up Drive</h5>
                    <span class="badge badge-soft-primary rounded-pill">Submitted</span>
                </div>
                <div class="d-flex gap-3">
                    <i class="bi bi-pencil action-icon edit"></i>
                    <i class="bi bi-trash action-icon delete"></i>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-4 text-muted small mb-3">
                <span class="d-flex align-items-center"><i class="bi bi-geo-alt me-1"></i> Sunset Bay Beach</span>
                <span class="d-flex align-items-center"><i class="bi bi-people me-1"></i> 45 participants</span>
                <span>Completed: 4/15/2026</span>
            </div>
            <div class="report-section-title">Participants:</div>
            <p class="report-section-text">45 CWTS students from Section A</p>
            <div class="report-section-title">Accomplishments:</div>
            <p class="report-section-text">Successfully collected 150kg of waste materials. Engaged with local community members and raised awareness about proper waste disposal.</p>
            <div class="report-section-title">Challenges:</div>
            <p class="report-section-text">Weather conditions were challenging in the afternoon. Some students arrived late.</p>
            <div class="report-section-title mb-2">Supporting Evidence:</div>
            <div class="mb-3">
                <span class="file-pill"><i class="bi bi-file-earmark-zip"></i> activity-photos.zip</span>
                <span class="file-pill"><i class="bi bi-file-earmark-pdf"></i> attendance-sheet.pdf</span>
                <span class="file-pill"><i class="bi bi-image"></i> location-photo-1.jpg</span>
            </div>
            <div class="text-muted mt-4" style="font-size: 0.8rem;">Submitted: 4/16/2026</div>
        </div>
    </div>

</div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/pages/instructor-reports.js"></script>
</body>
</html>
