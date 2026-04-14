<?php
// admin/controllers/ManageStudentsController.php

$message = '';
$msgType = '';

// Handle form submission to insert a new student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    $student_id = trim($_POST['student_id']);
    $full_name = trim($_POST['full_name']);
    $course = trim($_POST['course']);
    $year_level = $_POST['year_level'];
    $contact = trim($_POST['contact_number']);
    $email = trim($_POST['email']);

    // Auto-split the Full Name into first and last name for the database
    $name_parts = explode(' ', $full_name, 2);
    $first_name = $name_parts[0];
    $last_name = $name_parts[1] ?? ''; // If they only enter one name, avoid an error

    try {
        $stmt = $pdo->prepare("INSERT INTO students (student_id, first_name, last_name, course, year_level, contact_number, email) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$student_id, $first_name, $last_name, $course, $year_level, $contact, $email]);

        $message = "Student successfully enrolled.";
        $msgType = "success";
        logAction($pdo, 'Created Student', "Enrolled $first_name $last_name ($student_id) under $course");
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
        $stmt = $pdo->prepare("INSERT INTO enrollments (student_id, section_id, status) VALUES (?, ?, 'Pending')");
        $stmt->execute([$student_id, $section_id]);

        $message = "Student successfully enrolled into section.";
        $msgType = "success";
        logAction($pdo, 'Enrolled Student', "Enrolled student ($student_id) into section ID $section_id");
    } catch (PDOException $e) {
        $message = "Database Error: " . $e->getMessage();
        $msgType = "danger";
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
