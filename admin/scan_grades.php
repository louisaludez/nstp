<?php
session_start();
require '../config/db.php';
require_once 'controllers/ScanGradesController.php';

// Fetch grade settings
$stmtSettings = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('grade_scale_range', 'pass_range', 'fail_range')");
$settingsRows = $stmtSettings->fetchAll(PDO::FETCH_KEY_PAIR);
$gradeScaleRange = $settingsRows['grade_scale_range'] ?? '1.0 - 5.0';
$passRange = $settingsRows['pass_range'] ?? '1.0 - 3.0';
$failRange = $settingsRows['fail_range'] ?? '5.0';

// Fetch Import History
$upload_dir = '../uploads/ocr/';
$import_history = [];
if (is_dir($upload_dir)) {
    $files = scandir($upload_dir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $filepath = $upload_dir . $file;
            $import_history[] = [
                'name' => basename($file),
                'date' => filemtime($filepath),
                'size' => filesize($filepath)
            ];
        }
    }
    // Sort by newest first
    usort($import_history, function($a, $b) {
        return $b['date'] - $a['date'];
    });
}

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
    
    <div class="mb-4 pb-1">
        <h4 class="fw-bold mb-1" style="color: #111827;">OCR Grade Import</h4>
        <p class="text-muted" style="font-size: 0.9rem;">Upload XLSX/XLS grade sheets to automatically record grades and enrollments in the database</p>
    </div>

    <div class="row g-4 align-items-start">

        <!-- Left Panel: Upload & Stats -->
        <div class="col-lg-7 col-xl-8">
            <div class="dash-panel" style="padding: 32px; border-radius: 12px; background: white; border: 1px solid #E2E8F0;">

                <!-- Dropzone -->
                <form action="" method="POST" enctype="multipart/form-data" id="uploadForm" class="mb-4">
                    <input type="file" name="grade_sheet" id="fileInput" class="d-none"
                           accept=".xlsx,.xls,.pdf"
                           onchange="document.getElementById('uploadForm').submit();">

                    <div id="dropzone"
                         class="ocr-dropzone d-flex flex-column align-items-center justify-content-center text-center"
                         onclick="document.getElementById('fileInput').click();"
                         ondragover="handleDragOver(event)"
                         ondragleave="handleDragLeave(event)"
                         ondrop="handleDrop(event)"
                         style="border: 2px dashed #C7D2FE; background-color: #ffffff; border-radius: 12px; padding: 60px 32px; cursor: pointer; transition: all 0.2s ease;">

                        <div class="mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: white; border: 1px solid #E2E8F0; border-radius: 12px; color: #4F46E5; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                            <i class="bi bi-upload" style="font-size: 1.5rem;"></i>
                        </div>

                        <h6 class="fw-bold mb-2" style="color: #1E293B; font-size: 1.05rem;">
                            Drop your XLSX grade sheet here or <span style="color: #4F46E5; text-decoration: underline;">click to browse</span>
                        </h6>
                        <p class="mb-0" style="font-size: 0.85rem; color: #94A3B8;">
                            Supports formats (.xlsx, .xls, .pdf) - up to 25 MB
                        </p>
                    </div>
                </form>

                <!-- Grade Scaling editable boxes -->
                <div class="d-flex gap-3 mb-3 flex-wrap flex-md-nowrap">
                    <div class="flex-fill p-3 rounded text-center" style="background-color: #F8FAFC; border: 1px solid #E2E8F0; transition: all 0.2s;">
                        <div class="text-muted fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">GRADE SCALE</div>
                        <input type="text" id="setting-grade-scale" class="form-control form-control-sm text-center fw-bold border-0 bg-transparent" style="font-size: 1.1rem; color: #1E293B; outline: none; box-shadow: none;" value="<?= htmlspecialchars($gradeScaleRange) ?>">
                    </div>
                    <div class="flex-fill p-3 rounded text-center" style="background-color: #ECFDF5; border: 1px solid #D1FAE5; transition: all 0.2s;">
                        <div class="fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px; color: #10B981;">PASS RANGE</div>
                        <input type="text" id="setting-pass-range" class="form-control form-control-sm text-center fw-bold border-0 bg-transparent" style="font-size: 1.1rem; color: #059669; outline: none; box-shadow: none;" value="<?= htmlspecialchars($passRange) ?>">
                    </div>
                    <div class="flex-fill p-3 rounded text-center" style="background-color: #FEF2F2; border: 1px solid #FEE2E2; transition: all 0.2s;">
                        <div class="fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px; color: #EF4444;">FAIL RANGE</div>
                        <input type="text" id="setting-fail-range" class="form-control form-control-sm text-center fw-bold border-0 bg-transparent" style="font-size: 1.1rem; color: #DC2626; outline: none; box-shadow: none;" value="<?= htmlspecialchars($failRange) ?>">
                    </div>
                </div>
                
                <div class="text-end mb-4">
                    <button type="button" class="btn btn-sm text-white px-4 py-2" style="background-color: #4F46E5; border-radius: 6px; font-weight: 500;" onclick="saveGradeSettings()">
                        <i class="bi bi-save me-1"></i> Save Settings
                    </button>
                </div>

                <!-- Info notice -->
                <div class="p-3 rounded d-flex align-items-start gap-3" style="background-color: #FEFEF5; border: 1px solid #FEF08A;">
                    <div style="color: #CA8A04; font-size: 1.25rem;">
                        <i class="bi bi-exclamation-circle"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1" style="color: #854D0E; font-size: 0.85rem;">Expected XLSX Column Structure:</h6>
                        <p class="mb-0" style="color: #A16207; font-size: 0.82rem; line-height: 1.5;">
                            The sheet must contain at least a <strong>Student Name</strong> column and a <strong>Final Grade (or GWA)</strong> column. A <strong>Section</strong> column is optional; if missing, it will be inferred from the filename.
                        </p>
                    </div>
                </div>

                <?php if (!empty($mock_extracted_data)): ?>
                <!-- The table is rendered inside a modal below -->
                <?php endif; ?>

            </div>
        </div>

        <!-- Right Panel: Import History -->
        <div class="col-lg-5 col-xl-4">
            <div class="dash-panel p-4" style="border-radius: 12px; border: 1px solid #E2E8F0; background: white; height: 100%; min-height: 400px;">
                <div class="mb-4">
                    <h6 class="fw-bold mb-1" style="font-size: 1.05rem; color: #111827;">Import History</h6>
                    <p class="text-muted mb-0" style="font-size: 0.85rem;"><?= count($import_history) ?> file(s) processed</p>
                </div>

                <?php if (empty($import_history)): ?>
                <div class="d-flex flex-column align-items-center justify-content-center text-center h-100 py-5">
                    <i class="bi bi-upload mb-3" style="font-size: 2rem; color: #CBD5E1;"></i>
                    <p class="text-muted" style="font-size: 0.85rem; max-width: 200px;">No uploads yet. Drop an XLSX file to get started.</p>
                </div>
                <?php else: ?>
                <div class="ocr-upload-list" style="max-height: 400px; overflow-y: auto; padding-right: 5px;">
                    <?php foreach ($import_history as $history_file): ?>
                    <div class="d-flex align-items-center gap-3 py-3" style="border-bottom: 1px solid #F1F5F9;">
                        <div style="width: 40px; height: 40px; border-radius: 8px; background: #F8FAFC; color: #94A3B8; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                            <i class="bi bi-file-earmark-excel"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0" style="min-width: 0;">
                            <div class="text-truncate" style="color: #1E293B; font-weight: 600; font-size: 0.85rem;"><?= htmlspecialchars($history_file['name']) ?></div>
                            <div class="text-truncate" style="color: #64748B; font-size: 0.75rem;">
                                <?= date('M d, Y h:i A', $history_file['date']) ?>
                            </div>
                        </div>
                        <div style="flex-shrink: 0;">
                            <a href="<?= htmlspecialchars($upload_dir . $history_file['name']) ?>" download class="btn btn-sm text-muted" style="padding: 2px 6px;"><i class="bi bi-download"></i></a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

