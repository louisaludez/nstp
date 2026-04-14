<?php
// instructor/controllers/StudentsController.php

$instructor_name = $_SESSION['full_name'];
$instructor_id = $_SESSION['user_id'];

// Get instructor's sections
$stmtSec = $pdo->prepare("SELECT * FROM sections WHERE instructor_id = ?");
$stmtSec->execute([$instructor_id]);
$sections = $stmtSec->fetchAll();

$students = [];
if (count($sections) > 0) {
    $secIds = array_column($sections, 'id');
    $placeholders = implode(',', array_fill(0, count($secIds), '?'));
    $stmtStu = $pdo->prepare("
        SELECT s.*, sec.section_name as section, e.section_id
        FROM students s
        JOIN enrollments e ON s.student_id = e.student_id
        LEFT JOIN sections sec ON e.section_id = sec.id
        WHERE e.section_id IN ($placeholders)
        ORDER BY s.last_name ASC
    ");
    $stmtStu->execute($secIds);
    $students = $stmtStu->fetchAll();
    
    foreach ($students as &$stu) {
        $stmtAtt = $pdo->prepare("SELECT COUNT(*) as tot, SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as pres FROM attendance WHERE student_id = ?");
        $stmtAtt->execute([$stu['student_id']]);
        $attData = $stmtAtt->fetch();
        $stu['attendance'] = ($attData['tot'] > 0) ? round(($attData['pres'] / $attData['tot']) * 100) : 100;
        
        $stmtGrade = $pdo->prepare("SELECT final_grade, status FROM enrollments WHERE student_id = ?");
        $stmtGrade->execute([$stu['student_id']]);
        $enrollment = $stmtGrade->fetch();
        $stu['grade'] = isset($enrollment['final_grade']) ? $enrollment['final_grade'] : 'N/A';
        $stu['status'] = $enrollment['status'] ?? 'Active';
    }
}
