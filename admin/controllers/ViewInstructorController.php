<?php
// admin/controllers/ViewInstructorController.php

if (!isset($_GET['id'])) {
    header("Location: manage_instructors.php");
    exit;
}

$instructor_id = (int)$_GET['id'];

// 1. Fetch Instructor Profile
$stmtInst = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'Instructor'");
$stmtInst->execute([$instructor_id]);
$instructor = $stmtInst->fetch();

if (!$instructor) {
    header("Location: manage_instructors.php");
    exit;
}

// 2. Fetch Handled Class Sections
$stmtSec = $pdo->prepare("SELECT id, section_name, component, school_year, room FROM sections WHERE instructor_id = ? ORDER BY section_name ASC");
$stmtSec->execute([$instructor_id]);
$sections = $stmtSec->fetchAll();

// 3. Fetch Handled Students
$stmtStud = $pdo->prepare("
    SELECT s.student_id, s.first_name, s.last_name, s.course, sec.section_name, e.status as grade_status 
    FROM students s
    JOIN enrollments e ON s.student_id = e.student_id
    JOIN sections sec ON e.section_id = sec.id
    WHERE sec.instructor_id = ?
    ORDER BY s.last_name ASC, s.first_name ASC
");
$stmtStud->execute([$instructor_id]);
$students = $stmtStud->fetchAll();