</div>

<?php if (!empty($mock_extracted_data)): ?>
<div class="modal fade" id="extractedGradesModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <form action="scan_grades.php" method="POST">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title fw-bold">Review Extracted Grades</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-hover align-middle">
              <thead class="table-light sticky-top">
                <tr>
                  <th>Student ID</th>
                  <th>Name</th>
                  <th>Grade</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($mock_extracted_data as $index => $data): ?>
                  <tr>
                    <td>
                      <?= htmlspecialchars($data['student_id']) ?>
                      <input type="hidden" name="students[<?= $index ?>][student_id]" value="<?= htmlspecialchars($data['student_id']) ?>">
                    </td>
                    <td><?= htmlspecialchars($data['name']) ?></td>
                    <td>
                      <?= htmlspecialchars($data['grade']) ?>
                      <input type="hidden" name="students[<?= $index ?>][grade]" value="<?= htmlspecialchars($data['grade']) ?>">
                    </td>
                    <td>
                      <?php if ($data['status'] === 'Passed'): ?>
                        <span class="badge bg-success">Passed</span>
                      <?php else: ?>
                        <span class="badge bg-danger">Failed</span>
                      <?php endif; ?>
                      <input type="hidden" name="students[<?= $index ?>][status]" value="<?= htmlspecialchars($data['status']) ?>">
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="save_grades" class="btn btn-primary px-4" style="background-color: #4F46E5; border: none;">Save Grades to Database</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<script>
function saveGradeSettings() {
    const scale = document.getElementById('setting-grade-scale').value;
    const pass = document.getElementById('setting-pass-range').value;
    const fail = document.getElementById('setting-fail-range').value;

    fetch('controllers/GradeSettingsController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ grade_scale_range: scale, pass_range: pass, fail_range: fail })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                confirmButtonColor: '#4F46E5'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: data.message,
                confirmButtonColor: '#d33'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Something went wrong!',
            confirmButtonColor: '#d33'
        });
    });
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (!empty($message)): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: '<?= $msgType === 'success' ? 'success' : 'error' ?>',
            title: '<?= $msgType === 'success' ? 'Success!' : 'Error!' ?>',
            text: '<?= addslashes($message) ?>',
            <?php if (isset($file_uploaded) && $file_uploaded && $msgType === 'success' && !empty($mock_extracted_data)): ?>
            showCancelButton: true,
            confirmButtonColor: '#4F46E5',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'View extracted students',
            cancelButtonText: 'Cancel'
            <?php else: ?>
            confirmButtonColor: '#4F46E5',
            <?php endif; ?>
        }).then((result) => {
            <?php if (isset($file_uploaded) && $file_uploaded && $msgType === 'success' && !empty($mock_extracted_data)): ?>
            if (result.isConfirmed) {
                var modalEl = document.getElementById('extractedGradesModal');
                if (modalEl) {
                    var extractedModal = new bootstrap.Modal(modalEl);
                    extractedModal.show();
                }
            } else {
                window.location.href = 'scan_grades.php';
            }
            <?php endif; ?>
        });
    });
</script>
<?php endif; ?>
</body>
</html>
