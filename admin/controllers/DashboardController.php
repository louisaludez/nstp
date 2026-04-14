<?php
// admin/controllers/DashboardController.php

$stmt = $pdo->query("
    SELECT 
        COUNT(*) as total_students,
        SUM(CASE WHEN component = 'CWTS' THEN 1 ELSE 0 END) as cwts_count,
        SUM(CASE WHEN component = 'LTS' THEN 1 ELSE 0 END) as lts_count,
        SUM(CASE WHEN component = 'ROTC' THEN 1 ELSE 0 END) as rotc_count
    FROM students
");
$stats = $stmt->fetch();
$total_students = $stats['total_students'] ?: 0;
$cwts_count = $stats['cwts_count'] ?: 0;
$lts_count = $stats['lts_count'] ?: 0;
$rotc_count = $stats['rotc_count'] ?: 0;

$cwts_percent = $total_students > 0 ? round(($cwts_count / $total_students) * 100) : 0;
$lts_percent = $total_students > 0 ? round(($lts_count / $total_students) * 100) : 0;
$rotc_percent = $total_students > 0 ? round(($rotc_count / $total_students) * 100) : 0;

$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'Instructor'");
$active_instructors = $stmt->fetchColumn() ?: 0;

$stmt = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE status = 'Passed'");
$passed_students = $stmt->fetchColumn() ?: 0;

$stmt = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE status = 'Failed'");
$failed_students = $stmt->fetchColumn() ?: 0;

$stmt = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE serial_number IS NOT NULL AND serial_number != ''");
$certificates_issued = $stmt->fetchColumn() ?: 0;

$stmt = $pdo->query("SELECT COUNT(*) FROM activities WHERE activity_date >= CURDATE()");
$upcoming_activities = $stmt->fetchColumn() ?: 0;

try {
    $recent_stmt = $pdo->query("SELECT * FROM audit_logs ORDER BY id DESC LIMIT 5");
    $recent_activities = $recent_stmt->fetchAll();
} catch (PDOException $e) {
    $recent_activities = [];
}

function time_elapsed_string($datetime) {
    if (!$datetime) return 'just now';
    try {
        $now = new DateTime();
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
        if ($diff->y > 0) return $diff->y . ' yr' . ($diff->y > 1 ? 's' : '') . ' ago';
        if ($diff->m > 0) return $diff->m . ' mo' . ($diff->m > 1 ? 's' : '') . ' ago';
        if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
        if ($diff->h > 0) return $diff->h . ' hr' . ($diff->h > 1 ? 's' : '') . ' ago';
        if ($diff->i > 0) return $diff->i . ' min' . ($diff->i > 1 ? 's' : '') . ' ago';
        return 'just now';
    } catch (Exception $e) {
        return 'recently';
    }
}
