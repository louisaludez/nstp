<?php
// admin/export_passed.php
session_start();
require '../config/db.php';
require '../vendor/autoload.php';

use Shuchkin\SimpleXLSXGen;

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

$data = [];
// Headers
$data[] = ['Student ID', 'Full Name', 'Course', 'Year Level', 'Component', 'Section', 'Status'];

try {
    $stmt = $pdo->query("
        SELECT 
            s.student_id, 
            CONCAT(s.first_name, ' ', s.last_name) as full_name,
            s.course,
            s.year_level,
            sec.component,
            sec.section_name,
            e.status
        FROM students s
        JOIN enrollments e ON s.student_id = e.student_id
        JOIN sections sec ON e.section_id = sec.id
        WHERE e.status = 'Passed'
        ORDER BY sec.component, sec.section_name, s.last_name ASC
    ");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = [
            $row['student_id'],
            $row['full_name'],
            $row['course'],
            $row['year_level'] ?? 'N/A',
            $row['component'],
            $row['section_name'],
            $row['status']
        ];
    }
} catch (Exception $e) {
    $data[] = ['Error generating export: ' . $e->getMessage()];
}

$filename = 'Passed_Students_Masterlist_' . date('Y-m-d') . '.xlsx';
$xlsx = SimpleXLSXGen::fromArray($data);
$xlsx->downloadAs($filename);
exit;
