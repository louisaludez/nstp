<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Instructor') {
    header("Location: ../login");
    exit;
}

$instructor_name = $_SESSION['full_name'];

$extra_css = ['../assets/css/pages/instructor/activities.css'];
include '../includes/header.php';
include '../includes/instructor_sidebar.php';
?>

<div class="flex-grow-1 p-5">
    
    <?php include '../includes/topbar.php'; ?>

    <div class="d-flex justify-content-between align-items-start mb-4 pb-2">
        <div>
            <h3 class="fw-bold mb-1" style="color: #111827;">Activity Plans</h3>
            <p class="text-muted mb-0">Submit and manage your NSTP activity plans</p>
        </div>
        <button type="button" class="btn btn-teal" data-bs-toggle="modal" data-bs-target="#activityModal">
            <i class="bi bi-plus-lg me-1"></i> New Activity Plan
        </button>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="activity-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h4 class="card-title">Community Outreach Program</h4>
                    <span class="badge-status bg-approved">Approved</span>
                </div>
                <div class="card-meta"><i class="bi bi-calendar3"></i> 4/10/2026</div>
                <div class="card-meta"><i class="bi bi-geo-alt"></i> Barangay San Jose</div>
                <div class="card-meta"><i class="bi bi-people"></i> 54 participants (CWTS-A)</div>
                <p class="card-desc">Community clean-up and health awareness campaign</p>
                <div class="d-flex gap-2 mt-auto">
                    <button class="btn btn-gray-fill flex-fill">Edit</button>
                    <button class="btn btn-teal flex-fill">View Details</button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="activity-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h4 class="card-title">Tree Planting Activity</h4>
                    <span class="badge-status bg-pending">Pending</span>
                </div>
                <div class="card-meta"><i class="bi bi-calendar3"></i> 4/15/2026</div>
                <div class="card-meta"><i class="bi bi-geo-alt"></i> University Campus</div>
                <div class="card-meta"><i class="bi bi-people"></i> 54 participants (CWTS-B)</div>
                <p class="card-desc">Environmental conservation initiative</p>
                <div class="d-flex gap-2 mt-auto">
                    <button class="btn btn-gray-fill flex-fill">Edit</button>
                    <button class="btn btn-teal flex-fill">View Details</button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="activity-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h4 class="card-title">Literacy Training Session</h4>
                    <span class="badge-status bg-draft">Draft</span>
                </div>
                <div class="card-meta"><i class="bi bi-calendar3"></i> 4/18/2026</div>
                <div class="card-meta"><i class="bi bi-geo-alt"></i> Community Center</div>
                <div class="card-meta"><i class="bi bi-people"></i> 54 participants (CWTS-A)</div>
                <p class="card-desc">Basic reading and writing workshop for community members</p>
                <div class="d-flex gap-2 mt-auto">
                    <button class="btn btn-gray-fill flex-fill">Edit</button>
                    <button class="btn btn-teal flex-fill">View Details</button>
                </div>
            </div>
        </div>
    </div>

</div>

</div>
<div class="modal fade" id="activityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="fw-bold mb-0">Submit Activity Plan</h4>
            </div>
            <div class="modal-body">
                <form id="activityForm">
                    <div class="mb-3"><label class="form-label">Activity Title</label><input type="text" class="form-control"></div>
                    <div class="row mb-3">
                        <div class="col-md-6"><label class="form-label">Section</label><select class="form-select"><option>CWTS-A</option><option>CWTS-B</option></select></div>
                        <div class="col-md-6"><label class="form-label">Date</label><input type="date" class="form-control" value="2026-08-04"></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Location</label><input type="text" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Expected Participants</label><input type="text" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Description</label><textarea class="form-control" rows="3"></textarea></div>
                    <div class="mb-3"><label class="form-label">Objectives</label><textarea class="form-control" rows="3" placeholder="List the main objectives of this activity..."></textarea></div>
                    <div class="mb-3"><label class="form-label">Required Resources</label><textarea class="form-control" rows="2" placeholder="Materials, equipment, budget needed..."></textarea></div>
                    <div class="mb-2">
                        <label class="form-label">Attach Documents (Optional)</label>
                        <div class="upload-zone" onclick="document.getElementById('modalFileUpload').click()">
                            <i class="bi bi-upload upload-icon d-block"></i>
                            <span class="d-block text-dark fw-medium mb-1">Click to upload or drag and drop</span>
                            <small class="text-muted">PDF, DOC, DOCX up to 10MB</small>
                            <input type="file" id="modalFileUpload" class="d-none" multiple>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-gray-fill">Save as Draft</button>
                <button type="button" class="btn btn-teal">Submit for Approval</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
