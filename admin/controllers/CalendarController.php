<?php
// admin/controllers/CalendarController.php

$message  = '';
$msgType  = '';

// ── Handle new activity POST ─────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_activity'])) {
    $title       = trim($_POST['title']);
    $component   = $_POST['component'];
    $date        = $_POST['activity_date'];
    $time        = $_POST['activity_time'];
    $location    = trim($_POST['location']);
    $description = trim($_POST['description']);
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO activities (title, component, activity_date, activity_time, location, description)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$title, $component, $date, $time, $location, $description]);
        if (function_exists('logAction')) {
            logAction($pdo, 'Create Activity', "Created activity: $title");
        }
        $message = "Activity successfully scheduled.";
        $msgType = "success";
    } catch (PDOException $e) {
        $message = "Database Error: " . $e->getMessage();
        $msgType = "danger";
    }
}

// ── Handle edit activity POST ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_activity'])) {
    $id          = $_POST['activity_id'];
    $title       = trim($_POST['title']);
    $component   = $_POST['component'];
    $date        = $_POST['activity_date'];
    $time        = $_POST['activity_time'];
    $location    = trim($_POST['location']);
    $description = trim($_POST['description']);
    try {
        $stmt = $pdo->prepare(
            "UPDATE activities 
             SET title = ?, component = ?, activity_date = ?, activity_time = ?, location = ?, description = ?
             WHERE id = ?"
        );
        $stmt->execute([$title, $component, $date, $time, $location, $description, $id]);
        if (function_exists('logAction')) {
            logAction($pdo, 'Edit Activity', "Updated activity ID: $id ($title)");
        }
        $message = "Activity successfully updated.";
        $msgType = "success";
    } catch (PDOException $e) {
        $message = "Database Error: " . $e->getMessage();
        $msgType = "danger";
    }
}

// ── Handle delete activity POST ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_activity'])) {
    $id = $_POST['activity_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM activities WHERE id = ?");
        $stmt->execute([$id]);
        if (function_exists('logAction')) {
            logAction($pdo, 'Delete Activity', "Deleted activity ID: $id");
        }
        $message = "Activity successfully deleted.";
        $msgType = "success";
    } catch (PDOException $e) {
        $message = "Database Error: " . $e->getMessage();
        $msgType = "danger";
    }
}


// ── Week window (Mon → Sun, shifted by weekOffset) ───────────────────────────
$today      = new DateTime();
$weekOffset = (int) ($_GET['weekOffset'] ?? 0);
$isoDay     = (int) $today->format('N');          // 1 = Mon … 7 = Sun
$weekStart  = (clone $today)->modify('-' . ($isoDay - 1) . ' days');
if ($weekOffset !== 0) {
    $weekStart->modify(($weekOffset > 0 ? '+' : '') . ($weekOffset * 7) . ' days');
}
$weekEnd = (clone $weekStart)->modify('+6 days');

$weekStartStr = $weekStart->format('Y-m-d');
$weekEndStr   = $weekEnd->format('Y-m-d');

// Fetch activities that fall inside the current week
$stmtWeek = $pdo->prepare(
    "SELECT * FROM activities
     WHERE activity_date BETWEEN ? AND ?
     ORDER BY activity_date ASC, activity_time ASC"
);
$stmtWeek->execute([$weekStartStr, $weekEndStr]);
$week_activities_raw = $stmtWeek->fetchAll();

// Index week activities by date string  (Y-m-d => [activity, …])
$week_activities = [];
foreach ($week_activities_raw as $act) {
    $week_activities[$act['activity_date']][] = $act;
}

// Build the 7-day array for the week strip
$week_days = [];
for ($i = 0; $i < 7; $i++) {
    $d = (clone $weekStart)->modify("+$i days");
    $ymd = $d->format('Y-m-d');
    $week_days[] = [
        'date'       => $d,
        'ymd'        => $ymd,
        'day_abbr'   => strtoupper($d->format('D')),  // MON, TUE …
        'day_num'    => $d->format('j'),
        'is_today'   => ($ymd === $today->format('Y-m-d')),
        'activities' => $week_activities[$ymd] ?? [],
    ];
}

// ── All upcoming activities (for the list below) ─────────────────────────────
$stmtAll = $pdo->query(
    "SELECT * FROM activities
     ORDER BY activity_date ASC, activity_time ASC"
);
$upcoming_activities = $stmtAll->fetchAll();

// ── Month-level data (kept for legacy compatibility if needed) ────────────────
$month              = date('m');
$year               = date('Y');
$days_in_month      = date('t', mktime(0, 0, 0, $month, 1, $year));
$first_day_of_month = date('w', mktime(0, 0, 0, $month, 1, $year));

$stmtDates = $pdo->prepare(
    "SELECT DISTINCT DAY(activity_date) as act_day
     FROM activities
     WHERE MONTH(activity_date) = ? AND YEAR(activity_date) = ?"
);
$stmtDates->execute([$month, $year]);
$active_days = $stmtDates->fetchAll(PDO::FETCH_COLUMN);

// ── Colour map helpers ────────────────────────────────────────────────────────
function componentColor(string $component): array {
    return match ($component) {
        'LTS'  => ['bg' => '#FFF7ED', 'text' => '#C2410C', 'bar' => '#F97316', 'pill' => '#FED7AA'],
        'ROTC' => ['bg' => '#F0FDF4', 'text' => '#15803D', 'bar' => '#22C55E', 'pill' => '#BBF7D0'],
        default=> ['bg' => '#EEF2FF', 'text' => '#4338CA', 'bar' => '#6366F1', 'pill' => '#C7D2FE'],
    };
}
