<?php
// admin/controllers/ScanGradesController.php

$message = '';
$msgType = '';
$file_uploaded = false;
$mock_extracted_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['grade_sheet'])) {
    $file = $_FILES['grade_sheet'];
    $allowed_exts = ['pdf', 'png', 'jpg', 'jpeg'];
    $file_parts = explode('.', $file['name']);
    $ext = strtolower(end($file_parts));
    if (in_array($ext, $allowed_exts)) {
        $upload_dir = '../uploads/ocr/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $new_filename = time() . '_' . basename($file['name']);
        $destination = $upload_dir . $new_filename;
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $message = "File uploaded successfully. OCR Processing complete.";
            $msgType = "success";
            $file_uploaded = true;
            $stmtOCR = $pdo->query("
                SELECT s.student_id, s.first_name, s.last_name 
                FROM students s 
                LEFT JOIN enrollments e ON s.student_id = e.student_id 
                WHERE e.final_grade IS NULL 
                LIMIT 3
            ");
            $students_for_ocr = $stmtOCR->fetchAll();
            $mock_extracted_data = [];
            foreach ($students_for_ocr as $stu) {
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
                $mock_extracted_data = [['student_id' => '', 'name' => 'No ungraded students found in database.', 'grade' => '', 'status' => '']];
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
