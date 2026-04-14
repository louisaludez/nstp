<?php
// instructor/controllers/DashboardController.php

$instructor_id = $_SESSION['user_id'];
$instructor_name = $_SESSION['full_name'];

$stmtAllSections = $pdo->prepare("SELECT * FROM sections WHERE instructor_id = ? ORDER BY component, section_name");
$stmtAllSections->execute([$instructor_id]);
$all_assigned_sections = $stmtAllSections->fetchAll();

$current_section_id = $_GET['section_id'] ?? null;
if (!$current_section_id && count($all_assigned_sections) > 0) {
    $current_section_id = $all_assigned_sections[0]['id'];
}

$section = null;
if ($current_section_id) {
    foreach ($all_assigned_sections as $sec) {
        if ($sec['id'] == $current_section_id) { $section = $sec; break; }
    }
}

$student_count = 0;
$active_student_count = 0;
$attendance_rate = 100;

if ($section) {
    $stmtStu = $pdo->prepare("SELECT COUNT(s.student_id) FROM students s JOIN enrollments e ON s.student_id = e.student_id WHERE e.section_id = ?");
    $stmtStu->execute([$section['id']]);
    $student_count = $stmtStu->fetchColumn();

    $stmtAct = $pdo->prepare("SELECT COUNT(s.student_id) FROM students s JOIN enrollments e ON s.student_id = e.student_id WHERE e.section_id = ? AND s.enrollment_status = 'Active'");
    $stmtAct->execute([$section['id']]);
    $active_student_count = $stmtAct->fetchColumn();
    
    $stmtAtt = $pdo->prepare("SELECT COUNT(*) as tot, SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as pres FROM attendance WHERE section_id = ?");
    $stmtAtt->execute([$section['id']]);
    $attData = $stmtAtt->fetch();
    if ($attData['tot'] > 0) $attendance_rate = round(($attData['pres'] / $attData['tot']) * 100);
}

$stmtPlans = $pdo->prepare("SELECT * FROM activity_plans WHERE instructor_id = ? ORDER BY submitted_date DESC LIMIT 5");
$stmtPlans->execute([$instructor_id]);
$activity_plans = $stmtPlans->fetchAll();

$stmtReps = $pdo->prepare("SELECT * FROM accomplishment_reports WHERE instructor_id = ? ORDER BY submitted_date DESC LIMIT 5");
$stmtReps->execute([$instructor_id]);
$accomplishment_reports = $stmtReps->fetchAll();

$stmtSessions = $pdo->prepare("SELECT * FROM activities WHERE component = ? AND activity_date >= CURDATE() ORDER BY activity_date ASC LIMIT 5");
$stmtSessions->execute([$section['component'] ?? '']);
$upcoming_sessions = $stmtSessions->fetchAll();
