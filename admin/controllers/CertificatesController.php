<?php
// admin/controllers/CertificatesController.php

// ── Certificate batches: sections grouped by component ──────────────────────
$cert_batches = [];
try {
    $stmtBatches = $pdo->query("
        SELECT
            sec.component,
            sec.section_name,
            COUNT(DISTINCT e.student_id) AS student_count,
            SUM(CASE WHEN e.status = 'Passed' THEN 1 ELSE 0 END) AS passed_count,
            SUM(CASE WHEN e.serial_number IS NOT NULL THEN 1 ELSE 0 END) AS issued_count
        FROM sections sec
        LEFT JOIN enrollments e ON e.section_id = sec.id
        GROUP BY sec.component, sec.section_name
        ORDER BY sec.component ASC
    ");
    $cert_batches = $stmtBatches->fetchAll();
} catch (Exception $e) {}

// ── Recently issued certificates (enrollments with a serial number) ──────────
$recent_certs = [];
try {
    $stmtRecent = $pdo->query("
        SELECT
            s.full_name,
            sec.component,
            sec.section_name,
            e.serial_number,
            e.updated_at
        FROM enrollments e
        JOIN students s  ON e.student_id = s.id
        JOIN sections sec ON e.section_id = sec.id
        WHERE e.serial_number IS NOT NULL
        ORDER BY e.updated_at DESC
        LIMIT 6
    ");
    $recent_certs = $stmtRecent->fetchAll();
} catch (Exception $e) {}

// ── Total certs issued ───────────────────────────────────────────────────────
$total_certs = 0;
try {
    $total_certs = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE serial_number IS NOT NULL")->fetchColumn();
} catch (Exception $e) {}

// ── Total eligible (passed but no serial number yet) ────────────────────────
$total_eligible = 0;
try {
    $total_eligible = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE status = 'Passed' AND serial_number IS NULL")->fetchColumn();
} catch (Exception $e) {}

// ── Total students ───────────────────────────────────────────────────────────
$total_students = 0;
try {
    $total_students = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
} catch (Exception $e) {}

// ── Component-level stats ────────────────────────────────────────────────────
$component_stats = [];
try {
    $stmtComp = $pdo->query("
        SELECT
            sec.component,
            COUNT(DISTINCT e.student_id) AS total,
            SUM(CASE WHEN e.status = 'Passed' THEN 1 ELSE 0 END) AS passed,
            SUM(CASE WHEN e.serial_number IS NOT NULL THEN 1 ELSE 0 END) AS issued
        FROM enrollments e
        JOIN sections sec ON e.section_id = sec.id
        GROUP BY sec.component
    ");
    while ($row = $stmtComp->fetch(PDO::FETCH_ASSOC)) {
        $component_stats[$row['component']] = $row;
    }
} catch (Exception $e) {}

// Colour map per component
function certComponentColor(string $comp): array {
    return match ($comp) {
        'LTS'   => ['bg' => '#FFF7ED', 'text' => '#C2410C', 'icon_bg' => '#FED7AA', 'bar' => '#F97316'],
        'ROTC'  => ['bg' => '#F0FDF4', 'text' => '#15803D', 'icon_bg' => '#BBF7D0', 'bar' => '#22C55E'],
        default => ['bg' => '#EEF2FF', 'text' => '#4338CA', 'icon_bg' => '#C7D2FE', 'bar' => '#6366F1'],
    };
}

// Human-readable relative date
function relativeDate(string $dateStr): string {
    $ts    = strtotime($dateStr);
    $today = strtotime(date('Y-m-d'));
    $diff  = (int)(($today - strtotime(date('Y-m-d', $ts))) / 86400);
    if ($diff === 0) return 'Today';
    if ($diff === 1) return 'Yesterday';
    return date('M d', $ts);
}
