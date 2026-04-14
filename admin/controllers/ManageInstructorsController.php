<?php
// admin/controllers/ManageInstructorsController.php

$message = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_instructor'])) {
    $section_id = $_POST['section_id'];
    $instructor_id = $_POST['instructor_id'];
    try {
        $stmt = $pdo->prepare("UPDATE sections SET instructor_id = ? WHERE id = ?");
        $stmt->execute([$instructor_id, $section_id]);
        $instQuery = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
        $instQuery->execute([$instructor_id]);
        $instName = $instQuery->fetchColumn();
        $secQuery = $pdo->prepare("SELECT section_name FROM sections WHERE id = ?");
        $secQuery->execute([$section_id]);
        $secName = $secQuery->fetchColumn();
        $message = "Prof. $instName successfully assigned to $secName.";
        $msgType = "success";
        logAction($pdo, 'Assigned Instructor', "Assigned Prof. $instName to section $secName");
    } catch (PDOException $e) {
        $message = "Database Error: " . $e->getMessage();
        $msgType = "danger";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_new_instructor'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $component = $_POST['component'];
    $contact_number = trim($_POST['contact_number']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    if ($password !== $confirm_password) {
        $message = "Error: Passwords do not match.";
        $msgType = "danger";
    } elseif (strlen($password) < 6) {
        $message = "Error: Password must be at least 6 characters long.";
        $msgType = "danger";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role, component, contact_number) VALUES (?, ?, ?, 'Instructor', ?, ?)");
            $stmt->execute([$full_name, $email, $hashedPassword, $component, $contact_number]);
            $message = "Instructor Prof. $full_name successfully added.";
            $msgType = "success";
            logAction($pdo, 'Created Instructor', "Created account for Prof. $full_name ($component)");
        } catch (PDOException $e) {
            $message = ($e->getCode() == 23000) ? "Error: Email already exists." : "Error: " . $e->getMessage();
            $msgType = "danger";
        }
    }
}

$stmtInstructors = $pdo->query("
    SELECT u.id, u.full_name, u.email, u.component AS primary_component, u.contact_number,
           GROUP_CONCAT(DISTINCT s.section_name SEPARATOR ',') as assigned_sections,
           (SELECT COUNT(e.student_id) FROM enrollments e JOIN sections sec ON e.section_id = sec.id WHERE sec.instructor_id = u.id) as student_count
    FROM users u 
    LEFT JOIN sections s ON u.id = s.instructor_id 
    WHERE u.role = 'Instructor' 
    GROUP BY u.id
    ORDER BY u.full_name ASC
");
$instructors = $stmtInstructors->fetchAll();

$stmtUnassigned = $pdo->query("
    SELECT s.*, 
           (SELECT COUNT(student_id) FROM enrollments WHERE section_id = s.id) as enrolled_count
    FROM sections s 
    WHERE instructor_id IS NULL 
    ORDER BY component, section_name
");
$unassigned_sections = $stmtUnassigned->fetchAll();
