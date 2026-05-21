<?php
// instructor/controllers/DashboardController.php

$instructor_id = $_SESSION['user_id'];
$instructor_name = $_SESSION['full_name'];

// ── All assigned sections ────────────────────────────────────────────────────
$stmtAllSections = $pdo->prepare("SELECT * FROM sections WHERE instructor_id = ? ORDER BY component, section_name");
$stmtAllSections->execute([$instructor_id]);
$all_assigned_sections = $stmtAllSections->fetchAll();

$current_section_id = $_GET['section_id'] ?? null;
if (!$current_section_id && count($all_assigned_sections) > 0) {
    $current_section_id = $all_assigned_sections[0]['id'];
}

$section = null;
if ($current_section_id) {
    foreach ($all_assigned_sections as $sec) {
        if ($sec['id'] == $current_section_id) { $section = $sec; break; }
    }
}

$student_count = 0;
$active_student_count = 0;
$attendance_rate = 100;

if ($section) {
    $stmtStu = $pdo->prepare("SELECT COUNT(s.student_id) FROM students s JOIN enrollments e ON s.student_id = e.student_id WHERE e.section_id = ?");
    $stmtStu->execute([$section['id']]);
    $student_count = $stmtStu->fetchColumn();

    $stmtAct = $pdo->prepare("SELECT COUNT(s.student_id) FROM students s JOIN enrollments e ON s.student_id = e.student_id WHERE e.section_id = ? AND s.enrollment_status = 'Active'");
    $stmtAct->execute([$section['id']]);
    $active_student_count = $stmtAct->fetchColumn();
    
    $stmtAtt = $pdo->prepare("SELECT COUNT(*) as tot, SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as pres FROM attendance WHERE section_id = ?");
    $stmtAtt->execute([$section['id']]);
    $attData = $stmtAtt->fetch();
    if ($attData['tot'] > 0) $attendance_rate = round(($attData['pres'] / $attData['tot']) * 100);
}

// ── Activity plans & accomplishment reports (recent) ─────────────────────────
$stmtPlans = $pdo->prepare("SELECT * FROM activity_plans WHERE instructor_id = ? ORDER BY submitted_date DESC LIMIT 5");
$stmtPlans->execute([$instructor_id]);
$activity_plans = $stmtPlans->fetchAll();

$stmtReps = $pdo->prepare("SELECT * FROM accomplishment_reports WHERE instructor_id = ? ORDER BY submitted_date DESC LIMIT 5");
$stmtReps->execute([$instructor_id]);
$accomplishment_reports = $stmtReps->fetchAll();

$stmtSessions = $pdo->prepare("SELECT * FROM activities WHERE component = ? AND activity_date >= CURDATE() ORDER BY activity_date ASC LIMIT 5");
$stmtSessions->execute([$section['component'] ?? '']);
$upcoming_sessions = $stmtSessions->fetchAll();

// ── "Due this week" count (pending plans with scheduled_date in current week) ─
$stmtDueWeek = $pdo->prepare("
    SELECT COUNT(*) FROM activity_plans
    WHERE instructor_id = ? AND status = 'Pending'
      AND scheduled_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL (6 - WEEKDAY(CURDATE())) DAY)
");
$stmtDueWeek->execute([$instructor_id]);
$due_this_week = $stmtDueWeek->fetchColumn() ?: 0;

