<?php
// admin/controllers/ManageInstructorsController.php

$message = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Delete Instructor
    if (isset($_POST['delete_instructor'])) {
        $instructor_id = $_POST['instructor_id'];
        try {
            // First, remove them from sections
            $stmt = $pdo->prepare("UPDATE sections SET instructor_id = NULL WHERE instructor_id = ?");
            $stmt->execute([$instructor_id]);
            // Then delete user
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$instructor_id]);
            
            $message = "Instructor successfully removed.";
            $msgType = "success";
            logAction($pdo, 'Deleted Instructor', "Removed instructor ID $instructor_id from system");
        } catch (PDOException $e) {
            $message = "Database Error: " . $e->getMessage();
            $msgType = "danger";
        }
    }

    // 2. Edit Instructor
    elseif (isset($_POST['edit_instructor'])) {
        $instructor_id = $_POST['instructor_id'];
        $full_name = trim($_POST['full_name']);
        $component = $_POST['component']; 
        $email = trim($_POST['email']);
        $status = $_POST['status'];

        try {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, component = ?, email = ?, status = ? WHERE id = ?");
            $stmt->execute([$full_name, $component, $email, $status, $instructor_id]);
            $message = "Instructor details successfully updated.";
            $msgType = "success";
            logAction($pdo, 'Updated Instructor', "Updated details for Prof. $full_name");
        } catch (PDOException $e) {
            $message = ($e->getCode() == 23000) ? "Error: Email already exists." : "Error: " . $e->getMessage();
            $msgType = "danger";
        }
    }

    // 3. Add New Instructor
    elseif (isset($_POST['add_new_instructor'])) {
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $component = $_POST['component'];
        $section_name = trim($_POST['section_name']);
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
                $pdo->beginTransaction();

                $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role, component) VALUES (?, ?, ?, 'Instructor', ?)");
                $stmt->execute([$full_name, $email, $hashedPassword, $component]);
                
                $instructor_id = $pdo->lastInsertId();

                if (!empty($section_name)) {
                    $checkSec = $pdo->prepare("SELECT id FROM sections WHERE section_name = ?");
                    $checkSec->execute([$section_name]);
                    $existing_section = $checkSec->fetch();

                    if ($existing_section) {
                        $updSec = $pdo->prepare("UPDATE sections SET instructor_id = ? WHERE id = ?");
                        $updSec->execute([$instructor_id, $existing_section['id']]);
                    } else {
                        $insSec = $pdo->prepare("INSERT INTO sections (component, section_name, school_year, semester, instructor_id) VALUES (?, ?, '2026-2027', '1st', ?)");
                        $insSec->execute([$component, $section_name, $instructor_id]);
                    }
                }

                $pdo->commit();

                $message = "Instructor Prof. $full_name successfully added.";
                $msgType = "success";
                logAction($pdo, 'Created Instructor', "Created account for Prof. $full_name ($component)");
            } catch (PDOException $e) {
                $pdo->rollBack();
                $message = ($e->getCode() == 23000) ? "Error: Email already exists." : "Error: " . $e->getMessage();
                $msgType = "danger";
            }
        }
    }
}

$stmtInstructors = $pdo->query("
    SELECT u.id, u.full_name, u.email, u.component AS primary_component, u.status, u.contact_number,
           GROUP_CONCAT(DISTINCT s.section_name SEPARATOR ', ') as assigned_sections,
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
