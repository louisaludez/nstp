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
    .panel-container { 
        background-color: #fff; 
        border: 1px solid #E5E7EB; 
        border-radius: 12px; 
        padding: 24px; 
        margin-bottom: 24px; 
    }
    
    /* Form Styling */
    .form-label {
        font-size: 0.85rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 6px;
    }
    .form-control {
        border-radius: 8px;
        border: 1px solid #D1D5DB;
        padding: 10px 14px;
        font-size: 0.95rem;
    }
    .form-control:focus {
        border-color: #00695C;
        box-shadow: 0 0 0 3px rgba(0, 105, 92, 0.1);
    }

    /* File Upload Zone */
    .upload-zone {
        border: 2px dashed #D1D5DB;
        border-radius: 8px;
        padding: 32px;
        text-align: center;
        background-color: #F9FAFB;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .upload-zone:hover { background-color: #F3F4F6; }
    .upload-icon { font-size: 1.5rem; color: #6B7280; margin-bottom: 8px; }

    /* Custom Green Buttons for Reports */
    .btn-green-brand {
        background-color: #059669; /* Match the screenshot green */
        color: white;
        font-weight: 500;
        border-radius: 8px;
        padding: 8px 20px;
        border: none;
        transition: background-color 0.2s;
    }
    .btn-green-brand:hover { background-color: #047857; color: white; }
    
    .btn-cancel-light {
        background-color: #F3F4F6;
        color: #4B5563;
        font-weight: 500;
        border-radius: 8px;
        padding: 8px 20px;
        border: none;
        transition: background-color 0.2s;
    }
    .btn-cancel-light:hover { background-color: #E5E7EB; }

    /* Submitted Report Cards */
    .report-card {
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 24px;
        background-color: #fff;
    }
    .badge-soft-primary { background-color: #DBEAFE; color: #1E40AF; padding: 4px 10px; font-weight: 500; font-size: 0.75rem; }
    
    .action-icon { cursor: pointer; font-size: 1.1rem; transition: 0.2s; }
    .action-icon.edit { color: #3B82F6; }
    .action-icon.delete { color: #EF4444; }
    .action-icon:hover { opacity: 0.7; }
    
    /* Content Sections inside Card */
    .report-section-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 4px;
        margin-top: 16px;
    }
    .report-section-text {
        font-size: 0.9rem;
        color: #4B5563;
        margin-bottom: 0;
        line-height: 1.5;
    }

    .file-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background-color: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 0.85rem;
        color: #374151;
        margin-right: 8px;
        margin-bottom: 8px;
    }
    .file-pill i { color: #6B7280; }
</style>

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
            <div class="mb-3">
                <label class="form-label">Activity Title *</label>
                <input type="text" class="form-control" placeholder="e.g., Community Clean-up Drive">
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Date Completed *</label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Location *</label>
                    <input type="text" class="form-control" placeholder="e.g., Sunset Bay Beach">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Number of Participants *</label>
                    <input type="number" class="form-control" placeholder="e.g., 45">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Participant Details *</label>
                    <input type="text" class="form-control" placeholder="e.g., 45 CWTS students from Section A">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Accomplishments & Outcomes *</label>
                <textarea class="form-control" rows="3" placeholder="Describe what was accomplished and the impact of the activity..."></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Challenges Encountered</label>
                <textarea class="form-control" rows="2" placeholder="Describe any challenges or issues encountered during the activity (optional)..."></textarea>
            </div>
            
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
            
            <div class="text-muted mt-4" style="font-size: 0.8rem;">
                Submitted: 4/16/2026
            </div>
            
        </div>
    </div>

</div>

</div> <script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggleFormBtn');
    const cancelBtnBottom = document.getElementById('cancelFormBtnBottom');
    const formContainer = document.getElementById('reportFormContainer');
    let isFormVisible = false;

    function toggleForm() {
        isFormVisible = !isFormVisible;
        
        if (isFormVisible) {
            formContainer.style.display = 'block';
            toggleBtn.innerHTML = 'Cancel';
            // Change button to look like a cancel button when form is open
            toggleBtn.classList.remove('btn-green-brand');
            toggleBtn.classList.add('btn-cancel-light');
        } else {
            formContainer.style.display = 'none';
            toggleBtn.innerHTML = '<i class="bi bi-plus-lg me-1"></i> New Report';
            // Change back to original green
            toggleBtn.classList.remove('btn-cancel-light');
            toggleBtn.classList.add('btn-green-brand');
        }
    }

    toggleBtn.addEventListener('click', toggleForm);
    cancelBtnBottom.addEventListener('click', toggleForm);
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>