// ── "Approved this month" count ──────────────────────────────────────────────
$stmtAppMonth = $pdo->prepare("
    SELECT COUNT(*) FROM activity_plans
    WHERE instructor_id = ? AND status = 'Approved'
      AND submitted_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
");
$stmtAppMonth->execute([$instructor_id]);
$approved_this_month = $stmtAppMonth->fetchColumn() ?: 0;

// ── Calendar: 14-day window of activities (from today) ───────────────────────
// Build an array of instructor's components for the activity lookup
$instructor_components = array_unique(array_column($all_assigned_sections, 'component'));
$calendar_activities_by_date = [];

if (!empty($instructor_components)) {
    // Build placeholders for IN clause
    $placeholders = implode(',', array_fill(0, count($instructor_components), '?'));
    $stmtCalAct = $pdo->prepare("
        SELECT activity_date, component
        FROM activities
        WHERE activity_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 13 DAY)
          AND (component IN ($placeholders) OR component = 'All Programs')
        ORDER BY activity_date
    ");
    $stmtCalAct->execute($instructor_components);
    while ($row = $stmtCalAct->fetch(PDO::FETCH_ASSOC)) {
        $calendar_activities_by_date[$row['activity_date']] = $row['component'];
    }
}

// Build the 14-day calendar array dynamically
$today_str = date('Y-m-d');
$calendar_days = [];
$dot_colors = ['CWTS' => '#3B82F6', 'LTS' => '#F59E0B', 'ROTC' => '#EF4444', 'All Programs' => '#10B981'];

for ($i = 0; $i < 14; $i++) {
    $ts = strtotime("+{$i} days");
    $date_str = date('Y-m-d', $ts);
    $dot = '';
    if (isset($calendar_activities_by_date[$date_str])) {
        $comp = $calendar_activities_by_date[$date_str];
        $dot = $dot_colors[$comp] ?? '#6366F1';
    }
    $calendar_days[] = [
        'day'    => strtoupper(date('D', $ts)),
        'date'   => date('j', $ts),
        'dot'    => $dot,
        'active' => ($date_str === $today_str),
    ];
}
$calendar_month_label = date('F Y');

// ── Submission Tracker: combine activity_plans + accomplishment_reports ───────
// We fetch recent items from both tables and merge them into one timeline.
$tracker_items = [];

// Activity Plans (with section info)
$stmtTracker1 = $pdo->prepare("
    SELECT ap.title, ap.status, ap.scheduled_date, ap.submitted_date,
           s.component, s.section_name
    FROM activity_plans ap
    LEFT JOIN sections s ON ap.section_id = s.id
    WHERE ap.instructor_id = ?
    ORDER BY ap.submitted_date DESC
    LIMIT 5
");
$stmtTracker1->execute([$instructor_id]);
while ($row = $stmtTracker1->fetch(PDO::FETCH_ASSOC)) {
    $status = $row['status'];
    // Map DB status to display badge
    if ($status === 'Approved') {
        $badge_label = 'Approved';
        $badge_border = '#A7F3D0';
        $badge_color = '#10B981';
        $badge_icon = 'bi-check-circle';
        $date_label = 'Approved ' . date('M j', strtotime($row['submitted_date']));
    } elseif ($status === 'Rejected') {
        $badge_label = 'Revisions';
        $badge_border = '#FECACA';
        $badge_color = '#EF4444';
        $badge_icon = 'bi-exclamation-circle';
        $date_label = 'Needs revisions';
    } else { // Pending
        $badge_label = 'Submitted';
        $badge_border = '#BFDBFE';
        $badge_color = '#3B82F6';
        $badge_icon = 'bi-clock-history';
        $date_label = $row['scheduled_date']
            ? 'Due ' . date('M j', strtotime($row['scheduled_date']))
            : 'Submitted ' . date('M j', strtotime($row['submitted_date']));
    }

    $sec_label = $row['component']
        ? htmlspecialchars($row['component']) . ' · ' . htmlspecialchars($row['section_name'])
        : 'All sections';

    $tracker_items[] = [
        'title'        => htmlspecialchars($row['title']),
        'subtitle'     => $sec_label . ' · ' . $date_label,
        'badge_label'  => $badge_label,
        'badge_border' => $badge_border,
        'badge_color'  => $badge_color,
        'badge_icon'   => $badge_icon,
        'sort_date'    => $row['submitted_date'],
    ];
}

// Also include accomplishment reports
$stmtTracker2 = $pdo->prepare("
    SELECT ar.title, ar.status, ar.completed_date, ar.submitted_date,
           s.component, s.section_name
    FROM accomplishment_reports ar
    LEFT JOIN sections s ON ar.section_id = s.id
    WHERE ar.instructor_id = ?
    ORDER BY ar.submitted_date DESC
    LIMIT 5
");
$stmtTracker2->execute([$instructor_id]);
while ($row = $stmtTracker2->fetch(PDO::FETCH_ASSOC)) {
    $status = $row['status'];
    if ($status === 'Reviewed') {
        $badge_label = 'Approved';
        $badge_border = '#A7F3D0';
        $badge_color = '#10B981';
        $badge_icon = 'bi-check-circle';
        $date_label = 'Reviewed ' . date('M j', strtotime($row['submitted_date']));
    } else { // Pending
        $badge_label = 'Submitted';
        $badge_border = '#BFDBFE';
        $badge_color = '#3B82F6';
        $badge_icon = 'bi-clock-history';
        $date_label = 'Submitted ' . date('M j', strtotime($row['submitted_date']));
    }

    $sec_label = $row['component']
        ? htmlspecialchars($row['component']) . ' · ' . htmlspecialchars($row['section_name'])
        : 'All sections';

    $tracker_items[] = [
        'title'        => htmlspecialchars($row['title']),
        'subtitle'     => $sec_label . ' · ' . $date_label,
        'badge_label'  => $badge_label,
        'badge_border' => $badge_border,
        'badge_color'  => $badge_color,
        'badge_icon'   => $badge_icon,
        'sort_date'    => $row['submitted_date'],
    ];
}

// Sort by date descending and cap at 5
usort($tracker_items, fn($a, $b) => strtotime($b['sort_date']) - strtotime($a['sort_date']));
$tracker_items = array_slice($tracker_items, 0, 5);

