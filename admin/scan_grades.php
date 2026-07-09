<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

$extra_css = ['../assets/css/style.css'];
include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

<div class="flex-grow-1 p-4 p-lg-5 w-100">
    
    <?php include '../includes/topbar.php'; ?>
    
    <div class="mb-4 pb-1 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">OCR Grade Upload</h4>
            <p class="text-muted" style="font-size: 0.9rem;">Scan grade sheets and import directly into student records</p>
        </div>
        <button type="button" class="btn btn-sm d-inline-flex align-items-center gap-2" style="background: white; color: #4F46E5; border: 1px solid #C7D2FE; border-radius: 8px; font-weight: 500; padding: 8px 16px; transition: all 0.2s;" onmouseover="this.style.background='#EEF2FF'" onmouseout="this.style.background='white'" data-bs-toggle="modal" data-bs-target="#gradeScaleModal">
            <i class="bi bi-sliders"></i> Grade Scaling
        </button>
    </div>

    <div class="row g-4 align-items-start">

        <!-- Left Panel: Upload & Stats -->
        <div class="col-lg-7 col-xl-8">
            <div class="dash-panel d-flex flex-column gap-3" style="padding: 24px;">

                <!-- Dropzone -->
                <form action="" method="POST" enctype="multipart/form-data" id="uploadForm">
                    <input type="file" name="grade_sheet" id="fileInput" class="d-none"
                           accept=".pdf,.png,.jpg,.jpeg"
                           onchange="document.getElementById('uploadForm').submit();">

                    <div id="dropzone"
                         class="ocr-dropzone d-flex flex-column align-items-center justify-content-center text-center"
                         onclick="document.getElementById('fileInput').click();"
                         ondragover="handleDragOver(event)"
                         ondragleave="handleDragLeave(event)"
                         ondrop="handleDrop(event)">

                        <div class="ocr-upload-icon mb-4">
                            <i class="bi bi-upload"></i>
                        </div>

                        <h6 class="fw-bold mb-2" style="color: var(--text-dark); font-size: 1.05rem; letter-spacing: -0.01em;">
                            Drop grade sheets here or click to upload
                        </h6>
                        <p class="mb-4" style="font-size: 0.82rem; color: var(--text-muted); max-width: 320px; line-height: 1.5;">
                            PDF, PNG, JPG up to 25 MB. Supports multi-page scans.
                        </p>
                        <div class="ocr-engine-badge">
                            <i class="bi bi-upc-scan"></i>
                            OCR engine v3.2
                        </div>
                    </div>
                </form>

                <!-- Stats Row -->
                <div class="ocr-stats-row">
                    <div class="ocr-stat-cell">
                        <div class="ocr-stat-label">Confidence</div>
                        <div class="ocr-stat-value">98.4%</div>
                    </div>
                    <div class="ocr-stat-divider"></div>
                    <div class="ocr-stat-cell">
                        <div class="ocr-stat-label">Queue</div>
                        <div class="ocr-stat-value">2 files</div>
                    </div>
                    <div class="ocr-stat-divider"></div>
                    <div class="ocr-stat-cell">
                        <div class="ocr-stat-label">Avg time</div>
                        <div class="ocr-stat-value">~12s / page</div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Right Panel: Recent Uploads -->
        <div class="col-lg-5 col-xl-4">
            <div class="dash-panel p-0" style="overflow: hidden; border-radius: 12px; border: 1px solid #E2E8F0;">

                <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center" style="border-color: #E2E8F0 !important;">
                    <h6 class="fw-bold mb-0" style="font-size: 0.95rem; color: #111827;">Recent Uploads</h6>
                    <a href="export_passed.php" class="btn btn-sm d-inline-flex align-items-center gap-2" style="background: #10B981; color: white; border-radius: 6px; font-weight: 500; font-size: 0.75rem; padding: 6px 10px; transition: background 0.2s; text-decoration: none;" onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10B981'">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Export Passed
                    </a>
                </div>

                <div class="ocr-upload-list">
                    <!-- Item 1 -->
                    <div class="ocr-upload-item">
                        <div class="ocr-file-icon">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </div>
                        <div class="ocr-file-info">
                            <div class="ocr-file-name" style="color: #1E293B; font-weight: 600; font-size: 0.85rem;">BSCS-2A_Midterm.pdf</div>
                            <div class="ocr-file-meta" style="color: #64748B; font-size: 0.75rem;">BSCS-2A · 42 students · Today 10:14 AM</div>
                        </div>
                        <span class="badge-status-new processed">Processed</span>
                    </div>

                    <!-- Item 2 -->
                    <div class="ocr-upload-item" style="border-bottom: none;">
                        <div class="ocr-file-icon">
                            <i class="bi bi-file-earmark-image"></i>
                        </div>
                        <div class="ocr-file-info">
                            <div class="ocr-file-name" style="color: #1E293B; font-weight: 600; font-size: 0.85rem;">BSIT-3B_Finals.jpg</div>
                            <div class="ocr-file-meta" style="color: #64748B; font-size: 0.75rem;">BSIT-3B · 41 students · Today 9:02 AM</div>
                        </div>
                        <span class="badge-status-new reviewing">Reviewing</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

</div>

