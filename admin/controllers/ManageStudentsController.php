<?php
// admin/controllers/ManageStudentsController.php

$message = '';
$msgType = '';

// Handle form submission to insert a new student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    $student_id  = trim($_POST['student_id']);
    $full_name   = trim($_POST['full_name']);
    $course      = trim($_POST['course']);
    $year_level  = $_POST['year_level'];
    $contact     = trim($_POST['contact_number']);
    $email       = trim($_POST['email']);
    $section_id  = !empty($_POST['section_id']) ? (int)$_POST['section_id'] : null;

    // Auto-split the Full Name into first and last name for the database
    $name_parts = explode(' ', $full_name, 2);
    $first_name = $name_parts[0];
    $last_name  = $name_parts[1] ?? '';

    // Determine component from selected section (if any)
    $component = null;
    if ($section_id) {
        $stmtSec = $pdo->prepare("SELECT component FROM sections WHERE id = ?");
        $stmtSec->execute([$section_id]);
        $secRow = $stmtSec->fetch();
        $component = $secRow ? $secRow['component'] : null;
    }

    try {
        // Enforce Platoon Limit if section is selected
        if ($section_id) {
            $stmtCap = $pdo->prepare("SELECT max_capacity, (SELECT COUNT(*) FROM enrollments WHERE section_id = sections.id) as current_count FROM sections WHERE id = ?");
            $stmtCap->execute([$section_id]);
            $capData = $stmtCap->fetch();
            if ($capData && $capData['current_count'] >= $capData['max_capacity']) {
                throw new Exception("Platoon / Section is full (Max: {$capData['max_capacity']}). Cannot enroll student.");
            }
        }

        $stmt = $pdo->prepare("INSERT INTO students (student_id, first_name, last_name, course, year_level, contact_number, email, component) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$student_id, $first_name, $last_name, $course, $year_level, $contact, $email, $component]);

        // Enroll into section if selected
        if ($section_id) {
            $stmtE = $pdo->prepare("INSERT INTO enrollments (student_id, section_id, status) VALUES (?, ?, 'Pending')");
            $stmtE->execute([$student_id, $section_id]);
        }

        $message = "Student successfully added" . ($section_id ? " and enrolled into section." : ".");
        $msgType = "success";
        logAction($pdo, 'Created Student', "Added $first_name $last_name ($student_id) under $course" . ($component ? " [$component]" : ""));
    } catch (Exception $e) {
        if ($e->getCode() == 23000) {
            $message = "Error: A student with ID $student_id already exists.";
            $msgType = "danger";
        } else {
            $message = "Database Error: " . $e->getMessage();
            $msgType = "danger";
        }
    }
}

// Handle form submission to edit a student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_student'])) {
    $original_id = trim($_POST['original_student_id']);
    $student_id = trim($_POST['student_id']);
    $full_name = trim($_POST['full_name']);
    $course = trim($_POST['course']);
    $year_level = $_POST['year_level'];
    $contact = trim($_POST['contact_number']);
    $email = trim($_POST['email']);

    $name_parts = explode(' ', $full_name, 2);
    $first_name = $name_parts[0];
    $last_name = $name_parts[1] ?? '';

    try {
        $stmt = $pdo->prepare("UPDATE students SET student_id=?, first_name=?, last_name=?, course=?, year_level=?, contact_number=?, email=? WHERE student_id=?");
        $stmt->execute([$student_id, $first_name, $last_name, $course, $year_level, $contact, $email, $original_id]);

        $message = "Student successfully updated.";
        $msgType = "success";
        logAction($pdo, 'Updated Student', "Updated details for $first_name $last_name ($student_id)");
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $message = "Error: A student with ID $student_id already exists.";
            $msgType = "danger";
        } else {
            $message = "Database Error: " . $e->getMessage();
            $msgType = "danger";
        }
    }
}

// Handle form submission to delete a student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_student'])) {
    $student_id = trim($_POST['student_id']);

    try {
        $stmt = $pdo->prepare("DELETE FROM students WHERE student_id=?");
        $stmt->execute([$student_id]);

        $message = "Student successfully deleted.";
        $msgType = "success";
        logAction($pdo, 'Deleted Student', "Deleted student with ID ($student_id)");
    } catch (PDOException $e) {
        $message = "Database Error: " . $e->getMessage();
        $msgType = "danger";
    }
}

