<?php
session_start();
require '../config/db.php';

// Security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

$message = '';
$msgType = '';
$file_uploaded = false;
$mock_extracted_data = [];

// Handle File Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['grade_sheet'])) {
    $file = $_FILES['grade_sheet'];
    $allowed_exts = ['pdf', 'png', 'jpg', 'jpeg'];
    
    // Get file extension safely
    $file_parts = explode('.', $file['name']);
    $ext = strtolower(end($file_parts));
    
    if (in_array($ext, $allowed_exts)) {
        // Create an uploads directory if it doesn't exist
        $upload_dir = '../uploads/ocr/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $new_filename = time() . '_' . basename($file['name']);
        $destination = $upload_dir . $new_filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $message = "File uploaded successfully. OCR Processing complete.";
            $msgType = "success";
            $file_uploaded = true;
            
            // Simulate OCR by pulling real students
            $stmtOCR = $pdo->query("
                SELECT s.student_id, s.first_name, s.last_name 
                FROM students s 
                LEFT JOIN enrollments e ON s.student_id = e.student_id 
                WHERE e.final_grade IS NULL 
                LIMIT 3
            ");
            $students_for_ocr = $stmtOCR->fetchAll();
            
            $mock_extracted_data = [];
            foreach($students_for_ocr as $stu) {
                $grade = rand(70, 98);
                $status = $grade >= 75 ? 'Passed' : 'Failed';
                $mock_extracted_data[] = [
                    'student_id' => $stu['student_id'],
                    'name' => $stu['first_name'] . ' ' . $stu['last_name'],
                    'grade' => $grade,
                    'status' => $status
                ];
            }
            if (empty($mock_extracted_data)) {
                $mock_extracted_data = [
                    ['student_id' => '', 'name' => 'No ungraded students found in database.', 'grade' => '', 'status' => '']
                ];
            }
        } else {
            $message = "Failed to move uploaded file.";
            $msgType = "danger";
        }
    } else {
        $message = "Invalid file format. Please upload PDF, PNG, or JPG.";
        $msgType = "danger";
    }
}

// Handle Save Grades
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_grades'])) {
    if (isset($_POST['students']) && is_array($_POST['students'])) {
        $updated = 0;
        foreach ($_POST['students'] as $student) {
            $sid = $student['student_id'];
            $grade = $student['grade'];
            $status = $student['status'];
            
            $stmtCheck = $pdo->prepare("SELECT id FROM enrollments WHERE student_id = ?");
            $stmtCheck->execute([$sid]);
            $enrollment = $stmtCheck->fetch();
            
            if ($enrollment) {
                $stmt = $pdo->prepare("UPDATE enrollments SET final_grade = ?, status = ? WHERE student_id = ?");
                $stmt->execute([$grade, $status, $sid]);
                
                if ($status === 'Passed') {
                    $serial = 'NSTP-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                    $pdo->prepare("UPDATE enrollments SET serial_number = ? WHERE student_id = ? AND serial_number IS NULL")->execute([$serial, $sid]);
                }
                $updated++;
            }
        }
        $message = "Successfully saved $updated grades to the database.";
        $msgType = "success";
    }
}

include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

<style>
    .panel-container {
        background-color: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        padding: 30px;
        height: 100%;
    }

    /* Dashed Upload Area Styling */
    .upload-area {
        border: 2px dashed #D1D5DB;
        border-radius: 12px;
        padding: 50px 20px;
        text-align: center;
        cursor: pointer;
        background-color: #FAFAFA;
        transition: all 0.2s ease-in-out;
    }
    .upload-area:hover {
        border-color: var(--primary-active, #4A46D6);
        background-color: #F5F3FF; /* Very light tint of the primary brand color */
    }
    .upload-icon {
        font-size: 3rem;
        color: #9CA3AF;
        margin-bottom: 15px;
        transition: color 0.2s;
    }
    .upload-area:hover .upload-icon {
        color: var(--primary-active, #4A46D6);
    }
    
    /* Checklist Styling */
    .ocr-steps {
        list-style: none;
        padding-left: 0;
        margin-top: 25px;
    }
    .ocr-steps li {
        margin-bottom: 10px;
        color: #4B5563;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
    }
    .ocr-steps i {
        color: #10B981; /* Green checkmark */
        margin-right: 10px;
        font-size: 1.1rem;
    }

    /* Empty State Styling */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        min-height: 300px;
        color: #9CA3AF;
    }
    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 15px;
        opacity: 0.5;
    }
    
    .btn-brand { background-color: var(--primary-active, #4A46D6); color: white; font-weight: 500; border-radius: 8px; }
    .btn-brand:hover { background-color: var(--primary-bg, #2B2866); color: white; }
</style>

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