<!-- Grade Scaling Modal -->
<div class="modal fade" id="gradeScaleModal" tabindex="-1" aria-labelledby="gradeScaleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow" style="border-radius: 12px;">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold" id="gradeScaleModalLabel">Configure Grade Scaling</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-3">
        <p class="text-muted small mb-4">Define how raw OCR scores translate to final grades. This configuration will be applied to all future scans.</p>
        
        <form id="gradeScaleForm">
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <thead class="text-muted small" style="border-bottom: 2px solid #F1F5F9;">
                        <tr>
                            <th class="fw-medium pb-2" style="width: 30%;">Min Score</th>
                            <th class="fw-medium pb-2" style="width: 30%;">Max Score</th>
                            <th class="fw-medium pb-2">Final Grade</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="gradeScaleBody">
                        <!-- Example Rows -->
                        <tr>
                            <td><input type="number" class="form-control form-control-sm" value="95" style="border-radius: 6px;"></td>
                            <td><input type="number" class="form-control form-control-sm" value="100" style="border-radius: 6px;"></td>
                            <td><input type="text" class="form-control form-control-sm" value="1.0" style="border-radius: 6px;"></td>
                            <td><button type="button" class="btn btn-sm text-danger border-0"><i class="bi bi-trash"></i></button></td>
                        </tr>
                        <tr>
                            <td><input type="number" class="form-control form-control-sm" value="90" style="border-radius: 6px;"></td>
                            <td><input type="number" class="form-control form-control-sm" value="94" style="border-radius: 6px;"></td>
                            <td><input type="text" class="form-control form-control-sm" value="1.25" style="border-radius: 6px;"></td>
                            <td><button type="button" class="btn btn-sm text-danger border-0"><i class="bi bi-trash"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-sm btn-light w-100 mt-2 text-primary fw-medium" style="border: 1px dashed #C7D2FE; border-radius: 6px;" onclick="addScaleRow()">
                <i class="bi bi-plus"></i> Add Range
            </button>
        </form>
      </div>
      <div class="modal-footer border-top-0 pt-0">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 500;">Cancel</button>
        <button type="button" class="btn text-white" style="background: #4F46E5; border-radius: 8px; font-weight: 500;" onclick="saveGradeScale()">Save Scaling</button>
      </div>
    </div>
  </div>
</div>

<script>
function addScaleRow() {
    const tbody = document.getElementById('gradeScaleBody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="number" class="form-control form-control-sm" style="border-radius: 6px;"></td>
        <td><input type="number" class="form-control form-control-sm" style="border-radius: 6px;"></td>
        <td><input type="text" class="form-control form-control-sm" style="border-radius: 6px;"></td>
        <td><button type="button" class="btn btn-sm text-danger border-0" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i></button></td>
    `;
    tbody.appendChild(tr);
}

function saveGradeScale() {
    // In future: send data to backend via fetch
    alert("Grade scaling saved! (Backend integration pending)");
    const modal = bootstrap.Modal.getInstance(document.getElementById('gradeScaleModal'));
    modal.hide();
}
</script>

<style>
/* ── OCR Grade Upload Page Styles ── */

/* Dropzone */
.ocr-dropzone {
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    padding: 56px 32px;
    background-image: url("data:image/svg+xml,%3csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100%25' height='100%25' fill='none' rx='12' ry='12' stroke='%23C7D2FE' stroke-width='2' stroke-dasharray='10%2c 8' stroke-dashoffset='0' stroke-linecap='square'/%3e%3c/svg%3e");
    background-color: white;
}
.ocr-dropzone:hover,
.ocr-dropzone.drag-over {
    background-color: #F8FAFC;
    background-image: url("data:image/svg+xml,%3csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100%25' height='100%25' fill='none' rx='12' ry='12' stroke='%23818CF8' stroke-width='2' stroke-dasharray='10%2c 8' stroke-dashoffset='0' stroke-linecap='square'/%3e%3c/svg%3e");
}

/* Upload icon */
.ocr-upload-icon {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    background: #6366F1;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
    flex-shrink: 0;
}

/* OCR engine badge */
.ocr-engine-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #6366F1;
    background: #EEF2FF;
    border-radius: 20px;
    padding: 6px 14px;
}

/* Stats Row */
.ocr-stats-row {
    display: flex;
    align-items: stretch;
    border: 1px solid #E2E8F0;
    border-radius: 10px;
    overflow: hidden;
    background: #F8FAFC;
}
.ocr-stat-cell {
    flex: 1;
    padding: 16px 24px;
}
.ocr-stat-divider {
    width: 1px;
    background: #E2E8F0;
    flex-shrink: 0;
}
.ocr-stat-label {
    font-size: 0.75rem;
    color: #64748B;
    font-weight: 500;
    margin-bottom: 4px;
}
.ocr-stat-value {
    font-size: 1.05rem;
    font-weight: 700;
    color: #0F172A;
    letter-spacing: -0.01em;
}

/* Recent Uploads Items */
.ocr-upload-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 20px;
    border-bottom: 1px solid #F3F4F6;
    cursor: pointer;
    transition: background-color 0.15s ease;
}
.ocr-upload-item:hover {
    background-color: #F9FAFB;
}
.ocr-file-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: #F3F4F6;
    color: #9CA3AF;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}
.ocr-file-info {
    flex: 1;
    min-width: 0;
}
.ocr-file-name {
    font-size: 0.83rem;
    font-weight: 600;
    color: var(--text-dark);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.4;
}
.ocr-file-meta {
    font-size: 0.72rem;
    color: var(--text-muted);
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.badge-status-new {
    font-size: 0.7rem;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 8px;
    white-space: nowrap;
}
.badge-status-new.processed {
    background: #ECFDF5;
    color: #10B981;
}
.badge-status-new.reviewing {
    background: #F5F3FF;
    color: #8B5CF6;
}
</style>

<script>
function handleDragOver(e) {
    e.preventDefault();
    document.getElementById('dropzone').classList.add('drag-over');
}
function handleDragLeave(e) {
    document.getElementById('dropzone').classList.remove('drag-over');
}
function handleDrop(e) {
    e.preventDefault();
    document.getElementById('dropzone').classList.remove('drag-over');
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        document.getElementById('fileInput').files = files;
        document.getElementById('uploadForm').submit();
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
