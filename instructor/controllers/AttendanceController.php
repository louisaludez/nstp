<?php
// instructor/controllers/AttendanceController.php

$instructor_id = $_SESSION['user_id'];
$instructor_name = $_SESSION['full_name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $section_id = $_POST['section'];
    $date = $_POST['date'];
    $statuses = $_POST['status'];
    foreach ($statuses as $sid => $status) {
        $statusMatch = ['present' => 'Present', 'late' => 'Late', 'absent' => 'Absent'];
        $dbStatus = $statusMatch[$status] ?? 'Present';
        $stmtCheck = $pdo->prepare("SELECT id FROM attendance WHERE student_id = ? AND attendance_date = ?");
        $stmtCheck->execute([$sid, $date]);
        if ($stmtCheck->fetch()) {
            $pdo->prepare("UPDATE attendance SET status = ? WHERE student_id = ? AND attendance_date = ?")->execute([$dbStatus, $sid, $date]);
        } else {
            $pdo->prepare("INSERT INTO attendance (student_id, section_id, attendance_date, status) VALUES (?, ?, ?, ?)")->execute([$sid, $section_id, $date, $dbStatus]);
        }
    }
    header("Location: attendance?msg=saved");
    exit;
}

$stmtSec = $pdo->prepare("SELECT * FROM sections WHERE instructor_id = ?");
$stmtSec->execute([$instructor_id]);
$sections = $stmtSec->fetchAll();

$students = [];
$active_section_id = null;
if (count($sections) > 0) {
    $active_section_id = $_GET['section'] ?? $sections[0]['id'];
    $stmtStu = $pdo->prepare("SELECT s.* FROM students s JOIN enrollments e ON s.student_id = e.student_id WHERE e.section_id = ? ORDER BY s.last_name ASC");
    $stmtStu->execute([$active_section_id]);
    $students = $stmtStu->fetchAll();
}
