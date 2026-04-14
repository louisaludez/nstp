<?php
// admin/controllers/ManageSectionsController.php

$message = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_component'])) {
    $student_id = $_POST['student_id'];
    $component = $_POST['component'];
    try {
        $stmt = $pdo->prepare("UPDATE students SET component = ? WHERE student_id = ?");
        $stmt->execute([$component, $student_id]);
        $message = "Student successfully assigned to $component.";
        $msgType = "success";
    } catch (PDOException $e) {
        $message = "Database Error: " . $e->getMessage();
        $msgType = "danger";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_section'])) {
    $section_name = trim($_POST['section_name']);
    $component = $_POST['component'];
    $school_year = trim($_POST['school_year']);
    $semester = $_POST['semester'];
    try {
        $stmt = $pdo->prepare("INSERT INTO sections (component, section_name, school_year, semester) VALUES (?, ?, ?, ?)");
        $stmt->execute([$component, $section_name, $school_year, $semester]);
        $message = "Section $section_name ($component) successfully created!";
        $msgType = "success";
    } catch (PDOException $e) {
        $message = "Database Error: " . $e->getMessage();
        $msgType = "danger";
    }
}

$stmtCounts = $pdo->query("SELECT component, COUNT(*) as total FROM students WHERE component IS NOT NULL GROUP BY component");
$counts = $stmtCounts->fetchAll(PDO::FETCH_KEY_PAIR);
$cwts_count = $counts['CWTS'] ?? 0;
$lts_count  = $counts['LTS'] ?? 0;
$rotc_count = $counts['ROTC'] ?? 0;
$cwts_cap = 800;
$lts_cap = 400;
$rotc_cap = 400;

$stmtUnassigned = $pdo->query("SELECT * FROM students WHERE component IS NULL ORDER BY created_at DESC");
$unassigned_students = $stmtUnassigned->fetchAll();
