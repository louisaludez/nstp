<?php
session_start();
require '../config/db.php';

// Security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Instructor') {
    header("Location: ../login");
    exit;
}

$instructor_name = $_SESSION['full_name'];

include '../includes/header.php';
include '../includes/instructor_sidebar.php';
?>

<style>
    /* Card Grid Styling */
    .activity-card {
        background-color: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        padding: 24px;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: box-shadow 0.2s;
    }
    .activity-card:hover {
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    }
    
    /* Typography & Icons in Cards */
    .card-title { font-size: 1.1rem; font-weight: 700; color: #111827; margin-bottom: 0; }
    .card-meta { font-size: 0.85rem; color: #6B7280; display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
    .card-meta i { font-size: 1rem; color: #9CA3AF; width: 16px; text-align: center; }
    .card-desc { font-size: 0.85rem; color: #4B5563; margin-top: 16px; margin-bottom: 24px; line-height: 1.5; flex-grow: 1; }

    /* Custom Badges */
    .badge-status { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 500; }
    .bg-approved { background-color: #D1FAE5; color: #065F46; }
    .bg-pending { background-color: #FEF3C7; color: #92400E; }
    .bg-draft { background-color: #F3F4F6; color: #4B5563; }

    /* Buttons */
    .btn-teal {
        background-color: #00897B; /* Match the teal in the Figma */
        color: white;
        font-weight: 500;
        border-radius: 8px;
        padding: 8px 16px;
        border: none;
        transition: background-color 0.2s;
    }
    .btn-teal:hover { background-color: #00695C; color: white; }
    
    .btn-gray-fill {
        background-color: #F3F4F6;
        color: #374151;
        font-weight: 500;
        border-radius: 8px;
        padding: 8px 16px;
        border: none;
        transition: background-color 0.2s;
    }
    .btn-gray-fill:hover { background-color: #E5E7EB; color: #111827; }

    .btn-outline-custom {
        background-color: white;
        color: #374151;
        font-weight: 500;
        border-radius: 8px;
        padding: 8px 16px;
        border: 1px solid #D1D5DB;
    }
    .btn-outline-custom:hover { background-color: #F9FAFB; }

    /* Modal Form Styling */
    .modal-content { border-radius: 16px; border: none; }
    .modal-header { border-bottom: none; padding: 24px 24px 16px 24px; }
    .modal-body { padding: 0 24px 24px 24px; }
    .modal-footer { border-top: none; padding: 16px 24px 24px 24px; }
    
    .form-label { font-size: 0.85rem; font-weight: 600; color: #374151; margin-bottom: 6px; }
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #D1D5DB;
        padding: 10px 14px;
        font-size: 0.95rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: #00897B;
        box-shadow: 0 0 0 3px rgba(0, 137, 123, 0.1);
    }

    /* Drag & Drop Upload Zone */
    .upload-zone {
        border: 2px dashed #D1D5DB;
        border-radius: 8px;
        padding: 24px;
        text-align: center;
        background-color: #F9FAFB;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .upload-zone:hover { background-color: #F3F4F6; }
    .upload-icon { font-size: 1.8rem; color: #9CA3AF; margin-bottom: 8px; }
</style>

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

</div> <div class="modal fade" id="activityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            
            <div class="modal-header">
                <h4 class="fw-bold mb-0">Submit Activity Plan</h4>
            </div>
            
            <div class="modal-body">
                <form id="activityForm">
                    <div class="mb-3">
                        <label class="form-label">Activity Title</label>
                        <input type="text" class="form-control">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Section</label>
                            <select class="form-select">
                                <option>CWTS-A</option>
                                <option>CWTS-B</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" value="2026-08-04">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Expected Participants</label>
                        <input type="text" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Objectives</label>
                        <textarea class="form-control" rows="3" placeholder="List the main objectives of this activity..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Required Resources</label>
                        <textarea class="form-control" rows="2" placeholder="Materials, equipment, budget needed..."></textarea>
                    </div>

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