// Handle form submission to enroll an assigned student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll_student'])) {
    $student_id = trim($_POST['student_id']);
    $section_id = $_POST['section_id'];

    try {
        // Enforce Platoon Limit
        $stmtCap = $pdo->prepare("SELECT max_capacity, (SELECT COUNT(*) FROM enrollments WHERE section_id = sections.id) as current_count FROM sections WHERE id = ?");
        $stmtCap->execute([$section_id]);
        $capData = $stmtCap->fetch();
        if ($capData && $capData['current_count'] >= $capData['max_capacity']) {
            throw new Exception("Platoon / Section is full (Max: {$capData['max_capacity']}). Cannot enroll student.");
        }

        $stmt = $pdo->prepare("INSERT INTO enrollments (student_id, section_id, status) VALUES (?, ?, 'Pending')");
        $stmt->execute([$student_id, $section_id]);

        $message = "Student successfully enrolled into section.";
        $msgType = "success";
        logAction($pdo, 'Enrolled Student', "Enrolled student ($student_id) into section ID $section_id");
    } catch (Exception $e) {
        $message = "Database Error: " . $e->getMessage();
        $msgType = "danger";
    }
}

// Handle bulk CSV import
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_import'])) {
    $section_id = !empty($_POST['bulk_section_id']) ? (int)$_POST['bulk_section_id'] : null;

    if (!$section_id) {
        $message = "Please select a section before importing.";
        $msgType = "danger";
    } elseif (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        $message = "Please upload a valid CSV file.";
        $msgType = "danger";
    } else {
        // Get component from section
        $stmtSec = $pdo->prepare("SELECT component FROM sections WHERE id = ?");
        $stmtSec->execute([$section_id]);
        $secRow = $stmtSec->fetch();
        $component = $secRow ? $secRow['component'] : null;

        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, 'r');
        $imported = 0;
        $skipped  = 0;
        $rowNum   = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            if ($rowNum === 1) continue; // Skip header row

            // Map columns: student_id, full_name, course, year_level, contact_number, email
            if (count($row) < 2) { $skipped++; continue; }
            $sid      = trim($row[0]);
            $fullName = trim($row[1]);
            $course   = trim($row[2] ?? '');
            $year     = trim($row[3] ?? '1');
            $contact  = trim($row[4] ?? '');
            $email    = trim($row[5] ?? '');

            if (empty($sid) || empty($fullName)) { $skipped++; continue; }

            $parts = explode(' ', $fullName, 2);
            $fname = $parts[0];
            $lname = $parts[1] ?? '';

            try {
                $stmtIns = $pdo->prepare("INSERT INTO students (student_id, first_name, last_name, course, year_level, contact_number, email, component) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmtIns->execute([$sid, $fname, $lname, $course, $year, $contact, $email, $component]);

                $stmtE = $pdo->prepare("INSERT INTO enrollments (student_id, section_id, status) VALUES (?, ?, 'Pending')");
                $stmtE->execute([$sid, $section_id]);
                $imported++;
            } catch (PDOException $e) {
                $skipped++; // Duplicate or DB error — skip row
            }
        }
        fclose($handle);

        $message = "Bulk import complete: $imported student(s) imported" . ($skipped ? ", $skipped skipped (duplicates/errors)." : ".");
        $msgType = $imported > 0 ? "success" : "warning";
        logAction($pdo, 'Bulk Import', "Imported $imported students into section ID $section_id");
    }
}

// Fetch all sections
$stmtSecs = $pdo->query("SELECT id, component, section_name FROM sections ORDER BY component, section_name");
$allSections = $stmtSecs->fetchAll();

// Fetch all students AND their sections
$stmt = $pdo->query("
    SELECT st.*, s.section_name 
    FROM students st 
    LEFT JOIN enrollments e ON st.student_id = e.student_id 
    LEFT JOIN sections s ON e.section_id = s.id 
    ORDER BY st.created_at DESC
");
$students = $stmt->fetchAll();
