<?php
// instructor/controllers/MySectionController.php

$instructor_name = $_SESSION['full_name'];
$instructor_id = $_SESSION['user_id'];

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

$total_students = 0;
$active_students = 0;
$students = [];
$attendance_rate = 100;

if ($section) {
    $stmtStudents = $pdo->prepare("SELECT s.* FROM students s JOIN enrollments e ON s.student_id = e.student_id WHERE e.section_id = ? ORDER BY s.last_name ASC");
    $stmtStudents->execute([$section['id']]);
    $students = $stmtStudents->fetchAll();
    $total_students = count($students);
    foreach ($students as $s) {
        if (($s['enrollment_status'] ?? 'Active') === 'Active') $active_students++;
    }
    $stmtAtt = $pdo->prepare("SELECT COUNT(*) as tot, SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as pres FROM attendance WHERE section_id = ?");
    $stmtAtt->execute([$section['id']]);
    $attData = $stmtAtt->fetch();
    if ($attData['tot'] > 0) $attendance_rate = round(($attData['pres'] / $attData['tot']) * 100);
}
