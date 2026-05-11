<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login");
    exit;
}

// Fetch Data (Same as ReportsController but formatted for CSV)

// 1. Overall Stats
$stmtTotalStudents = $pdo->query("SELECT COUNT(*) FROM students");
$total_students = $stmtTotalStudents->fetchColumn();

$stmtStats = $pdo->query("
    SELECT 
        SUM(CASE WHEN status = 'Passed' THEN 1 ELSE 0 END) as passed_count,
        SUM(CASE WHEN status = 'Failed' THEN 1 ELSE 0 END) as failed_count
    FROM enrollments
");
$global_stats = $stmtStats->fetch();
$total_passed = $global_stats['passed_count'] ?? 0;
$total_failed = $global_stats['failed_count'] ?? 0;
$total_graded = $total_passed + $total_failed;
$pass_rate = $total_graded > 0 ? round(($total_passed / $total_graded) * 100, 1) : 0;

$stmtCerts = $pdo->query("SELECT COUNT(serial_number) FROM enrollments WHERE serial_number IS NOT NULL");
$total_certs = $stmtCerts->fetchColumn();

// 2. Component Stats
$stmtComp = $pdo->query("
    SELECT 
        sec.component,
        SUM(CASE WHEN e.status = 'Passed' THEN 1 ELSE 0 END) as passed_count,
        SUM(CASE WHEN e.status = 'Failed' THEN 1 ELSE 0 END) as failed_count
    FROM enrollments e
    JOIN sections sec ON e.section_id = sec.id
    GROUP BY sec.component
");
$comp_stats = $stmtComp->fetchAll(PDO::FETCH_ASSOC);

// 3. Instructor Workload
$stmtWorkload = $pdo->query("
    SELECT 
        u.full_name as instructor, 
        COUNT(DISTINCT s.id) as section_count,
        (SELECT COUNT(e.student_id) FROM enrollments e JOIN sections sec ON e.section_id = sec.id WHERE sec.instructor_id = u.id) as student_count
    FROM users u
    LEFT JOIN sections s ON u.id = s.instructor_id
    WHERE u.role = 'Instructor'
    GROUP BY u.id
    ORDER BY section_count DESC
");
$instructor_workload = $stmtWorkload->fetchAll(PDO::FETCH_ASSOC);

// Generate CSV
$filename = "NSTP_Report_" . date('Y-m-d_H-i-s') . ".csv";
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// Title
fputcsv($output, ["NSTP Program Analytics Report"]);
fputcsv($output, ["Generated on:", date('Y-m-d H:i:s')]);
fputcsv($output, []);

// Summary Section
fputcsv($output, ["SUMMARY STATISTICS"]);
fputcsv($output, ["Metric", "Value"]);
fputcsv($output, ["Overall Pass Rate", $pass_rate . "%"]);
fputcsv($output, ["Total Enrolled", $total_students]);
fputcsv($output, ["Certificates Issued", $total_certs]);
fputcsv($output, []);

// Component Distribution Section
fputcsv($output, ["COMPONENT DISTRIBUTION"]);
fputcsv($output, ["Component", "Passed", "Failed"]);
foreach ($comp_stats as $row) {
    fputcsv($output, [$row['component'], $row['passed_count'], $row['failed_count']]);
}
fputcsv($output, []);

// Instructor Workload Section
fputcsv($output, ["INSTRUCTOR WORKLOAD"]);
fputcsv($output, ["Instructor", "Students", "Sections", "Avg per Section"]);
foreach ($instructor_workload as $inst) {
    $avg = $inst['section_count'] > 0 ? round($inst['student_count'] / $inst['section_count']) : 0;
    fputcsv($output, [
        "Prof. " . $inst['instructor'],
        $inst['student_count'] ?? 0,
        $inst['section_count'],
        $avg
    ]);
}

fclose($output);
exit;
