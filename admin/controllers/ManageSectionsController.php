<?php
// admin/controllers/ManageSectionsController.php

$message = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_component'])) {
    $student_id = $_POST['student_id'];
    $component = $_POST['component'];
    try {
        $stmt = $pdo->prepare("UPDATE students SET component = ? WHERE student_id = ?");
        $stmt->execute([$component, $student_id]);
        $message = "Student successfully assigned to $component.";
        $msgType = "success";
    } catch (PDOException $e) {
        $message = "Database Error: " . $e->getMessage();
        $msgType = "danger";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_section'])) {
    $section_name = trim($_POST['section_name']);
    $component = $_POST['component'];
    $school_year = trim($_POST['school_year']);
    $semester = $_POST['semester'];
    try {
        $stmt = $pdo->prepare("INSERT INTO sections (component, section_name, school_year, semester, instructor_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$component, $section_name, $school_year, $semester, $_POST['instructor_id'] ?? null]);
        $message = "Section $section_name ($component) successfully created!";
        $msgType = "success";
    } catch (PDOException $e) {
        $message = "Database Error: " . $e->getMessage();
        $msgType = "danger";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_instructor'])) {
    $section_id = $_POST['section_id'];
    $instructor_id = $_POST['instructor_id'];
    try {
        $stmt = $pdo->prepare("UPDATE sections SET instructor_id = ? WHERE id = ?");
        $stmt->execute([$instructor_id, $section_id]);
        $message = "Instructor successfully assigned to section.";
        $msgType = "success";
    } catch (PDOException $e) {
        $message = "Database Error: " . $e->getMessage();
        $msgType = "danger";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_master_list'])) {
    if (isset($_FILES['master_list_file']) && $_FILES['master_list_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['master_list_file']['tmp_name'];
        $fileName = $_FILES['master_list_file']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($fileExtension === 'csv') {
            if (($handle = fopen($fileTmpPath, 'r')) !== FALSE) {
                // Skip the header row
                fgetcsv($handle);

                $inserted = 0;
                $skipped = 0;

                try {
                    $pdo->beginTransaction();
                    $stmt = $pdo->prepare("INSERT IGNORE INTO students (student_id, first_name, last_name, course, year_level, component, sex, contact_number, email, date_of_birth, complete_address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                    while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                        // Ensure we have enough columns, otherwise skip row
                        if (count($data) < 3 || empty(trim($data[0]))) {
                            continue; // Skip invalid rows missing student ID
                        }

                        $student_id = trim($data[0]);
                        $first_name = trim($data[1] ?? '');
                        $last_name = trim($data[2] ?? '');
                        $course = !empty(trim($data[3] ?? '')) ? trim($data[3]) : null;
                        $year_level = !empty(trim($data[4] ?? '')) ? (int)trim($data[4]) : null;
                        
                        $raw_component = trim($data[5] ?? '');
                        $component = in_array($raw_component, ['CWTS', 'LTS', 'ROTC']) ? $raw_component : null;
                        
                        $raw_sex = trim($data[6] ?? '');
                        $sex = in_array($raw_sex, ['Male', 'Female']) ? $raw_sex : null;
                        
                        $contact_number = !empty(trim($data[7] ?? '')) ? trim($data[7]) : null;
                        $email = !empty(trim($data[8] ?? '')) ? trim($data[8]) : null;
                        $date_of_birth = !empty(trim($data[9] ?? '')) ? trim($data[9]) : null;
                        $complete_address = !empty(trim($data[10] ?? '')) ? trim($data[10]) : null;

                        $stmt->execute([
                            $student_id,
                            $first_name,
                            $last_name,
                            $course,
                            $year_level,
                            $component,
                            $sex,
                            $contact_number,
                            $email,
                            $date_of_birth,
                            $complete_address
                        ]);

                        if ($stmt->rowCount() > 0) {
                            $inserted++;
                        } else {
                            $skipped++;
                        }
                    }

                    $pdo->commit();
                    fclose($handle);

                    $message = "CSV imported successfully. Inserted: $inserted. Skipped (duplicates/invalid): $skipped.";
                    $msgType = "success";
                    
                    if ($inserted > 0 && function_exists('logAction')) {
                        logAction($pdo, 'Import Master List', "Imported $inserted students via CSV.");
                    }

                } catch (Exception $e) {
                    $pdo->rollBack();
                    $message = "Error importing data: " . $e->getMessage();
                    $msgType = "danger";
                }
            } else {
                $message = "Failed to open the uploaded file.";
                $msgType = "danger";
            }
        } else {
            $message = "Invalid file format. Please upload a .csv file.";
            $msgType = "danger";
        }
    } else {
        $message = "No file uploaded or an upload error occurred.";
        $msgType = "danger";
    }
}

$stmtCounts = $pdo->query("SELECT component, COUNT(*) as total FROM students WHERE component IS NOT NULL GROUP BY component");
$counts = $stmtCounts->fetchAll(PDO::FETCH_KEY_PAIR);
$cwts_count = $counts['CWTS'] ?? 0;
$lts_count  = $counts['LTS'] ?? 0;
$rotc_count = $counts['ROTC'] ?? 0;
$cwts_cap = 800;
$lts_cap = 400;
$rotc_cap = 400;

$stmtUnassigned = $pdo->query("SELECT * FROM students WHERE component IS NULL ORDER BY created_at DESC");
$unassigned_students = $stmtUnassigned->fetchAll();
