<?php
// admin/controllers/ReportsController.php

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
$instructor_workload = $stmtWorkload->fetchAll();

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

$stmtComp = $pdo->query("
    SELECT 
        sec.component,
        SUM(CASE WHEN e.status = 'Passed' THEN 1 ELSE 0 END) as passed_count,
        SUM(CASE WHEN e.status = 'Failed' THEN 1 ELSE 0 END) as failed_count
    FROM enrollments e
    JOIN sections sec ON e.section_id = sec.id
    GROUP BY sec.component
");
$comp_data = [];
while ($row = $stmtComp->fetch(PDO::FETCH_ASSOC)) {
    if ($row['component']) $comp_data[$row['component']] = $row;
}

$cwts_p = $comp_data['CWTS']['passed_count'] ?? 0;
$cwts_f = $comp_data['CWTS']['failed_count'] ?? 0;
$lts_p  = $comp_data['LTS']['passed_count'] ?? 0;
$lts_f  = $comp_data['LTS']['failed_count'] ?? 0;
$rotc_p = $comp_data['ROTC']['passed_count'] ?? 0;
$rotc_f = $comp_data['ROTC']['failed_count'] ?? 0;

// Total enrolled per component (all statuses)
$stmtCompTotal = $pdo->query("
    SELECT sec.component, COUNT(e.student_id) as total
    FROM enrollments e
    JOIN sections sec ON e.section_id = sec.id
    GROUP BY sec.component
");
$comp_totals = [];
while ($row = $stmtCompTotal->fetch(PDO::FETCH_ASSOC)) {
    if ($row['component']) $comp_totals[$row['component']] = $row['total'];
}
$cwts_total = $comp_totals['CWTS'] ?? 0;
$lts_total  = $comp_totals['LTS'] ?? 0;
$rotc_total = $comp_totals['ROTC'] ?? 0;

$failure_rate = $total_graded > 0 ? round(($total_failed / $total_graded) * 100, 1) : 0;

$stmtArchived = $pdo->query("
    SELECT 
        e.id as enrollment_id,
        s.student_id,
        CONCAT(s.last_name, ', ', s.first_name) as full_name,
        s.sex as gender,
        sec.section_name,
        sec.component as program,
        u.full_name as instructor_name,
        e.final_grade,
        e.status as remarks,
        e.created_at as date_archived
    FROM enrollments e
    JOIN students s ON e.student_id = s.student_id
    JOIN sections sec ON e.section_id = sec.id
    LEFT JOIN users u ON sec.instructor_id = u.id
    WHERE e.status IN ('Passed', 'Failed')
    ORDER BY e.created_at DESC
");
$archived_records = $stmtArchived->fetchAll();
$archived_count = count($archived_records);
