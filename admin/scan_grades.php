<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login");
    exit;
}

require 'controllers/ScanGradesController.php';

$extra_css = ['../assets/css/pages/admin/scan-grades.css'];
include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

<div class="flex-grow-1 p-5 w-100">
    
    <?php include '../includes/topbar.php'; ?>
    <div class="mb-4">
        <h3 class="fw-bold mb-1" style="color: #111827;">OCR Grade Sheet Upload</h3>
        <p class="text-muted">Upload scanned grade sheets for automatic processing</p>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $msgType ?> alert-dismissible fade show rounded-3">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        
        <div class="col-xl-6">
            <div class="panel-container">
                <h5 class="fw-bold mb-4" style="color: #111827;">Upload Grade Sheet</h5>
                <form action="" method="POST" enctype="multipart/form-data" id="uploadForm">
                    <input type="file" name="grade_sheet" id="fileInput" class="d-none" accept=".pdf, .png, .jpg, .jpeg" onchange="document.getElementById('uploadForm').submit();">
                    <div class="upload-area" onclick="document.getElementById('fileInput').click();">
                        <i class="bi bi-upload upload-icon"></i>
                        <h6 class="fw-bold" style="color: #111827;">Click to upload grade sheet</h6>
                        <small class="text-muted">Support for PDF, PNG, JPG formats</small>
                    </div>
                </form>
                <div class="mt-4 pt-2">
                    <h6 class="fw-bold fs-6" style="color: #111827;">OCR Processing Steps:</h6>
                    <ul class="ocr-steps">
                        <li><i class="bi bi-check-circle"></i> 1. Upload scanned grade sheet</li>
                        <li><i class="bi bi-check-circle"></i> 2. System extracts student data</li>
                        <li><i class="bi bi-check-circle"></i> 3. Review and verify information</li>
                        <li><i class="bi bi-check-circle"></i> 4. Auto-generate certificates for passed students</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="panel-container d-flex flex-column">
                <h5 class="fw-bold mb-4" style="color: #111827;">Extracted Data</h5>
                <?php if (!$file_uploaded): ?>
                    <div class="empty-state flex-grow-1">
                        <i class="bi bi-eye empty-state-icon"></i>
                        <p class="mb-0 text-muted">Upload a grade sheet to see extracted data</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive flex-grow-1">
                        <table class="table table-borderless align-middle">
                            <thead style="background-color: #F9FAFB; border-bottom: 1px solid #E5E7EB;">
                                <tr>
                                    <th class="py-3 text-muted small fw-semibold">Student ID</th>
                                    <th class="py-3 text-muted small fw-semibold">Name</th>
                                    <th class="py-3 text-muted small fw-semibold">Grade</th>
                                    <th class="py-3 text-muted small fw-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($mock_extracted_data as $data): ?>
                                    <tr style="border-bottom: 1px solid #F3F4F6;">
                                        <td class="py-3 text-muted"><?= htmlspecialchars($data['student_id']) ?></td>
                                        <td class="py-3 fw-medium text-dark"><?= htmlspecialchars($data['name']) ?></td>
                                        <td class="py-3 text-dark"><?= htmlspecialchars($data['grade']) ?></td>
                                        <td class="py-3">
                                            <?php if ($data['status'] === 'Passed'): ?>
                                                <span class="badge bg-success bg-opacity-10 text-success px-2 py-1">Passed</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1">Failed</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end mt-3 border-top pt-3">
                        <form method="POST" action="">
                            <input type="hidden" name="save_grades" value="1">
                            <?php foreach ($mock_extracted_data as $i => $data): ?>
                                <input type="hidden" name="students[<?= $i ?>][student_id]" value="<?= htmlspecialchars($data['student_id']) ?>">
                                <input type="hidden" name="students[<?= $i ?>][grade]" value="<?= htmlspecialchars($data['grade']) ?>">
                                <input type="hidden" name="students[<?= $i ?>][status]" value="<?= htmlspecialchars($data['status']) ?>">
                            <?php endforeach; ?>
                            <button type="submit" class="btn btn-brand px-4"><i class="bi bi-save me-2"></i>Save to Database</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

</div> 
</div> 

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
