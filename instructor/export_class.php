<?php
// instructor/export_class.php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Instructor') {
    header("Location: ../login");
    exit;
}

$section_id = $_GET['section_id'] ?? null;
if (!$section_id) {
    header("Location: my_section.php");
    exit;
}

// Fetch section info
$stmtSec = $pdo->prepare("SELECT * FROM sections WHERE id = ? AND instructor_id = ?");
$stmtSec->execute([$section_id, $_SESSION['user_id']]);
$section = $stmtSec->fetch();

if (!$section) {
    echo "Section not found or access denied.";
    exit;
}

$filename = 'Class_List_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $section['component'] . '_' . $section['section_name']) . '_' . date('Ymd') . '.csv';

// Generate headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');

// CSV Headers
$headers = ['#', 'Student No.', 'Name', 'Program'];
// Add 20 empty columns for grid
for ($i = 0; $i < 20; $i++) {
    $headers[] = '';
}
fputcsv($output, $headers);

// Fetch students
$stmtStudents = $pdo->prepare("
    SELECT s.student_id, s.first_name, s.last_name, s.course 
    FROM students s 
    JOIN enrollments e ON s.student_id = e.student_id 
    WHERE e.section_id = ? 
    ORDER BY s.last_name ASC
");
$stmtStudents->execute([$section_id]);

$idx = 1;
while ($student = $stmtStudents->fetch(PDO::FETCH_ASSOC)) {
    $row = [
        $idx++,
        $student['student_id'],
        $student['last_name'] . ', ' . $student['first_name'],
        $student['course']
    ];
    // Add 20 empty cells for grid
    for ($i = 0; $i < 20; $i++) {
        $row[] = '';
    }
    fputcsv($output, $row);
}

fclose($output);
exit;
