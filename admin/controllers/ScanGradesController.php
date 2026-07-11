<?php
// admin/controllers/ScanGradesController.php

$message = '';
$msgType = '';
$file_uploaded = false;
$mock_extracted_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['grade_sheet'])) {
    $file = $_FILES['grade_sheet'];
    $allowed_exts = ['xlsx', 'xls', 'pdf'];
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
            $students_for_ocr = [];
            $mock_extracted_data = [];
            
            if (in_array($ext, ['xlsx', 'xls'])) {
                require_once '../vendor/autoload.php';
                if ($xlsx = \Shuchkin\SimpleXLSX::parse($destination)) {
                    $header_values = [];
                    $student_id_index = -1;
                    $name_index = -1;
                    $grade_index = -1;
                    
                    foreach ($xlsx->rows() as $row_index => $row) {
                        if ($row_index === 0) {
                            $header_values = array_map('trim', array_map('strtolower', $row));
                            foreach ($header_values as $idx => $val) {
                                if (strpos($val, 'student id') !== false || strpos($val, 'id number') !== false) $student_id_index = $idx;
                                elseif (strpos($val, 'name') !== false || strpos($val, 'student name') !== false) $name_index = $idx;
                                elseif (strpos($val, 'grade') !== false || strpos($val, 'gwa') !== false || strpos($val, 'final') !== false) $grade_index = $idx;
                            }
                            continue;
                        }
                        
                        if ($name_index === -1 && $grade_index === -1) {
                            break; // Could not find required columns
                        }
                        
                        $student_id = $student_id_index !== -1 ? trim($row[$student_id_index] ?? '') : '';
                        $name = $name_index !== -1 ? trim($row[$name_index] ?? '') : '';
                        $grade = $grade_index !== -1 ? trim($row[$grade_index] ?? '') : '';
                        
                        if (empty($name) && empty($student_id)) continue;
                        
                        $grade_val = floatval($grade);
                        $status = 'Failed';
                        if ($grade_val >= 75 || ($grade_val >= 1.0 && $grade_val <= 3.0)) {
                            $status = 'Passed';
                        }
                        
                        $stu = false;
                        if (!empty($student_id)) {
                            $stmtCheck = $pdo->prepare("SELECT s.student_id, s.first_name, s.last_name FROM students s LEFT JOIN enrollments e ON s.student_id = e.student_id WHERE s.student_id = ?");
                            $stmtCheck->execute([$student_id]);
                            $stu = $stmtCheck->fetch();
                        }
                        
                        if (!$stu && !empty($name)) {
                            $stmtCheck = $pdo->prepare("SELECT s.student_id, s.first_name, s.last_name FROM students s LEFT JOIN enrollments e ON s.student_id = e.student_id WHERE CONCAT(s.last_name, ', ', s.first_name) LIKE ? OR CONCAT(s.first_name, ' ', s.last_name) LIKE ?");
                            $stmtCheck->execute(['%'.$name.'%', '%'.$name.'%']);
                            $stu = $stmtCheck->fetch();
                        }
                        
                        if ($stu) {
                            $mock_extracted_data[] = [
                                'student_id' => $stu['student_id'],
                                'name' => $stu['first_name'] . ' ' . $stu['last_name'],
                                'grade' => $grade,
                                'status' => $status
                            ];
                        } else {
                            $mock_extracted_data[] = [
                                'student_id' => $student_id ?: 'Unknown',
                                'name' => $name,
                                'grade' => $grade,
                                'status' => $status
                            ];
                        }
                    }
                    if (empty($mock_extracted_data)) {
                        $message = "No valid student grades found in the Excel file. Please check the column names.";
                        $msgType = "danger";
                    }
                } else {
                    $message = "Failed to parse Excel file: " . \Shuchkin\SimpleXLSX::parseError();
                    $msgType = "danger";
                }
            } elseif ($ext === 'pdf') {
                $python_script = __DIR__ . '/parse_pdf.py';
                $command = "python " . escapeshellarg($python_script) . " " . escapeshellarg($destination);
                $output = shell_exec($command);
                
                if ($output && strpos($output, 'ERROR:') === false) {
                    $pattern = '/(20\d{2}-\d{4,5})\s+(.*?)\s+((?:\d{2,3}(?:\.\d+)?)|DRP|INC)\s+(\d\.\d+|DRP|INC)\s+\d+\s+(PASSED|FAILED|DROPPED|INC)/i';
                    if (preg_match_all($pattern, $output, $matches, PREG_SET_ORDER)) {
                        foreach ($matches as $match) {
                            $student_id = trim($match[1]);
                            $name = trim($match[2]);
                            $percentage = trim($match[3]);
                            $final_grade = trim($match[4]);
                            $status_raw = strtoupper(trim($match[5]));
                            
                            $status_mapped = 'Failed';
                            if ($status_raw === 'PASSED') $status_mapped = 'Passed';
                            
                            // Check if student exists in database
                            $stmtCheck = $pdo->prepare("SELECT s.student_id, s.first_name, s.last_name FROM students s LEFT JOIN enrollments e ON s.student_id = e.student_id WHERE s.student_id = ?");
                            $stmtCheck->execute([$student_id]);
                            $stu = $stmtCheck->fetch();
                            
                            if ($stu) {
                                $mock_extracted_data[] = [
                                    'student_id' => $stu['student_id'],
                                    'name' => $stu['first_name'] . ' ' . $stu['last_name'],
                                    'grade' => $percentage,
                                    'status' => $status_mapped
                                ];
                            } else {
                                $mock_extracted_data[] = [
                                    'student_id' => $student_id,
                                    'name' => $name,
                                    'grade' => $percentage,
                                    'status' => $status_mapped
                                ];
                            }
                        }
                    }
                    if (empty($mock_extracted_data)) {
                        $message = "No valid student grades found in the PDF. Ensure the PDF format matches the standard grading sheet.";
                        $msgType = "danger";
                    }
                } else {
                    $message = "Failed to parse PDF file. Ensure Python and pypdf are installed.";
                    $msgType = "danger";
                }
            }
        } else {
            $message = "Failed to move uploaded file.";
            $msgType = "danger";
        }
    } else {
        $message = "Invalid file format. Please upload XLSX, XLS, or PDF.";
        $msgType = "danger";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_grades'])) {
    if (isset($_POST['students']) && is_array($_POST['students'])) {
        require_once '../includes/EmailHelper.php';
        $emailHelper = new EmailHelper();
        $updated = 0;
        foreach ($_POST['students'] as $student) {
            $sid = $student['student_id'];
            $grade = $student['grade'];
            $status = $student['status'];
            $stmtCheck = $pdo->prepare("SELECT e.id, e.serial_number, s.email, s.first_name, s.last_name, s.course, sec.component, sec.section_name FROM enrollments e JOIN students s ON e.student_id = s.student_id JOIN sections sec ON e.section_id = sec.id WHERE e.student_id = ?");
            $stmtCheck->execute([$sid]);
            $enrollment = $stmtCheck->fetch();
            if ($enrollment) {
                $stmt = $pdo->prepare("UPDATE enrollments SET final_grade = ?, status = ? WHERE student_id = ?");
                $stmt->execute([$grade, $status, $sid]);
                if ($status === 'Passed' && empty($enrollment['serial_number'])) {
                    $serial = 'NSTP-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                    $pdo->prepare("UPDATE enrollments SET serial_number = ? WHERE student_id = ?")->execute([$serial, $sid]);
                    
                    if (!empty($enrollment['email'])) {
                        $courseInfo = $enrollment['component'] . ' - ' . $enrollment['section_name'];
                        $studentName = $enrollment['first_name'] . ' ' . $enrollment['last_name'];
                        $emailHelper->sendPassNotification($enrollment['email'], $studentName, $serial, $courseInfo);
                    }
                }
                $updated++;
            }
        }
        $message = "Successfully saved $updated grades to the database.";
        $msgType = "success";
    }
}
