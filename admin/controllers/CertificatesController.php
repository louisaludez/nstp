<?php
// admin/controllers/CertificatesController.php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'generate_certificates') {
    $section_name = $_POST['section_name'] ?? '';
    $serials = $_POST['serials'] ?? []; // array of [enrollment_id => serial_number]
    
    // Find the section id
    $stmt = $pdo->prepare("SELECT id, component FROM sections WHERE section_name = ?");
    $stmt->execute([$section_name]);
    $section = $stmt->fetch();
    
    if ($section && !empty($serials)) {
        $section_id = $section['id'];
        $component = $section['component'];
        
        $generated = 0;
        
        foreach ($serials as $enrollment_id => $serial) {
            if (!empty($serial)) {
                $updateStmt = $pdo->prepare("UPDATE enrollments SET serial_number = ? WHERE id = ? AND section_id = ?");
                $updateStmt->execute([$serial, $enrollment_id, $section_id]);
                if ($updateStmt->rowCount() > 0) {
                    $generated++;
                }
            }
        }
        
        if ($generated > 0) {
            logAction($pdo, 'Generated Certificates', "Generated $generated certificates for section $section_name");
        }
        
        header("Location: certificates.php?success=1&download_section=" . urlencode($section_name));
        exit;
    }
}

// Handle CHED approval toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_ched_approval') {
    $pdo->exec("UPDATE settings SET setting_value = IF(setting_value = '1', '0', '1') WHERE setting_key = 'ched_approval'");
    header("Location: certificates.php");
    exit;
}

// Fetch CHED approval status
$ched_approval_status = false;
try {
    $stmtSettings = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'ched_approval'");
    $setting = $stmtSettings->fetch();
    if ($setting && $setting['setting_value'] === '1') {
        $ched_approval_status = true;
    }
} catch (Exception $e) {}

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
            CONCAT(s.last_name, ', ', s.first_name) AS full_name,
            sec.component,
            sec.section_name,
            e.serial_number,
            e.created_at
        FROM enrollments e
        JOIN students s  ON e.student_id = s.student_id
        JOIN sections sec ON e.section_id = sec.id
        WHERE e.serial_number IS NOT NULL
        ORDER BY e.created_at DESC
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
