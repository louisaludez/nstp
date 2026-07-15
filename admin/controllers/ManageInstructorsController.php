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
        $status = $_POST['status'];
        $assign_section_id = $_POST['assign_section_id'] ?? '';

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, component = ?, status = ? WHERE id = ?");
            $stmt->execute([$full_name, $component, $status, $instructor_id]);

            if (!empty($assign_section_id)) {
                $updSec = $pdo->prepare("UPDATE sections SET instructor_id = ? WHERE id = ?");
                $updSec->execute([$instructor_id, $assign_section_id]);
            }

            $pdo->commit();
            $message = "Instructor details successfully updated.";
            $msgType = "success";
            logAction($pdo, 'Updated Instructor', "Updated details for Prof. $full_name");
        } catch (PDOException $e) {
            $pdo->rollBack();
            $message = "Error: " . $e->getMessage();
            $msgType = "danger";
        }
    }

    // 3. Configure Existing User as Instructor
    elseif (isset($_POST['configure_instructor'])) {
        $user_id = $_POST['user_id'];
        $component = $_POST['component'];
        $section_id = $_POST['section_id'] ?? null;
        
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("UPDATE users SET role = 'Instructor', component = ? WHERE id = ?");
            $stmt->execute([$component, $user_id]);
            
            if (!empty($section_id)) {
                $updSec = $pdo->prepare("UPDATE sections SET instructor_id = ? WHERE id = ?");
                $updSec->execute([$user_id, $section_id]);
            }

            $pdo->commit();

            $message = "Instructor successfully configured.";
            $msgType = "success";
            logAction($pdo, 'Configured Instructor', "Configured user ID $user_id as Instructor ($component)");
        } catch (PDOException $e) {
            $pdo->rollBack();
            $message = "Error: " . $e->getMessage();
            $msgType = "danger";
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
